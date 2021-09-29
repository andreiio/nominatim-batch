<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Hub;
use GuzzleHttp\Utils;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class HubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Hub::truncate();

        $hubs = collect(Utils::jsonDecode(Storage::get('hubs.json'), true))
            ->map(function ($hub) {
                $hub['coordinates'] = null;

                return $hub;
            });

        Hub::insert($hubs->all());
    }
}
