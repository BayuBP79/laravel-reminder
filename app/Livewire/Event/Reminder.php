<?php

namespace App\Livewire\Event;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Route;
use App\Models\Reminder as ModelsReminder;

class Reminder extends Component
{
    use WithPagination;

    // form pop-up properties
    public $modalTitle = 'Add New Reminder';
    public $modalButtonText = 'Create';
    public $showModal = false;

    //filter prop
    public $perPage = 10,
        $search = '',
        $timeFilter = '',
        $sortBy = 'created_at',
        $sortDirection = 'DESC';

    //data properties
    public $currentEvent = null,
        $reminderId = null,
        $reminderData = null,
        $message = '',
        $reminder_date,
        $currentUser;

    protected $rules = [
        'message' => 'required|string',
        'reminder_date' => 'required|date_format:Y-m-d\TH:i',
    ];

    public function mount()
    {
        $this->currentEvent = Route::current()->parameter('event');
        $this->currentUser = auth()->user()->id;
    }

    public function render()
    {
        return view('livewire.event.reminder', [
            'reminders' => ModelsReminder::where('event_id', $this->currentEvent)->search($this->search)
                ->when($this->timeFilter !== '', function ($query) {
                    $query->where('category_id', $this->timeFilter);
                })
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage),
        ]);
    }

    public function editReminder($reminderId)
    {
        $this->toggleModal();
        $this->modalTitle = 'Edit Category';
        $this->modalButtonText = 'Update';
        $this->reminderId = $reminderId;

        $this->reminderData = ModelsReminder::findOrFail($reminderId);
        $this->message = $this->reminderData->message;
        $this->reminder_date = Carbon::parse($this->reminderData->reminder_date)->format('Y-m-d\TH:i');
    }

    public function save()
    {
        $this->validate();

        if ($this->reminderId) {
            $this->reminderData->update([
                'user_id' => $this->currentUser,
                'message' => $this->message,
                'reminder_date' => Carbon::parse($this->reminder_date)->format('Y-m-d\TH:i'),
            ]);
        } else {
            $this->reminderData = ModelsReminder::create([
                'event_id' => $this->currentEvent,
                'user_id' => $this->currentUser,
                'message' => $this->message,
                'reminder_date' => Carbon::parse($this->reminder_date)->format('Y-m-d\TH:i'),
            ]);
        }

        $this->toggleModal();
    }

    public function deleteReminder(ModelsReminder $reminder)
    {
        if ($reminder) {
            $reminder->delete();
            session()->flash('message', 'Purchase Order deleted successfully!');
        } else {
            session()->flash('error', 'Purchase Order not found!');
        }

        $this->render();
    }

    public function setSortBy($sortBy)
    {
        if ($this->sortBy === $sortBy) {
            $this->sortDirection = $this->sortDirection === 'ASC' ? 'DESC' : 'ASC';
            return;
        }
        $this->sortBy = $sortBy;
        $this->sortDirection = 'DESC';
    }

    public function toggleModal()
    {
        $this->showModal = !$this->showModal;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['reminderId', 'reminderData', 'message', 'reminder_date', 'modalTitle', 'modalButtonText']);
    }
}
