import { useState, useEffect, useRef } from 'react';
import { createRoot } from 'react-dom/client';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function durationToMs(hhmm) {
    if (!hhmm) return 10000; // fallback: 10 s
    const parts = hhmm.split(':').map(Number);
    const h = parts[0] ?? 0;
    const m = parts[1] ?? 0;
    const total = (h * 3600 + m * 60) * 1000;
    return total > 0 ? total : 10000;
}

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
// Screens
// ---------------------------------------------------------------------------

function Inactive() {
    return (
        <div style={styles.centered}>
            <span style={styles.message}>Painel inativo</span>
        </div>
    );
}

function NoLinks() {
    return (
        <div style={styles.centered}>
            <span style={styles.message}>Nenhum link disponível</span>
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

function PanelDisplay({ hash }) {
    const [panel, setPanel] = useState(null);
    const [currentIndex, setCurrentIndex] = useState(0);
    const timerRef = useRef(null);

    // Fetch initial data
    useEffect(function () {
        fetch('/api/panels/' + hash)
            .then(function (res) { return res.json(); })
            .then(function (data) { setPanel(data); setCurrentIndex(0); })
            .catch(function () {
                setPanel({ status: false, links: [] });
            });
    }, [hash]);

    // WebSocket subscription
    useEffect(function () {
        var echo = buildEcho();
        var channel = echo.channel('panel.' + hash);

        channel.listen('.PanelUpdated', function (event) {
            setPanel(event);
            setCurrentIndex(0);
        });

        return function () {
            echo.leave('panel.' + hash);
        };
    }, [hash]);

    // Timer to advance to next link
    useEffect(function () {
        if (timerRef.current) {
            clearTimeout(timerRef.current);
        }

        if (!panel || !panel.status || !panel.links || panel.links.length === 0) {
            return;
        }

        var link = panel.links[currentIndex];
        var ms = durationToMs(link.duration_time);

        timerRef.current = setTimeout(function () {
            setCurrentIndex(function (prev) {
                return (prev + 1) % panel.links.length;
            });
        }, ms);

        return function () {
            clearTimeout(timerRef.current);
        };
    }, [panel, currentIndex]);

    if (!panel) return <Loading />;
    if (!panel.status) return <Inactive />;
    if (!panel.links || panel.links.length === 0) return <NoLinks />;

    var link = panel.links[currentIndex];

    return (
        <iframe
            key={link.id + '-' + currentIndex}
            src={link.url}
            title={link.title || 'Painel'}
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

var root = document.getElementById('panel-display');
if (root) {
    var hash = root.getAttribute('data-hash');
    createRoot(root).render(<PanelDisplay hash={hash} />);
}
