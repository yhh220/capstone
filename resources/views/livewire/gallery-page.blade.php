<div>
    <div class="bg-brand-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2" data-aos="fade-up">{{ __('Gallery') }}</h1>
            <p class="text-gray-400" data-aos="fade-up" data-aos-delay="80">{{ __('Browse our portfolio of installations and modifications.') }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-10">
        {{-- Category Filter --}}
        <div class="flex flex-wrap gap-2 mb-8" role="tablist" aria-label="{{ __('Filter gallery by category') }}" data-aos="fade-up">
            <button wire:click="$set('activeCategory', '')"
                    class="px-4 py-2 rounded-full text-sm font-semibold transition-colors
                           {{ $activeCategory === '' ? 'bg-brand-red text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                {{ __('All') }}
            </button>
            @foreach($categories as $cat)
            <button wire:click="$set('activeCategory', '{{ $cat }}')"
                    class="px-4 py-2 rounded-full text-sm font-semibold transition-colors capitalize
                           {{ $activeCategory === $cat ? 'bg-brand-red text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                {{ ucfirst($cat) }}
            </button>
            @endforeach
        </div>

        @if($items->count() > 0)
        <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
            @foreach($items as $i => $item)
            <div class="break-inside-avoid bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700 group hover:-translate-y-1 transition-transform duration-300"
                 data-aos="zoom-in" data-aos-delay="{{ min($i * 60, 400) }}">
                <div class="overflow-hidden">
                    @if($item->getImageUrl('thumb'))
                    <img src="{{ $item->getImageUrl('thumb') }}"
                         alt="{{ $item->title }}"
                         loading="lazy"
                         class="w-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @elseif($item->image)
                    <img src="{{ Storage::url($item->image) }}"
                         alt="{{ $item->title }}"
                         loading="lazy"
                         class="w-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @endif
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="font-bold text-gray-800 dark:text-white text-sm">{{ $item->title }}</h3>
                        @if($item->is_featured)
                        <span class="text-brand-yellow text-xs font-bold">★</span>
                        @endif
                    </div>
                    <span class="inline-block text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-full capitalize">
                        {{ $item->category }}
                    </span>
                    @if($item->description)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $item->description }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-20" data-aos="fade-up">
            <div class="text-6xl mb-4" aria-hidden="true">📸</div>
            <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-200 mb-2">
                {{ $activeCategory ? __('No photos in this category yet') : __('Gallery coming soon') }}
            </h2>
            <p class="text-gray-500 dark:text-gray-400">{{ __('Check our Facebook page for more photos!') }}</p>
            @php $fb = config('services.store.facebook_url'); @endphp
            @if($fb)
            <a href="{{ $fb }}" target="_blank" rel="noopener noreferrer"
               class="inline-block mt-4 bg-brand-red text-white px-6 py-2.5 rounded-full font-semibold hover:bg-red-700 transition-colors">
                {{ __('Visit Our Facebook') }}
            </a>
            @endif
        </div>
        @endif
    </div>
</div>
