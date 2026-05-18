<?php

namespace App\Support\Hazards;

use App\Models\HazardMapPoint;
use App\Models\PotentialHazardReport;

class PublicHazardMapData
{
    public function build(): array
    {
        $hazardMarkers = PotentialHazardReport::query()
            ->with(['location'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest('mapped_at')
            ->get()
            ->map(fn (PotentialHazardReport $report) => [
                'id' => $report->id,
                'report_number' => $report->report_number,
                'title' => $report->title,
                'location' => $report->location?->name ?? '-',
                'specific_location' => $report->specific_location ?: '-',
                'hazard_type' => str_replace('-', ' ', $report->hazard_type),
                'risk_level' => $report->risk_level ?: 'sedang',
                'status' => $report->status,
                'latitude' => (float) $report->latitude,
                'longitude' => (float) $report->longitude,
                'mapped_at' => optional($report->mapped_at)->format('d M Y H:i'),
            ])
            ->values();

        $mapPointMarkers = HazardMapPoint::query()
            ->where('is_active', true)
            ->where('map_source', 'satellite')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest()
            ->get()
            ->map(fn (HazardMapPoint $point) => [
                'id' => $point->id,
                'report_number' => 'GIS-' . str_pad((string) $point->id, 4, '0', STR_PAD_LEFT),
                'title' => $point->title,
                'location' => 'Titik GIS Satgas',
                'specific_location' => $point->description ?: '-',
                'hazard_type' => $point->hazard_type ? str_replace('-', ' ', $point->hazard_type) : '-',
                'risk_level' => $point->risk_level,
                'status' => 'active',
                'latitude' => (float) $point->latitude,
                'longitude' => (float) $point->longitude,
                'mapped_at' => optional($point->created_at)->format('d M Y H:i'),
            ])
            ->values();

        $floorplanMarkers = PotentialHazardReport::query()
            ->with(['location'])
            ->whereNotNull('floorplan_x')
            ->whereNotNull('floorplan_y')
            ->latest('mapped_at')
            ->get()
            ->map(fn (PotentialHazardReport $report) => [
                'id' => $report->id,
                'report_number' => $report->report_number,
                'title' => $report->title,
                'location' => $report->location?->name ?? '-',
                'specific_location' => $report->specific_location ?: '-',
                'risk_level' => $report->risk_level ?: 'sedang',
                'status' => $report->status,
                'building_key' => 'gedung-teori',
                'floor' => 2,
                'x' => (float) $report->floorplan_x,
                'y' => (float) $report->floorplan_y,
            ])
            ->values();

        $floorplanMapPointMarkers = HazardMapPoint::query()
            ->where('is_active', true)
            ->where('map_source', 'floorplan')
            ->whereNotNull('floorplan_x')
            ->whereNotNull('floorplan_y')
            ->latest()
            ->get()
            ->map(fn (HazardMapPoint $point) => [
                'id' => $point->id,
                'report_number' => 'GIS-' . str_pad((string) $point->id, 4, '0', STR_PAD_LEFT),
                'title' => $point->title,
                'location' => 'Titik GIS Satgas',
                'specific_location' => $point->description ?: '-',
                'risk_level' => $point->risk_level,
                'status' => 'active',
                'building_key' => 'gedung-teori',
                'floor' => 2,
                'x' => (float) $point->floorplan_x,
                'y' => (float) $point->floorplan_y,
            ])
            ->values();

        $hazardMarkers = $hazardMarkers->concat($mapPointMarkers)->values();
        $floorplanMarkers = $floorplanMarkers->concat($floorplanMapPointMarkers)->values();

        return [
            'hazardMarkers' => $hazardMarkers,
            'floorplanMarkers' => $floorplanMarkers,
            'summaryCounts' => [
                'total' => $hazardMarkers->count() + $floorplanMarkers->count(),
                'tinggi' => $hazardMarkers->whereIn('risk_level', ['tinggi', 'kritis'])->count()
                    + $floorplanMarkers->whereIn('risk_level', ['tinggi', 'kritis'])->count(),
                'aktif' => $hazardMarkers->where('status', '!=', 'resolved')->count()
                    + $floorplanMarkers->where('status', '!=', 'resolved')->count(),
            ],
            'campusBuildingPolygons' => $this->campusBuildingPolygons(),
        ];
    }

    public function campusBuildingPolygons(): array
    {
        return [
            [
                'key' => 'gedung-teori',
                'name' => 'Gedung Teori & Kantor',
                'color' => '#2563eb',
                'default_floor' => 2,
                'floors' => [2, 3],
                'coordinates' => [
                    [-6.87732, 107.62028],
                    [-6.87733, 107.62114],
                    [-6.87753, 107.62114],
                    [-6.87751, 107.62029],
                ],
            ],
            [
                'key' => 'gedung-kantor',
                'name' => 'Gedung Kantor',
                'color' => '#dc2626',
                'coordinates' => [
                    [-6.87717, 107.61987],
                    [-6.87717, 107.62008],
                    [-6.87733, 107.62007],
                    [-6.87734, 107.62012],
                    [-6.87752, 107.62011],
                    [-6.87753, 107.61984],
                ],
            ],
            [
                'key' => 'gedung-mekanik',
                'name' => 'Gedung Mekanik',
                'color' => '#16a34a',
                'coordinates' => [
                    [-6.87702, 107.62026],
                    [-6.87704, 107.62118],
                    [-6.87728, 107.62117],
                    [-6.87726, 107.62024],
                ],
            ],
            [
                'key' => 'gedung-fe',
                'name' => 'Gedung FE',
                'color' => '#9333ea',
                'coordinates' => [
                    [-6.87639, 107.62084],
                    [-6.87639, 107.62127],
                    [-6.87694, 107.62127],
                    [-6.87694, 107.62084],
                ],
            ],
            [
                'key' => 'gedung-grc',
                'name' => 'Gedung GRC',
                'color' => '#f59e0b',
                'coordinates' => [
                    [-6.87675, 107.62039],
                    [-6.87676, 107.62078],
                    [-6.87693, 107.62077],
                    [-6.87693, 107.62038],
                ],
            ],
        ];
    }
}
