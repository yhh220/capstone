<div>
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        @foreach([
            ['📦', 'Total Products', $stats['total_products'], 'bg-blue-50 text-blue-600'],
            ['🛒', 'Total Orders', $stats['total_orders'], 'bg-purple-50 text-purple-600'],
            ['⏳', 'Pending Orders', $stats['pending_orders'], 'bg-yellow-50 text-yellow-600'],
            ['✉️', 'Unread Messages', $stats['unread_messages'], 'bg-red-50 text-red-600'],
            ['📂', 'Categories', $stats['total_categories'], 'bg-green-50 text-green-600'],
            ['💰', 'Revenue (RM)', number_format($stats['total_revenue'], 0), 'bg-orange-50 text-orange-600'],
        ] as [$icon, $label, $value, $colors])
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="w-10 h-10 {{ $colors }} rounded-lg flex items-center justify-center text-xl mb-3">{{ $icon }}</div>
            <div class="text-2xl font-black text-gray-800">{{ $value }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Recent Orders</h3>
                <a href="{{ route('admin.orders') }}" class="text-brand-red text-sm hover:underline">View All</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentOrders as $order)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-sm text-gray-800">{{ $order->order_number }}</div>
                        <div class="text-xs text-gray-500">{{ $order->customer_name }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold text-gray-800">RM {{ number_format($order->total, 2) }}</div>
                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                            @if($order->status === 'pending') bg-yellow-100 text-yellow-700
                            @elseif($order->status === 'processing') bg-blue-100 text-blue-700
                            @elseif($order->status === 'shipped') bg-indigo-100 text-indigo-700
                            @elseif($order->status === 'delivered') bg-green-100 text-green-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-400 text-sm">No orders yet</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Messages -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Unread Messages</h3>
                <a href="{{ route('admin.contacts') }}" class="text-brand-red text-sm hover:underline">View All</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentMessages as $msg)
                <div class="px-6 py-4">
                    <div class="flex justify-between items-start mb-1">
                        <span class="font-semibold text-sm text-gray-800">{{ $msg->name }}</span>
                        <span class="text-xs text-gray-400">{{ $msg->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="text-xs font-medium text-brand-red">{{ $msg->subject }}</div>
                    <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $msg->message }}</div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-400 text-sm">No unread messages</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.products') }}" class="bg-brand-red text-white rounded-xl p-4 text-center hover:bg-red-700 transition">
            <div class="text-3xl mb-2">➕</div>
            <div class="font-semibold text-sm">Add Product</div>
        </a>
        <a href="{{ route('admin.categories') }}" class="bg-brand-black text-white rounded-xl p-4 text-center hover:bg-gray-800 transition">
            <div class="text-3xl mb-2">📂</div>
            <div class="font-semibold text-sm">Add Category</div>
        </a>
        <a href="{{ route('admin.orders') }}" class="bg-blue-600 text-white rounded-xl p-4 text-center hover:bg-blue-700 transition">
            <div class="text-3xl mb-2">📦</div>
            <div class="font-semibold text-sm">View Orders</div>
        </a>
        <a href="{{ route('admin.contacts') }}" class="bg-brand-yellow text-brand-black rounded-xl p-4 text-center hover:bg-yellow-400 transition">
            <div class="text-3xl mb-2">✉️</div>
            <div class="font-semibold text-sm">View Messages</div>
        </a>
    </div>
</div>
