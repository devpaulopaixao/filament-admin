<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div {{ $getExtraAttributeBag() }}>
        <div>
            <ul>
                @foreach($getState() ?? [] as $key => $value)
                    <li class="mb-2">
                        <span class="inline-block rounded-md whitespace-normal text-gray-700 dark:text-gray-200">
                           {{ is_int($key) ? '#'.($key + 1) : Str::title($key) }}:
                        </span>
                        <span class="font-semibold">
                            @if(is_array($value))
                                <pre class="text-xs font-mono whitespace-pre-wrap break-all text-gray-700 dark:text-gray-200 leading-relaxed">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                            @else
                                {{ $value }}
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</x-dynamic-component>
