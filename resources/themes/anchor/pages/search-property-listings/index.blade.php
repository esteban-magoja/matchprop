<?php

use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use App\Models\PropertyListing;
use Illuminate\Database\Eloquent\Collection;
use Pgvector\Laravel\Vector;

middleware('auth');
name('search-property-listings.index');

new class extends Component {
    public Collection $propertyListings;
    public string $searchTerm = '';

    public function mount(): void
    {
        $this->propertyListings = new Collection();
    }

    public function search(): void
    {
        if (empty($this->searchTerm)) {
            $this->propertyListings = new Collection();
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

            $this->propertyListings = PropertyListing::query()
                ->select('*')
                ->selectRaw('(1 - (embedding <=> ?)) * 50 + 50 as similarity', [$embedding])
                ->whereRaw('(embedding <=> ?) < 1', [$embedding]) // Similarity threshold at 50%
                ->orderByDesc('similarity')
                ->get();

        } catch (\Exception $e) {
            // Handle exceptions, e.g., show an error message
            $this->dispatch('error', 'Could not perform search: ' . $e->getMessage());
            $this->propertyListings = new Collection();
        }
    }

    public function clear(): void
    {
        $this->searchTerm = '';
        $this->propertyListings = new Collection();
    }
};
?>

<x-layouts.app>
    @volt('search-property-listings')
    <x-app.container>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Search All Property Listings</h1>
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

        <div class="mt-6">
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $searchTerm ? 5 : 4 }}" class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                    @if($searchTerm)
                                        No property listings found matching your search.
                                    @else
                                        Please enter a search term to find property listings.
                                    @endif
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