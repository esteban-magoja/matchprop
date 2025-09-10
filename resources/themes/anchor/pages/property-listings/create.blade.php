<?php

use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use App\Models\PropertyListing;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Storage;

middleware('auth');
name('property-listings.create');

new class extends Component {
    use WithFileUploads;

    public int $step = 1;

    #[Rule('required|string|max:255')]
    public string $title = '';

    #[Rule('required|string')]
    public string $description = '';

    #[Rule('required|string')]
    public string $property_type = 'house';

    #[Rule('required|string')]
    public string $transaction_type = 'sale';

    #[Rule('required|numeric|min:0')]
    public string $price = '';

    #[Rule('required|integer|min:0')]
    public int $bedrooms = 0;

    #[Rule('required|integer|min:0')]
    public int $bathrooms = 0;

    #[Rule('integer|min:0')]
    public int $parking_spaces = 0;

    #[Rule('required|numeric|min:0')]
    public string $area = '';

    #[Rule('required|string|max:255')]
    public string $address = '';

    #[Rule('required|string|max:255')]
    public string $city = '';

    #[Rule('required|string|max:255')]
    public string $state = '';

    #[Rule('required|string|max:255')]
    public string $country = 'Argentina';

    public ?PropertyListing $propertyListing = null;

    public array $images = [];
    public array $imageUploads = [];
    public ?int $primaryImageIndex = null;

    public function save(): void
    {
        $validated = $this->validate();
        $validated['user_id'] = auth()->id();

        $this->propertyListing = PropertyListing::create($validated);

        $this->step = 2;
    }

    public function updatedImageUploads()
    {
        $this->validate(['imageUploads.*' => 'image|max:10240']);
        $this->images = array_merge($this->images, $this->imageUploads);
        if (is_null($this->primaryImageIndex) && count($this->images) > 0) {
            $this->primaryImageIndex = 0;
        }
        $this->imageUploads = [];
    }

    public function removeImage($index)
    {
        array_splice($this->images, $index, 1);
        if (count($this->images) == 0) {
            $this->primaryImageIndex = null;
        } elseif ($this->primaryImageIndex == $index) {
            $this->primaryImageIndex = 0;
        } elseif ($this->primaryImageIndex > $index) {
            $this->primaryImageIndex--;
        }
    }

    public function saveImages(): void
    {
        $this->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'image|max:10240', // 10MB Max
        ]);

        foreach ($this->images as $index => $image) {
            $path = $image->store('property_images', 'public');
            $this->propertyListing->images()->create([
                'image_path' => $path,
                'image_url' => Storage::url($path),
                'is_primary' => $index === $this->primaryImageIndex,
            ]);
        }

        $this->redirectRoute('property-listings.index');
    }
};
?>

<x-layouts.app>
    @volt('property-listings.create')
    <x-app.container>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Create New Property Listing</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                @if ($step == 1)
                    Step 1: Fill out the form below to add a new property.
                @else
                    Step 2: Upload images for your property.
                @endif
            </p>
        </div>

        <div class="mt-6">
            @if ($step == 1)
                <form wire:submit.prevent="save" class="space-y-8">
                    <div class="p-8 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                        <h2 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Property Details</h2>
                        <div class="grid grid-cols-1 mt-6 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-6">
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                                <input type="text" wire:model="title" id="title" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('title') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea wire:model="description" id="description" rows="4" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                @error('description') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="property_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Property Type</label>
                                <select wire:model="property_type" id="property_type" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="house">House</option>
                                    <option value="apartment">Apartment</option>
                                    <option value="condo">Condo</option>
                                    <option value="land">Land</option>
                                </select>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="transaction_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Transaction Type</label>
                                <select wire:model="transaction_type" id="transaction_type" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="sale">For Sale</option>
                                    <option value="rent">For Rent</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price</label>
                                <input type="number" wire:model="price" id="price" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                 @error('price') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="bedrooms" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bedrooms</label>
                                <input type="number" wire:model="bedrooms" id="bedrooms" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                 @error('bedrooms') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="bathrooms" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bathrooms</label>
                                <input type="number" wire:model="bathrooms" id="bathrooms" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                 @error('bathrooms') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="parking_spaces" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Parking Spaces</label>
                                <input type="number" wire:model="parking_spaces" id="parking_spaces" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="area" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Area (sqft)</label>
                                <input type="number" wire:model="area" id="area" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                 @error('area') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="p-8 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                        <h2 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Location</h2>
                        <div class="grid grid-cols-1 mt-6 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-6">
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                                <input type="text" wire:model="address" id="address" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                 @error('address') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">City</label>
                                <input type="text" wire:model="city" id="city" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                 @error('city') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300">State</label>
                                <input type="text" wire:model="state" id="state" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                 @error('state') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Country</label>
                                <select wire:model="country" id="country" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option>Argentina</option>
                                    <option>Chile</option>
                                    <option>Mexico</option>
                                </select>
                                @error('country') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-5">
                        <a href="{{ route('property-listings.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Cancel</a>
                        <button type="submit" class="inline-flex justify-center px-4 py-2 ml-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Next: Add Images
                        </button>
                    </div>
                </form>
            @else
                <form wire:submit.prevent="saveImages" class="space-y-8">
                    <div class="p-8 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                        <h2 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Upload Images</h2>
                        <div class="grid grid-cols-1 mt-6 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-6">
                                <label for="imageUploads" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Add Property Images</label>
                                <input type="file" wire:model="imageUploads" id="imageUploads" multiple class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                                @error('imageUploads.*') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

                                <div wire:loading wire:target="imageUploads">Uploading...</div>

                                @if ($images)
                                    <div class="grid grid-cols-2 gap-4 mt-4 sm:grid-cols-3 md:grid-cols-4">
                                        @foreach ($images as $index => $image)
                                            <div class="relative p-2 border-2 rounded-lg" :class="{{ $primaryImageIndex === $index ? 'border-indigo-500' : 'border-transparent' }}">
                                                <img src="{{ $image->temporaryUrl() }}" class="object-cover w-full h-32 rounded-lg">
                                                <button wire:click.prevent="removeImage({{ $index }})" class="absolute top-0 right-0 p-1 text-white bg-red-500 rounded-full shadow-md -mt-2 -mr-2 hover:bg-red-600 focus:outline-none">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                                </button>
                                                <div class="mt-2 text-center">
                                                    <label class="flex items-center justify-center space-x-2 text-sm">
                                                        <input type="radio" wire:model="primaryImageIndex" value="{{ $index }}" name="primary_image">
                                                        <span>Primary</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-5">
                        <button type="button" wire:click="$set('step', 1)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Back</button>
                        <button type="submit" class="inline-flex justify-center px-4 py-2 ml-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" @if (empty($images)) disabled @endif>
                            Save Images & Finish
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </x-app.container>
    @endvolt
</x-layouts.app>