<?php

declare(strict_types=1);

namespace App\Models;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    use SpatialTrait;

    public $timestamps = false;

    public $casts = [
        'date' => 'date',
    ];

    protected $spatialFields = [
        'coordinates',
    ];

    public function toFeature(): array
    {
        return [
            'type'     => 'Feature',
            'geometry' => $this->coordinates,
            'properties' => [
                'region'  => $this->region,
                'country' => $this->country,
                'city'    => $this->city,
                'date'    => $this->date->toDateString(),
            ],
        ];
    }
}
