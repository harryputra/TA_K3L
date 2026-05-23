@php
    $id = $id ?? 'floorplan-building-viewer';
    $buildingKey = $buildingKey ?? 'gedung-teori';
    $mode = $mode ?? 'incident'; // 'incident' atau 'hazard'
    
    $floorplans = collect(
        $floorplans ??
            \App\Models\Floorplan::query()
                ->where('building_key', $buildingKey)
                ->where('is_active', true)
                ->whereNotNull('svg_markup')
                ->orderBy('floor')
                ->orderByDesc('version')
                ->get(),
    )
        ->unique('floor')
        ->values();
    $hasFloorplans = $floorplans->isNotEmpty();
    $buildingName = $buildingName ?? ($floorplans->first()?->building_name ?? 'Gedung');
    $activeFloor = (int) ($activeFloor ?? ($floorplans->first()?->floor ?? 0));
    $floorplanIds = $floorplans->pluck('id');
    
    // ==================== INCIDENT DATA ====================
    $incidentRoomIds = $hasFloorplans
        ? \App\Models\IncidentReport::query()
            ->whereNotNull('campus_room_id')
            ->whereNotIn('status', ['resolved', 'closed', 'rejected'])
            ->whereIn(
                'campus_room_id',
                \App\Models\FloorplanRoom::query()->whereIn('floorplan_id', $floorplanIds)->select('campus_room_id'),
            )
            ->pluck('campus_room_id')
            ->unique()
        : collect();
        
    $incidentRoomColors = $incidentRoomIds->isNotEmpty()
        ? \App\Models\FloorplanRoom::query()
            ->whereIn('floorplan_id', $floorplanIds)
            ->whereIn('campus_room_id', $incidentRoomIds)
            ->get()
            ->mapWithKeys(fn($room) => [(string) $room->campus_room_id => $room->incident_fill_color])
        : collect();

    $incidentCounts = $hasFloorplans && $incidentRoomIds->isNotEmpty()
        ? \App\Models\IncidentReport::query()
            ->whereNotNull('campus_room_id')
            ->whereNotIn('status', ['resolved', 'closed', 'rejected'])
            ->whereIn('campus_room_id', $incidentRoomIds)
            ->selectRaw('campus_room_id, COUNT(*) as count')
            ->groupBy('campus_room_id')
            ->pluck('count', 'campus_room_id')
            ->mapWithKeys(fn($count, $roomId) => [(string) $roomId => $count])
        : collect();
    
    // ==================== HAZARD DATA ====================
    $hazardRoomIds = $hasFloorplans
        ? \App\Models\PotentialHazardReport::query()
            ->whereNotNull('campus_room_id')
            ->where('status', '!=', 'resolved')
            ->whereIn(
                'campus_room_id',
                \App\Models\FloorplanRoom::query()->whereIn('floorplan_id', $floorplanIds)->select('campus_room_id'),
            )
            ->pluck('campus_room_id')
            ->unique()
        : collect();
        
    $hazardRoomColors = $hazardRoomIds->isNotEmpty()
        ? \App\Models\FloorplanRoom::query()
            ->whereIn('floorplan_id', $floorplanIds)
            ->whereIn('campus_room_id', $hazardRoomIds)
            ->get()
            ->mapWithKeys(fn($room) => [(string) $room->campus_room_id => '#f97316']) // Warna orange untuk hazard
        : collect();

    $hazardCounts = $hasFloorplans && $hazardRoomIds->isNotEmpty()
        ? \App\Models\PotentialHazardReport::query()
            ->whereNotNull('campus_room_id')
            ->where('status', '!=', 'resolved')
            ->whereIn('campus_room_id', $hazardRoomIds)
            ->selectRaw('campus_room_id, COUNT(*) as count')
            ->groupBy('campus_room_id')
            ->pluck('count', 'campus_room_id')
            ->mapWithKeys(fn($count, $roomId) => [(string) $roomId => $count])
        : collect();
@endphp

