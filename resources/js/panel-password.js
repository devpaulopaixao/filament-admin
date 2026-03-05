import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

var el   = document.getElementById('panel-password');
var hash = el ? el.getAttribute('data-hash') : null;

if (hash) {
    window.Pusher = Pusher;

    var echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });

    echo.channel('panel.' + hash).listen('.PanelUpdated', function (event) {
        if (!event.blocked) {
            window.location.href = '/painel/' + hash;
        }
    });
}
