@php
    $riskBadge = fn (?string $risk): string => match ($risk) {
        'rendah' => 'bg-emerald-100 text-emerald-800',
        'sedang' => 'bg-amber-100 text-amber-800',
        'tinggi' => 'bg-orange-100 text-orange-800',
        'kritis' => 'bg-rose-100 text-rose-800',
        default => 'bg-slate-100 text-slate-700',
    };
@endphp

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

<section id="peta-gis-hazard" class="mx-auto flex w-full max-w-[1600px] flex-col gap-6 px-4 py-14 sm:px-6 lg:px-8">
    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_360px]">
        <div class="rounded-[1.45rem] bg-white px-8 py-8 shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[var(--primary-color)]">GIS Hazard</p>
            <h2 class="mt-3 text-4xl font-bold text-[var(--primary-color)] lg:text-5xl">Peta Titik Rawan Kampus</h2>
            <p class="mt-4 max-w-4xl text-base font-semibold leading-8 text-slate-600">
                Titik pada peta ditentukan oleh Satgas berdasarkan review hazard report dan citra satelit area Kampus Polman Bandung.
            </p>
        </div>

        <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
            <article class="rounded-[1.25rem] bg-white px-5 py-5 shadow-[0_16px_35px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Total Titik</p>
                <p class="mt-2 text-3xl font-bold text-[var(--primary-color)]">{{ $summaryCounts['total'] }}</p>
            </article>
            <article class="rounded-[1.25rem] bg-white px-5 py-5 shadow-[0_16px_35px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Risiko Tinggi</p>
                <p class="mt-2 text-3xl font-bold text-orange-600">{{ $summaryCounts['tinggi'] }}</p>
            </article>
            <article class="rounded-[1.25rem] bg-white px-5 py-5 shadow-[0_16px_35px_rgba(15,23,42,0.08)] ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Belum Selesai</p>
                <p class="mt-2 text-3xl font-bold text-rose-600">{{ $summaryCounts['aktif'] }}</p>
            </article>
        </div>
    </div>

    <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
        <div class="overflow-hidden rounded-[1.45rem] bg-white shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
            <div id="public-hazard-map" class="h-[72vh] min-h-[560px] w-full"></div>
        </div>

        <aside class="rounded-[1.45rem] bg-white p-5 shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Daftar Titik</p>
                    <h3 class="mt-1 text-2xl font-bold text-slate-900">Area Terpetakan</h3>
                </div>
                <span class="material-symbols-outlined text-[var(--primary-color)]">satellite_alt</span>
            </div>

            <div class="mt-5 max-h-[64vh] space-y-3 overflow-y-auto pr-1">
                @forelse ($hazardMarkers as $marker)
                    <article class="rounded-[1.1rem] bg-[#f8fbff] p-4 ring-1 ring-slate-200">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $marker['title'] }}</p>
                                <p class="mt-1 text-xs leading-5 text-slate-500">{{ $marker['location'] }} - {{ $marker['specific_location'] }}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-3 py-1 text-[11px] font-bold uppercase {{ $riskBadge($marker['risk_level']) }}">
                                {{ $marker['risk_level'] }}
                            </span>
                        </div>
                        <p class="mt-3 text-xs font-semibold text-[var(--primary-color)]">{{ $marker['report_number'] }}</p>
                    </article>
                @empty
                    <div class="rounded-[1.1rem] bg-slate-50 px-4 py-6 text-sm leading-7 text-slate-500">
                        Belum ada titik hazard yang dipetakan Satgas.
                    </div>
                @endforelse
            </div>
        </aside>
    </section>

    <section class="overflow-hidden rounded-[1.45rem] bg-white shadow-[0_22px_55px_rgba(15,23,42,0.14)] ring-1 ring-slate-200">
        <div class="flex flex-col gap-2 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Denah Kampus</p>
                <h3 class="mt-1 text-2xl font-bold text-slate-900">Titik rawan pada denah gedung</h3>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span id="public-gps-building-status" class="text-sm font-semibold text-slate-500">{{ $floorplanMarkers->count() }} titik</span>
                <button id="public-use-gps-button" type="button" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-full bg-[var(--primary-color)] px-4 text-sm font-bold text-white transition hover:bg-[var(--primary-deep)]">
                    <span class="material-symbols-outlined text-[20px]">my_location</span>
                    Gunakan GPS
                </button>
            </div>
        </div>
        @include('partials.floorplan-building-viewer', ['id' => 'public-floorplan-viewer'])
    </section>
