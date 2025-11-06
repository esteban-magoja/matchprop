<section class="flex relative top-0 flex-col justify-center items-center -mt-24 w-full  bg-white pb-5">
    <div class="flex flex-col flex-1 gap-6 justify-between items-center px-8 pt-32 mx-auto w-full max-w-2xl text-center md:px-12 xl:px-20 lg:pt-32 lg:pb-16 lg:max-w-7xl lg:flex-row">
        <div class="w-full">
            <h1 class="text-6xl font-bold tracking-tighter text-center sm:text-7xl md:text-[84px] text-zinc-900 text-balance">
                Encuentra tu <span class="text-transparent bg-clip-text bg-gradient-to-b from-neutral-900 to-neutral-500">Match Inmobiliario</span>
            </h1>
            <p class="mx-auto mt-5 text-lg font-normal text-center md:text-xl max-w-2xl text-zinc-500">
                Conectamos con IA, propiedades con compradores y agentes. En Raxta, solo se publican propiedades monitoreadas y auténticas.
            </p>
            <div class="flex flex-col gap-3 justify-center items-center mx-auto mt-8 md:gap-2 md:flex-row">
                <x-button size="lg" class="w-full md:w-auto" href="{{ route('property.search') }}" tag="a">Buscar Propiedades</x-button>
                @guest
                <x-button size="lg" color="secondary" class="w-full md:w-auto" href="/join_us" tag="a">Agregar Inmueble</x-button>
                <x-button size="lg" color="secondary" class="w-full md:w-auto" href="{{ route('requests.create') }}" tag="a">Publicar Solicitud</x-button>
                @else
                <x-button size="lg" color="secondary" class="w-full md:w-auto" href="/property-listings/create" tag="a">Agregar Inmueble</x-button>
                <x-button size="lg" color="secondary" class="w-full md:w-auto" href="/dashboard/requests/create" tag="a">Publicar Solicitud</x-button>
                @endguest
            </div>
        </div>
    </div>
</section>