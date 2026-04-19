<?php

namespace App\Livewire;

use App\Models\CarModel;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class CompatibilityChecker extends Component
{
    public string $selectedBrand = '';
    public string $selectedModel = '';
    public string $selectedYear  = '';
    public bool   $searched      = false;

    public function updatedSelectedBrand(): void
    {
        $this->selectedModel = '';
        $this->selectedYear  = '';
        $this->searched      = false;
    }

    public function updatedSelectedModel(): void
    {
        $this->selectedYear = '';
        $this->searched     = false;
    }

    public function getBrandsProperty(): array
    {
        if (!Schema::hasTable('car_models')) {
            return [];
        }

        return CarModel::distinct()->orderBy('brand')->pluck('brand')->toArray();
    }

    public function getModelsForBrandProperty(): array
    {
        if ($this->selectedBrand === '' || !Schema::hasTable('car_models')) return [];

        return CarModel::where('brand', $this->selectedBrand)
            ->distinct()
            ->orderBy('model')
            ->pluck('model')
            ->toArray();
    }

    public function getYearsForModelProperty(): array
    {
        if ($this->selectedBrand === '' || $this->selectedModel === '' || !Schema::hasTable('car_models')) return [];

        // Aggregate ranges across multiple generations (e.g. Civic FD 2006-2011 + FC 2016-2021)
        $ranges = CarModel::where('brand', $this->selectedBrand)
            ->where('model', $this->selectedModel)
            ->get(['year_from', 'year_to']);

        $years = [];
        foreach ($ranges as $range) {
            for ($y = $range->year_from; $y <= $range->year_to; $y++) {
                $years[$y] = $y;
            }
        }

        krsort($years);
        return array_values($years);
    }

    public function findParts(): void
    {
        $this->searched = true;
    }

    public function getCompatibleProductsProperty()
    {
        if (
            !$this->searched
            || $this->selectedBrand === ''
            || $this->selectedModel === ''
            || $this->selectedYear === ''
            || !Schema::hasTable('car_models')
        ) {
            return collect();
        }

        // Find all matching CarModel rows (a year may sit in multiple generations)
        $carModelIds = CarModel::where('brand', $this->selectedBrand)
            ->where('model', $this->selectedModel)
            ->where('year_from', '<=', $this->selectedYear)
            ->where('year_to', '>=', $this->selectedYear)
            ->pluck('id');

        if ($carModelIds->isEmpty()) return collect();

        return Product::whereHas('carModels', fn ($q) => $q->whereIn('car_model_id', $carModelIds))
            ->where('is_active', true)
            ->with('category')
            ->get();
    }

    public function render()
    {
        return view('livewire.compatibility-checker');
    }
}
