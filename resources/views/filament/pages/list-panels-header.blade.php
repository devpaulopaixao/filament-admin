<x-filament::section>
    <x-slot name="heading">
        <div class="flex items-center gap-2 text-info-600 dark:text-info-400">
            <x-filament::icon icon="heroicon-o-information-circle" class="h-5 w-5" />
            {{ __('Sobre este módulo') }}
        </div>
    </x-slot>

    <p class="text-sm text-gray-600 dark:text-gray-400">
        {{ __('Os painéis agrupam links que serão embedados na tela, em formato de exibição e apresentados dinâmicamente e em ordem previamente estabelecida. Gerencie essas e outras configurações disponíveis para cadas painel.') }}
    </p>
</x-filament::section>
