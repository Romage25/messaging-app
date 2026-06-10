<?php

use Livewire\Component;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;

new class extends Component {
    public $users = [];
    public $selectedUser = null;
    public $conversation = null;
    public $messages = [];
    public $message = '';
    public $conversationId = null;

    protected $listeners = [
        'refreshMessages' => 'loadMessages',
    ];

    public function mount()
    {
        $this->users = User::where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get();
    }

    public function selectUser($userId)
    {
        $this->selectedUser = User::findOrFail($userId);

        $this->conversation = Conversation::whereHas('users', function ($q) use ($userId) {
            $q->where('user_id', auth()->id());
        })
            ->whereHas('users', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->first();

        if (!$this->conversation) {
            $this->conversation = Conversation::create();
            $this->conversation->users()->attach([auth()->id(), $userId]);
        }

        $this->conversationId = $this->conversation->id;

        $this->loadMessages();

        $this->dispatch('setConversation', $this->conversationId);
    }

    public function loadMessages()
    {
        if (!$this->conversation) {
            return;
        }

        $this->messages = $this->conversation->messages()->with('user')->orderBy('id')->get();
    }

    public function sendMessage()
    {
        if (!$this->conversation || trim($this->message) === '') {
            return;
        }

        $message = $this->conversation->messages()->create([
            'user_id' => auth()->id(),
            'message' => $this->message,
        ]);

        $this->message = '';

        // update sender instantly
        $this->messages[] = $message;

        broadcast(new \App\Events\MessageSent($message))->toOthers();
    }
};
?>

<div class="h-screen flex flex-col bg-gray-100 overflow-hidden">

    <div class="flex flex-1 overflow-hidden">

        <!-- USERS SIDEBAR -->
        <div class="w-1/4 bg-white border-r overflow-y-auto">
            <div class="p-4 font-bold text-lg border-b">
                Chats
            </div>

            @foreach ($users as $user)
                <div wire:click="selectUser({{ $user->id }})" class="p-4 cursor-pointer hover:bg-gray-100 border-b">
                    <div class="font-medium">
                        {{ $user->name }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- CHAT AREA -->
        <div class="flex flex-col flex-1 min-h-0 bg-white">

            @if ($selectedUser)

                <!-- HEADER -->
                <div class="p-4 border-b bg-gray-50 font-semibold">
                    {{ $selectedUser->name }}
                </div>

                <!-- MESSAGES (ONLY SCROLL AREA) -->
                <div class="flex-1 overflow-y-auto p-4 space-y-2">
                    @foreach ($messages as $msg)
                        <div class="flex {{ $msg->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div
                                class="max-w-xs px-4 py-2 rounded-lg
                                {{ $msg->user_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-black' }}">
                                {{ $msg->message }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- INPUT -->
                <div class="p-4 border-t flex gap-2 bg-white">
                    <input type="text" wire:model="message" wire:keydown.enter="sendMessage"
                        class="flex-1 border rounded-full px-4 py-2 focus:outline-none"
                        placeholder="Type a message..." />

                    <button wire:click="sendMessage" class="bg-blue-500 text-white px-6 rounded-full">
                        Send
                    </button>
                </div>
            @else
                <div class="flex-1 flex items-center justify-center text-gray-500">
                    Select a user to start chatting
                </div>
            @endif

        </div>
    </div>
</div>