</section>

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (() => {
            const mapElement = document.getElementById('public-hazard-map');
            const floorplanViewer = document.getElementById('public-floorplan-viewer');
            const gpsButton = document.getElementById('public-use-gps-button');
            const gpsStatus = document.getElementById('public-gps-building-status');
            const markers = @json($hazardMarkers);
            const floorplanMarkers = @json($floorplanMarkers);
            const campusBuildings = @json($campusBuildingPolygons);

            if (typeof L === 'undefined') {
                return;
            }

            const colors = {
                rendah: '#159947',
                sedang: '#e7aa14',
                tinggi: '#ef6a22',
                kritis: '#d93f33',
            };

            const createIcon = (riskLevel, size = 22) => {
                const color = colors[riskLevel] || '#0a4db3';
                return L.divIcon({
                    className: '',
                    html: `<span style="display:block;width:${size}px;height:${size}px;border-radius:9999px;background:${color};border:3px solid white;box-shadow:0 10px 24px rgba(15,23,42,.35);"></span>`,
                    iconSize: [size, size],
                    iconAnchor: [size / 2, size / 2],
                });
            };

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

            if (mapElement) {
                const campusCenter = [-6.8761, 107.62063];
                const map = L.map(mapElement, { zoomControl: true }).setView(campusCenter, 18);

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
                    L.marker([marker.latitude, marker.longitude], { icon: createIcon(marker.risk_level) })
                        .addTo(map)
                        .bindPopup(`
                            <strong>${marker.title}</strong><br>
                            ${marker.location}<br>
                            Risiko: ${marker.risk_level}<br>
                            Status: ${marker.status}
                        `);

                    bounds.push([marker.latitude, marker.longitude]);
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [48, 48], maxZoom: 19 });
                }
            }

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
                    const width = Number(floorplan?.dataset.floorplanWidth || 4080);
                    const height = Number(floorplan?.dataset.floorplanHeight || 3060);
                    const buildingKey = floorplan?.dataset.buildingKey || 'gedung-teori';
                    const floor = Number(floorplan?.dataset.floor || 2);

                    if (!layer) {
                        return;
                    }

                    layer.innerHTML = '';

                    markerData
                        .filter((marker) => (marker.building_key || 'gedung-teori') === buildingKey && Number(marker.floor || 2) === floor)
                        .forEach((marker) => {
                            const color = colors[marker.risk_level] || '#0a4db3';
                            const pin = document.createElement('button');
                            pin.type = 'button';
                            pin.className = 'pointer-events-auto absolute z-30 h-6 w-6 -translate-x-1/2 -translate-y-1/2 rounded-full border-[3px] border-white shadow-[0_10px_24px_rgba(15,23,42,.35)] transition hover:scale-125 focus:scale-125 focus:outline-none focus:ring-4 focus:ring-sky-200';
                            pin.style.left = `${(Number(marker.x) / width) * 100}%`;
                            pin.style.top = `${(Number(marker.y) / height) * 100}%`;
                            pin.style.background = color;
                            pin.title = `${marker.title} - Risiko: ${marker.risk_level}`;
                            pin.setAttribute('aria-label', pin.title);
                            pin.addEventListener('click', () => {
                                alert(`${marker.title}\n${marker.location}\nRisiko: ${marker.risk_level}\nStatus: ${marker.status}`);
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

            floorplanViewer?.querySelectorAll('[data-floor-tab]').forEach((tab) => {
                tab.addEventListener('click', () => selectFloor(floorplanViewer, tab.dataset.targetFloor));
            });
            gpsButton?.addEventListener('click', useGpsForFloorplan);
            renderFloorplanMarkers(floorplanViewer, floorplanMarkers);
        })();
    </script>
@endpush
