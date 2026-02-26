@php
    $values = isset($data) ? $data : $getState();
@endphp

<div {{ $getExtraAttributeBag() }} class="fi-ta-col">
    @if($values)
        <pre class="text-xs font-mono whitespace-pre-wrap break-all text-gray-700 dark:text-gray-200 leading-relaxed">{{ is_array($values) ? json_encode($values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $values }}</pre>
    @endif
</div>
