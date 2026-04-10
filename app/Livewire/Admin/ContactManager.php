<?php

namespace App\Livewire\Admin;

use App\Models\Contact;
use Livewire\Component;
use Livewire\WithPagination;

class ContactManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showUnreadOnly = false;
    public ?int $viewingId = null;

    public function viewMessage(int $id): void
    {
        $this->viewingId = $id;
        Contact::findOrFail($id)->update(['is_read' => true]);
    }

    public function closeView(): void
    {
        $this->viewingId = null;
    }

    public function delete(int $id): void
    {
        Contact::findOrFail($id)->delete();
        session()->flash('success', 'Message deleted.');
    }

    public function render()
    {
        $query = Contact::query();

        if ($this->search !== '') {
            $query->where(function ($builder) {
                $builder->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('subject', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->showUnreadOnly) {
            $query->where('is_read', false);
        }

        return view('livewire.admin.contact-manager', [
            'messages' => $query->latest()->paginate(15),
            'viewing' => $this->viewingId ? Contact::find($this->viewingId) : null,
        ])->layout('layouts.admin');
    }
}
