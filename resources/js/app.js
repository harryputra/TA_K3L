import './bootstrap';

const LIVE_LOADING_OPACITY = '0.55';

function toUrlWithParams(form) {
    const action = form.getAttribute('action') || window.location.href;
    const url = new URL(action, window.location.origin);
    const params = new URLSearchParams(new FormData(form));
    url.search = params.toString();

    return url;
}

function setLoadingState(target, isLoading) {
    if (!target) {
        return;
    }

    target.style.opacity = isLoading ? LIVE_LOADING_OPACITY : '';
    target.style.pointerEvents = isLoading ? 'none' : '';
    target.setAttribute('aria-busy', isLoading ? 'true' : 'false');
}

async function replaceLiveTarget(url, targetSelector, historyMode = 'replace') {
    const currentTarget = document.querySelector(targetSelector);

    if (!currentTarget) {
        window.location.assign(url.toString());
        return;
    }

    setLoadingState(currentTarget, true);

    try {
        const response = await fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error(`Live request failed with status ${response.status}`);
        }

        const html = await response.text();
        const parser = new DOMParser();
        const nextDocument = parser.parseFromString(html, 'text/html');
        const nextTarget = nextDocument.querySelector(targetSelector);

        if (!nextTarget) {
            throw new Error(`Target ${targetSelector} not found in response`);
        }

        currentTarget.replaceWith(nextTarget);
        document.title = nextDocument.title || document.title;

        if (historyMode === 'push') {
            window.history.pushState({}, '', url.toString());
        } else {
            window.history.replaceState({}, '', url.toString());
        }

        initializeAutoSubmit(document);
    } catch (error) {
        window.location.assign(url.toString());
    } finally {
        const refreshedTarget = document.querySelector(targetSelector);
        setLoadingState(refreshedTarget, false);
    }
}

function initializeAutoSubmit(root = document) {
    const forms = root.querySelectorAll('[data-auto-submit-form]');

    forms.forEach((form) => {
        if (form.dataset.autoSubmitBound === 'true') {
            return;
        }

        form.dataset.autoSubmitBound = 'true';

        let timer = null;
        const debounce = Number(form.dataset.autoSubmitDelay || 350);
        const textInputs = form.querySelectorAll('input[type="text"], input[type="search"], input[type="email"], input:not([type])');
        const instantInputs = form.querySelectorAll('select, input[type="checkbox"], input[type="radio"]');

        const submitForm = (historyMode = 'replace') => {
            if ('liveSubmit' in form.dataset) {
                replaceLiveTarget(toUrlWithParams(form), form.dataset.liveTarget, historyMode);
                return;
            }

            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        };

        form.addEventListener('submit', (event) => {
            if (!('liveSubmit' in form.dataset)) {
                return;
            }

            event.preventDefault();
            submitForm('replace');
        });

        textInputs.forEach((input) => {
            input.addEventListener('input', () => {
                window.clearTimeout(timer);
                timer = window.setTimeout(() => submitForm('replace'), debounce);
            });
        });

        instantInputs.forEach((input) => {
            input.addEventListener('change', () => submitForm('replace'));
        });
    });
}

function initializeLivePagination() {
    document.addEventListener('click', (event) => {
        const paginationLink = event.target.closest('[data-live-pagination] a[href]');

        if (paginationLink) {
            const paginationContainer = paginationLink.closest('[data-live-pagination]');
            const targetSelector = paginationContainer?.dataset.liveTarget;

            if (targetSelector) {
                event.preventDefault();
                replaceLiveTarget(paginationLink.href, targetSelector, 'push');
                return;
            }
        }

        const liveLink = event.target.closest('a[data-live-link][data-live-target]');

        if (liveLink) {
            event.preventDefault();
            replaceLiveTarget(liveLink.href, liveLink.dataset.liveTarget, 'push');
        }
    });
}

