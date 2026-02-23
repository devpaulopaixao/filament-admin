import { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { importKey, decryptResponse } from './crypto-utils.js';

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
        <div style={styles.statusPage}>
            <div style={styles.statusCard}>
                <div style={styles.iconWrap}>
                    {/* Monitor com X */}
                    <svg viewBox="0 0 24 24" fill="none" style={styles.statusIcon}>
                        <rect x="2" y="3" width="20" height="14" rx="2" stroke="currentColor" strokeWidth="1.5"/>
                        <path d="M8 21h8M12 17v4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
                        <path d="M9 8l6 6M15 8l-6 6" stroke="#ef4444" strokeWidth="2" strokeLinecap="round"/>
                    </svg>
                    <div style={styles.statusDot} />
                </div>
                <p style={styles.statusLabel}>Tela inativa</p>
                <p style={styles.statusSub}>Esta tela está desativada e não exibirá conteúdo.</p>
            </div>
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

function ScreenDisplay({ id, pageToken, pageKey }) {
    var [screen, setScreen] = useState(null);

    // Update browser tab title
    useEffect(function () {
        if (screen && screen.title) {
            document.title = screen.title;
        }
    }, [screen]);

    // Fetch initial data (encrypted)
    useEffect(function () {
        importKey(pageKey)
            .then(function (keyBytes) {
                return fetch('/api/display', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ token: pageToken }),
                })
                .then(function (res) {
                    if (!res.ok) {
                        return res.text().then(function (body) {
                            throw new Error('HTTP ' + res.status + ': ' + body);
                        });
                    }
                    return res.json();
                })
                .then(function (envelope) { return decryptResponse(envelope, keyBytes); });
            })
            .then(function (data) { setScreen(data); })
            .catch(function (err) {
                console.error('[tela] Falha ao carregar:', err);
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
        background: '#0a0a0a',
    },
    message: {
        color: 'rgba(255,255,255,0.5)',
        fontSize: '1.25rem',
        fontFamily: 'system-ui, sans-serif',
        letterSpacing: '0.02em',
    },
    iframe: {
        width: '100%',
        height: '100vh',
        border: 'none',
        display: 'block',
    },

    // Inactive layout
    statusPage: {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        width: '100%',
        height: '100vh',
        background: 'radial-gradient(ellipse at center, #111827 0%, #030712 100%)',
        fontFamily: 'system-ui, sans-serif',
    },
    statusCard: {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        gap: '16px',
        padding: '48px 56px',
        background: 'rgba(255,255,255,0.04)',
        border: '1px solid rgba(255,255,255,0.08)',
        borderRadius: '16px',
        backdropFilter: 'blur(8px)',
        maxWidth: '360px',
        textAlign: 'center',
    },
    iconWrap: {
        position: 'relative',
        marginBottom: '8px',
    },
    statusIcon: {
        width: '72px',
        height: '72px',
        color: 'rgba(255,255,255,0.25)',
    },
    statusDot: {
        position: 'absolute',
        bottom: '-2px',
        right: '-2px',
        width: '14px',
        height: '14px',
        borderRadius: '50%',
        background: '#ef4444',
        border: '2px solid #030712',
        boxShadow: '0 0 8px rgba(239,68,68,0.6)',
    },
    statusLabel: {
        color: '#f1f5f9',
        fontSize: '1.25rem',
        fontWeight: '600',
        letterSpacing: '0.01em',
        margin: 0,
    },
    statusSub: {
        color: 'rgba(255,255,255,0.35)',
        fontSize: '0.875rem',
        lineHeight: '1.5',
        margin: 0,
    },
};

// ---------------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------------

var root = document.getElementById('screen-display');
if (root) {
    var id        = root.getAttribute('data-id');
    var pageToken = root.getAttribute('data-token');
    var pageKey   = root.getAttribute('data-key');
    createRoot(root).render(<ScreenDisplay id={id} pageToken={pageToken} pageKey={pageKey} />);
}
