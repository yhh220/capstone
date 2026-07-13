<div>
    @php
        $storePhoneRaw = config('services.store.phone_raw');
        $storeAddress = config('services.store.address');
        $whatsAppUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode(__("Hi Win Win Car Studio! I'm interested in :product. Can you provide more details?", ['product' => $product->translated_name]));
        $mapUrl = 'https://www.google.com/maps?cid=' . config('services.store.place_cid');
    @endphp

    <div class="bg-gray-50 dark:bg-gray-900 py-3 border-b border-gray-100 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 text-sm text-gray-500 dark:text-gray-400" aria-label="{{ __('Breadcrumb') }}" role="navigation">
            <ol class="flex items-center flex-wrap gap-1" role="list">
                <li role="listitem">
                    <a href="{{ route('home') }}" wire:navigate class="hover:text-brand-red transition-colors">{{ __('Home') }}</a>
                </li>
                <li role="listitem" aria-hidden="true"><span class="mx-1">/</span></li>
                <li role="listitem">
                    <a href="{{ route('products') }}" wire:navigate class="hover:text-brand-red transition-colors">{{ __('Products') }}</a>
                </li>
                <li role="listitem" aria-hidden="true"><span class="mx-1">/</span></li>
                <li role="listitem" class="text-gray-800 dark:text-gray-200 font-medium truncate max-w-xs" aria-current="page">
                    {{ $product->translated_name }}
                </li>
            </ol>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
            <div data-aos="fade-right">
                @php
                    $gallery = $galleryMedia->map(fn ($media) => [
                        // A conversion URL is non-empty even before its file has
                        // been written. Check it first so a new gallery image
                        // always falls back to its original instead of a 404.
                        'main' => $media->hasGeneratedConversion('card') ? $media->getUrl('card') : $media->getUrl(),
                        'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl(),
                    ])->values();
                    if ($gallery->isEmpty() && $product->image) $gallery = collect([['main' => Storage::url($product->image), 'thumb' => Storage::url($product->image)]]);
                @endphp
                @if($gallery->isNotEmpty())
                    <div x-data="{ images: {{ Illuminate\Support\Js::from($gallery) }}, active: 0, previous() { this.active = (this.active - 1 + this.images.length) % this.images.length }, next() { this.active = (this.active + 1) % this.images.length } }" @keydown.left.prevent="previous()" @keydown.right.prevent="next()" tabindex="0" class="outline-none">
                        <div class="relative bg-gray-100 dark:bg-gray-700 rounded-2xl h-64 sm:h-80 md:h-96 overflow-hidden">
                            <img :src="images[active].main" :alt="'{{ e($product->translated_name) }} — {{ __('Product image') }} ' + (active + 1)" class="w-full h-full object-cover rounded-2xl" fetchpriority="high">
                            @if($gallery->count() > 1)
                                <button type="button" @click="previous()" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/55 text-white hover:bg-black/75 focus:outline-none focus:ring-2 focus:ring-white" aria-label="{{ __('Previous image') }}">‹</button>
                                <button type="button" @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/55 text-white hover:bg-black/75 focus:outline-none focus:ring-2 focus:ring-white" aria-label="{{ __('Next image') }}">›</button>
                                <span class="absolute bottom-3 right-3 rounded-full bg-black/55 px-2.5 py-1 text-xs font-bold text-white" x-text="(active + 1) + ' / ' + images.length" aria-live="polite"></span>
                            @endif
                        </div>
                        @if($gallery->count() > 1)
                            <div class="mt-3 flex gap-2 overflow-x-auto pb-1 snap-x" aria-label="{{ __('Product images') }}">
                                <template x-for="(image, index) in images" :key="image.main">
                                    <button type="button" @click="active = index" :aria-label="'{{ __('Show image') }} ' + (index + 1)" :aria-current="active === index ? 'true' : 'false'" class="shrink-0 snap-start rounded-lg overflow-hidden border-2 focus:outline-none focus:ring-2 focus:ring-brand-red" :class="active === index ? 'border-brand-red' : 'border-transparent opacity-70 hover:opacity-100'">
                                        <img :src="image.thumb" alt="" class="h-16 w-20 object-cover sm:h-20 sm:w-24">
                                    </button>
                                </template>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl h-64 sm:h-80 md:h-96 flex items-center justify-center text-gray-300 dark:text-gray-600" aria-label="{{ __('No product image available') }}">
                        <svg class="w-24 h-24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="m7.5 4.27 9 5.15"></path><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path></svg>
                    </div>
                @endif
            </div>

            <div data-aos="fade-left" data-aos-delay="80">
                <div class="text-sm text-brand-red font-semibold mb-2">
                    {{ __($product->category?->name ?? 'Accessories') }}
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-brand-black dark:text-white mb-4">
                    {{ $product->translated_name }}
                </h1>
                @if($product->brand)
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    {{ __('Brand') }}: <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $product->brand }}</span>
                </div>
                @endif

                {{-- Price Section (when shopping is enabled) --}}
                @if($shoppingEnabled)
                <div class="mb-6">
                    <div class="flex items-end gap-3 mb-2">
                        @if($product->sale_price && $product->sale_price < $product->price)
                            <span class="text-3xl font-black text-brand-red">RM {{ number_format($product->sale_price, 2) }}</span>
                            <span class="text-xl text-gray-400 line-through">RM {{ number_format($product->price, 2) }}</span>
                            <span class="bg-brand-red-solid text-white text-xs font-bold px-2 py-1 rounded-full">
                                {{ __('SAVE') }} {{ round((1 - $product->sale_price / $product->price) * 100) }}%
                            </span>
                        @else
                            <span class="text-3xl font-black text-brand-red">RM {{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                    {{-- Stock / backorder status (out-of-stock items can be backordered) --}}
                    <div class="text-sm font-semibold flex items-center gap-1.5">
                        @if($product->stock > 5)
                            <span class="text-green-600 dark:text-green-400 inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-500" aria-hidden="true"></span>{{ __('In Stock') }}</span>
                        @elseif($product->stock > 0)
                            <span class="text-amber-600 dark:text-amber-400 inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500" aria-hidden="true"></span>{{ __('Only :n left', ['n' => $product->stock]) }}</span>
                        @else
                            <span class="text-amber-600 dark:text-amber-400 inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500" aria-hidden="true"></span>{{ __('On backorder · ships in ~:days days', ['days' => (int) setting('BACKORDER_DAYS', 7)]) }}</span>
                        @endif
                    </div>
                </div>

                {{-- Quantity Selector + Add to Cart (out-of-stock items can be backordered) --}}
                <div class="flex flex-col sm:flex-row gap-3 mb-6">
                    <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-full px-2">
                        <x-tooltip text="{{ __('Decrease') }}">
                        <button wire:click="decrementQuantity"
                                class="w-10 h-10 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-bold text-lg"
                                aria-label="{{ __('Decrease quantity') }}">
                            −
                        </button>
                        </x-tooltip>
                        <span class="w-12 text-center font-bold text-gray-800 dark:text-gray-200 tabular-nums"
                              wire:key="qty-{{ $quantity }}">
                            {{ $quantity }}
                        </span>
                        <x-tooltip text="{{ __('Increase') }}">
                        <button wire:click="incrementQuantity"
                                class="w-10 h-10 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-bold text-lg"
                                aria-label="{{ __('Increase quantity') }}">
                            +
                        </button>
                        </x-tooltip>
                    </div>
                    <button wire:click="addToCart"
                            class="group relative overflow-hidden flex-1 bg-brand-red-solid text-white py-3 px-8 rounded-full font-bold text-lg hover:bg-red-700 transition-all duration-300 shadow-lg hover:shadow-[0_4px_15px_rgba(232,100,96,0.4)] hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2">
                        <span class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:translate-x-0 group-active:translate-x-0 transition-transform duration-500 ease-out"></span>
                        <svg class="w-5 h-5 relative z-10 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                        <span class="relative z-10">{{ __('Add to Cart') }}</span>
                    </button>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mb-8">
                    <a href="{{ $whatsAppUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="btn btn-ghost btn-lg btn-pill flex-1" style="border:2px solid rgb(var(--brand-red-rgb));">
                        {{ __('Ask on WhatsApp') }}
                    </a>
                    <a href="{{ $mapUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="btn btn-secondary btn-lg btn-pill flex-1">
                        {{ __('Visit the showroom') }}
                    </a>
                </div>
                @else
                {{-- Shopping disabled: show enquiry notice --}}
                <div class="flex items-start gap-2 bg-red-50 dark:bg-red-900/20 text-brand-red px-4 py-2.5 rounded-xl text-sm font-semibold mb-6">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"></path></svg>
                    <span>{{ __('Enquire on WhatsApp or visit the showroom for pricing and compatibility.') }}</span>
                </div>

                @if($product->translated_short_description)
                <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">{{ $product->translated_short_description }}</p>
                @endif

                <div class="flex flex-col sm:flex-row gap-3 mb-8">
                    <a href="{{ $whatsAppUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="btn btn-whatsapp btn-lg btn-pill btn-shine flex-1">
                        <svg class="icon-md" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        {{ __('Ask on WhatsApp') }}
                    </a>
                    <a href="{{ $mapUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="btn btn-secondary btn-lg btn-pill flex-1">
                        <svg class="icon-md icon-arrow" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        {{ __('Visit the showroom') }}
                    </a>
                </div>
                @endif

                <div class="border-t border-gray-100 dark:border-gray-700 pt-6 space-y-2 text-sm text-gray-500 dark:text-gray-400">
                    @if($product->sku)
                    <div>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">SKU:</span>
                        {{ $product->sku }}
                    </div>
                    @endif
                    @if($product->category)
                    <div>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Category') }}:</span>
                        {{ __($product->category->name) }}
                    </div>
                    @endif
                    @if(!empty($product->compatible_vehicles))
                    <div>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Compatible Vehicles') }}:</span>
                        {{ implode(', ', $product->compatible_vehicles) }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($product->has_3d && $product->model_url)
        <div class="mt-12 bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700" data-aos="fade-up">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h2 class="text-2xl font-black text-brand-black dark:text-white">{{ __('3D Viewer') }}</h2>
                <span class="text-xs uppercase tracking-widest text-gray-400">{{ __('Mount Point Ready') }}</span>
            </div>
            <div id="3d-mount-product"
                 data-model-url="{{ $product->model_url }}"
                 data-product-name="{{ $product->name }}"
                 class="min-h-[320px] rounded-2xl bg-gray-100 dark:bg-gray-900 border border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center text-center px-6">
                <div>
                    <div class="text-5xl mb-3">3D</div>
                    <div class="font-semibold text-gray-800 dark:text-gray-200">{{ __('Interactive product viewer will mount here.') }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Fallback content remains visible until the viewer script is integrated.') }}</div>
                </div>
            </div>
        </div>
        @endif

        @if($translatedDescription)
        <div class="mt-12 bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700" data-aos="fade-up">
            <h2 class="text-2xl font-black text-brand-black dark:text-white mb-4">{{ __('Product Overview') }}</h2>
            <div class="text-gray-600 dark:text-gray-400 leading-relaxed prose dark:prose-invert max-w-none">
                {!! nl2br(e($translatedDescription)) !!}
            </div>
        </div>
        @endif

        @if(!empty($product->specs))
        <div class="mt-12 bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700" data-aos="fade-up">
            <h2 class="text-2xl font-black text-brand-black dark:text-white mb-4">{{ __('Specifications') }}</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                @foreach($product->specs as $label => $value)
                <div class="rounded-xl bg-gray-50 dark:bg-gray-900 px-4 py-3">
                    <div class="text-xs uppercase tracking-widest text-gray-400 mb-1">{{ $label }}</div>
                    <div class="font-semibold text-gray-800 dark:text-gray-100">{{ is_array($value) ? implode(', ', $value) : $value }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <section id="reviews" class="mt-12 scroll-mt-24" aria-labelledby="reviews-heading">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex flex-col gap-4 border-b border-gray-100 pb-5 dark:border-gray-700 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 id="reviews-heading" class="text-2xl font-black text-brand-black dark:text-white">{{ __('Customer Reviews') }}</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Total reviews: :count', ['count' => $reviewCount]) }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 sm:justify-end">
                        <div class="flex items-center gap-2" aria-label="{{ __('Average rating') }}">
                            <span class="text-2xl font-black text-brand-red">{{ $reviewAverage ? number_format($reviewAverage, 1) : '—' }}</span>
                            <span class="text-amber-400 tracking-tight" aria-hidden="true">★★★★★</span>
                            <span class="sr-only">{{ $reviewAverage ? __(':rating out of 5 stars', ['rating' => number_format($reviewAverage, 1)]) : __('No reviews yet') }}</span>
                        </div>
                        @if($reviewCount > 0)
                        <label class="flex flex-wrap items-center gap-2 text-sm font-semibold text-gray-600 dark:text-gray-300" for="review-sort">
                            <span>{{ __('Sort reviews') }}</span>
                            <select id="review-sort" wire:model.live="reviewSort" class="rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-sm font-medium text-gray-700 focus:border-brand-red focus:ring-brand-red dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="newest">{{ __('Newest') }}</option>
                                <option value="highest">{{ __('Highest rating') }}</option>
                                <option value="lowest">{{ __('Lowest rating') }}</option>
                            </select>
                        </label>
                        @endif
                    </div>
                </div>

                @if(session('review-success'))
                    <div role="status" class="mt-5 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/40 px-4 py-3 text-sm text-green-700 dark:text-green-300">{{ session('review-success') }}</div>
                @endif

                <div class="mt-6 space-y-5">
                    @forelse($reviews as $review)
                        <article class="border-b border-gray-100 dark:border-gray-700 last:border-0 pb-5 last:pb-0">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-bold text-gray-800 dark:text-white">{{ $review->user?->name ?? __('Verified customer') }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $review->created_at->locale(app()->getLocale())->translatedFormat('d M Y') }} · {{ __('Verified purchase') }}</p>
                                </div>
                                <span class="text-amber-400 text-sm tracking-tight" aria-label="{{ __(':rating out of 5 stars', ['rating' => $review->rating]) }}">{{ str_repeat('★', $review->rating) }}<span class="text-gray-300 dark:text-gray-600">{{ str_repeat('★', 5 - $review->rating) }}</span></span>
                            </div>
                            <p class="mt-3 text-sm leading-relaxed text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $review->comment }}</p>
                        </article>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No reviews yet. Be the first verified customer to share your experience.') }}</p>
                    @endforelse
                </div>

                @if($reviews->hasPages())
                <nav class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-5 text-sm dark:border-gray-700 sm:flex-nowrap" aria-label="{{ __('Customer Reviews') }}">
                    @if($reviews->onFirstPage())
                        <span class="rounded-lg border border-gray-200 px-3 py-2 font-bold text-gray-400 dark:border-gray-700">{{ __('Previous') }}</span>
                    @else
                        <button type="button" wire:click="previousPage('reviewsPage')" class="rounded-lg border border-gray-300 px-3 py-2 font-bold text-gray-700 transition-colors hover:border-brand-red hover:text-brand-red focus:outline-none focus:ring-2 focus:ring-brand-red dark:border-gray-600 dark:text-gray-200">{{ __('Previous') }}</button>
                    @endif
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('Page :current of :last', ['current' => $reviews->currentPage(), 'last' => $reviews->lastPage()]) }}</span>
                    @if($reviews->hasMorePages())
                        <button type="button" wire:click="nextPage('reviewsPage')" class="rounded-lg border border-gray-300 px-3 py-2 font-bold text-gray-700 transition-colors hover:border-brand-red hover:text-brand-red focus:outline-none focus:ring-2 focus:ring-brand-red dark:border-gray-600 dark:text-gray-200">{{ __('Next') }}</button>
                    @else
                        <span class="rounded-lg border border-gray-200 px-3 py-2 font-bold text-gray-400 dark:border-gray-700">{{ __('Next') }}</span>
                    @endif
                </nav>
                @endif

                <div class="mt-7 pt-6 border-t border-gray-100 dark:border-gray-700">
                    @guest
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Bought this product? Sign in after your order is completed to leave a verified review.') }} <a wire:navigate href="{{ route('login') }}" class="font-bold text-brand-red hover:underline">{{ __('Sign in') }}</a></p>
                    @else
                        @if($canReview)
                            <form wire:submit="submitReview" class="space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-5">
                                    <label class="font-bold text-gray-800 dark:text-white" for="review-rating">{{ $myReview ? __('Update your review') : __('Write a review') }}</label>
                                    <select id="review-rating" wire:model="reviewRating" class="rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:border-brand-red focus:ring-brand-red">
                                        @for($star = 5; $star >= 1; $star--) <option value="{{ $star }}">{{ $star }} {{ $star === 1 ? __('star') : __('stars') }}</option> @endfor
                                    </select>
                                </div>
                                <div>
                                    <label for="review-comment" class="sr-only">{{ __('Your review') }}</label>
                                    <textarea id="review-comment" wire:model="reviewComment" rows="4" maxlength="1000" placeholder="{{ __('Tell other customers about your experience…') }}" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 text-sm focus:border-brand-red focus:ring-brand-red"></textarea>
                                    @error('reviewComment') <span role="alert" class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <button type="submit" wire:loading.attr="disabled" class="rounded-full bg-brand-red-solid px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700 disabled:opacity-60">{{ $myReview ? __('Update review') : __('Submit review') }}</button>
                            </form>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Only customers with a completed order can review this product.') }}</p>
                        @endif
                    @endguest
                </div>
            </div>
        </section>

        @if($related->count() > 0)
        <div class="mt-12" aria-labelledby="related-heading">
            <h2 id="related-heading" class="text-2xl sm:text-3xl font-black text-brand-black dark:text-white mb-8" data-aos="fade-up">
                {{ __('More Products to Explore') }}
            </h2>
            <div class="grid grid-cols-1 min-[400px]:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($related as $i => $item)
                <a href="{{ route('product.show', $item->slug) }}" wire:navigate
                   class="group bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-gray-100 dark:border-gray-700"
                   data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                    <div class="bg-gray-100 dark:bg-gray-700 h-40 flex items-center justify-center overflow-hidden">
                        @if($item->getImageUrl('thumb'))
                        <img src="{{ $item->getImageUrl('thumb') }}"
                             alt="{{ $item->translated_name }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @elseif($item->image)
                        <img src="{{ Storage::url($item->image) }}"
                             alt="{{ $item->translated_name }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-800 transition-all duration-500 text-gray-300 dark:text-gray-600 group-hover:rotate-[15deg] group-hover:scale-125 group-hover:text-brand-yellow group-hover:-translate-y-1" aria-hidden="true">
                            <svg class="w-12 h-12 drop-shadow-sm transition-all duration-500" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m7.5 4.27 9 5.15"></path><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path></svg>
                        </div>
                        @endif
                    </div>
                    <div class="p-3">
                        <div class="font-semibold text-sm text-gray-800 dark:text-gray-200 group-hover:text-brand-red transition-colors line-clamp-2">
                            {{ $item->translated_name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('View details and enquire') }}</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
