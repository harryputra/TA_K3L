<div id="delete-confirm-modal"
    class="pointer-events-none fixed inset-0 z-[120] flex items-center justify-center px-4 opacity-0 transition duration-200"
    aria-hidden="true">
    <div data-delete-modal-backdrop class="absolute inset-0 bg-slate-950/45 backdrop-blur-[2px]"></div>
    <div
        class="relative z-10 w-full max-w-xl overflow-hidden rounded-[2rem] border border-white/70 bg-white/96 shadow-[0_30px_80px_rgba(15,23,42,0.28)] ring-1 ring-[var(--primary-color)]/8">
        <div
            id="delete-confirm-hero"
            class="bg-[linear-gradient(135deg,rgba(7,45,112,0.94),rgba(10,77,179,0.82),rgba(239,106,34,0.5))] px-6 py-6 text-white lg:px-7">
            <span
                id="delete-confirm-badge"
                class="inline-flex rounded-full border border-white/20 bg-white/12 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-white/90">
                Konfirmasi Penghapusan
            </span>
            <h3 id="delete-confirm-title" class="mt-4 text-2xl font-bold lg:text-3xl">Hapus data ini?</h3>
            <p id="delete-confirm-message" class="mt-3 text-sm leading-7 text-white/88 lg:text-base">
                Tindakan ini akan menghapus data penting. Pastikan Anda benar-benar ingin melanjutkan.
            </p>
        </div>

        <div class="px-6 py-6 lg:px-7 lg:py-7">
            <div id="delete-confirm-item-card" class="rounded-[1.35rem] bg-[#f8fbff] px-5 py-4 ring-1 ring-slate-200">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Data yang dipilih</p>
                <p id="delete-confirm-item" class="mt-2 text-lg font-bold text-[var(--primary-color)]">Item terpilih</p>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button type="button" data-delete-modal-cancel
                    class="inline-flex min-h-12 items-center justify-center rounded-full border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" id="delete-confirm-submit"
                    class="inline-flex min-h-12 items-center justify-center rounded-full bg-[var(--red)] px-6 py-3 text-sm font-semibold text-white shadow-[0_15px_30px_rgba(217,63,51,0.2)] transition hover:opacity-90">
                    Ya, Hapus Data
                </button>
            </div>
        </div>
    </div>
</div>
