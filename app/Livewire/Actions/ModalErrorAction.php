<?php

namespace App\Livewire\Actions;

use Livewire\Component;

class ModalErrorAction
{
    /**
     * Dispatches an error to a component to be displayed in a modal.
     *
     * IMPORTANT: The Livewire component using this action via `ModalErrorAction::dispatch($this, ...)`
     * MUST declare the following public properties for this action to function correctly:
     * - `public bool $showErrorModal = false;`
     * - `public string $errorTitle = 'Error';`
     * - `public string $errorMessage = 'An unexpected error occurred.';`
     *
     * And include a modal in its Blade view like:
     * ```html
     *  <x-modal wire:model="showErrorModal" title="{{ $errorTitle ?? 'Error' }}" persistent class="backdrop-blur">
     *      <p>{{ $errorMessage ?? 'An unexpected error occurred.' }}</p>
     *      <x-slot:actions>
     *          <x-button label="Close" @click="$wire.showErrorModal = false" class="btn-primary" />
     *      </x-slot:actions>
     *  </x-modal>
     * ```
     *
     * @param Component $component The Livewire component instance that should display the error.
     * @param string $title The title for the error modal.
     * @param string $message The error message to display.
     */
    public static function dispatch(Component $component, string $title, string $message): void
    {
        if (
            property_exists($component, 'showErrorModal') &&
            property_exists($component, 'errorTitle') &&
            property_exists($component, 'errorMessage')
        ) {
            // The following assignments are safe due to the property_exists checks above.
            // Static analysis tools (like Intelephense) might still flag these if they
            // cannot infer type from property_exists when analyzing this file in isolation.
            // However, if the calling component declares these properties, analysis there should be fine.
            $component->errorTitle = $title;
            $component->errorMessage = $message;
            $component->showErrorModal = true;
        } else {
            // Log a warning if the component is not set up correctly.
            logger()->warning(
                'ModalErrorAction dispatched to a component lacking necessary public properties (showErrorModal, errorTitle, errorMessage).',
                [
                    'component' => get_class($component),
                    'title' => $title,
                    'message' => $message,
                ]
            );

            // As a fallback, try to use the component's own 'error' toast method if it exists and is callable.
            if (method_exists($component, 'error') && is_callable([$component, 'error'])) {
                // This dynamic call is also potentially problematic for static analysis,
                // but common in flexible systems.
                /** @phpstan-ignore-next-line */
                $component->error("$title: $message");
            }
        }
    }
}
