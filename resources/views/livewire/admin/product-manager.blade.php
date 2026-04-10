<div>
    @section('page-title', 'Products')

    <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center mb-6">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search products..."
               class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red w-full sm:w-72">
        <button wire:click="openCreate"
                class="bg-brand-red text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-red-700 transition whitespace-nowrap">
            + Add Product
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-left">Product</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Category</th>
                    <th class="px-4 py-3 text-left">Price Range</th>
                    <th class="px-4 py-3 text-left hidden sm:table-cell">Stock</th>
                    <th class="px-4 py-3 text-left hidden lg:table-cell">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-xl overflow-hidden flex-shrink-0">
                                @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                🚗
                                @endif
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800">{{ \Illuminate\Support\Str::limit($product->name, 40) }}</div>
                                @if($product->sku)
                                <div class="text-xs text-gray-400">SKU: {{ $product->sku }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell text-gray-600">{{ $product->category?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="font-bold text-gray-800">RM {{ number_format($product->price, 2) }}</div>
                        @if($product->sale_price)
                        <div class="text-xs text-gray-500">Promo: RM {{ number_format($product->sale_price, 2) }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 hidden sm:table-cell">
                        <span class="font-semibold {{ $product->stock > 0 ? 'text-green-600' : 'text-red-500' }}">{{ $product->stock }}</span>
                    </td>
                    <td class="px-4 py-3 hidden lg:table-cell">
                        <div class="flex flex-col gap-1">
                            <span class="text-xs px-2 py-0.5 rounded-full w-fit {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if($product->is_featured)
                            <span class="text-xs px-2 py-0.5 rounded-full w-fit bg-yellow-100 text-yellow-700">Featured</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button wire:click="openEdit({{ $product->id }})" class="text-blue-600 hover:text-blue-800 font-medium text-xs px-2 py-1 rounded hover:bg-blue-50 transition">Edit</button>
                            <button wire:click="delete({{ $product->id }})" wire:confirm="Are you sure you want to delete this product?" class="text-red-600 hover:text-red-800 font-medium text-xs px-2 py-1 rounded hover:bg-red-50 transition">Delete</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                        <div class="text-4xl mb-2">📦</div>
                        No products found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-gray-100">
            {{ $products->links() }}
        </div>
    </div>

    @if($showModal)
    <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="closeModal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl">
                <h2 class="text-xl font-black text-gray-800">{{ $isEditing ? 'Edit Product' : 'Add New Product' }}</h2>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>

            <form wire:submit="save" class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">Product Name *</label>
                        <input wire:model="name" type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">Price (RM) *</label>
                        <input wire:model="price" type="number" step="0.01" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red">
                        @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">Promo / Min Price (RM)</label>
                        <input wire:model="sale_price" type="number" step="0.01" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red">
                        @error('sale_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">SKU</label>
                        <input wire:model="sku" type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red">
                        @error('sku') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">Stock *</label>
                        <input wire:model="stock" type="number" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red">
                        @error('stock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">Category</label>
                        <select wire:model="category_id" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red">
                            <option value="">— No Category —</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">Short Description</label>
                        <input wire:model="short_description" type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">Description</label>
                        <textarea wire:model="description" rows="4" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red resize-none"></textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 mb-1 block">Product Image</label>
                        <input wire:model="image" type="file" accept="image/*" class="w-full text-sm">
                        @error('image') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <input wire:model="is_active" type="checkbox" id="is_active" class="w-4 h-4 accent-brand-red">
                        <label for="is_active" class="text-sm font-semibold text-gray-700">Active</label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input wire:model="is_featured" type="checkbox" id="is_featured" class="w-4 h-4 accent-brand-red">
                        <label for="is_featured" class="text-sm font-semibold text-gray-700">Featured</label>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-100">
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
