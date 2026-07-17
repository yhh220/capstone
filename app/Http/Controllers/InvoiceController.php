<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /** Printable on-screen invoice (with Print / Download PDF buttons). */
    public function show(string $orderNumber)
    {
        return view('invoice', ['order' => $this->authorizedOrder($orderNumber)]);
    }

    /** Downloadable PDF invoice. */
    public function download(string $orderNumber)
    {
        $order = $this->authorizedOrder($orderNumber);

        return Pdf::loadView('invoice', ['order' => $order, 'pdf' => true])
            ->download('invoice-'.$order->order_number.'.pdf');
    }

    /**
     * The order must belong to the signed-in customer, or the viewer must be
     * admin/staff. Anything else is forbidden.
     */
    private function authorizedOrder(string $orderNumber): Order
    {
        // Resolve from either guard — customers sign in on 'web', admins on 'admin'.
        $user = Auth::guard('web')->user() ?? Auth::guard('admin')->user();
        abort_unless($user, 403);

        $order = Order::where('order_number', $orderNumber)->with('items')->firstOrFail();

        abort_unless(
            $order->user_id === $user->id || $user->isAdmin() || $user->isStaff(),
            403,
        );

        return $order;
    }
}
