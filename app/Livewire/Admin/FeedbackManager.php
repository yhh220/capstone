<?php

namespace App\Livewire\Admin;

use App\Models\Feedback;
use Livewire\Component;
use Livewire\WithPagination;

class FeedbackManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $location = '';
    public string $message = '';
    public int $rating = 5;
    public bool $is_active = true;
    public int $sort_order = 0;

    protected function rules(): array
    {
        return [
            'name' => 'required|min:2|max:120',
            'location' => 'nullable|max:120',
            'message' => 'required|min:10|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'sort_order' => 'required|integer|min:0',
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
        $feedback = Feedback::findOrFail($id);

        $this->editingId = $id;
        $this->name = $feedback->name;
        $this->location = $feedback->location ?? '';
        $this->message = $feedback->message;
        $this->rating = $feedback->rating;
        $this->is_active = $feedback->is_active;
        $this->sort_order = $feedback->sort_order;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->isEditing) {
            Feedback::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Feedback updated.');
        } else {
            Feedback::create($data);
            session()->flash('success', 'Feedback created.');
        }

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        Feedback::findOrFail($id)->delete();
        session()->flash('success', 'Feedback deleted.');
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
        $this->location = '';
        $this->message = '';
        $this->rating = 5;
        $this->is_active = true;
        $this->sort_order = 0;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.feedback-manager', [
            'feedbackItems' => Feedback::query()
                ->when($this->search !== '', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('location', 'like', '%' . $this->search . '%')
                        ->orWhere('message', 'like', '%' . $this->search . '%');
                })
                ->orderBy('sort_order')
                ->latest('id')
                ->paginate(15),
        ])->layout('layouts.admin');
    }
}
