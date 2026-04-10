<div x-data="{
        isOpen: false,
        selectedYear: '',
        selectedMake: '',
        selectedModel: '',
        isConfigured: false,
        years: ['2026', '2025', '2024', '2023', '2022', '2021', '2020'],
        makes: ['Honda', 'Toyota', 'Ford', 'BMW', 'Mercedes', 'Nissan', 'Mazda'],
        models: ['Civic', 'Accord', 'CR-V', 'City', 'HR-V'],
        
        saveGarage() {
            if (this.selectedYear && this.selectedMake && this.selectedModel) {
                this.isConfigured = true;
                this.isOpen = false;
            }
        },
        resetGarage() {
            this.isConfigured = false;
            this.selectedYear = '';
            this.selectedMake = '';
            this.selectedModel = '';
            this.isOpen = true;
        }
    }" 
    class="w-full bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm relative z-40">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <!-- Display State: When car is already selected -->
        <div x-show="isConfigured" style="display: none;" class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-green-100 dark:bg-green-900/40 p-2 rounded-lg text-green-600 dark:text-green-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Showing guaranteed parts for:</p>
                    <p class="font-bold text-gray-900 dark:text-white" x-text="`${selectedYear} ${selectedMake} ${selectedModel}`"></p>
                </div>
            </div>
            <button @click="resetGarage" class="text-sm font-medium text-brand-red hover:text-red-700 dark:hover:text-red-400 transition-colors">
                Change Vehicle
            </button>
        </div>

        <!-- Configuration State: When selecting car -->
        <div x-show="!isConfigured" class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-shrink-0">
                <div class="bg-gray-100 dark:bg-gray-800 p-2 rounded-lg text-gray-600 dark:text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white">Check Vehicle Fitment</h3>
            </div>
            
            <div class="flex-1 flex flex-col sm:flex-row gap-3 w-full max-w-3xl">
                <!-- Select Year -->
                <select x-model="selectedYear" class="flex-1 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-brand-red focus:border-brand-red block w-full p-2.5">
                    <option value="" disabled selected>1. Select Year</option>
                    <template x-for="year in years" :key="year">
                        <option :value="year" x-text="year"></option>
                    </template>
                </select>

                <!-- Select Make -->
                <select x-model="selectedMake" :disabled="!selectedYear" class="flex-1 disabled:opacity-50 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-brand-red focus:border-brand-red block w-full p-2.5">
                    <option value="" disabled selected>2. Select Make</option>
                    <template x-for="make in makes" :key="make">
                        <option :value="make" x-text="make"></option>
                    </template>
                </select>

                <!-- Select Model -->
                <select x-model="selectedModel" :disabled="!selectedMake" class="flex-1 disabled:opacity-50 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-brand-red focus:border-brand-red block w-full p-2.5">
                    <option value="" disabled selected>3. Select Model</option>
                    <template x-for="model in models" :key="model">
                        <option :value="model" x-text="model"></option>
                    </template>
                </select>

                <button @click="saveGarage" 
                        :disabled="!selectedYear || !selectedMake || !selectedModel"
                        class="disabled:opacity-50 disabled:cursor-not-allowed bg-brand-red hover:bg-red-700 text-white font-medium rounded-lg text-sm px-6 py-2.5 text-center transition-colors shadow-sm">
                    Find Parts
                </button>
            </div>
        </div>
    </div>
</div>
