<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\GetHubCoordinates;
use App\Models\Hub;
use Illuminate\Console\Command;

class Geocode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubs:geocode';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Hub::query()
            ->whereNull('coordinates')
            ->each(
                fn (Hub $hub, int $index) => GetHubCoordinates::dispatch($hub)->delay(now()->addSeconds($index))
            );

        return 0;
    }
}
