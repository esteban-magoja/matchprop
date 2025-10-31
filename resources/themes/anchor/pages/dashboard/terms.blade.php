<?php
    use function Laravel\Folio\{middleware, name};
    
    middleware('auth');
    name('dashboard.terms');
?>

<x-layouts.app>
    <x-app.container>
        <div class="max-w-4xl mx-auto py-2 px-2 sm:px-6 lg:px-4">
            <div class="bg-white shadow rounded-lg p-8">
<div class="max-w-4xl mx-auto mt-2">

            <h1 class="text-3xl font-extrabold text-gray-900 mb-6">
                <span>Términos y Condiciones de Participación en Raxta</span>
                
            </h1>

        </div>

        
        <div class="max-w-4xl mx-auto">
            <p class="mb-3">Al registrarse en <strong>Raxta</strong>, el usuario declara haber leído, comprendido y aceptado los siguientes términos, que rigen su participación en el programa de ventas colectivas inmobiliarias de la plataforma:</p>
            <ul>
                <li><p class="mb-3"><strong>Asociación y membresía:</strong><br>Para participar del programa, es requisito indispensable asociarse mediante el pago de un costo fijo mensual. La membresía otorga acceso pleno a las herramientas y beneficios del portal.</p></li>
                <li><p class="mb-3"><strong>Publicación de anuncios:</strong><br>Los socios pueden publicar anuncios inmobiliarios sin límite de cantidad, siempre que cumplan con las normas de calidad, veracidad y actualización establecidas por Raxta.</p></li>
                <li><p class="mb-3"><strong>Comisiones compartidas:</strong><br>Los inmuebles publicados deberán estar disponibles para compartir comisiones con agentes, socios o clientes provistos por Raxta, así como en casos de operaciones colectivas.</p></li>
                <li><p class="mb-3"><strong>Delegación de clientes y transacciones:</strong><br>Los clientes o compradores publicados pueden ser delegados a otros miembros. En caso de concretarse una transacción, las partes deberán respetar la comisión previamente pactada y declarada.</p></li>
                <li><p class="mb-3"><strong>Operaciones con clientes provistos por Raxta:</strong><br>En caso de que Raxta provea un cliente en forma directa y se concrete una venta, la plataforma cobrará una comisión del <strong>1%</strong> del valor final de la operación, o el equivalente al <strong>50% del costo mensual</strong> en caso de alquiler.</p></li>
                <li><p class="mb-3"><strong>Operaciones entre socios:</strong><br>Raxta <strong>no cobrará comisiones adicionales</strong> cuando las operaciones se realicen exclusivamente entre miembros socios de la plataforma.</p></li>
                <li><p class="mb-3"><strong>Cumplimiento y sanciones:</strong><br>El incumplimiento de las normas, la publicación de información falsa o desactualizada, o cualquier conducta contraria a los términos aquí establecidos podrá derivar en <strong>bloqueo temporal o permanente de la cuenta</strong> y la aplicación de <strong>penalizaciones económicas</strong>.</p></li>
            </ul>
            
            <p class="mb-3 mt-3"><strong>Al hacer clic en “Aceptar”, el usuario confirma su adhesión plena a estos Términos y Condiciones, reconociendo que forman parte del acuerdo de participación en Raxta.</strong></p>
        </div>
                @if(!auth()->user()->hasAcceptedTerms())
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <form action="{{ route('terms.accept') }}" method="POST">
                            @csrf
                            <div class="flex items-start mb-4">
                                <div class="flex items-center h-5">
                                    <input id="accept_terms" name="accept_terms" type="checkbox" required
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="accept_terms" class="font-medium text-gray-700">
                                        He leído y acepto los términos y condiciones para publicar anuncios
                                    </label>
                                </div>
                            </div>
                            <button type="submit" 
                                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Aceptar Términos y Condiciones
                            </button>
                        </form>
                    </div>
                @else
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                            <div class="flex">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">
                                        Ya has aceptado los términos y condiciones el {{ auth()->user()->terms_accepted_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                ← Volver al Dashboard
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </x-app.container>
</x-layouts.app>
