<?php

namespace App\Console\Commands;

use App\Actions\Logistics\SyncBiteshipTrackingAction;
use App\Models\Shipment;
use Illuminate\Console\Command;

class SyncActiveShipmentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logistics:sync-active';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync active in-transit Biteship shipments with live tracking milestones';

    /**
     * Execute the console command.
     */
    public function handle(SyncBiteshipTrackingAction $syncAction): int
    {
        $this->info('Starting automated sync for active shipments...');

        // Find shipments that are not completed, delivered, cancelled or returned
        $activeShipments = Shipment::with('order')
            ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
            ->whereNotNull('biteship_tracking_id')
            ->get();

        $count = $activeShipments->count();
        $this->info("Found {$count} active shipments to synchronize.");

        $synced = 0;
        foreach ($activeShipments as $shipment) {
            if (! $shipment->order) {
                continue;
            }

            $res = $syncAction->execute($shipment->order);
            if ($res['success']) {
                $synced++;
                $this->line("✓ Synced Order #{$shipment->order->order_number} [{$shipment->waybill_id}]: {$res['message']}");
            } else {
                $this->warn("⚠ Failed Order #{$shipment->order->order_number}: {$res['message']}");
            }
        }

        $this->info("Successfully synchronized {$synced}/{$count} active shipments.");

        return Command::SUCCESS;
    }
}
