<?php

use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use App\Models\PropertyListing;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Pgvector\Laravel\Vector;

middleware('auth');
name('property-listings.index');

new class extends Component {
    public Collection $propertyListings;
    public string $searchTerm = '';
    public ?PropertyListing $listingToDelete = null;

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

            $this->propertyListings = PropertyListing::query()
                ->with('primaryImage')
                ->select('*')
                ->selectRaw('(1 - (embedding <=> ?)) * 50 + 50 as similarity', [$embedding])
                ->where('user_id', auth()->id())
                ->whereRaw('(embedding <=> ?) < 1', [$embedding])
                ->orderByDesc('similarity')
                ->get();

        } catch (\Exception $e) {
            $this->dispatch('error', 'Could not perform search: ' . $e->getMessage());
            $this->loadAllListings();
        }
    }

    public function clear(): void
    {
        $this->searchTerm = '';
        $this->loadAllListings();
    }

    public function confirmDelete(int $listingId): void
    {
        $this->listingToDelete = PropertyListing::where('user_id', auth()->id())->findOrFail($listingId);
    }

    public function delete(): void
    {
        if (!$this->listingToDelete) {
            return;
        }

        // Ensure the user is authorized to delete this listing
        if ($this->listingToDelete->user_id !== auth()->id()) {
            $this->cancelDelete();
            return;
        }

        // Eager load images to get their paths
        $this->listingToDelete->load('images');

        // Delete physical files
        foreach ($this->listingToDelete->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        // Delete the listing from the database (cascades to images table)
        $this->listingToDelete->delete();

        // Refresh the list and close the modal
        $this->loadAllListings();
        $this->cancelDelete();
    }

    public function cancelDelete(): void
    {
        $this->listingToDelete = null;
    }

    private function loadAllListings(): void
    {
        $this->propertyListings = PropertyListing::where('user_id', auth()->id())->with('primaryImage')->latest()->get();
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
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Image</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Title</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Type</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Price</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Status</th>
                            @if($searchTerm)
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Similarity</th>
                            @endif
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900">
                        @forelse($propertyListings as $listing)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($listing->primaryImage)
                                        <img src="{{ $listing->primaryImage->image_url }}" alt="{{ $listing->title }}" class="object-cover w-32 h-20 rounded-md" style="max-width: 150px;">
                                    @else
                                        <div class="flex items-center justify-center w-32 h-20 text-gray-400 bg-gray-100 rounded-md dark:bg-gray-700">
                                            No Image
                                        </div>
                                    @endif
                                </td>
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
                                    <button wire:click="confirmDelete({{ $listing->id }})" class="ml-4 text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $searchTerm ? 8 : 7 }}" class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                    No property listings found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($listingToDelete)
        <div class="fixed inset-0 z-10 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-25" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 dark:bg-gray-800">
                    <div>
                        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                            <svg class="w-6 h-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                                Delete Property Listing
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Are you sure you want to delete "{{ $listingToDelete->title }}"? This will permanently delete the listing and all its images. This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button wire:click="delete" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm">
                            Delete
                        </button>
                        <button wire:click="cancelDelete" type="button" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </x-app.container>
    @endvolt
</x-layouts.app>