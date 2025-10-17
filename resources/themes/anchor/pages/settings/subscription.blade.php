<?php
    
    use Filament\Forms\Components\TextInput;
    use Livewire\Volt\Component;
    use function Laravel\Folio\{middleware, name};
    use Filament\Forms\Concerns\InteractsWithForms;
    use Filament\Forms\Contracts\HasForms;
    use Filament\Forms\Form;
    use Filament\Notifications\Notification;
    
    middleware('auth');
    name('settings.subscription');

	new class extends Component
	{
        public function mount(): void
        {
            
        }
    }

?>

<x-layouts.app>
    @volt('settings.subscription') 
        <div class="relative">
            <x-app.settings-layout
                title="Membresía"
                description="Detalles de tu membresía"
            >
                @role('admin')
                    <x-app.alert id="no_subscriptions" :dismissable="false" type="info">
                        You are logged in as an admin and have full access. Authenticate with a different user and visit this page to see the subscription checkout process.
                    </x-app.alert>
                @else
                    @subscriber
                        
                        <div class="relative w-full h-auto">                            
                            <x-app.alert id="no_subscriptions" :dismissable="false" type="success">
                                <div class="flex items-center w-full">
                                    <x-phosphor-seal-check-duotone class="flex-shrink-0 mr-1.5 -ml-1.5 w-6 h-6" /> 
                                    <span>Actualmente estás suscrito a {{ auth()->user()->plan()->name }} {{ auth()->user()->planInterval() }} Plan.</span>
                                </div>
                            </x-app.alert>
                            <p class="my-4">Gestiona tu suscripción haciendo clic a continuación. Edita esta página desde el siguiente archivo:  <x-code-inline>resources/views/{{ $theme->folder }}/pages/settings/subscription.blade.php</x-code-inline></p>
                            @if (session('update'))
                                <div class="my-4 text-sm text-green-600">Se ha actualizado correctamente tu suscripción</div>
                            @endif
                            <livewire:billing.update />
                        </div>
                    @endsubscriber

                    @notsubscriber
                        <div class="mb-4">
                            <x-app.alert id="no_subscriptions" :dismissable="false" type="info">
                                <div class="flex items-center space-x-1.5">
                                    <x-phosphor-shopping-bag-open-duotone class="flex-shrink-0 mr-1.5 -ml-1.5 w-6 h-6" />
                                    <span>No se encontraron suscripciones activas. Por favor selecciona un plan a continuación.</span>
                                </div>
                            </x-app.alert>
                        </div>
                        <livewire:billing.checkout />
                        <p class="flex items-center mt-3 mb-4">
                            <x-phosphor-shield-check-duotone class="w-4 h-4 mr-1" />
                            <span class="mr-1">La facturación se gestiona de forma segura a través de </span><strong>{{ ucfirst(config('wave.billing_provider')) }}</strong>.
                        </p>
                    @endnotsubscriber
                @endrole
            </x-app.settings-layout>
        </div>
    @endvolt
</x-layouts.app>