function initializeDeleteConfirmation() {
    const modal = document.getElementById('delete-confirm-modal');
    const hero = document.getElementById('delete-confirm-hero');
    const badge = document.getElementById('delete-confirm-badge');
    const title = document.getElementById('delete-confirm-title');
    const message = document.getElementById('delete-confirm-message');
    const item = document.getElementById('delete-confirm-item');
    const itemCard = document.getElementById('delete-confirm-item-card');
    const submitButton = document.getElementById('delete-confirm-submit');
    const cancelButtons = modal?.querySelectorAll('[data-delete-modal-cancel], [data-delete-modal-backdrop]') || [];

    if (!modal || !hero || !badge || !title || !message || !item || !itemCard || !submitButton) {
        return;
    }

    let pendingForm = null;

    const normalHeroClass = 'bg-[linear-gradient(135deg,rgba(7,45,112,0.94),rgba(10,77,179,0.82),rgba(239,106,34,0.5))]';
    const criticalHeroClass = 'bg-[linear-gradient(135deg,rgba(127,29,29,0.98),rgba(185,28,28,0.88),rgba(239,106,34,0.6))]';

    const closeModal = () => {
        modal.classList.add('pointer-events-none', 'opacity-0');
        modal.setAttribute('aria-hidden', 'true');
        pendingForm = null;
    };

    const openModal = (form) => {
        const itemLabel = form.dataset.confirmItem || 'Data terpilih';
        const actionLabel = form.dataset.confirmAction || 'data ini';
        const severity = form.dataset.confirmSeverity || 'default';
        const isCritical = severity === 'critical';

        title.textContent = form.dataset.confirmTitle || `Hapus ${actionLabel}?`;
        message.textContent = form.dataset.confirmMessage || 'Tindakan ini tidak dapat dibatalkan. Pastikan data yang dipilih memang boleh dihapus.';
        item.textContent = itemLabel;
        pendingForm = form;

        hero.classList.remove(normalHeroClass, criticalHeroClass);
        hero.classList.add(isCritical ? criticalHeroClass : normalHeroClass);

        badge.textContent = isCritical ? 'Peringatan Penting' : 'Konfirmasi Penghapusan';
        badge.className = isCritical
            ? 'inline-flex rounded-full border border-white/20 bg-white/14 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-white'
            : 'inline-flex rounded-full border border-white/20 bg-white/12 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-white/90';

        itemCard.className = isCritical
            ? 'rounded-[1.35rem] bg-rose-50 px-5 py-4 ring-1 ring-rose-200'
            : 'rounded-[1.35rem] bg-[#f8fbff] px-5 py-4 ring-1 ring-slate-200';

        item.className = isCritical
            ? 'mt-2 text-lg font-bold text-rose-700'
            : 'mt-2 text-lg font-bold text-[var(--primary-color)]';

        submitButton.textContent = isCritical ? 'Ya, Hapus Data Penting' : 'Ya, Hapus Data';
        submitButton.className = isCritical
            ? 'inline-flex min-h-12 items-center justify-center rounded-full bg-rose-700 px-6 py-3 text-sm font-semibold text-white shadow-[0_18px_34px_rgba(190,24,93,0.24)] transition hover:bg-rose-800'
            : 'inline-flex min-h-12 items-center justify-center rounded-full bg-[var(--red)] px-6 py-3 text-sm font-semibold text-white shadow-[0_15px_30px_rgba(217,63,51,0.2)] transition hover:opacity-90';

        modal.classList.remove('pointer-events-none', 'opacity-0');
        modal.setAttribute('aria-hidden', 'false');
    };

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || form.dataset.deleteConfirmBound === 'ignore') {
            return;
        }

        const spoofMethod = form.querySelector('input[name="_method"]')?.value?.toUpperCase();
        const isDeleteForm = spoofMethod === 'DELETE' || form.dataset.confirmDelete === 'true';

        if (!isDeleteForm || form.dataset.deleteConfirmed === 'true') {
            return;
        }

        event.preventDefault();
        openModal(form);
    });

    submitButton.addEventListener('click', () => {
        if (!pendingForm) {
            return;
        }

        pendingForm.dataset.deleteConfirmed = 'true';

        if (typeof pendingForm.requestSubmit === 'function') {
            pendingForm.requestSubmit();
        } else {
            pendingForm.submit();
        }

        closeModal();
    });

    cancelButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') {
            closeModal();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initializeAutoSubmit(document);
    initializeLivePagination();
    initializeDeleteConfirmation();
});
