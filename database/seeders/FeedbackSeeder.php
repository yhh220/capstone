<?php

namespace Database\Seeders;

use App\Models\Feedback;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            [
                'name'       => 'AZ',
                'location'   => 'Local Guide · 18 reviews',
                'message'    => 'Good explanation from Jacky! I just trade in my old android player with him and get more discount. Less than 30mins installed, most murah compare to other shops 😍 and 1 more thing the player so smooth not lagging at all. Definitely will come back!',
                'rating'     => 5,
                'sort_order' => 1,
            ],
            [
                'name'       => 'YC',
                'location'   => '5 reviews',
                'message'    => 'I whatsapp the boss before come, prompt respond. I came for dash cam and carplay installation, very fast and excellent service! Recommend 👍👍👍. They have tinted too.',
                'rating'     => 5,
                'sort_order' => 2,
            ],
            [
                'name'       => 'Muhammad Zulhafizie MZ',
                'location'   => '4 reviews',
                'message'    => 'Good service, friendly staff, and reasonable prices. The job was done quickly and professionally. Highly recommended!',
                'rating'     => 5,
                'sort_order' => 3,
            ],
            [
                'name'       => 'QuoRaF KmY',
                'location'   => 'Local Guide · 142 reviews',
                'message'    => 'Jacky was great in explaining things and he totally knows the audio world from the back of his hands. Just by listening to the audio alone he could tell what the problem is. Good job Jacky. Thanks also for fixing my reverse sensor. Very recommended!',
                'rating'     => 5,
                'sort_order' => 4,
            ],
            [
                'name'       => 'Azrin Azman',
                'location'   => '6 reviews',
                'message'    => 'Service laju, boleh walk in terus. Price reasonable and affordable!!',
                'rating'     => 5,
                'sort_order' => 5,
            ],
            [
                'name'       => 'hanis muhamad',
                'location'   => 'Local Guide · 106 reviews',
                'message'    => 'Good package, good explanation by the boss. Highly recommended.',
                'rating'     => 5,
                'sort_order' => 6,
            ],
            [
                'name'       => 'dina',
                'location'   => '1 review',
                'message'    => 'Really good customer service. Well explained and give us good deal.',
                'rating'     => 5,
                'sort_order' => 7,
            ],
            [
                'name'       => 'Haiqal Zulkurnain',
                'location'   => '5 reviews',
                'message'    => 'Came in for a dashcam installation and it was done very quickly, and I got some friendly chatter and tips along the way. I recommend them!',
                'rating'     => 5,
                'sort_order' => 8,
            ],
            [
                'name'       => 'Iwana Najwa',
                'location'   => '4 reviews',
                'message'    => 'Excellent service! I came to change my car radio and speakers, and the boss was really friendly and helpful. The work was neat and I\'m very satisfied. Highly recommended! 👍🏻👍🏻👍🏻',
                'rating'     => 5,
                'sort_order' => 9,
            ],
            [
                'name'       => 'Noor Sharazain Ahmad Noordin',
                'location'   => '1 review',
                'message'    => 'Experienced owner with a team of polite and careful workers. Sent two cars to install Android player and fix some wiring issues. Excellent work with reasonable price. Highly recommended!',
                'rating'     => 5,
                'sort_order' => 10,
            ],
            [
                // sort_order 0 → shown as the large hero pull-quote on the homepage.
                // Concise, on-brand (tinting), and from a high-credibility reviewer.
                'name'       => 'Sheila Abd Majid',
                'location'   => 'Local Guide · 292 reviews',
                'message'    => 'Highly recommended for tinted and other services. The staff and boss are really friendly and helpful.',
                'rating'     => 5,
                'sort_order' => 0,
            ],
            [
                'name'       => 'Aqmal',
                'location'   => '1 review',
                'message'    => 'Good service and price are within my budget.',
                'rating'     => 5,
                'sort_order' => 12,
            ],
            [
                'name'       => 'shahir',
                'location'   => '2 reviews',
                'message'    => 'Brought a couple of cars to this shop, the work done is very well and the price is inexpensive and reasonable. Will continue bringing to this shop. Highly recommended :)',
                'rating'     => 5,
                'sort_order' => 13,
            ],
            [
                'name'       => 'Ned Neder',
                'location'   => '7 reviews',
                'message'    => 'Installed mohawk speaker. Sound tiptop. Boss friendly. Staff install professionally. Reasonable price 👍👍 Thank you.',
                'rating'     => 5,
                'sort_order' => 14,
            ],
            [
                'name'       => 'MUHAMMAD HARITH ASYRAF',
                'location'   => '3 reviews',
                'message'    => 'Nice shop. Friendly staff. Nice services.',
                'rating'     => 5,
                'sort_order' => 15,
            ],
            [
                'name'       => 'Azzah Izni bt Kamaruddin',
                'location'   => '4 reviews',
                'message'    => 'Installed dashcam here. Good and fast service from boss. Reasonable price. Very recommended!!',
                'rating'     => 5,
                'sort_order' => 16,
            ],
            [
                'name'       => 'nor adibah',
                'location'   => '2 reviews',
                'message'    => 'Good service & advice support.',
                'rating'     => 5,
                'sort_order' => 17,
            ],
            [
                'name'       => 'Bayuu',
                'location'   => '6 reviews',
                'message'    => 'Friendly staff and boss with reasonable prices, definitely would go again if need any help with car accessories installation.',
                'rating'     => 5,
                'sort_order' => 18,
            ],
            [
                'name'       => 'Dnesh J.',
                'location'   => 'Local Guide · 30 reviews',
                'message'    => 'Genuine and friendly owner! Went in to fix a car alarm issue I was facing and they helped me fix it on the spot at a reasonable price! Recommended to go here for your auto accessories if you\'re around the area :)',
                'rating'     => 5,
                'sort_order' => 19,
            ],
            [
                'name'       => 'Nurul Syazwina',
                'location'   => '2 reviews',
                'message'    => 'Good service and very friendly welcoming the guest! Recommended 👍🏼',
                'rating'     => 5,
                'sort_order' => 20,
            ],
            [
                'name'       => 'Mohammed Sani Abdullah',
                'location'   => 'Local Guide · 39 reviews',
                'message'    => 'The boss there very experienced and honest. Will patiently trouble shoot audio system problem which many shops failed to detect. Also will list you the available options for you to choose according to your budget. Price is reasonable too. Highly recommended.',
                'rating'     => 5,
                'sort_order' => 21,
            ],
        ];

        // Idempotent: upsert each real review by name so re-running never
        // duplicates them. Location is intentionally blanked — we don't show
        // the Google "Local Guide / N reviews" badge under testimonials.
        foreach ($reviews as $review) {
            $review['location'] = '';
            Feedback::updateOrCreate(
                ['name' => $review['name']],
                array_merge($review, ['is_active' => true]),
            );
        }

        // Authoritative: drop anything not in the curated list (e.g. old
        // placeholder/AI-generated test entries) so the table holds only these
        // real Google reviews.
        Feedback::whereNotIn('name', array_column($reviews, 'name'))->forceDelete();
    }
}
