<?php

namespace App\Livewire\Concerns;

use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;

trait SetsSeo
{
    protected function setSeo(string $title, string $description, ?string $imageUrl = null): void
    {
        $storeName = config('services.store.name', 'Win Win Car Studio');
        $fullTitle  = $title . ' | ' . $storeName;

        SEOMeta::setTitle($fullTitle);
        SEOMeta::setDescription($description);
        SEOMeta::setCanonical(url()->current());

        OpenGraph::setTitle($fullTitle);
        OpenGraph::setDescription($description);
        OpenGraph::setUrl(url()->current());

        if ($imageUrl) {
            OpenGraph::addImage($imageUrl);
        }

        JsonLd::setTitle($fullTitle);
        JsonLd::setDescription($description);
        if ($imageUrl) {
            JsonLd::addImage($imageUrl);
        }
    }
}