<div id="{{ $id }}" class="floorplan-building-viewer" 
    data-building-key="{{ $buildingKey }}"
    data-active-floor="{{ $activeFloor }}" 
    data-has-floorplans="{{ $hasFloorplans ? 'true' : 'false' }}"
    data-mode="{{ $mode }}"
    data-incident-room-colors='@json($incidentRoomColors)' 
    data-incident-counts='@json($incidentCounts)'
    data-hazard-room-colors='@json($hazardRoomColors)'
    data-hazard-counts='@json($hazardCounts)'>
    
    <div class="flex flex-col gap-4 border-b border-slate-200 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Gedung terdeteksi</p>
            <h3 class="mt-1 text-xl font-bold text-slate-900" data-floorplan-building-label>{{ $buildingName }}</h3>
        </div>

        @if ($hasFloorplans)
            <div class="flex max-w-full gap-2 overflow-x-auto pb-1" role="tablist" aria-label="Pilih lantai denah">
                @foreach ($floorplans as $floorplan)
                    @php
                        $floorNumber = (int) $floorplan->floor;
                        $isActive = $floorNumber === $activeFloor;
                    @endphp
                    <button type="button"
                        class="{{ $isActive ? 'bg-[var(--primary-color)] text-white shadow-[0_10px_24px_rgba(10,77,179,0.24)]' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} inline-flex min-h-11 shrink-0 items-center justify-center rounded-full px-4 text-sm font-bold transition"
                        data-floor-tab data-target-floor="{{ $floorNumber }}"
                        aria-selected="{{ $isActive ? 'true' : 'false' }}">
                        Lantai {{ $floorNumber }}
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    @if (!$hasFloorplans)
        <div class="flex min-h-[360px] items-center justify-center bg-white px-6 py-16 text-center sm:min-h-[460px] lg:min-h-[560px]">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-400">Denah belum tersedia</p>
                <h4 class="mt-3 text-2xl font-bold text-slate-900">Belum ada data denah untuk ditampilkan.</h4>
                <p class="mt-3 max-w-xl text-sm leading-7 text-slate-600">Silakan buat denah dari menu admin agar tampilan GIS dapat menampilkan area ruangan dan titik pada denah.</p>
            </div>
        </div>
    @else
        @foreach ($floorplans as $floorplan)
            @php
                $floorNumber = (int) $floorplan->floor;
                $isActive = $floorNumber === $activeFloor;
            @endphp
            <div class="{{ $isActive ? '' : 'hidden' }}" data-floor-panel data-panel-floor="{{ $floorNumber }}">
                <div id="{{ $id }}-floor-{{ $floorNumber }}"
                    class="floorplan-html relative max-h-[72vh] min-h-[360px] overflow-auto overscroll-contain bg-white sm:min-h-[460px] lg:min-h-[560px]"
                    data-building-key="{{ $floorplan->building_key }}" data-floor="{{ $floorNumber }}"
                    data-floorplan-width="{{ $floorplan->canvas_width }}"
                    data-floorplan-height="{{ $floorplan->canvas_height }}">
                    <div class="relative m-3 min-w-[760px] bg-white sm:m-4 sm:min-w-[980px] lg:min-w-[1184px]"
                        style="aspect-ratio: {{ max((int) $floorplan->canvas_width, 1) }} / {{ max((int) $floorplan->canvas_height, 1) }};">
                        {!! $floorplan->svg_markup !!}
                        <div class="floorplan-marker-layer pointer-events-none absolute inset-0"></div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

