<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $description = '';
    public string $short_description = '';
    public string $price = '';
    public string $sale_price = '';
    public string $sku = '';
    public int $stock = 0;
    public ?int $category_id = null;
    public bool $is_active = true;
    public bool $is_featured = false;
    public $image = null;

    protected function rules(): array
    {
        return [
            'name' => 'required|min:2|max:255',
            'description' => 'nullable|max:5000',
            'short_description' => 'nullable|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|unique:products,sku' . ($this->editingId ? ',' . $this->editingId : ''),
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $product = Product::findOrFail($id);

        $this->editingId = $id;
        $this->name = $product->name;
        $this->description = $product->description ?? '';
        $this->short_description = $product->short_description ?? '';
        $this->price = (string) $product->price;
        $this->sale_price = $product->sale_price !== null ? (string) $product->sale_price : '';
        $this->sku = $product->sku ?? '';
        $this->stock = $product->stock;
        $this->category_id = $product->category_id;
        $this->is_active = $product->is_active;
        $this->is_featured = $product->is_featured;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'sale_price' => $this->sale_price ?: null,
            'sku' => $this->sku ?: null,
            'stock' => $this->stock,
            'category_id' => $this->category_id,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('products', 'public');
        }

        if ($this->isEditing) {
            Product::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Product updated successfully.');
        } else {
            Product::create($data);
            session()->flash('success', 'Product created successfully.');
        }

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        Product::findOrFail($id)->delete();
        session()->flash('success', 'Product deleted.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->description = '';
        $this->short_description = '';
        $this->price = '';
        $this->sale_price = '';
        $this->sku = '';
        $this->stock = 0;
        $this->category_id = null;
        $this->is_active = true;
        $this->is_featured = false;
        $this->image = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.product-manager', [
            'products' => Product::with('category')
                ->when($this->search, fn ($query) => $query->where('name', 'like', '%' . $this->search . '%'))
                ->latest()
                ->paginate(15),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ])->layout('layouts.admin');
    }
}
