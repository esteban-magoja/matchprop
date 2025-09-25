<?php

use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use App\Models\PropertyListing;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Storage;
use Nnjeim\World\Models\Country;
use Nnjeim\World\Models\State;
use Nnjeim\World\Models\City;
use Livewire\Attributes\Computed;

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
    public int $area = 0;

    #[Rule('required|string|max:255')]
    public string $address = '';

    #[Rule('required|string|max:255')]
    public string $city = '';

    #[Rule('required')]
    public $selectedCountry = null;

    #[Rule('required')]
    public $selectedState = null;

    public ?PropertyListing $propertyListing = null;

    public array $images = [];
    public array $imageUploads = [];
    public ?int $primaryImageIndex = null;

    public $countries;
    public $states = [];
    public $cities = [];

    #[Computed]
    public function country()
    {
        return Country::find($this->selectedCountry)?->name;
    }

    #[Computed]
    public function state()
    {
        return State::find($this->selectedState)?->name;
    }

    public function mount()
    {
        $this->countries = Country::all();
    }

    public function updatedSelectedCountry($countryId)
    {
        $this->states = State::where('country_id', $countryId)->get();
        $this->selectedState = null;
    }

    public function updatedSelectedState($stateId)
    {
        $this->cities = City::where('state_id', $stateId)->get();
    }

    public function with(): array
    {
        return [
            'countries' => Country::all(),
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();
        $validated['user_id'] = auth()->id();
        $validated['country'] = $this->country;
        $validated['state'] = $this->state;

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

        $this->dispatch('upload-finished');
    }

    public function removeImage($index)
    {
        array_splice($this->images, $index, 1);
        if (count($this->images) == 0) {
            $this->primaryImageIndex = null;
        }
        elseif ($this->primaryImageIndex == $index) {
            $this->primaryImageIndex = 0;
        }
        elseif ($this->primaryImageIndex > $index) {
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
                            
                            <div class="sm:col-span-2">
                                <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Country</label>
                                <select wire:model.live="selectedCountry" id="country" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select a country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                @error('country') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300">State</label>
                                <select wire:model.live="selectedState" id="state" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select a state</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                                 @error('state') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">City</label>
                                <select wire:model="city" id="city" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select a city</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->name }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                 @error('city') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-6">
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                                <input type="text" wire:model="address" id="address" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                 @error('address') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                        </div>
                    </div>

                    <div class="flex justify-end pt-5">
                        <a href="{{ route('property-listings.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Cancel</a>
                        <button type="submit" wire:loading.attr="disabled" wire:target="save" class="inline-flex items-center justify-center px-4 py-2 ml-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-75 disabled:cursor-wait">
                            <span wire:loading.remove wire:target="save">
                                Next: Add Images
                            </span>
                            <span wire:loading wire:target="save">
                                <svg class="inline-block w-4 h-4 mr-2 animate-spin" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.4857 8.02381C14.4857 4.42133 11.5787 1.51429 8 1.51429C4.42133 1.51429 1.51429 4.42133 1.51429 8.02381C1.51429 11.6263 4.42133 14.5333 8 14.5333" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-25"></path><path d="M8 1.51429V4.51429" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-75"></path></svg>
                                <span>Saving...</span>
                            </span>
                        </button>
                    </div>
                </form>
            @else
                <form wire:submit.prevent="saveImages" class="space-y-8" x-data="imageResizer()" @upload-finished.window="isResizing = false">
                    <div class="p-8 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                        <h2 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Upload Images</h2>
                        <div class="grid grid-cols-1 mt-6 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-6">
                                <label for="imageUploads" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Add Property Images</label>
                                <input type="file" x-ref="imageInput" @change="handleFiles" id="imageUploads" multiple class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                                <div x-show="isResizing" class="mt-2 text-sm text-gray-500">
                                    <svg class="inline-block w-4 h-4 mr-2 animate-spin" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.4857 8.02381C14.4857 4.42133 11.5787 1.51429 8 1.51429C4.42133 1.51429 1.51429 4.42133 1.51429 8.02381C1.51429 11.6263 4.42133 14.5333 8 14.5333" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-25"></path><path d="M8 1.51429V4.51429" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-75"></path></svg>
                                    <span>Processing and uploading images...</span>
                                </div>
                                @error('imageUploads.*') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

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
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveImages" class="inline-flex items-center justify-center px-4 py-2 ml-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-75 disabled:cursor-wait" @if (empty($images)) disabled @endif>
                            <span wire:loading.remove wire:target="saveImages">
                                Save Images & Finish
                            </span>
                            <span wire:loading wire:target="saveImages">
                                <svg class="inline-block w-4 h-4 mr-2 animate-spin" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.4857 8.02381C14.4857 4.42133 11.5787 1.51429 8 1.51429C4.42133 1.51429 1.51429 4.42133 1.51429 8.02381C1.51429 11.6263 4.42133 14.5333 8 14.5333" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-25"></path><path d="M8 1.51429V4.51429" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-75"></path></svg>
                                <span>Finishing...</span>
                            </span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </x-app.container>
    @endvolt
    <x-slot:javascript>
        <script>
            function imageResizer() {
                return {
                    isResizing: false,
                    handleFiles() {
                        let files = this.$refs.imageInput.files;
                        if (!files.length) return;

                        this.isResizing = true;

                        let livewire = this.$wire;
                        let resizedFiles = [];
                        let filesToProcess = files.length;

                        for (let i = 0; i < files.length; i++) {
                            this.resize(files[i], (resizedBlob) => {
                                resizedFiles.push(new File([resizedBlob], files[i].name, { type: 'image/jpeg' }));
                                filesToProcess--;
                                if (filesToProcess === 0) {
                                    livewire.uploadMultiple('imageUploads', resizedFiles, () => {
                                        this.isResizing = false;
                                    });
                                    this.$refs.imageInput.value = ''; // Clear input
                                }
                            });
                        }
                    },
                    resize(file, callback) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const img = new Image();
                            img.onload = () => {
                                const canvas = document.createElement('canvas');
                                const MAX_SIZE = 1200;
                                let width = img.width;
                                let height = img.height;

                                if (width > height) {
                                    if (width > MAX_SIZE) {
                                        height *= MAX_SIZE / width;
                                        width = MAX_SIZE;
                                    }
                                } else {
                                    if (height > MAX_SIZE) {
                                        width *= MAX_SIZE / height;
                                        height = MAX_SIZE;
                                    }
                                }
                                canvas.width = width;
                                canvas.height = height;
                                const ctx = canvas.getContext('2d');
                                ctx.drawImage(img, 0, 0, width, height);
                                canvas.toBlob(callback, 'image/jpeg', 0.85);
                            };
                            img.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }
        </script>
    </x-slot:javascript>
</x-layouts.app>