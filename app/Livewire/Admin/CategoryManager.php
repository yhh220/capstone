<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryManager extends Component
{
    public string $search = '';
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $description = '';
    public bool $is_active = true;

    protected array $rules = [
        'name' => 'required|min:2|max:100',
        'description' => 'nullable|max:500',
        'is_active' => 'boolean',
    ];

    public function openCreate(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $cat = Category::findOrFail($id);
        $this->editingId = $id;
        $this->name = $cat->name;
        $this->description = $cat->description ?? '';
        $this->is_active = $cat->is_active;
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
            'is_active' => $this->is_active,
        ];

        if ($this->isEditing) {
            Category::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Category updated.');
        } else {
            Category::create($data);
            session()->flash('success', 'Category created.');
        }

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        Category::findOrFail($id)->delete();
        session()->flash('success', 'Category deleted.');
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
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.category-manager', [
            'categories' => Category::withCount('products')
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->latest()
                ->paginate(15),
        ])->layout('layouts.admin');
    }
}
