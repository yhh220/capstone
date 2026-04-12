<?php

namespace App\Livewire;

use App\Models\CarModel;
use App\Models\Product;
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
        return CarModel::distinct()->orderBy('brand')->pluck('brand')->toArray();
    }

    public function getModelsForBrandProperty(): array
    {
        if ($this->selectedBrand === '') return [];
        return CarModel::where('brand', $this->selectedBrand)
            ->distinct()
            ->orderBy('model')
            ->pluck('model')
            ->toArray();
    }

    public function getYearsForModelProperty(): array
    {
        if ($this->selectedBrand === '' || $this->selectedModel === '') return [];

        $carModel = CarModel::where('brand', $this->selectedBrand)
            ->where('model', $this->selectedModel)
            ->first();

        if (!$carModel) return [];

        return range($carModel->year_from, $carModel->year_to);
    }

    public function findParts(): void
    {
        $this->searched = true;
    }

    public function getCompatibleProductsProperty()
    {
        if (!$this->searched || $this->selectedBrand === '' || $this->selectedModel === '' || $this->selectedYear === '') {
            return collect();
        }

        $carModel = CarModel::where('brand', $this->selectedBrand)
            ->where('model', $this->selectedModel)
            ->where('year_from', '<=', $this->selectedYear)
            ->where('year_to', '>=', $this->selectedYear)
            ->first();

        if (!$carModel) return collect();

        return Product::whereHas('carModels', fn ($q) => $q->where('car_model_id', $carModel->id))
            ->where('is_active', true)
            ->with('category')
            ->get();
    }

    public function render()
    {
        return view('livewire.compatibility-checker');
    }
}
