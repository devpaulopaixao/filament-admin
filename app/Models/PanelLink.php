<?php

namespace App\Models;

use App\Events\PanelUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class PanelLink extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'panel_id',
        'title',
        'url',
        'status',
        'duration_time',
        'display_number',
    ];

    /** @var array<int, array> Snapshot dos links antes da alteração, indexado por panel_id */
    protected static $panelLinksBeforeChange = [];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PanelLink $link) {
            if (empty($link->display_number)) {
                $link->display_number = static::where('panel_id', $link->panel_id)->max('display_number') + 1;
            }
            static::captureLinksSnapshot($link->panel_id);
        });

        static::updating(function (PanelLink $link) {
            static::captureLinksSnapshot($link->panel_id);
        });

        static::deleting(function (PanelLink $link) {
            static::captureLinksSnapshot($link->panel_id);
        });

        static::saved(function (PanelLink $link) {
            static::auditPanelLinksChange($link->panel_id);
            broadcast(new PanelUpdated($link->panel));
        });

        static::deleted(function (PanelLink $link) {
            static::auditPanelLinksChange($link->panel_id);
            broadcast(new PanelUpdated($link->panel));
        });
    }

    /**
     * Captura o estado atual dos links do painel antes da alteração.
     * Usa o panel_id como chave para evitar capturas duplicadas em operações em lote.
     */
    private static function captureLinksSnapshot(int $panelId): void
    {
        if (isset(static::$panelLinksBeforeChange[$panelId])) {
            return;
        }

        static::$panelLinksBeforeChange[$panelId] = static::where('panel_id', $panelId)
            ->orderBy('display_number')
            ->get(['id', 'title', 'url', 'status', 'duration_time', 'display_number'])
            ->toArray();
    }

    /**
     * Cria um registro de auditoria no Panel pai mostrando
     * os links antes e depois da alteração.
     */
    private static function auditPanelLinksChange(int $panelId): void
    {
        $oldLinks = static::$panelLinksBeforeChange[$panelId] ?? null;

        if ($oldLinks === null) {
            return;
        }

        $newLinks = static::where('panel_id', $panelId)
            ->orderBy('display_number')
            ->get(['id', 'title', 'url', 'status', 'duration_time', 'display_number'])
            ->toArray();

        $auditClass = config('audit.implementation', \OwenIt\Auditing\Models\Audit::class);

        $auditClass::create([
            'user_type' => auth()->user() ? get_class(auth()->user()) : null,
            'user_id'   => auth()->id(),
            'event'     => 'links_updated',
            'auditable_type' => Panel::class,
            'auditable_id'   => $panelId,
            'old_values' => static::formatLinksForAudit($oldLinks),
            'new_values' => static::formatLinksForAudit($newLinks),
            'url'        => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        unset(static::$panelLinksBeforeChange[$panelId]);
    }

    /**
     * Converte o array de links para um mapa associativo legível pelo template de auditoria.
     * Formato: ['#1 Título' => 'url | duração | status']
     *
     * @param  array<int, array>  $links
     * @return array<string, string>
     */
    private static function formatLinksForAudit(array $links): array
    {
        $result = [];

        foreach ($links as $i => $link) {
            $position = $link['display_number'] ?? ($i + 1);
            $title    = $link['title'] ?? 'Link';
            $key      = "#{$position} {$title}";

            $status = ($link['status'] ?? true) ? 'ativo' : 'inativo';
            $result[$key] = ($link['url'] ?? '') . ' | ' . ($link['duration_time'] ?? '') . ' | ' . $status;
        }

        return $result;
    }

    public function panel(): BelongsTo
    {
        return $this->belongsTo(Panel::class);
    }
}
