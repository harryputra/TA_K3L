@php
    $id = $id ?? 'floorplan-building-viewer';
    $buildingKey = $buildingKey ?? 'gedung-teori';
    $buildingName = $buildingName ?? 'Gedung Teori';
    $activeFloor = (int) ($activeFloor ?? 2);
    $floors = collect($floors ?? [
        ['number' => 2, 'label' => 'Lantai 2', 'partial' => 'partials.floorplan-gedung-teori-lt2'],
        ['number' => 3, 'label' => 'Lantai 3', 'partial' => 'partials.floorplan-gedung-teori-lt2'],
    ]);
@endphp

<div
    id="{{ $id }}"
    class="floorplan-building-viewer"
    data-building-key="{{ $buildingKey }}"
    data-active-floor="{{ $activeFloor }}"
>
    <div class="flex flex-col gap-4 border-b border-slate-200 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Gedung terdeteksi</p>
            <h3 class="mt-1 text-xl font-bold text-slate-900" data-floorplan-building-label>{{ $buildingName }}</h3>
        </div>

        <div class="flex max-w-full gap-2 overflow-x-auto pb-1" role="tablist" aria-label="Pilih lantai denah">
            @foreach ($floors as $floorOption)
                @php
                    $floorNumber = (int) $floorOption['number'];
                    $isActive = $floorNumber === $activeFloor;
                @endphp
                <button
                    type="button"
                    class="{{ $isActive ? 'bg-[var(--primary-color)] text-white shadow-[0_10px_24px_rgba(10,77,179,0.24)]' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} inline-flex min-h-11 shrink-0 items-center justify-center rounded-full px-4 text-sm font-bold transition"
                    data-floor-tab
                    data-target-floor="{{ $floorNumber }}"
                    aria-selected="{{ $isActive ? 'true' : 'false' }}"
                >
                    {{ $floorOption['label'] }}
                </button>
            @endforeach
        </div>
    </div>

    @foreach ($floors as $floorOption)
        @php
            $floorNumber = (int) $floorOption['number'];
            $isActive = $floorNumber === $activeFloor;
        @endphp
        <div
            class="{{ $isActive ? '' : 'hidden' }}"
            data-floor-panel
            data-panel-floor="{{ $floorNumber }}"
        >
            @include($floorOption['partial'], ['id' => "{$id}-floor-{$floorNumber}", 'floor' => $floorNumber])
        </div>
    @endforeach
</div>
