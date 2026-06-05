<?php

use Livewire\Component;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;

new class extends Component {
    public $emailAddress = '';
    public $content = '';

    public function send()
    {
        $this->validate([
            'emailAddress' => 'required|email',
            'content' => 'required|string',
        ]);

        try {
            Mail::to($this->emailAddress)->send(new SendMail($this->emailAddress, $this->content));

            session()->flash('status', 'success');
            session()->flash('message', 'Email sent successfully!');

            $this->reset(['emailAddress', 'content']);
        } catch (\Exception $e) {
            session()->flash('status', 'error');
            session()->flash('message', 'Failed to send email. Please try again.');
        }
    }
};
?>

<div class="max-w-lg mx-auto mt-10">

    {{-- ALERTS --}}
    @if (session()->has('message'))
        <div
            class="mb-4 px-4 py-3 rounded-lg text-sm font-medium
            {{ session('status') === 'success'
                ? 'bg-green-100 text-green-800 border border-green-300'
                : 'bg-red-100 text-red-800 border border-red-300' }}">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit="send" class="bg-white shadow-lg rounded-xl p-6 space-y-5 border border-gray-100">

        <h2 class="text-2xl font-bold text-gray-800">
            Send Email
        </h2>

        {{-- EMAIL --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Email Address
            </label>

            <input type="email" wire:model="emailAddress"
                class="w-full rounded-lg border px-4 py-3 focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none"
                placeholder="john@example.com">

            @error('emailAddress')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- MESSAGE --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Message
            </label>

            <textarea wire:model="content" rows="5"
                class="w-full rounded-lg border px-4 py-3 focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none"
                placeholder="Write your message..."></textarea>

            @error('content')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- BUTTON --}}
        <button type="submit" wire:loading.attr="disabled"
            class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition disabled:opacity-50">
            <span wire:loading.remove>
                Send Email
            </span>

            <span wire:loading>
                Sending...
            </span>
        </button>
    </form>
</div>
