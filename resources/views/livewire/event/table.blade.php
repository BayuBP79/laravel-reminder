<div>
    <div x-data="eventHandler()" x-init="initEventHandler">
        <div x-show="showModal"
            class="z-10 fixed inset-0 flex items-center justify-center bg-slate-300 dark:bg-transparent bg-opacity-75 backdrop-blur-sm">
            <div class="bg-white p-6 rounded shadow-lg w-full md:w-2/3 lg:w-1/2 dark:bg-slate-700 dark:text-white">
                <h2 class="text-lg font-bold mb-4" x-text="modalTitle"></h2>
                <form x-on:submit.prevent="submitEvent">
                    <div class="flex flex-row w-full gap-4 mb-4">
                        <div class="flex-1">
                            <label for="title">Title</label>
                            <input type="text" id="title" x-model="eventData.title"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-cyan-600 sm:text-sm sm:leading-6 dark:text-white dark:bg-slate-700">
                            @error('title')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex-1">
                            <label for="event_date">Date and Time</label>
                            <input x-model="eventData.event_date" type="datetime-local" id="event_date"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-cyan-600 sm:text-sm sm:leading-6 dark:text-white dark:bg-slate-700">
                            @error('event_date')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="description">Description</label>
                        <textarea x-model="eventData.description"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-cyan-600 sm:text-sm sm:leading-6 dark:text-white dark:bg-slate-700"></textarea>
                        @error('description')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="max-h-[60vh] overflow-y-auto">
                        <div class="mb-4">
                            <label class="flex items-center mb-2">
                                Participants
                                <button type="button" @click="addParticipant"
                                    class="ml-2 text-blue-500 hover:text-blue-700">
                                    <x-plus-icon />
                                </button>
                            </label>
                            <div>
                                <template x-for="(participant, index) in eventData.participants" :key="index">
                                    <div class="flex items-center gap-2 mb-2">
                                        <input type="text" x-model="participant.name" placeholder="Name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-cyan-600 sm:text-sm sm:leading-6 dark:text-white dark:bg-slate-700">
                                        <input type="email" x-model="participant.email" placeholder="Email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-cyan-600 sm:text-sm sm:leading-6 dark:text-white dark:bg-slate-700">
                                        <button type="button" @click="removeParticipant(index)" class="ml-2 text-red-500 hover:text-red-700">
                                            <x-trash-icon />
                                        </button>
                                    </div>
                                </template>
                            </div>
                            @error('participants.*.email')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" x-text="modalButtonText"
                            class="text-white bg-gradient-to-r from-cyan-400 via-cyan-500 to-cyan-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-cyan-300 dark:focus:ring-cyan-800 shadow-lg shadow-cyan-500/50 dark:shadow-lg dark:shadow-cyan-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 cursor-pointer"></button>
                        <button type="button" @click="closeModal"
                            class="text-white bg-gradient-to-r from-gray-400 via-gray-500 to-gray-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-gray-300 dark:focus:ring-gray-800 shadow-lg shadow-gray-500/50 dark:shadow-lg dark:shadow-gray-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 cursor-pointer">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        <div x-show="isOffline" class="bg-white dark:bg-gray-800  border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4"
            role="alert">
            <p class="font-bold text-gray-900 dark:text-slate-100">Offline Mode</p>
            <p class="font-bold text-gray-900 dark:text-slate-100">You are currently working offline. You still can create data, changes will
                be synced when you're back online.</p>
        </div>
        <button type="button" @click="openModal"
            class="text-white bg-gradient-to-r from-cyan-400 via-cyan-500 to-cyan-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-cyan-300 dark:focus:ring-cyan-800 shadow-lg shadow-cyan-500/50 dark:shadow-lg dark:shadow-cyan-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 cursor-pointer">
            Create
        </button>
        <a href="{{ route('upload') }}" wire:navigate
            class="text-white bg-gradient-to-r from-cyan-400 via-cyan-500 to-cyan-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-cyan-300 dark:focus:ring-cyan-800 shadow-lg shadow-cyan-500/50 dark:shadow-lg dark:shadow-cyan-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 cursor-pointer">
            Import
        </a>
        <div>
            <section class="mt-10">
                <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                    <div class="flex items-center justify-between d p-4">
                        <div class="flex">
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                        fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input wire:model.live.debounce.300ms = "search" type="text"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Search" required="">
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <div class="flex space-x-3 items-center">
                                <label class="w-20 text-sm font-medium text-gray-900 dark:text-white">Category</label>
                                <select wire:model.live = "categoryFilter"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">All</option>
                                    {{-- @foreach ($allCategory as $filter)
                                        <option value="{{ $filter->id }}">{{ $filter->name }}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-900 dark:text-slate-100">
                            <thead class="text-xs uppercase bg-gray-50 dark:bg-slate-600">
                                <tr>
                                    @include('livewire.includes.table-sortable-th', [
                                        'name' => 'id',
                                        'displayName' => 'Id',
                                    ])
                                    @include('livewire.includes.table-sortable-th', [
                                        'name' => 'title',
                                        'displayName' => 'Title',
                                    ])
                                    @include('livewire.includes.table-sortable-th', [
                                        'name' => 'description',
                                        'displayName' => 'Description',
                                    ])
                                    @include('livewire.includes.table-sortable-th', [
                                        'name' => 'event_date',
                                        'displayName' => 'Event Date',
                                    ])
                                    <th scope="col" class="px-4 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($events as $event)
                                    <tr wire:key="{{ $event->id }}" class=" border-b dark:border-gray-700">
                                        <th scope="row" class="px-4 py-3 font-medium whitespace-nowrap">
                                            {{ $event->id }}
                                        </th>
                                        <td class="px-4 py-3">
                                            {{ $event->title }}
                                        </td>
                                        <td class="px-4 py-3 flex items-center gap-2">
                                            {{ $event->description }}
                                        </td>
                                        <td class="px-4 py-3 gap-2">
                                            {{ $event->event_date->format('d F Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3 flex items-center justify-end gap-2">
                                            <a href="{{ route('event.reminder', $event->id) }}" wire:navigate>
                                                <x-time-icon class="size-4 text-gray-500" />
                                            </a>
                                            <button type="button" wire:click="editEvent('{{ $event->id }}')">
                                                <x-edit-icon class="w-4 h4 fill-current text-gray-500" />
                                            </button>
                                            <button wire:click="deleteEvent('{{ $event->id }}')"
                                                wire:confirm="Are you sure you want to delete this Purchase Order?"
                                                wire:loading.attr="disabled">
                                                <x-delete-icon class="w-4 h4 fill-current text-gray-500" />
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    <div class="py-4 px-3">
                        <div class="flex justify-between">
                            <div class="flex space-x-4 items-center mb-3">
                                <label class="w-32 text-sm font-medium  text-gray-900 dark:text-white">Per Page</label>
                                <select wire:model.live="perPage"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="flex gap-2 space-x-4 items-center mb-3">
                                {{ $events->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script>
    function eventHandler() {
        return {
            isOffline: false,
            showModal: false,
            modalTitle: 'Add New Event',
            modalButtonText: 'Create',
            eventData: {
                id: null,
                user_id: {{ $currentUser }},
                title: '',
                description: '',
                event_date: '',
                participants: [{ name: '', email: '' }]
            },
            initEventHandler() {
                this.$watch('showModal', value => {
                    if (!value) this.resetForm();
                });
                this.checkOnlineStatus();
                window.addEventListener('online', () => this.handleOnline());
                window.addEventListener('offline', () => this.handleOffline());

                Livewire.on('eventDataUpdated', data => {
                    this.showModal = true;
                    this.modalTitle = data[0].modalTitle;
                    this.modalButtonText = data[0].modalButtonText;
                    this.eventData = {
                        id: data[0].eventData.id || null,
                        title: data[0].eventData.title || '',
                        description: data[0].eventData.description || '',
                        event_date: data[0].eventData.event_date || '',
                        participants: data[0].eventData.participants || [{ name: '', email: '' }]
                    };
                });
            },
            checkOnlineStatus() {
                this.isOffline = !navigator.onLine;
                @this.call('setOfflineStatus', this.isOffline);
            },
            openModal() {
                this.showModal = true;
            },
            closeModal() {
                this.showModal = false;
            },
            resetForm() {
                this.eventData = {
                    id: null,
                    user_id: {{ $currentUser }},
                    title: '',
                    description: '',
                    event_date: '',
                    participants: [{ name: '', email: '' }]
                };
                this.modalTitle = 'Add New Event';
                this.modalButtonText = 'Create';
            },
            submitEvent() {
                if (navigator.onLine) {
                    @this.save(this.eventData);
                } else {
                    const offlineEvent = { ...this.eventData, id: Date.now() }; // Use timestamp as temporary ID
                    @this.offlineEvents.push(offlineEvent);
                    localStorage.setItem('offlineEvents', JSON.stringify(@this.offlineEvents));
                    this.$dispatch('event-stored-offline');
                }
                this.closeModal();
            },
            addParticipant() {
                this.eventData.participants.push({ name: '', email: '' });
            },
            removeParticipant(index) {
                this.eventData.participants.splice(index, 1);
            },
            handleOnline() {
                this.isOffline = false;
                @this.call('setOfflineStatus', false);
            },
            handleOffline() {
                this.isOffline = true;
                @this.call('setOfflineStatus', true);
            },
        }
    }

    window.addEventListener('online', () => {
        const offlineEvents = JSON.parse(localStorage.getItem('offlineEvents') || '[]');
        if (offlineEvents.length > 0) {
            @this.offlineEvents = offlineEvents;
            @this.syncOfflineEvents();
            localStorage.removeItem('offlineEvents');
        }
    });

    window.addEventListener('livewire:load', function () {
        Livewire.on('eventStoredOffline', () => {
            alert('Event stored offline. It will be synced when you are back online.');
        });

        Livewire.on('offlineEventsSynced', () => {
            alert('Offline events have been synced successfully.');
        });
    });
    </script>
