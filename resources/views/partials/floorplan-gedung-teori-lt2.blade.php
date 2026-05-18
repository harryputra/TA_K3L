@php
    $floor = (int) ($floor ?? 2);
    $id = $id ?? "floorplan-gedung-teori-lt{$floor}";

    $rooms = [
        ['id' => 'lab-sensor', 'name' => 'Lab. Sensor', 'x' => 10, 'y' => 0, 'w' => 175, 'h' => 102],
        ['id' => 'tangga-barat-atas', 'name' => 'Tangga', 'x' => 102, 'y' => 102, 'w' => 83, 'h' => 52],
        ['id' => 'lab-robotik', 'name' => 'Lab. Robotik', 'x' => 185, 'y' => 0, 'w' => 160, 'h' => 154],
        ['id' => 'sanggar', 'name' => 'Sanggar', 'x' => 345, 'y' => 0, 'w' => 60, 'h' => 80],
        ['id' => 'bem', 'name' => 'BEM', 'x' => 405, 'y' => 0, 'w' => 60, 'h' => 80],
        ['id' => 'tangga-turun', 'name' => 'Tangga Turun', 'x' => 465, 'y' => 56, 'w' => 73, 'h' => 98],
        ['id' => 'wc-tengah-atas', 'name' => 'WC', 'x' => 538, 'y' => 0, 'w' => 82, 'h' => 154],
        ['id' => 'ruang-dosen', 'name' => 'Ruang Dosen', 'x' => 10, 'y' => 247, 'w' => 90, 'h' => 153],
        ['id' => 'wc-barat-bawah', 'name' => 'WC', 'x' => 100, 'y' => 285, 'w' => 85, 'h' => 115],
        ['id' => 'lab-bawah', 'name' => 'Lab', 'x' => 185, 'y' => 247, 'w' => 160, 'h' => 153],
        ['id' => 'tangga-darurat', 'name' => 'Tangga Darurat', 'x' => 345, 'y' => 337, 'w' => 90, 'h' => 63],
        ['id' => 'koridor-apsi', 'name' => '', 'x' => 435, 'y' => 337, 'w' => 31, 'h' => 63],
        ['id' => 'apsi', 'name' => 'APSI', 'x' => 466, 'y' => 337, 'w' => 154, 'h' => 63],
        ['id' => 'tangga-lt4', 'name' => 'Tg. Ke LT4', 'x' => 490, 'y' => 252, 'w' => 49, 'h' => 98],
        ['id' => 'b206', 'name' => 'B206', 'x' => 620, 'y' => 112, 'w' => 63, 'h' => 80],
        ['id' => 'b205', 'name' => 'B205', 'x' => 683, 'y' => 112, 'w' => 63, 'h' => 80],
        ['id' => 'b204', 'name' => 'B204', 'x' => 746, 'y' => 112, 'w' => 63, 'h' => 80],
        ['id' => 'b203', 'name' => 'B203', 'x' => 809, 'y' => 112, 'w' => 63, 'h' => 80],
        ['id' => 'b202', 'name' => 'B202', 'x' => 872, 'y' => 112, 'w' => 63, 'h' => 80],
        ['id' => 'b201', 'name' => 'B201', 'x' => 935, 'y' => 112, 'w' => 64, 'h' => 80],
        ['id' => 'b207', 'name' => 'B207', 'x' => 620, 'y' => 251, 'w' => 63, 'h' => 80],
        ['id' => 'b208', 'name' => 'B208', 'x' => 683, 'y' => 251, 'w' => 63, 'h' => 80],
        ['id' => 'b209', 'name' => 'B209', 'x' => 746, 'y' => 251, 'w' => 63, 'h' => 80],
        ['id' => 'b210', 'name' => 'B210', 'x' => 809, 'y' => 251, 'w' => 63, 'h' => 80],
        ['id' => 'b211', 'name' => 'B211', 'x' => 872, 'y' => 251, 'w' => 63, 'h' => 80],
        ['id' => 'b212r', 'name' => 'B212r', 'x' => 935, 'y' => 251, 'w' => 64, 'h' => 80],
        ['id' => 'kantin-teori', 'name' => 'Kantin Teori', 'x' => 999, 'y' => 0, 'w' => 185, 'h' => 110],
        ['id' => 'hall-timur', 'name' => '', 'x' => 999, 'y' => 110, 'w' => 110, 'h' => 221],
        ['id' => 'tangga-timur', 'name' => 'Tangga', 'x' => 1109, 'y' => 110, 'w' => 75, 'h' => 61],
        ['id' => 'void-timur', 'name' => '', 'x' => 1109, 'y' => 171, 'w' => 75, 'h' => 89],
        ['id' => 'lift', 'name' => 'Lift', 'x' => 1109, 'y' => 260, 'w' => 75, 'h' => 38],
        ['id' => 'koridor-timur-bawah', 'name' => '', 'x' => 1109, 'y' => 298, 'w' => 75, 'h' => 33],
    ];
@endphp

<div
    id="{{ $id }}"
    class="floorplan-html relative max-h-[72vh] min-h-[360px] overflow-auto overscroll-contain bg-white sm:min-h-[460px] lg:min-h-[560px]"
    data-building-key="gedung-teori"
    data-floor="{{ $floor }}"
    data-floorplan-width="4080"
    data-floorplan-height="3060"
>
    <div class="relative m-3 aspect-[4080/3060] min-w-[760px] bg-white sm:m-4 sm:min-w-[980px] lg:min-w-[1184px]">
        <div class="absolute left-[1.32%] top-[16.27%] h-[43.25%] w-[96.1%]">
            @foreach ($rooms as $room)
                <button
                    type="button"
                    class="group absolute flex items-center justify-center border-2 border-slate-950 bg-slate-200 text-center text-[11px] font-medium leading-tight text-slate-950 transition hover:z-20 hover:-translate-y-0.5 hover:bg-sky-100 hover:shadow-[0_10px_24px_rgba(2,132,199,0.28)] focus:z-20 focus:bg-sky-100 focus:outline-none focus:ring-4 focus:ring-sky-200"
                    style="left: {{ $room['x'] / 1184 * 100 }}%; top: {{ $room['y'] / 400 * 100 }}%; width: {{ $room['w'] / 1184 * 100 }}%; height: {{ $room['h'] / 400 * 100 }}%;"
                    data-room-id="{{ $room['id'] }}"
                    data-room-name="{{ $room['name'] }}"
                    aria-label="{{ $room['name'] !== '' ? $room['name'] : 'Area denah' }}"
                    title="{{ $room['name'] !== '' ? $room['name'] : 'Area denah' }}"
                >
                    @if ($room['name'] !== '')
                        <span class="px-1 group-hover:font-bold">{{ $room['name'] }}</span>
                    @endif
                </button>
            @endforeach
        </div>

        <div class="floorplan-marker-layer pointer-events-none absolute inset-0"></div>
    </div>
</div>
