@extends('satgas.layouts.app')

@section('title', 'GIS Insiden')
@section('hero_eyebrow', 'GIS Insiden')
@section('hero_title', 'Peta satelit kejadian kecelakaan')
@section('hero_description', 'Pantau titik koordinat insiden, saring kejadian, dan ekspor laporan GIS dalam format Excel.')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
    @php
        $statusBadge = fn (string $status): string => match ($status) {
            'submitted' => 'bg-amber-100 text-amber-800',
            'verified' => 'bg-emerald-100 text-emerald-800',
            'investigating' => 'bg-sky-100 text-sky-800',
            'resolved' => 'bg-indigo-100 text-indigo-700',
            'closed' => 'bg-slate-200 text-slate-700',
            'rejected' => 'bg-rose-100 text-rose-700',
            default => 'bg-slate-100 text-slate-600',
        };
        $severityLabel = [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'critical' => 'Kritis',
        ];
        $filterQuery = collect($filters)->filter(fn ($value) => filled($value))->all();
    @endphp

    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-3">
            <article class="rounded-[1.5rem] bg-white px-5 py-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Insiden Terpetakan</p>
                <p class="mt-3 text-4xl font-bold text-slate-900">{{ $summary['total'] }}</p>
            </article>
            <article class="rounded-[1.5rem] bg-white px-5 py-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Di Dalam Polman</p>
                <p class="mt-3 text-4xl font-bold text-emerald-700">{{ $summary['inside'] }}</p>
            </article>
            <article class="rounded-[1.5rem] bg-white px-5 py-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Diluar Polman</p>
                <p class="mt-3 text-4xl font-bold text-rose-700">{{ $summary['outside'] }}</p>
            </article>
        </div>

        <form action="{{ route('satgas.incidents.gis') }}" method="GET" class="rounded-[2rem] bg-white p-5 shadow-sm ring-1 ring-slate-200 lg:p-6">
            <div class="grid gap-4 lg:grid-cols-4">
                <label class="block lg:col-span-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nama / Laporan</span>
                    <input name="q" type="search" value="{{ $filters['q'] }}" placeholder="Cari pelapor, korban, judul, atau nomor laporan"
                        class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                </label>

                <label class="block">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Dari Tanggal</span>
                    <input name="date_from" type="date" value="{{ $filters['date_from'] }}"
                        class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                </label>

                <label class="block">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Sampai Tanggal</span>
                    <input name="date_to" type="date" value="{{ $filters['date_to'] }}"
                        class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                </label>

                <label class="block">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Bulan</span>
                    <select name="month" class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                        <option value="">Semua Bulan</option>
                        @foreach (range(1, 12) as $month)
                            <option value="{{ $month }}" @selected((string) $filters['month'] === (string) $month)>{{ DateTime::createFromFormat('!m', $month)->format('F') }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tahun</span>
                    <input name="year" type="number" min="2020" max="2100" value="{{ $filters['year'] }}" placeholder="2026"
                        class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                </label>

                <label class="block">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Area</span>
                    <select name="scope" class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                        <option value="">Semua Area</option>
                        <option value="inside" @selected($filters['scope'] === 'inside')>Di Dalam Polman</option>
                        <option value="outside" @selected($filters['scope'] === 'outside')>Diluar Polman</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Lokasi</span>
                    <select name="location_id" class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                        <option value="">Semua Lokasi</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected((string) $filters['location_id'] === (string) $location->id)>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Kategori</span>
                    <select name="category_id" class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) $filters['category_id'] === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</span>
                    <select name="status" class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                        <option value="">Semua Status</option>
                        @foreach (['submitted', 'verified', 'investigating', 'resolved', 'closed', 'rejected'] as $status)
                            <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Keparahan</span>
                    <select name="severity_level" class="mt-2 h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-700 outline-none transition focus:border-[var(--primary-color)] focus:bg-white">
                        <option value="">Semua Level</option>
                        @foreach ($severityLabel as $value => $label)
                            <option value="{{ $value }}" @selected($filters['severity_level'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-full bg-[var(--primary-color)] px-6 py-3 text-sm font-bold text-white transition hover:bg-[var(--primary-deep)]">
                    <span class="material-symbols-outlined text-[20px]">filter_alt</span>
                    Terapkan Filter
                </button>
                <a href="{{ route('satgas.incidents.gis') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                    Reset
                </a>
                <a href="{{ route('satgas.incidents.gis.export', $filterQuery) }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-emerald-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-emerald-700">
                    <span class="material-symbols-outlined text-[20px]">download</span>
                    Export Excel
                </a>
            </div>
        </form>

        <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div id="satgas-incident-gis-map" class="h-[72vh] min-h-[580px] w-full"></div>
        </div>

        <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-2 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tabel Kejadian</p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-900">Data insiden terkoordinat</h2>
                </div>
                <p class="text-sm font-semibold text-slate-500">{{ $reports->total() }} data sesuai filter</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-5 py-4 font-semibold">Tanggal</th>
                            <th class="px-5 py-4 font-semibold">No. Laporan</th>
                            <th class="px-5 py-4 font-semibold">Nama</th>
                            <th class="px-5 py-4 font-semibold">Kejadian</th>
                            <th class="px-5 py-4 font-semibold">Lokasi</th>
                            <th class="px-5 py-4 font-semibold">Area</th>
                            <th class="px-5 py-4 font-semibold">Koordinat</th>
                            <th class="px-5 py-4 font-semibold">Status</th>
                            <th class="px-5 py-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($reports as $report)
                            @php
                                $locationName = $report->verifiedLocation?->name ?? $report->location?->name ?? '-';
                                $lat = $report->verified_latitude ?? $report->latitude;
                                $lng = $report->verified_longitude ?? $report->longitude;
                            @endphp
                            <tr>
                                <td class="px-5 py-4 text-slate-700">{{ optional($report->incident_date)->format('d M Y') }}</td>
                                <td class="px-5 py-4 font-medium text-slate-900">{{ $report->report_number }}</td>
                                <td class="px-5 py-4 text-slate-700">{{ $report->victim_name ?? $report->reporter?->name ?? $report->reporter_name ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-700">
                                    <p class="font-semibold text-slate-900">{{ $report->title }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $report->category?->name ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-4 text-slate-700">
                                    <p>{{ $locationName }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $report->verified_specific_location ?? $report->specific_location ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-4 text-slate-700">{{ $locationName === 'Diluar Polman' ? 'Diluar Polman' : 'Di Dalam Polman' }}</td>
                                <td class="px-5 py-4 text-slate-700">{{ $lat }}, {{ $lng }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $statusBadge($report->status) }}">
                                        {{ str_replace('_', ' ', $report->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('satgas.incidents.show', $report) }}" class="font-semibold text-[var(--primary-color)] hover:text-[var(--primary-deep)]">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-10 text-center text-slate-500">Belum ada data insiden berkoordinat sesuai filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-6 py-4">
                {{ $reports->links() }}
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (() => {
            const mapElement = document.getElementById('satgas-incident-gis-map');
            const markers = @json($markers);
            const campusBuildings = @json($campusBuildingPolygons);

            if (!mapElement || typeof L === 'undefined') {
                return;
            }

            const colors = {
                low: '#159947',
                medium: '#e7aa14',
                high: '#ef6a22',
                critical: '#d93f33',
                '-': '#0a4db3',
            };

            const createIcon = (marker) => {
                const color = marker.scope === 'outside' ? '#7f1d1d' : (colors[marker.severity_level] || '#0a4db3');
                return L.divIcon({
                    className: '',
                    html: `<span style="display:block;width:26px;height:26px;border-radius:9999px;background:${color};border:3px solid white;box-shadow:0 12px 26px rgba(15,23,42,.35);"></span>`,
                    iconSize: [26, 26],
                    iconAnchor: [13, 13],
                });
            };

            const map = L.map(mapElement).setView([-6.8761, 107.62063], 18);
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri',
                maxZoom: 20,
            }).addTo(map);
            L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Labels &copy; Esri',
                maxZoom: 20,
            }).addTo(map);

            const bounds = [];

            campusBuildings.forEach((building) => {
                L.polygon(building.coordinates, {
                    color: building.color,
                    fillColor: building.color,
                    fillOpacity: 0.24,
                    opacity: 0.95,
                    weight: 3,
                }).addTo(map).bindPopup(`<strong>${building.name}</strong><br>Area gedung kampus`);
                building.coordinates.forEach((coordinate) => bounds.push(coordinate));
            });

            markers.forEach((marker) => {
                L.marker([marker.latitude, marker.longitude], { icon: createIcon(marker) })
                    .addTo(map)
                    .bindPopup(`
                        <strong>${marker.report_number}</strong><br>
                        ${marker.title}<br>
                        ${marker.reporter}<br>
                        ${marker.location} - ${marker.specific_location}<br>
                        ${marker.category}<br>
                        <a href="${marker.show_url}">Buka detail</a>
                    `);
                bounds.push([marker.latitude, marker.longitude]);
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [48, 48], maxZoom: 19 });
            }
        })();
    </script>
@endpush
