<?php

namespace App\Livewire\Catalog;

use App\Models\ProductReview;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Rating & Ulasan Produk | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class ReviewIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $ratingFilter = '';

    public string $statusFilter = '';

    public string $sortBy = 'latest';

    // Admin Reply Modal State
    public ?int $replyingReviewId = null;

    public string $adminReplyText = '';

    // Delete Confirmation State
    public ?int $deletingReviewId = null;

    /**
     * Define custom pagination template.
     */
    public function paginationView(): string
    {
        return 'vendor.pagination.custom';
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRatingFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openReplyModal(int $id): void
    {
        $review = ProductReview::findOrFail($id);
        $this->replyingReviewId = $review->id;
        $this->adminReplyText = $review->admin_reply ?? '';

        $this->dispatch('open-modal-reply-modal');
    }

    public function saveReply(): void
    {
        $this->validate([
            'adminReplyText' => ['required', 'string', 'max:1000'],
        ]);

        if ($this->replyingReviewId) {
            $review = ProductReview::findOrFail($this->replyingReviewId);
            $review->update([
                'admin_reply' => trim($this->adminReplyText),
                'admin_replied_at' => now(),
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Balasan Tersimpan',
                'message' => 'Tanggapan resmi manajemen toko berhasil dipublikasikan.',
            ]);
        }

        $this->dispatch('close-modal-reply-modal');
        $this->replyingReviewId = null;
        $this->adminReplyText = '';
    }

    public function toggleStatus(int $id): void
    {
        $review = ProductReview::findOrFail($id);
        $review->status = $review->status === 'approved' ? 'hidden' : 'approved';
        $review->save();

        // Recalculate product rating
        $review->product?->recalculateRating();

        $statusLabel = $review->status === 'approved' ? 'ditampilkan kembali' : 'disembunyikan dari Storefront';
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Status Diperbarui',
            'message' => "Ulasan dari {$review->customer?->name} telah {$statusLabel}.",
        ]);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingReviewId = $id;
        $this->dispatch('open-confirmation-delete-review-modal');
    }

    public function deleteReview(): void
    {
        if (!$this->deletingReviewId) return;

        $review = ProductReview::find($this->deletingReviewId);
        if ($review) {
            $product = $review->product;
            $review->delete();
            $product?->recalculateRating();

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Ulasan Dihapus',
                'message' => 'Ulasan berhasil dihapus dari sistem secara permanen.',
            ]);
        }

        $this->deletingReviewId = null;
    }

    public function render()
    {
        $totalReviews = ProductReview::count();
        $globalAvgRating = round((float) (ProductReview::where('status', 'approved')->avg('rating') ?: 5.0), 1);
        $verifiedBuyerCount = ProductReview::where('is_verified_purchase', true)->count();
        $hiddenCount = ProductReview::where('status', 'hidden')->count();

        $query = ProductReview::with([
            'product:id,name,slug,featured_image',
            'customer:id,name,email,membership_tier,avatar',
            'order:id,order_number',
        ]);

        if (!empty($this->search)) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('headline', 'like', $s)
                  ->orWhere('review', 'like', $s)
                  ->orWhereHas('product', fn ($p) => $p->where('name', 'like', $s))
                  ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', $s)->orWhere('email', 'like', $s));
            });
        }

        if (!empty($this->ratingFilter)) {
            $query->where('rating', (int) $this->ratingFilter);
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        switch ($this->sortBy) {
            case 'rating_desc':
                $query->orderBy('rating', 'desc')->latest();
                break;
            case 'rating_asc':
                $query->orderBy('rating', 'asc')->latest();
                break;
            default:
                $query->latest();
                break;
        }

        $reviews = $query->paginate(12);

        return view('livewire.catalog.review-index', [
            'reviews' => $reviews,
            'totalReviews' => $totalReviews,
            'globalAvgRating' => $globalAvgRating,
            'verifiedBuyerCount' => $verifiedBuyerCount,
            'hiddenCount' => $hiddenCount,
        ]);
    }
}
