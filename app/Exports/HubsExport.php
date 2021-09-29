<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Hub;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class HubsExport implements FromCollection, WithHeadings, WithMapping
{
    public function headings(): array
    {
        return [
            'Name',
            'Region',
            'Country',
            'City',
            'Date Formed',
            'Longitude',
            'Latitude',
        ];
    }

    public function collection(): Collection
    {
        return Hub::query()
            ->orderBy('name')
            ->get();
    }

    public function map($hub): array
    {
        return [
            $hub->name,
            $hub->region,
            $hub->country,
            $hub->city,
            $hub->date->toDateString(),
            $hub->coordinates->getLng(),
            $hub->coordinates->getLat(),
        ];
    }
}
