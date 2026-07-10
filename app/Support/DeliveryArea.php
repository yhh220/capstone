<?php

namespace App\Support;

/**
 * Where the shop actually delivers. East Malaysia (Sabah, Sarawak, Labuan) is
 * deliberately absent — the business does not ship there — so every
 * customer-facing state picker and the checkout validation read this one list.
 * The admin CustomerResource keeps the full 16-state list on purpose: staff
 * must still be able to view/edit legacy customers with East-Malaysia
 * addresses.
 */
class DeliveryArea
{
    public const STATES = [
        'Selangor', 'Kuala Lumpur', 'Johor', 'Penang', 'Perak', 'Pahang',
        'Negeri Sembilan', 'Melaka', 'Kedah', 'Kelantan', 'Terengganu',
        'Perlis', 'Putrajaya',
    ];
}
