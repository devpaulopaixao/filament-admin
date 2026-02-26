@php
    $data = isset($state) ? $state : $getState();
@endphp

<div class="my-2">
    @if($data)
        <pre class="text-xs font-mono whitespace-pre-wrap break-all text-gray-700 dark:text-gray-200 leading-relaxed">{{ is_array($data) ? json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $data }}</pre>
    @endif
</div>
