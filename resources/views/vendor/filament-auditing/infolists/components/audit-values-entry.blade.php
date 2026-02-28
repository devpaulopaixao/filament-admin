<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div {{ $getExtraAttributeBag() }}>
        @php $values = $getState(); @endphp
        @if($values)
            <pre class="text-xs font-mono whitespace-pre-wrap break-all text-gray-700 dark:text-gray-200 leading-relaxed overflow-x-auto">{{ is_array($values) ? json_encode($values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $values }}</pre>
        @endif
    </div>
</x-dynamic-component>
