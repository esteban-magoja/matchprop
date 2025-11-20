{{-- Language Switcher Component --}}
<div x-data="{ open: false }" class="relative inline-block text-left">
    {{-- Botón principal --}}
    <button 
        @click="open = !open" 
        @click.away="open = false"
        type="button" 
        class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700"
        aria-expanded="false"
        aria-haspopup="true"
    >
        <span>{{ locale_name() }}</span>
        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    {{-- Dropdown menu --}}
    <div 
        x-show="open"
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
