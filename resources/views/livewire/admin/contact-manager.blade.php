<div>
    @section('page-title', 'Messages')

    <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center mb-6">
        <div class="flex gap-3 flex-1">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search messages..."
                   class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red flex-1">
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
            <input wire:model.live="showUnreadOnly" type="checkbox" class="w-4 h-4 accent-brand-red">
            Unread only
        </label>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-left">From</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Subject</th>
                    <th class="px-4 py-3 text-left hidden lg:table-cell">Date</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($messages as $message)
                <tr class="hover:bg-gray-50 transition {{ !$message->is_read ? 'font-semibold bg-yellow-50/50' : '' }}">
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $message->name }}</div>
                        <div class="text-xs text-gray-400">{{ $message->email }}</div>
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell text-gray-600">{{ \Illuminate\Support\Str::limit($message->subject, 40) }}</td>
                    <td class="px-4 py-3 hidden lg:table-cell text-gray-400 text-xs">{{ $message->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ !$message->is_read ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ !$message->is_read ? 'Unread' : 'Read' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button wire:click="viewMessage({{ $message->id }})" class="text-blue-600 hover:text-blue-800 font-medium text-xs px-2 py-1 rounded hover:bg-blue-50 transition">View</button>
                            <button wire:click="delete({{ $message->id }})" wire:confirm="Delete this message?" class="text-red-600 hover:text-red-800 font-medium text-xs px-2 py-1 rounded hover:bg-red-50 transition">Delete</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                        <div class="text-4xl mb-2">✉</div>
                        No messages found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-100">{{ $messages->links() }}</div>
    </div>

    @if($viewing)
    <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="closeView">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl">
                <h2 class="text-lg font-black text-gray-800">Message</h2>
                <button wire:click="closeView" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div class="p-6 space-y-4 text-sm">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-gray-400 mb-1">From</div>
                        <div class="font-semibold text-gray-800">{{ $viewing->name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-1">Date</div>
                        <div>{{ $viewing->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-1">Email</div>
                        <a href="mailto:{{ $viewing->email }}" class="text-brand-red hover:underline">{{ $viewing->email }}</a>
                    </div>
                    @if($viewing->phone)
                    <div>
                        <div class="text-xs text-gray-400 mb-1">Phone</div>
                        <div>{{ $viewing->phone }}</div>
                    </div>
                    @endif
                </div>
                <div>
                    <div class="text-xs text-gray-400 mb-1">Subject</div>
                    <div class="font-bold text-gray-800">{{ $viewing->subject }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 mb-2">Message</div>
                    <div class="bg-gray-50 rounded-xl p-4 text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $viewing->message }}</div>
                </div>
                <div class="flex gap-3 pt-2 border-t border-gray-100">
                    <a href="mailto:{{ $viewing->email }}" class="bg-brand-red text-white px-5 py-2 rounded-xl font-semibold text-sm hover:bg-red-700 transition">
                        Reply via Email
                    </a>
                    <button wire:click="closeView" class="border border-gray-200 text-gray-600 px-5 py-2 rounded-xl font-semibold text-sm hover:bg-gray-50 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
