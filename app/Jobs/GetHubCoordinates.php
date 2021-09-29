<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Hub;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\RateLimitedMiddleware\RateLimited;

class GetHubCoordinates implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public Hub $hub;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Hub $hub)
    {
        $this->hub = $hub;
    }

    public function middleware()
    {
        return [
            // (new RateLimited())
            //     ->allow(1)
            //     ->everySeconds(2),
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->hub->coordinates !== null) {
            Log::warning("Skipping hub in {$this->hub->city}");

            return;
        }

        $this->hub->coordinates = $this->getCoordinates($this->hub->city, $this->hub->country);

        $this->hub->save();
    }

    protected function getCoordinates(string $city, string $country): ?Point
    {
        return Cache::rememberForever(
            Str::lower("{$city}-{$country}"),
            function () use ($city, $country) {
                $response = Http::withHeaders(['User-Agent' => 'Nominatim city finder'])
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'city'    => $city,
                        'country' => $country,
                        'format'  => 'json',
                    ]);

                if ($response->failed()) {
                    return null;
                }

                $json = $response->json();

                if (! \count($json)) {
                    return null;
                }

                $place = $json[0];

                return new Point($place['lat'], $place['lon']);
            }
        );
    }
}
