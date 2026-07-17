<?php

namespace App\Livewire\Concerns;

use App\Services\Booking\BookingService;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;

trait SetsSeo
{
    protected function setSeo(string $title, string $description, ?string $imageUrl = null): void
    {
        $this->applyBusinessSchema();

        $storeName = config('services.store.seo_name', config('services.store.name'));
        $isBrand = $title === $storeName || $title === config('services.store.name');
        $fullTitle = $isBrand ? $storeName : $title.' | '.$storeName;

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

    /**
     * Enrich the AutoPartsStore JSON-LD with real local-business details —
     * address, phone, map coordinates, socials, and opening hours — so Google
     * can surface the shop's location and hours in local search and Maps,
     * rather than only a bare business name. All values come from the store
     * config (config/services.php) with no hardcoding here.
     */
    private function applyBusinessSchema(): void
    {
        $store = config('services.store');

        JsonLd::addValue('telephone', $store['phone_display'] ?? null);
        JsonLd::addValue('email', $store['email'] ?? null);
        JsonLd::addValue('priceRange', 'RM');

        JsonLd::addValue('address', array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $store['address'] ?? null,
            'addressLocality' => $store['city'] ?? null,
            'addressRegion' => $store['state'] ?? null,
            'postalCode' => $store['postcode'] ?? null,
            'addressCountry' => $store['country'] ?? null,
        ]));

        if (isset($store['lat'], $store['lng'])) {
            JsonLd::addValue('geo', [
                '@type' => 'GeoCoordinates',
                'latitude' => $store['lat'],
                'longitude' => $store['lng'],
            ]);
        }

        if (! empty($store['facebook_url'])) {
            JsonLd::addValue('sameAs', [$store['facebook_url']]);
        }

        if ($hours = $this->openingHoursSpecification()) {
            JsonLd::addValue('openingHoursSpecification', $hours);
        }
    }

    /**
     * Build a schema.org OpeningHoursSpecification from the same business-hours
     * settings the booking calendar uses (open days = all weekdays minus the
     * configured closed days), so the structured hours can never drift from what
     * the site actually offers for appointments.
     */
    private function openingHoursSpecification(): ?array
    {
        $start = (string) setting('BUSINESS_HOURS_START', '09:00');
        $end = (string) setting('BUSINESS_HOURS_END', '18:00');

        $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $closed = app(BookingService::class)->closedWeekdays();
        $openDays = array_values(array_diff([0, 1, 2, 3, 4, 5, 6], $closed));

        if ($openDays === []) {
            return null;
        }

        return [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => array_map(fn (int $d): string => $dayNames[$d], $openDays),
            'opens' => $start,
            'closes' => $end,
        ];
    }
}
