<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ShippingLabelController extends Controller
{
    /**
     * Render the 100mm x 150mm Thermal Shipping Label for a given Order.
     */
    public function print(Order $order, Request $request): View
    {
        $order->load(['customer', 'items', 'address', 'shipment']);

        return view('orders.shipping-label', [
            'order' => $order,
            'autoPrint' => $request->boolean('autoprint', true),
        ]);
    }
}
