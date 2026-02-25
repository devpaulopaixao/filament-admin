<x-filament::section>
    <x-slot name="heading">
        <div class="flex items-center gap-2 text-info-600 dark:text-info-400">
            <x-filament::icon icon="heroicon-o-information-circle" class="h-5 w-5" />
            {{ __('Sobre este módulo') }}
        </div>
    </x-slot>

    <p class="text-sm text-gray-600 dark:text-gray-400">
        {{ __('Os grupos de painéis organizam e categorizam os painéis disponíveis, permitindo estruturar a exibição de conteúdo de forma hierárquica e facilitar o gerenciamento de acesso por utilizador.') }}
    </p>
</x-filament::section>
