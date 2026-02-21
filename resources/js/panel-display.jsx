import { useState, useEffect, useRef, useCallback } from 'react';
import { createRoot } from 'react-dom/client';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function durationToMs(hhmm) {
    if (!hhmm) return 10000;
    var parts = hhmm.split(':').map(Number);
    var h = parts[0] || 0;
    var m = parts[1] || 0;
    var total = (h * 3600 + m * 60) * 1000;
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
// Status screens
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
// Controls — prev / pause-play / next
// ---------------------------------------------------------------------------

function Controls({ isPaused, onPrev, onTogglePause, onNext }) {
    return (
        <div style={styles.controls}>
            <button style={styles.controlBtn} onClick={onPrev} title="Anterior">
                <svg viewBox="0 0 24 24" fill="currentColor" style={styles.controlIcon}>
                    <path d="M6 6h2v12H6zm3.5 6 8.5 6V6z"/>
                </svg>
            </button>

            <button style={styles.controlBtn} onClick={onTogglePause} title={isPaused ? 'Reproduzir' : 'Pausar'}>
                {isPaused ? (
                    <svg viewBox="0 0 24 24" fill="currentColor" style={styles.controlIcon}>
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                ) : (
                    <svg viewBox="0 0 24 24" fill="currentColor" style={styles.controlIcon}>
                        <path d="M6 19h4V5H6zm8-14v14h4V5z"/>
                    </svg>
                )}
            </button>

            <button style={styles.controlBtn} onClick={onNext} title="Próximo">
                <svg viewBox="0 0 24 24" fill="currentColor" style={styles.controlIcon}>
                    <path d="M6 18l8.5-6L6 6zm2.5-6 5.5 3.9V8.1zM16 6h2v12h-2z"/>
                </svg>
            </button>
        </div>
    );
}

// ---------------------------------------------------------------------------
// Status bar — título, índice, barra de progresso e controles opcionais
// ---------------------------------------------------------------------------

function StatusBar({ link, currentIndex, total, elapsed, durationMs, showControls, isPaused, onPrev, onTogglePause, onNext }) {
    var singleLink = total === 1;
    var pct = (!singleLink && durationMs > 0) ? Math.min((elapsed / durationMs) * 100, 100) : 0;

    return (
        <div style={styles.statusBar}>
            <div style={styles.statusInfo}>
                {showControls && !singleLink && (
                    <Controls
                        isPaused={isPaused}
                        onPrev={onPrev}
                        onTogglePause={onTogglePause}
                        onNext={onNext}
                    />
                )}
                <span style={Object.assign({}, styles.statusTitle, singleLink ? { gridColumn: '1 / -1' } : {})}>{link.title || link.url}</span>
                {!singleLink && (
                    <span style={styles.statusIndex}>{currentIndex + 1}&nbsp;/&nbsp;{total}</span>
                )}
            </div>
            {!singleLink && (
                <div style={styles.progressTrack}>
                    <div style={Object.assign({}, styles.progressFill, { width: pct + '%' })} />
                </div>
            )}
        </div>
    );
}

// ---------------------------------------------------------------------------
// Main component
// ---------------------------------------------------------------------------

function PanelDisplay({ hash }) {
    var [panel, setPanel] = useState(null);
    var [currentIndex, setCurrentIndex] = useState(0);
    var [elapsed, setElapsed] = useState(0);
    var [isPaused, setIsPaused] = useState(false);
    var intervalRef = useRef(null);

    // Fetch initial data
    useEffect(function () {
        fetch('/api/panels/' + hash)
            .then(function (res) { return res.json(); })
            .then(function (data) { setPanel(data); setCurrentIndex(0); setElapsed(0); setIsPaused(false); })
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
            setElapsed(0);
            setIsPaused(false);
        });

        return function () {
            echo.leave('panel.' + hash);
        };
    }, [hash]);

    // Tick every 100ms — advances elapsed unless paused
    useEffect(function () {
        if (intervalRef.current) {
            clearInterval(intervalRef.current);
        }

        if (!panel || !panel.status || !panel.links || panel.links.length === 0) {
            return;
        }

        // Single link: no timer, no loop
        if (panel.links.length === 1) {
            return;
        }

        if (isPaused) {
            return;
        }

        var link = panel.links[currentIndex];
        var ms = durationToMs(link.duration_time);

        intervalRef.current = setInterval(function () {
            setElapsed(function (prev) {
                var next = prev + 100;
                if (next >= ms) {
                    // Advance to next link
                    setCurrentIndex(function (idx) {
                        return (idx + 1) % panel.links.length;
                    });
                    setElapsed(0);
                    return 0;
                }
                return next;
            });
        }, 100);

        return function () {
            clearInterval(intervalRef.current);
        };
    }, [panel, currentIndex, isPaused]);

    // Reset elapsed when link changes
    useEffect(function () {
        setElapsed(0);
    }, [currentIndex]);

    var handlePrev = useCallback(function () {
        if (!panel || !panel.links || panel.links.length === 0) return;
        setCurrentIndex(function (idx) {
            return (idx - 1 + panel.links.length) % panel.links.length;
        });
        setElapsed(0);
    }, [panel]);

    var handleNext = useCallback(function () {
        if (!panel || !panel.links || panel.links.length === 0) return;
        setCurrentIndex(function (idx) {
            return (idx + 1) % panel.links.length;
        });
        setElapsed(0);
    }, [panel]);

    var handleTogglePause = useCallback(function () {
        setIsPaused(function (p) { return !p; });
    }, []);

    if (!panel) return <Loading />;
    if (!panel.status) return <Inactive />;
    if (!panel.links || panel.links.length === 0) return <NoLinks />;

    var link = panel.links[currentIndex];
    var ms = durationToMs(link.duration_time);

    return (
        <>
            <iframe
                key={link.id + '-' + currentIndex}
                src={link.url}
                title={link.title || 'Painel'}
                style={styles.iframe}
                allowFullScreen
            />

            <StatusBar
                link={link}
                currentIndex={currentIndex}
                total={panel.links.length}
                elapsed={elapsed}
                durationMs={ms}
                showControls={panel.show_controls}
                isPaused={isPaused}
                onPrev={handlePrev}
                onTogglePause={handleTogglePause}
                onNext={handleNext}
            />
        </>
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

    // status bar
    statusBar: {
        position: 'fixed',
        bottom: 0,
        left: 0,
        right: 0,
        background: 'rgba(0, 0, 0, 0.72)',
        backdropFilter: 'blur(4px)',
        zIndex: 9999,
        fontFamily: 'sans-serif',
    },
    statusInfo: {
        display: 'grid',
        gridTemplateColumns: '1fr auto 1fr',
        alignItems: 'center',
        padding: '6px 14px',
        gap: '8px',
    },
    statusTitle: {
        color: '#fff',
        fontSize: '0.875rem',
        whiteSpace: 'nowrap',
        overflow: 'hidden',
        textOverflow: 'ellipsis',
        textAlign: 'center',
        gridColumn: '2',
    },
    statusIndex: {
        color: 'rgba(255,255,255,0.55)',
        fontSize: '0.8rem',
        textAlign: 'right',
        gridColumn: '3',
    },
    progressTrack: {
        height: '3px',
        background: 'rgba(255,255,255,0.15)',
        overflow: 'hidden',
    },
    progressFill: {
        height: '100%',
        background: '#3b82f6',
        transition: 'width 100ms linear',
    },

    // controls
    controls: {
        display: 'flex',
        gap: '4px',
        alignItems: 'center',
        gridColumn: '1',
    },
    controlBtn: {
        background: 'rgba(255,255,255,0.1)',
        border: 'none',
        borderRadius: '4px',
        color: '#fff',
        cursor: 'pointer',
        padding: '4px',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        lineHeight: 0,
    },
    controlIcon: {
        width: '20px',
        height: '20px',
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
