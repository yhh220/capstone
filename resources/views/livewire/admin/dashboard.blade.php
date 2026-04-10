<div>
    @section('page-title', 'Dashboard')

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-8">
        @foreach([
            ['Products', $stats['products'], '🚗', 'bg-red-50 text-red-600'],
            ['Categories', $stats['categories'], '📂', 'bg-gray-100 text-gray-700'],
            ['Messages', $stats['messages'], '✉', 'bg-yellow-50 text-yellow-700'],
            ['Unread', $stats['unread_messages'], '🔔', 'bg-blue-50 text-blue-700'],
            ['Feedback', $stats['feedback'], '⭐', 'bg-green-50 text-green-700'],
        ] as [$label, $value, $icon, $classes])
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="text-sm font-semibold text-gray-500">{{ $label }}</div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $classes }}">{{ $icon }}</div>
            </div>
            <div class="text-3xl font-black text-gray-800">{{ $value }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-black text-gray-800">Recent Products</h2>
                <a href="{{ route('admin.products') }}" class="text-brand-red text-sm hover:underline">Manage</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentProducts as $product)
                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <div>
                        <div class="font-semibold text-sm text-gray-800">{{ $product->name }}</div>
                        <div class="text-xs text-gray-500">{{ $product->created_at->format('d M Y') }}</div>
                    </div>
                    <div class="text-xs px-2 py-1 rounded-full {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-400 text-sm">No products yet</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-black text-gray-800">Recent Messages</h2>
                <a href="{{ route('admin.contacts') }}" class="text-brand-red text-sm hover:underline">Manage</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentMessages as $message)
                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <div>
                        <div class="font-semibold text-sm text-gray-800">{{ $message->name }}</div>
                        <div class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($message->subject, 40) }}</div>
                    </div>
                    <div class="text-xs px-2 py-1 rounded-full {{ $message->is_read ? 'bg-gray-100 text-gray-500' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $message->is_read ? 'Read' : 'Unread' }}
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-400 text-sm">No messages yet</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-black text-gray-800">Recent Feedback</h2>
                <a href="{{ route('admin.feedback') }}" class="text-brand-red text-sm hover:underline">Manage</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentFeedback as $feedback)
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between gap-3">
                        <div class="font-semibold text-sm text-gray-800">{{ $feedback->name }}</div>
                        <div class="text-xs text-brand-red">{{ str_repeat('★', $feedback->rating) }}</div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ $feedback->location ?: 'No location' }}</div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-400 text-sm">No feedback yet</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
