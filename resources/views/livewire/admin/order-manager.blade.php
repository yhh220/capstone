<div>
    @section('page-title', 'Orders')

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mb-6 text-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search order, customer..."
               class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red flex-1">
        <select wire:model.live="statusFilter" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="shipped">Shipped</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-left">Order #</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Customer</th>
                    <th class="px-4 py-3 text-left">Total</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left hidden lg:table-cell">Date</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-mono font-bold text-gray-800 text-xs">{{ $order->order_number }}</td>
                    <td class="px-4 py-3 hidden md:table-cell">
                        <div class="font-semibold text-gray-800">{{ $order->customer_name }}</div>
                        <div class="text-xs text-gray-400">{{ $order->customer_email }}</div>
                    </td>
                    <td class="px-4 py-3 font-bold text-gray-800">RM {{ number_format($order->total, 2) }}</td>
                    <td class="px-4 py-3">
                        <select wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                class="text-xs border-0 rounded-full px-2 py-1 font-semibold focus:outline-none
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-700
                                @elseif($order->status === 'processing') bg-blue-100 text-blue-700
                                @elseif($order->status === 'shipped') bg-indigo-100 text-indigo-700
                                @elseif($order->status === 'delivered') bg-green-100 text-green-700
                                @else bg-red-100 text-red-700 @endif">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </td>
                    <td class="px-4 py-3 hidden lg:table-cell text-gray-500 text-xs">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-right">
                        <button wire:click="viewOrder({{ $order->id }})"
                                class="text-blue-600 hover:text-blue-800 font-medium text-xs px-2 py-1 rounded hover:bg-blue-50 transition">
                            View
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                        <div class="text-4xl mb-2">🛒</div>
                        No orders found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-100">{{ $orders->links() }}</div>
    </div>

    <!-- Order Detail Modal -->
    @if($viewingOrder)
    <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="closeView">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl">
                <h2 class="text-lg font-black text-gray-800">Order {{ $viewingOrder->order_number }}</h2>
                <button wire:click="closeView" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div class="p-6 space-y-4 text-sm">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Customer</div>
                        <div class="font-semibold">{{ $viewingOrder->customer_name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Email</div>
                        <div>{{ $viewingOrder->customer_email }}</div>
                    </div>
                    @if($viewingOrder->customer_phone)
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Phone</div>
                        <div>{{ $viewingOrder->customer_phone }}</div>
                    </div>
                    @endif
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Date</div>
                        <div>{{ $viewingOrder->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-500 mb-1">Shipping Address</div>
                    <div class="text-gray-700">{{ $viewingOrder->shipping_address }}</div>
                </div>

                <div>
                    <div class="text-xs text-gray-500 mb-2">Order Items</div>
                    <div class="border border-gray-100 rounded-xl overflow-hidden">
                        @foreach($viewingOrder->items as $item)
                        <div class="flex justify-between items-center px-4 py-3 border-b border-gray-50 last:border-0">
                            <div>
                                <div class="font-semibold text-gray-800">{{ $item['name'] }}</div>
                                <div class="text-xs text-gray-400">Qty: {{ $item['quantity'] }}</div>
                            </div>
                            <div class="font-bold">RM {{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4 space-y-2">
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>RM {{ number_format($viewingOrder->subtotal, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Shipping</span><span>RM {{ number_format($viewingOrder->shipping_fee, 2) }}</span></div>
                    <div class="flex justify-between font-black text-lg"><span>Total</span><span class="text-brand-red">RM {{ number_format($viewingOrder->total, 2) }}</span></div>
                </div>

                @if($viewingOrder->notes)
                <div>
                    <div class="text-xs text-gray-500 mb-1">Notes</div>
                    <div class="text-gray-700 text-sm bg-gray-50 rounded-lg p-3">{{ $viewingOrder->notes }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
