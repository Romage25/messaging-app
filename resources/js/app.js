import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

document.addEventListener('livewire:init', () => {

    let channel = null;

    Livewire.on('setConversation', (conversationId) => {

        if (channel) {
            window.Echo.leave(channel);
        }

        channel = `chat.${conversationId}`;

        window.Echo.private(channel)
            .listen('.message.sent', (e) => {

                console.log('NEW MESSAGE:', e);

                Livewire.dispatch('refreshMessages');
            });
    });

});
