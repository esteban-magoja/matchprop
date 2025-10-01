<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use Livewire\Volt\Component;
use function Laravel\Folio\{middleware, name};

middleware(['guest']);
name('signup');

new class extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $movil = '';

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'movil' => 'nullable|string|max:20',
        ];
    }

    public function register()
    {
        $this->validate();

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ];

        // Agregar móvil si se proporciona
        if (!empty($this->movil)) {
            $userData['movil'] = $this->movil;
        }

        $user = User::create($userData);

        event(new Registered($user));

        Auth::login($user, true);

        if (config('devdojo.auth.settings.registration_require_email_verification')) {
            return redirect()->route('verification.notice');
        }

        if (session()->get('url.intended') != route('logout.get')) {
            session()->regenerate();
            redirect()->intended(config('devdojo.auth.settings.redirect_after_auth'));
        } else {
            session()->regenerate();
            return redirect(config('devdojo.auth.settings.redirect_after_auth'));
        }
    }
};

?>

<x-auth::layouts.app title="Registro - {{ config('app.name') }}">
    @volt('signup')
    <x-auth::elements.container>
        
        <x-auth::elements.heading text="Crear Cuenta" description="Únete a nuestra plataforma" />
        
        <x-auth::elements.session-message />

        <form wire:submit="register" class="space-y-5">
            
            <!-- Nombre -->
            <div>
                <x-auth::elements.input 
                    label="Nombre Completo" 
                    type="text" 
                    wire:model="name" 
                    autofocus="true" 
                    required 
                />
                @error('name') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Email -->
            <div>
                <x-auth::elements.input 
                    label="Dirección de Email" 
                    id="email" 
                    name="email" 
                    type="email" 
                    wire:model="email" 
                    autocomplete="email" 
                    required 
                />
                @error('email') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Teléfono Móvil -->
            <div>
                <x-auth::elements.input 
                    label="Teléfono Móvil (WhatsApp) - Opcional" 
                    id="movil" 
                    name="movil" 
                    type="tel" 
                    wire:model="movil" 
                    placeholder="+34600123456" 
                    autocomplete="tel" 
                />
                @error('movil') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
                <p class="text-sm text-gray-500 mt-1">Formato internacional recomendado para WhatsApp</p>
            </div>

            <!-- Contraseña -->
            <div>
                <x-auth::elements.input 
                    label="Contraseña" 
                    type="password" 
                    wire:model="password" 
                    id="password" 
                    name="password" 
                    autocomplete="new-password" 
                    required 
                />
                @error('password') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <x-auth::elements.input 
                    label="Confirmar Contraseña" 
                    type="password" 
                    wire:model="password_confirmation" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    autocomplete="new-password" 
                    required 
                />
                @error('password_confirmation') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Botón de Registro -->
            <x-auth::elements.button submit="true" rounded="md">
                Crear Cuenta
            </x-auth::elements.button>
        </form>

        <!-- Link a Login -->
        <div class="mt-6 text-center">
            <span class="text-sm opacity-70">¿Ya tienes una cuenta?</span>
            <x-auth::elements.text-link href="{{ route('auth.login') }}">
                Iniciar Sesión
            </x-auth::elements.text-link>
        </div>

    </x-auth::elements.container>
    @endvolt
</x-auth::layouts.app>