<?php

use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use App\Models\PropertyListing;
use Illuminate\Database\Eloquent\Collection;
use Pgvector\Laravel\Vector;

middleware('auth');
name('property-listings.index');

new class extends Component {
    public Collection $propertyListings;
    public string $searchTerm = '';

    public function mount(): void
    {
        $this->loadAllListings();
    }

    public function search(): void
    {
        if (empty($this->searchTerm)) {
            $this->loadAllListings();
            return;
        }

        try {
            $client = \OpenAI::client(env('OPENAI_API_KEY'));
            $model = env('EMBEDDINGS_MODEL', 'text-embedding-ada-002');

            $response = $client->embeddings()->create([
                'model' => $model,
                'input' => $this->searchTerm,
            ]);

            $embedding = new Vector($response->embeddings[0]->embedding);

            // Calculate similarity and filter in one go
            $this->propertyListings = PropertyListing::query()
                ->select('*')
                ->selectRaw('(1 - (embedding <=> ?)) * 50 + 50 as similarity', [$embedding])
                ->where('user_id', auth()->id())
                ->whereRaw('(embedding <=> ?) < 1', [$embedding]) // Similarity threshold at 50%
                ->orderByDesc('similarity')
                ->get();

        } catch (\Exception $e) {
            // Handle exceptions, e.g., show an error message
            $this->dispatch('error', 'Could not perform search: ' . $e->getMessage());
            $this->loadAllListings();
        }
    }

    public function clear(): void
    {
        $this->searchTerm = '';
        $this->loadAllListings();
    }

    private function loadAllListings(): void
    {
        $this->propertyListings = PropertyListing::where('user_id', auth()->id())->latest()->get();
    }
};
?>

<x-layouts.app>
    @volt('property-listings')
    <x-app.container>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">My published properties</h1>
            <a href="{{ route('property-listings.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Create Listing
            </a>
        </div>

        <div class="mt-6">
            <form wire:submit.prevent="search" class="flex items-center space-x-2">
                <input type="text" wire:model="searchTerm" placeholder="Search for properties..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200">
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Search
                </button>
                @if($searchTerm)
                    <button type="button" wire:click="clear" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                        Clear
                    </button>
                @endif
            </form>
        </div>

        <div wire:loading wire:target="search" class="flex items-center justify-center w-full p-4 mt-4 text-sm font-medium text-gray-500">
            <svg class="w-5 h-5 mr-3 -ml-1 text-indigo-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Searching...
        </div>

        <div class="mt-6" wire:loading.remove wire:target="search">
            <div class="overflow-hidden border-b border-gray-200 shadow sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Title</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Type</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Price</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Status</th>
                            @if($searchTerm)
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Similarity</th>
                            @endif
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900">
                        @forelse($propertyListings as $listing)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $listing->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $listing->city }}, {{ $listing->state }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($listing->property_type) }} / {{ ucfirst($listing->transaction_type) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        ${{ number_format($listing->price, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($listing->is_active)
                                        <span class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">Active</span>
                                    @else
                                        <span class="inline-flex px-2 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full">Inactive</span>
                                    @endif
                                </td>
                                @if($searchTerm)
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($listing->similarity, 2) }}%</div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $searchTerm ? 6 : 5 }}" class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                    No property listings found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-app.container>
    @endvolt
</x-layouts.app>