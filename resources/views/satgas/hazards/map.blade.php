@extends('satgas.layouts.app')

@section('title', 'Peta GIS Hazard')
@section('hero_eyebrow', 'GIS Hazard')
@section('hero_title', 'Peta pinpoint area rawan')
@section('hero_description', 'Pantau semua hazard yang sudah diberi koordinat oleh Satgas pada citra satelit Kampus Polman Bandung.')

@php
    $campusBuildingPolygons = [
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
@endphp

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-3">
            <article class="rounded-[1.5rem] bg-white px-5 py-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Titik Terpetakan</p>
                <p class="mt-3 text-4xl font-bold text-slate-900">{{ $hazardMarkers->count() + $floorplanMarkers->count() }}</p>
            </article>
            <article class="rounded-[1.5rem] bg-white px-5 py-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Belum Dipetakan</p>
                <p class="mt-3 text-4xl font-bold text-orange-600">{{ $unmappedCount }}</p>
            </article>
            <article class="rounded-[1.5rem] bg-white px-5 py-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Aksi Cepat</p>
                <a href="{{ route('satgas.hazards.index') }}" class="mt-4 inline-flex items-center gap-2 rounded-full bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white">
                    <span class="material-symbols-outlined text-[20px]">list_alt</span>
                    Review Hazard
                </a>
            </article>
        </div>

        <form action="{{ route('satgas.hazards.map-points.store') }}" method="POST"
            class="rounded-[2rem] bg-white p-5 shadow-sm ring-1 ring-slate-200 lg:p-6">
            @csrf
            <input id="new-point-map-source" name="map_source" type="hidden" value="{{ old('map_source', 'satellite') }}">

            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">Tambah Titik Area Rawan</p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">Klik map, lengkapi informasi, lalu simpan.</h2>
                    <p id="new-point-mode-help" class="mt-2 text-sm leading-7 text-slate-600">
                        Pilih mode titik satelit atau denah, kemudian klik lokasi bahaya pada map terkait.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <button id="new-satellite-pick-button" type="button"
                        class="inline-flex min-h-13 items-center justify-center gap-2 rounded-2xl bg-[var(--primary-color)] px-5 py-3 text-sm font-bold text-white transition hover:bg-[var(--primary-deep)]">
                        <span class="material-symbols-outlined text-[20px]">add_location_alt</span>
                        <span id="new-satellite-pick-label">Pilih Titik Satelit</span>
                    </button>
                    <button id="new-floorplan-pick-button" type="button"
                        class="inline-flex min-h-13 items-center justify-center gap-2 rounded-2xl bg-[var(--primary-color)] px-5 py-3 text-sm font-bold text-white transition hover:bg-[var(--primary-deep)]">
                        <span class="material-symbols-outlined text-[20px]">add_location_alt</span>
                        <span id="new-floorplan-pick-label">Pilih Titik Denah</span>
                    </button>
                </div>
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-[minmax(0,1fr)_180px_190px]">
                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-800">Nama titik rawan</span>
                    <input name="title" type="text" value="{{ old('title') }}" placeholder="Contoh: Jalur licin dekat GRC"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                    @error('title')
                        <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-800">Kategori</span>
                    <select name="hazard_type"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                        <option value="">Umum</option>
                        <option value="lingkungan" @selected(old('hazard_type') === 'lingkungan')>Lingkungan</option>
                        <option value="peralatan" @selected(old('hazard_type') === 'peralatan')>Peralatan</option>
                        <option value="listrik" @selected(old('hazard_type') === 'listrik')>Listrik</option>
                        <option value="zat-kimia" @selected(old('hazard_type') === 'zat-kimia')>Zat Kimia</option>
                    </select>
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-semibold text-slate-800">Level risiko</span>
                    <select name="risk_level"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                        @foreach (['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi', 'kritis' => 'Kritis'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('risk_level', 'sedang') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="mt-4 grid gap-4 lg:grid-cols-[minmax(0,1fr)_repeat(4,150px)_auto]">
                <label class="block lg:col-span-1">
                    <span class="mb-2 block text-sm font-semibold text-slate-800">Catatan</span>
                    <input name="description" type="text" value="{{ old('description') }}" placeholder="Keterangan singkat area rawan"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-4 focus:ring-[var(--blue-low-opacity)]/40">
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Latitude</span>
                    <input id="new-point-latitude" name="latitude" type="text" value="{{ old('latitude') }}"
                        class="w-full rounded-2xl border border-slate-300 px-3 py-3 text-xs outline-none">
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Longitude</span>
                    <input id="new-point-longitude" name="longitude" type="text" value="{{ old('longitude') }}"
                        class="w-full rounded-2xl border border-slate-300 px-3 py-3 text-xs outline-none">
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">X Denah</span>
                    <input id="new-point-floorplan-x" name="floorplan_x" type="text" value="{{ old('floorplan_x') }}"
                        class="w-full rounded-2xl border border-slate-300 px-3 py-3 text-xs outline-none">
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Y Denah</span>
                    <input id="new-point-floorplan-y" name="floorplan_y" type="text" value="{{ old('floorplan_y') }}"
                        class="w-full rounded-2xl border border-slate-300 px-3 py-3 text-xs outline-none">
                </label>
                <div class="flex items-end">
                    <button type="submit" class="inline-flex h-12 items-center justify-center rounded-full bg-[var(--primary-color)] px-6 text-sm font-bold text-white transition hover:bg-[var(--primary-deep)]">
                        Simpan Titik
                    </button>
                </div>
            </div>

            @if ($errors->any())
                <div class="mt-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                    Lengkapi nama titik dan pilih titik pada map sebelum menyimpan.
                </div>
            @endif
        </form>

        <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div id="satgas-hazard-map" class="h-[76vh] min-h-[620px] w-full"></div>
        </div>

        <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-2 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Denah Kampus</p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-900">Pinpoint pada denah gedung</h2>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span id="satgas-gps-building-status" class="text-sm font-semibold text-slate-500">{{ $floorplanMarkers->count() }} titik</span>
                    <button id="satgas-use-gps-button" type="button" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-full bg-[var(--primary-color)] px-4 text-sm font-bold text-white transition hover:bg-[var(--primary-deep)]">
                        <span class="material-symbols-outlined text-[20px]">my_location</span>
                        Gunakan GPS
                    </button>
                </div>
            </div>
            @include('partials.floorplan-building-viewer', ['id' => 'satgas-floorplan-viewer'])
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (() => {
            const mapElement = document.getElementById('satgas-hazard-map');
            const floorplanViewer = document.getElementById('satgas-floorplan-viewer');
            const gpsButton = document.getElementById('satgas-use-gps-button');
            const gpsStatus = document.getElementById('satgas-gps-building-status');
            const markers = @json($hazardMarkers);
            const floorplanMarkers = @json($floorplanMarkers);
            const campusBuildings = @json($campusBuildingPolygons);
            const mapSourceInput = document.getElementById('new-point-map-source');
            const latitudeInput = document.getElementById('new-point-latitude');
            const longitudeInput = document.getElementById('new-point-longitude');
            const floorplanXInput = document.getElementById('new-point-floorplan-x');
            const floorplanYInput = document.getElementById('new-point-floorplan-y');
            const satellitePickButton = document.getElementById('new-satellite-pick-button');
            const floorplanPickButton = document.getElementById('new-floorplan-pick-button');
            const satellitePickLabel = document.getElementById('new-satellite-pick-label');
            const floorplanPickLabel = document.getElementById('new-floorplan-pick-label');
            const modeHelp = document.getElementById('new-point-mode-help');

            if (typeof L === 'undefined') {
                return;
            }

            const colors = {
                rendah: '#159947',
                sedang: '#e7aa14',
                tinggi: '#ef6a22',
                kritis: '#d93f33',
            };

            const createIcon = (riskLevel, size = 24) => {
                const color = colors[riskLevel] || '#0a4db3';
                return L.divIcon({
                    className: '',
                    html: `<span style="display:block;width:${size}px;height:${size}px;border-radius:9999px;background:${color};border:3px solid white;box-shadow:0 10px 24px rgba(15,23,42,.35);"></span>`,
                    iconSize: [size, size],
                    iconAnchor: [size / 2, size / 2],
                });
            };

            const newPointIcon = L.divIcon({
                className: '',
                html: '<span style="display:flex;width:36px;height:36px;align-items:center;justify-content:center;border-radius:9999px;background:#d93f33;border:4px solid white;box-shadow:0 12px 28px rgba(15,23,42,.38);color:white;font-size:21px;font-weight:800;">+</span>',
                iconSize: [36, 36],
                iconAnchor: [18, 18],
            });

            const addCampusBuildings = (map, bounds) => {
                campusBuildings.forEach((building) => {
                    const polygon = L.polygon(building.coordinates, {
                        color: building.color,
                        fillColor: building.color,
                        fillOpacity: 0.28,
                        opacity: 0.95,
                        weight: 3,
                    }).addTo(map);

                    polygon.bindPopup(`<strong>${building.name}</strong><br>Area gedung kampus`);
                    building.coordinates.forEach((coordinate) => bounds.push(coordinate));
                });
            };

            const addBuildingLegend = (map) => {
                const legend = L.control({ position: 'bottomleft' });

                legend.onAdd = () => {
                    const container = L.DomUtil.create('div', 'campus-building-legend');
                    container.style.background = 'rgba(255,255,255,.94)';
                    container.style.borderRadius = '16px';
                    container.style.boxShadow = '0 14px 34px rgba(15,23,42,.22)';
                    container.style.padding = '12px 14px';
                    container.style.fontFamily = 'Poppins, sans-serif';
                    container.style.fontSize = '12px';
                    container.style.lineHeight = '1.4';
                    container.innerHTML = `
                        <strong style="display:block;margin-bottom:8px;color:#0f172a;">Polygon Gedung</strong>
                        ${campusBuildings.map((building) => `
                            <span style="display:flex;align-items:center;gap:8px;margin-top:6px;color:#334155;">
                                <span style="display:inline-block;width:14px;height:14px;border-radius:4px;background:${building.color};opacity:.85;"></span>
                                ${building.name}
                            </span>
                        `).join('')}
                    `;
                    L.DomEvent.disableClickPropagation(container);
                    return container;
                };

                legend.addTo(map);
            };

            let activePicker = null;
            let satelliteMap = null;
            let newSatelliteMarker = null;
            let newFloorplanMarker = null;

            const getActiveFloorplan = () => {
                const activeFloor = floorplanViewer?.dataset.activeFloor || '2';
                return floorplanViewer?.querySelector(`[data-panel-floor="${activeFloor}"] .floorplan-html`)
                    || floorplanViewer?.querySelector('.floorplan-html');
            };

            const getFloorplanCanvas = (floorplan) => floorplan?.firstElementChild;

            const createFloorplanPin = (marker, isNew = false) => {
                const color = isNew ? '#d93f33' : (colors[marker.risk_level] || '#0a4db3');
                const pin = document.createElement('button');
                pin.type = 'button';
                pin.className = `${isNew ? 'h-9 w-9 text-lg font-black text-white' : 'h-6 w-6'} pointer-events-auto absolute z-30 -translate-x-1/2 -translate-y-1/2 rounded-full border-[3px] border-white shadow-[0_10px_24px_rgba(15,23,42,.35)] transition hover:scale-125 focus:scale-125 focus:outline-none focus:ring-4 focus:ring-sky-200`;
                pin.style.background = color;
                pin.textContent = isNew ? '+' : '';
                pin.title = isNew ? 'Titik baru' : `${marker.title} - Risiko: ${marker.risk_level}`;
                pin.setAttribute('aria-label', pin.title);
                return pin;
            };

            const positionFloorplanPin = (pin, x, y, floorplan = getActiveFloorplan()) => {
                const width = Number(floorplan?.dataset.floorplanWidth || 4080);
                const height = Number(floorplan?.dataset.floorplanHeight || 3060);
                pin.style.left = `${(Number(x) / width) * 100}%`;
                pin.style.top = `${(Number(y) / height) * 100}%`;
            };

            const selectFloor = (viewer, floor) => {
                if (!viewer) {
                    return;
                }

                viewer.dataset.activeFloor = String(floor);
                viewer.querySelectorAll('[data-floor-tab]').forEach((tab) => {
                    const active = tab.dataset.targetFloor === String(floor);
                    tab.setAttribute('aria-selected', active ? 'true' : 'false');
                    tab.className = active
                        ? 'inline-flex min-h-11 items-center justify-center rounded-full bg-[var(--primary-color)] px-4 text-sm font-bold text-white shadow-[0_10px_24px_rgba(10,77,179,0.24)] transition'
                        : 'inline-flex min-h-11 items-center justify-center rounded-full bg-slate-100 px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-200';
                });

                viewer.querySelectorAll('[data-floor-panel]').forEach((panel) => {
                    panel.classList.toggle('hidden', panel.dataset.panelFloor !== String(floor));
                });
            };

            const renderFloorplanMarkers = (viewer, markerData) => {
                const floorplans = viewer?.querySelectorAll('.floorplan-html') ?? [];

                floorplans.forEach((floorplan) => {
                    const layer = floorplan?.querySelector('.floorplan-marker-layer');
                    const buildingKey = floorplan?.dataset.buildingKey || 'gedung-teori';
                    const floor = Number(floorplan?.dataset.floor || 2);

                if (!layer) {
                    return;
                }

                layer.innerHTML = '';

                    markerData
                        .filter((marker) => (marker.building_key || 'gedung-teori') === buildingKey && Number(marker.floor || 2) === floor)
                        .forEach((marker) => {
                    const pin = createFloorplanPin(marker);
                    const detail = marker.show_url ? `\n${marker.show_url}` : '';
                            positionFloorplanPin(pin, marker.x, marker.y, floorplan);
                    pin.addEventListener('click', (event) => {
                        event.stopPropagation();
                        alert(`${marker.title}\n${marker.location}\nRisiko: ${marker.risk_level}${detail}`);
                    });
                    layer.appendChild(pin);
                });
                });
            };

            const isPointInsidePolygon = ([lat, lng], polygon) => {
                let inside = false;

                for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
                    const [latI, lngI] = polygon[i];
                    const [latJ, lngJ] = polygon[j];
                    const intersects = ((lngI > lng) !== (lngJ > lng))
                        && (lat < ((latJ - latI) * (lng - lngI)) / (lngJ - lngI) + latI);

                    if (intersects) {
                        inside = !inside;
                    }
                }

                return inside;
            };

            const buildingAt = (lat, lng) => campusBuildings.find((building) => isPointInsidePolygon([lat, lng], building.coordinates));

            const useGpsForFloorplan = () => {
                if (!navigator.geolocation) {
                    gpsStatus.textContent = 'GPS tidak didukung browser.';
                    return;
                }

                gpsStatus.textContent = 'Mendeteksi posisi...';
                navigator.geolocation.getCurrentPosition((position) => {
                    const detected = buildingAt(position.coords.latitude, position.coords.longitude);

                    if (!detected) {
                        gpsStatus.textContent = 'Posisi belum masuk polygon gedung.';
                        return;
                    }

                    const floor = detected.default_floor || detected.floors?.[0] || 2;
                    gpsStatus.textContent = `${detected.name} terdeteksi`;

                    if (detected.key === floorplanViewer?.dataset.buildingKey) {
                        floorplanViewer.querySelector('[data-floorplan-building-label]').textContent = detected.name;
                        selectFloor(floorplanViewer, floor);
                    }
                }, () => {
                    gpsStatus.textContent = 'Izin GPS ditolak atau gagal dibaca.';
                }, {
                    enableHighAccuracy: true,
                    maximumAge: 15000,
                    timeout: 10000,
                });
            };

            const setPickingMode = (source) => {
                activePicker = activePicker === source ? null : source;
                const satelliteActive = activePicker === 'satellite';
                const floorplanActive = activePicker === 'floorplan';

                satellitePickButton.className = satelliteActive
                    ? 'inline-flex min-h-13 items-center justify-center gap-2 rounded-2xl bg-rose-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-rose-700'
                    : 'inline-flex min-h-13 items-center justify-center gap-2 rounded-2xl bg-[var(--primary-color)] px-5 py-3 text-sm font-bold text-white transition hover:bg-[var(--primary-deep)]';
                floorplanPickButton.className = floorplanActive
                    ? 'inline-flex min-h-13 items-center justify-center gap-2 rounded-2xl bg-rose-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-rose-700'
                    : 'inline-flex min-h-13 items-center justify-center gap-2 rounded-2xl bg-[var(--primary-color)] px-5 py-3 text-sm font-bold text-white transition hover:bg-[var(--primary-deep)]';

                satellitePickLabel.textContent = satelliteActive ? 'Klik Titik di Satelit' : 'Pilih Titik Satelit';
                floorplanPickLabel.textContent = floorplanActive ? 'Klik Titik di Denah' : 'Pilih Titik Denah';
                modeHelp.textContent = activePicker
                    ? 'Klik sekali pada map yang dipilih. Map sementara dikunci agar klik tidak berubah menjadi geser.'
                    : 'Pilih mode titik satelit atau denah, kemudian klik lokasi bahaya pada map terkait.';

                [satelliteMap].filter(Boolean).forEach((leafletMap) => {
                    if (activePicker) {
                        leafletMap.dragging.disable();
                        leafletMap.touchZoom.disable();
                        leafletMap.doubleClickZoom.disable();
                        leafletMap.scrollWheelZoom.disable();
                        leafletMap.boxZoom.disable();
                        leafletMap.keyboard.disable();
                        leafletMap.getContainer().style.cursor = 'crosshair';
                    } else {
                        leafletMap.dragging.enable();
                        leafletMap.touchZoom.enable();
                        leafletMap.doubleClickZoom.enable();
                        leafletMap.scrollWheelZoom.enable();
                        leafletMap.boxZoom.enable();
                        leafletMap.keyboard.enable();
                        leafletMap.getContainer().style.cursor = '';
                    }
                });

                if (floorplanViewer) {
                    floorplanViewer.classList.toggle('cursor-crosshair', floorplanActive);
                }
            };

            if (mapElement) {
                const campusCenter = [-6.8761, 107.62063];
                const map = L.map(mapElement).setView(campusCenter, 18);
                satelliteMap = map;

                L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles &copy; Esri',
                    maxZoom: 20,
                }).addTo(map);

                L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Labels &copy; Esri',
                    maxZoom: 20,
                }).addTo(map);

                const bounds = [];

                addCampusBuildings(map, bounds);
                addBuildingLegend(map);

                markers.forEach((marker) => {
                    const detailLink = marker.show_url ? `<br><a href="${marker.show_url}">Buka detail</a>` : '';

                    L.marker([marker.latitude, marker.longitude], { icon: createIcon(marker.risk_level) })
                        .addTo(map)
                        .bindPopup(`
                            <strong>${marker.title}</strong><br>
                            ${marker.location}<br>
                            Risiko: ${marker.risk_level}
                            ${detailLink}
                        `);

                    bounds.push([marker.latitude, marker.longitude]);
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [48, 48], maxZoom: 19 });
                }

                map.on('click', (event) => {
                    if (activePicker !== 'satellite') {
                        return;
                    }

                    mapSourceInput.value = 'satellite';
                    latitudeInput.value = event.latlng.lat.toFixed(7);
                    longitudeInput.value = event.latlng.lng.toFixed(7);

                    if (!newSatelliteMarker) {
                        newSatelliteMarker = L.marker(event.latlng, { draggable: true, icon: newPointIcon }).addTo(map);
                        newSatelliteMarker.on('dragend', () => {
                            const point = newSatelliteMarker.getLatLng();
                            latitudeInput.value = point.lat.toFixed(7);
                            longitudeInput.value = point.lng.toFixed(7);
                        });
                    } else {
                        newSatelliteMarker.setLatLng(event.latlng);
                    }

                    setPickingMode('satellite');
                });
            }

            if (floorplanViewer) {
                renderFloorplanMarkers(floorplanViewer, floorplanMarkers);

                floorplanViewer.addEventListener('click', (event) => {
                    if (activePicker !== 'floorplan') {
                        return;
                    }

                    if (event.target.closest('[data-floor-tab]')) {
                        return;
                    }

                    const floorplanElement = getActiveFloorplan();
                    const canvas = getFloorplanCanvas(floorplanElement);
                    const layer = floorplanElement?.querySelector('.floorplan-marker-layer');

                    if (!canvas || !layer) {
                        return;
                    }

                    const rect = canvas.getBoundingClientRect();
                    const width = Number(floorplanElement.dataset.floorplanWidth || 4080);
                    const height = Number(floorplanElement.dataset.floorplanHeight || 3060);
                    const x = Math.max(0, Math.min(width, ((event.clientX - rect.left) / rect.width) * width));
                    const y = Math.max(0, Math.min(height, ((event.clientY - rect.top) / rect.height) * height));

                    mapSourceInput.value = 'floorplan';
                    floorplanXInput.value = x.toFixed(3);
                    floorplanYInput.value = y.toFixed(3);

                    if (!newFloorplanMarker) {
                        newFloorplanMarker = createFloorplanPin({}, true);
                        layer.appendChild(newFloorplanMarker);
                    } else if (newFloorplanMarker.parentElement !== layer) {
                        layer.appendChild(newFloorplanMarker);
                    }

                    positionFloorplanPin(newFloorplanMarker, x, y, floorplanElement);
                    setPickingMode('floorplan');
                });
            }

            floorplanViewer?.querySelectorAll('[data-floor-tab]').forEach((tab) => {
                tab.addEventListener('click', () => selectFloor(floorplanViewer, tab.dataset.targetFloor));
            });
            gpsButton?.addEventListener('click', useGpsForFloorplan);
            satellitePickButton.addEventListener('click', () => setPickingMode('satellite'));
            floorplanPickButton.addEventListener('click', () => setPickingMode('floorplan'));
        })();
    </script>
@endpush
