<div>
    @section('page-title', 'Feedback')

    <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center mb-6">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search feedback..."
               class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red w-full sm:w-72">
        <button wire:click="openCreate"
                class="bg-brand-red text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-red-700 transition whitespace-nowrap">
            + Add Feedback
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-left">Customer</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Feedback</th>
                    <th class="px-4 py-3 text-left">Rating</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($feedbackItems as $feedback)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $feedback->name }}</div>
                        <div class="text-xs text-gray-400">{{ $feedback->location ?: 'No location' }}</div>
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell text-gray-600">{{ \Illuminate\Support\Str::limit($feedback->message, 70) }}</td>
                    <td class="px-4 py-3 text-brand-red">{{ str_repeat('★', $feedback->rating) }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $feedback->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $feedback->is_active ? 'Visible' : 'Hidden' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button wire:click="openEdit({{ $feedback->id }})" class="text-blue-600 hover:text-blue-800 font-medium text-xs px-2 py-1 rounded hover:bg-blue-50 transition">Edit</button>
                            <button wire:click="delete({{ $feedback->id }})" wire:confirm="Delete this feedback?" class="text-red-600 hover:text-red-800 font-medium text-xs px-2 py-1 rounded hover:bg-red-50 transition">Delete</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                        <div class="text-4xl mb-2">⭐</div>
                        No feedback yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-gray-100">{{ $feedbackItems->links() }}</div>
    </div>

    @if($showModal)
    <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="closeModal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl">
            <div class="border-b border-gray-100 px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-black text-gray-800">{{ $isEditing ? 'Edit Feedback' : 'Add Feedback' }}</h2>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <form wire:submit="save" class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">Customer Name *</label>
                        <input wire:model="name" type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">Location</label>
                        <input wire:model="location" type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red">
                        @error('location') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">Rating *</label>
                        <select wire:model="rating" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red">
                            @foreach(range(1, 5) as $star)
                            <option value="{{ $star }}">{{ $star }} star</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">Sort Order</label>
                        <input wire:model="sort_order" type="number" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">Feedback Message *</label>
                        <textarea wire:model="message" rows="5" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red resize-none"></textarea>
                        @error('message') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-center gap-3 sm:col-span-2">
                        <input wire:model="is_active" type="checkbox" id="feedback_active" class="w-4 h-4 accent-brand-red">
                        <label for="feedback_active" class="text-sm font-semibold text-gray-700">Show on website</label>
                    </div>
                </div>

                <div class="flex gap-3 pt-2 border-t border-gray-100">
                    <button type="submit" class="bg-brand-red text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-red-700 transition">
                        {{ $isEditing ? 'Update' : 'Create' }}
                    </button>
                    <button type="button" wire:click="closeModal" class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-xl font-semibold hover:bg-gray-50 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