@if ($hasFloorplans)
    @push('scripts')
        <script>
            (() => {
                const viewer = document.getElementById(@json($id));

                if (!viewer) {
                    return;
                }

                // Ambil data dari dataset
                const incidentRooms = JSON.parse(viewer.dataset.incidentRoomColors || '{}');
                const incidentCounts = JSON.parse(viewer.dataset.incidentCounts || '{}');
                const hazardRooms = JSON.parse(viewer.dataset.hazardRoomColors || '{}');
                const hazardCounts = JSON.parse(viewer.dataset.hazardCounts || '{}');
                
                let currentMode = viewer.dataset.mode || 'incident';

                // Fungsi untuk update tampilan berdasarkan mode
                function updateDisplayByMode(mode) {
                    const roomIds = mode === 'incident' 
                        ? Object.keys(incidentRooms)
                        : Object.keys(hazardRooms);
                    
                    const roomColors = mode === 'incident' ? incidentRooms : hazardRooms;
                    const roomCounts = mode === 'incident' ? incidentCounts : hazardCounts;
                    const fillColorDefault = mode === 'incident' ? '#ef4444' : '#f97316';
                    const strokeColor = mode === 'incident' ? '#991b1b' : '#c2410c';

                    roomIds.slice(0, 50).forEach((roomId) => {
                        const color = roomColors[roomId] || fillColorDefault;
                        const count = roomCounts[roomId];

                        const roomGroups = viewer.querySelectorAll(`[data-room-id="${roomId}"]`);

                        roomGroups.forEach((roomGroup) => {
                            const shape = roomGroup.querySelector('rect, polygon, path');
                            if (!shape) return;

                            shape.setAttribute('fill', color);
                            shape.setAttribute('stroke', strokeColor);
                            shape.setAttribute('stroke-width', '1.5');

                            // Hapus badge lama
                            const oldBadge = roomGroup.querySelector('.incident-badge');
                            if (oldBadge) oldBadge.remove();

                            if (count && count > 0) {
                                let cx = 0, cy = 0;

                                if (shape.tagName === 'rect') {
                                    const x = parseFloat(shape.getAttribute('x') || 0);
                                    const y = parseFloat(shape.getAttribute('y') || 0);
                                    const w = parseFloat(shape.getAttribute('width') || 100);
                                    const h = parseFloat(shape.getAttribute('height') || 100);
                                    cx = x + w - 18;
                                    cy = y + 18;
                                } else if (shape.tagName === 'polygon') {
                                    const points = shape.getAttribute('points');
                                    if (points) {
                                        const coords = points.split(/[\s,]+/).map(Number);
                                        let minX = Infinity, minY = Infinity;
                                        let maxX = -Infinity, maxY = -Infinity;

                                        for (let i = 0; i < coords.length; i += 2) {
                                            minX = Math.min(minX, coords[i]);
                                            minY = Math.min(minY, coords[i + 1]);
                                            maxX = Math.max(maxX, coords[i]);
                                            maxY = Math.max(maxY, coords[i + 1]);
                                        }
                                        cx = maxX - 18;
                                        cy = minY + 18;
                                    }
                                }

                                if (cx !== 0 || cy !== 0) {
                                    const badge = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                                    badge.setAttribute('class', 'incident-badge');

                                    const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                                    circle.setAttribute('cx', cx);
                                    circle.setAttribute('cy', cy);
                                    circle.setAttribute('r', '14');
                                    circle.setAttribute('fill', fillColorDefault);
                                    circle.setAttribute('stroke', '#fff');
                                    circle.setAttribute('stroke-width', '2.5');

                                    const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                                    text.setAttribute('x', cx);
                                    text.setAttribute('y', cy);
                                    text.setAttribute('text-anchor', 'middle');
                                    text.setAttribute('dominant-baseline', 'central');
                                    text.setAttribute('fill', '#fff');
                                    text.setAttribute('font-size', '11');
                                    text.setAttribute('font-weight', 'bold');
                                    text.textContent = count > 99 ? '99+' : count.toString();

                                    badge.appendChild(circle);
                                    badge.appendChild(text);
                                    roomGroup.appendChild(badge);
                                }
                            }
                        });
                    });
                }

                // Initial display
                updateDisplayByMode(currentMode);

                // Listen for mode changes from parent window
                window.addEventListener('message', (event) => {
                    if (event.data && event.data.type === 'GIS_MODE_CHANGE') {
                        currentMode = event.data.mode;
                        updateDisplayByMode(currentMode);
                    }
                });

                // Juga listen untuk custom event
                viewer.addEventListener('gis-mode-changed', (event) => {
                    if (event.detail && event.detail.mode) {
                        currentMode = event.detail.mode;
                        updateDisplayByMode(currentMode);
                    }
                });
            })();
        </script>
    @endpush
@endif