<?php

namespace App\Livewire\Admin\Vermaks;

use App\Models\AlterationOption;
use Livewire\Component;

class Index extends Component
{
    public $vermaks;
    public $showModal = false;
    public $editMode = false;
    public $vermakId = null;

    public $name = '';
    public $description = '';
    public $price = '';

    public function mount()
    {
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        $this->loadVermaks();
    }

    public function loadVermaks()
    {
        $this->vermaks = AlterationOption::orderBy('name')->get();
    }

    public function openModal()
    {
        $this->resetInput();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function editVermak($id)
    {
        $this->resetInput();
        $vermak = AlterationOption::findOrFail($id);
        $this->vermakId = $vermak->id;
        $this->name = $vermak->name;
        $this->description = $vermak->description;
        $this->price = $vermak->price;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
        ]);

        if ($this->editMode) {
            $vermak = AlterationOption::findOrFail($this->vermakId);
            $vermak->update([
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
            ]);
            session()->flash('success', 'Layanan vermak berhasil diperbarui.');
        } else {
            AlterationOption::create([
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
            ]);
            session()->flash('success', 'Layanan vermak berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->loadVermaks();
    }

    public function delete($id)
    {
        $vermak = AlterationOption::findOrFail($id);
        $vermak->delete();
        session()->flash('success', 'Layanan vermak berhasil dihapus.');
        $this->loadVermaks();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInput();
    }

    private function resetInput()
    {
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->vermakId = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.vermaks.index')->layout('layouts.admin');
    }
}
