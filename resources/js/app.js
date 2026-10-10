import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const collectionCurrency = new Intl.NumberFormat(undefined, {
    style: 'currency',
    currency: 'USD',
});

async function refreshCollections(url, updateHistory = true) {
    const currentSection = document.getElementById('payment-collections');

    if (!currentSection) {
        return;
    }

    const errorMessage = currentSection.querySelector('[data-collection-error]');
    const applyButton = currentSection.querySelector('form button[type="submit"], form button:not([type])');
    currentSection.setAttribute('aria-busy', 'true');
    if (applyButton) {
        applyButton.disabled = true;
        applyButton.textContent = 'Loading…';
    }
    if (errorMessage) {
        errorMessage.classList.add('hidden');
        errorMessage.textContent = '';
    }

    try {
        const response = await fetch(url, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            if (response.headers.get('content-type')?.includes('application/json')) {
                const result = await response.json();
                const messages = Object.values(result.errors ?? {}).flat();
                throw new Error(messages.join(' ') || 'Unable to load payment collections.');
            }

            throw new Error('Unable to load payment collections.');
        }

        const html = await response.text();
        const documentFragment = new DOMParser().parseFromString(html, 'text/html');
        const updatedSection = documentFragment.getElementById('payment-collections');

        if (!updatedSection) {
            throw new Error('The payment collections response was incomplete.');
        }

        currentSection.replaceWith(updatedSection);
        const summary = document.querySelector('[data-collection-summary-value]');
        if (summary) {
            summary.textContent = collectionCurrency.format(Number(updatedSection.dataset.total));
        }
        if (updateHistory) {
            window.history.pushState({}, '', url);
        }
    } catch (error) {
        const messageTarget = currentSection.querySelector('[data-collection-error]');
        if (messageTarget) {
            messageTarget.textContent = error instanceof Error ? error.message : 'Unable to load payment collections.';
            messageTarget.classList.remove('hidden');
        }
    } finally {
        const section = document.getElementById('payment-collections');
        const button = section?.querySelector('form button[type="submit"], form button:not([type])');
        section?.removeAttribute('aria-busy');
        if (button) {
            button.disabled = false;
            button.textContent = 'Apply filters';
        }
    }
}

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || !form.closest('#payment-collections')) {
        return;
    }

    event.preventDefault();
    const url = new URL(form.action, window.location.origin);
    new FormData(form).forEach((value, key) => {
        if (typeof value === 'string' && value !== '') {
            url.searchParams.set(key, value);
        }
    });
    url.searchParams.delete('all_dates');
    url.searchParams.delete('page');
    refreshCollections(url, true);
});

document.addEventListener('click', (event) => {
    const link = event.target instanceof Element ? event.target.closest('#payment-collections a') : null;

    if (!link) {
        return;
    }

    const isAllDates = link.hasAttribute('data-collection-all-dates');
    const isPagination = Boolean(link.closest('#payment-collections nav') && new URL(link.href).searchParams.has('page'));

    if (!isAllDates && !isPagination) {
        return;
    }

    event.preventDefault();
    refreshCollections(link.href, true);
});

window.addEventListener('popstate', () => {
    if (document.getElementById('payment-collections')) {
        refreshCollections(window.location.href, false);
    }
});
