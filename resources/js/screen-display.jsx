import { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function buildEcho() {
    window.Pusher = Pusher;
    return new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}

// ---------------------------------------------------------------------------
// Status screens
// ---------------------------------------------------------------------------

function Inactive() {
    return (
        <div style={styles.centered}>
            <span style={styles.message}>Tela inativa</span>
        </div>
    );
}

function NoPanel() {
    return (
        <div style={styles.centered}>
            <span style={styles.message}>Nenhum painel configurado</span>
        </div>
    );
}

function Loading() {
    return (
        <div style={styles.centered}>
            <span style={styles.message}>Carregando...</span>
        </div>
    );
}

// ---------------------------------------------------------------------------
// Main component
// ---------------------------------------------------------------------------

function ScreenDisplay({ id }) {
    var [screen, setScreen] = useState(null);

    // Fetch initial data
    useEffect(function () {
        fetch('/api/screens/' + id)
            .then(function (res) { return res.json(); })
            .then(function (data) { setScreen(data); })
            .catch(function () {
                setScreen({ status: false, panel_hash: null });
            });
    }, [id]);

    // WebSocket subscription
    useEffect(function () {
        var echo = buildEcho();
        var channel = echo.channel('screen.' + id);

        channel.listen('.ScreenUpdated', function (event) {
            setScreen(event);
        });

        return function () {
            echo.leave('screen.' + id);
        };
    }, [id]);

    if (!screen) return <Loading />;
    if (!screen.status) return <Inactive />;
    if (!screen.panel_hash) return <NoPanel />;

    return (
        <iframe
            key={screen.panel_hash}
            src={'/painel/' + screen.panel_hash}
            title={screen.title || 'Tela'}
            style={styles.iframe}
            allowFullScreen
        />
    );
}

// ---------------------------------------------------------------------------
// Styles
// ---------------------------------------------------------------------------

var styles = {
    centered: {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        width: '100%',
        height: '100vh',
        background: '#111',
    },
    message: {
        color: '#fff',
        fontSize: '1.5rem',
        fontFamily: 'sans-serif',
    },
    iframe: {
        width: '100%',
        height: '100vh',
        border: 'none',
        display: 'block',
    },
};

// ---------------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------------

var root = document.getElementById('screen-display');
if (root) {
    var id = root.getAttribute('data-id');
    createRoot(root).render(<ScreenDisplay id={id} />);
}
