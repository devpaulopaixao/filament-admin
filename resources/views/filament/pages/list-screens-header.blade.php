<x-filament::section>
    <x-slot name="heading">
        <div class="flex items-center gap-2 text-info-600 dark:text-info-400">
            <x-filament::icon icon="heroicon-o-information-circle" class="h-5 w-5" />
            {{ __('Sobre este módulo') }}
        </div>
    </x-slot>

    <p class="text-sm text-gray-600 dark:text-gray-400">
        {{ __('As telas representam as interfaces individuais que podem ser exibidas em diferentes dispositivos. Utilize esta funcionalidade para controlar dinâmicamente os painéis associados a estes dispositivos.') }}
    </p>
</x-filament::section>
