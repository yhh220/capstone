<div class="w-full bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm relative z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <div class="flex flex-col md:flex-row items-center gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <div class="bg-gray-100 dark:bg-gray-800 p-2 rounded-lg text-gray-600 dark:text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white text-sm whitespace-nowrap">{{ __('Check Vehicle Fitment') }}</h3>
            </div>

            <div class="flex-1 flex flex-col sm:flex-row gap-2 w-full">
                {{-- Brand --}}
                <select wire:model.live="selectedBrand"
                        class="flex-1 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-brand-red focus:border-brand-red block w-full p-2.5">
                    <option value="">{{ __('1. Brand') }}</option>
                    @foreach($this->brands as $brand)
                    <option value="{{ $brand }}">{{ $brand }}</option>
                    @endforeach
                </select>

                {{-- Model --}}
                <select wire:model.live="selectedModel"
                        @if(empty($selectedBrand)) disabled @endif
                        class="flex-1 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-brand-red focus:border-brand-red block w-full p-2.5 disabled:opacity-50">
                    <option value="">{{ __('2. Model') }}</option>
                    @foreach($this->modelsForBrand as $model)
                    <option value="{{ $model }}">{{ $model }}</option>
                    @endforeach
                </select>

                {{-- Year --}}
                <select wire:model.live="selectedYear"
                        @if(empty($selectedModel)) disabled @endif
                        class="flex-1 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-brand-red focus:border-brand-red block w-full p-2.5 disabled:opacity-50">
                    <option value="">{{ __('3. Year') }}</option>
                    @foreach($this->yearsForModel as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>

                <button wire:click="findParts"
                        @if(empty($selectedYear)) disabled @endif
                        class="bg-brand-red hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold rounded-lg text-sm px-6 py-2.5 transition-colors whitespace-nowrap">
                    {{ __('Find Parts') }}
                </button>
            </div>
        </div>

        {{-- Results --}}
        @if($searched)
        @if($this->compatibleProducts->count() > 0)
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
            <p class="text-sm text-green-600 dark:text-green-400 font-semibold mb-3">
                ✅ {{ $this->compatibleProducts->count() }} {{ __('compatible products found for') }} {{ $selectedYear }} {{ $selectedBrand }} {{ $selectedModel }}
            </p>
            <div class="flex flex-wrap gap-2">
                @foreach($this->compatibleProducts as $product)
                <a href="{{ route('product.show', $product->slug) }}"
                   class="inline-flex items-center gap-1.5 bg-gray-100 dark:bg-gray-800 hover:bg-brand-red hover:text-white text-gray-700 dark:text-gray-300 text-xs font-medium px-3 py-1.5 rounded-full transition-colors">
                    {{ $product->name }}
                </a>
                @endforeach
            </div>
        </div>
        @else
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('No specifically tagged products found for this vehicle. Most accessories are universal — WhatsApp us to confirm fitment.') }}
                <a href="{{ 'https://wa.me/' . config('services.store.phone_raw') }}" target="_blank" rel="noopener noreferrer" class="text-brand-red font-semibold hover:underline ml-1">{{ __('WhatsApp us') }}</a>
            </p>
        </div>
        @endif
        @endif
    </div>
</div>
