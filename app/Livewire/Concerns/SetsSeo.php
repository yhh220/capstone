<?php

namespace App\Livewire\Concerns;

use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;

trait SetsSeo
{
    protected function setSeo(string $title, string $description, ?string $imageUrl = null): void
    {
        $storeName = config('services.store.seo_name', config('services.store.name'));
        $isBrand   = $title === $storeName || $title === config('services.store.name');
        $fullTitle = $isBrand ? $storeName : $title . ' | ' . $storeName;

        // seotools appends "separator + defaults.title" itself, so pass the bare
        // page title (or suppress the default on brand-only pages like home).
        if ($isBrand) {
            SEOMeta::setTitle($storeName, false);
        } else {
            SEOMeta::setTitle($title);
        }
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
