<?php

namespace App\Livewire\Event;

use Carbon\Carbon;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EventParticipant;

class Table extends Component
{
    use WithPagination;

    // form pop-up properties
    public $modalTitle = 'Add New Event';
    public $modalButtonText = 'Create';
    public $showModal = false;

    //filter prop
    public $perPage = 10,
        $search = '',
        $timeFilter = '',
        $sortBy = 'created_at',
        $sortDirection = 'DESC';

    //data properties
    public $eventId = null,
        $eventData = null,
        $participants = [['name' => '', 'email' => '']],
        $title = '',
        $description = '',
        $event_date,
        $currentUser;

    //sycn function
    public $isOffline = false;
    public $offlineEvents = [];
    protected $listeners = ['eventsSync' => 'refreshEvents', 'syncOfflineEvents', 'setOfflineStatus'];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'event_date' => 'required|date',
    ];

    public function mount()
    {
        $this->event_date = Carbon::now()->format('Y-m-d\TH:i');
        $this->currentUser = auth()->user()->id;
    }

    public function render()
    {
        return view('livewire.event.table', [
            'events' => Event::search($this->search)
                ->when($this->timeFilter !== '', function ($query) {
                    $query->where('category_id', $this->timeFilter);
                })
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage),
        ]);
    }

    public function editEvent($eventId)
    {
        $this->toggleModal();
        $this->modalTitle = 'Edit Category';
        $this->modalButtonText = 'Update';
        $this->eventId = $eventId;

        $this->eventData = Event::findOrFail($eventId);
        $this->title = $this->eventData->title;
        $this->description = $this->eventData->description;
        $this->event_date = Carbon::parse($this->event_date)->format('Y-m-d\TH:i');
        $this->participants = EventParticipant::where('event_id', $this->eventId)
            ->get(['name', 'email'])
            ->toArray();

        $this->dispatch('eventDataUpdated', [
            'showModal' => true,
            'modalTitle' => $this->modalTitle,
            'modalButtonText' => $this->modalButtonText,
            'eventData' => [
                'id' => $this->eventId,
                'title' => $this->title,
                'description' => $this->description,
                'event_date' => $this->event_date,
                'participants' => $this->participants
            ]
        ]);
    }

    public function save($eventData = null)
    {
        if ($eventData) {
            $this->title = $eventData['title'];
            $this->description = $eventData['description'];
            $this->event_date = $eventData['event_date'];
            $this->participants = $eventData['participants'] ?? [];
        }

        $this->validate();

        $eventToSave = [
            'title' => $this->title,
            'user_id' => $this->currentUser,
            'description' => $this->description,
            'event_date' => Carbon::parse($this->event_date)->format('Y-m-d\TH:i'),
        ];

        if ($this->eventId && $this->isValidEventIdFormat($this->eventId)) {
            $eventToSave['id'] = null;
        } else {
            $eventToSave['id'] = Event::generateEventId();
        }

        if ($this->isOnline()) {
            $this->saveEventToDatabase($eventToSave);
        } else {
            $eventToSave['id'] = null;
            $this->offlineEvents[] = $eventToSave;
            $this->dispatch('eventStoredOffline');
        }

        $this->toggleModal();
    }

    public function deleteEvent(Event $event)
    {
        if ($event) {
            $event->delete();
            session()->flash('message', 'Purchase Order deleted successfully!');
        } else {
            session()->flash('error', 'Purchase Order not found!');
        }

        $this->render();
    }

    private function isValidEventIdFormat($eventId)
    {
        $pattern = '/^EVT-\d{8}-\d{4}$/';

        return preg_match($pattern, $eventId) === 1;
    }

    public function syncOfflineEvents()
    {
        foreach ($this->offlineEvents as $event) {
            $this->saveEventToDatabase($event);
        }
        $this->offlineEvents = [];
        $this->dispatch('offlineEventsSynced');
    }

    public function setOfflineStatus($status)
    {
        $this->isOffline = $status;
    }

    private function saveEventToDatabase($eventData)
    {
        $event = Event::updateOrCreate(['id' => $eventData['id']], $eventData);

        $existingParticipants = EventParticipant::where('event_id', $event->id)->pluck('email');
        $newParticipantEmails = array_column($this->participants, 'email');
        $emailsToRemove = $existingParticipants->diff($newParticipantEmails);
        EventParticipant::where('event_id', $event->id)
            ->whereIn('email', $emailsToRemove)
            ->delete();

        foreach ($this->participants as $participant) {
            if (filter_var($participant['email'], FILTER_VALIDATE_EMAIL)) {
                EventParticipant::updateOrCreate(
                    ['event_id' => $event->id, 'email' => $participant['email']],
                    ['name' => $participant['name']]
                );
            }
        }
    }

    private function isOnline()
    {
        if (app()->environment('local')) {
            return true;
        }

        if (request()->ajax()) {
            return request()->hasHeader('X-Livewire');
        }
        return true;
    }

    public function addParticipant()
    {
        array_unshift($this->participants, ['name' => '', 'email' => '']);
    }

    public function removeParticipant($index)
    {
        unset($this->participants[$index]);
        $this->participants = array_values($this->participants);
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
        $this->reset(['eventId', 'eventData', 'participants', 'title', 'description', 'event_date', 'modalTitle', 'modalButtonText']);
    }
}
