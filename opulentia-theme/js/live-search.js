/**
 * Live Search for Opulentia theme
 *
 * - Debounced AJAX search (300ms)
 * - Results dropdown with posts, pages, products
 * - Keyboard navigation (arrow keys, enter, escape)
 * - Click-outside to close
 * - Loading, no-results, and error states
 * - Accessibility (ARIA live region, role listbox)
 *
 * @package Opulentia
 */

(function () {
    'use strict';

    var searchInputs = [];
    var searchResultsContainers = [];
    var activeIndex = -1;
    var searchTimeout = null;
    var currentResults = [];
    var abortController = null;

    /**
     * Initialize live search on all matching inputs.
     */
    function init() {
        if (typeof OpulentiaLiveSearch === 'undefined') {
            return;
        }

        var inputs = document.querySelectorAll(
            '.search-panel__input, ' +
            '.search-form__input, ' +
            '.header-search__input, ' +
            '.live-search-input'
        );

        inputs.forEach(function (input) {
            if (searchInputs.indexOf(input) !== -1) return;

            searchInputs.push(input);
            var container = getResultsContainer(input);
            if (container) {
                searchResultsContainers.push(container);
                container.setAttribute('role', 'listbox');
                container.setAttribute('aria-label', 'Search results');
            }

            // Input handler with debounce
            input.addEventListener('input', onInputChange.bind(null, input));

            // Keyboard navigation
            input.addEventListener('keydown', onKeyDown.bind(null, input));

            // Focus/blur handling for click-outside
            input.addEventListener('focus', onFocus.bind(null, input));
        });

        // Global click handler for click-outside
        document.addEventListener('click', function (e) {
            searchInputs.forEach(function (input) {
                var container = getResultsContainer(input);
                if (!container) return;

                var isClickInside = input.contains(e.target) ||
                    (container && container.contains(e.target));

                if (!isClickInside && container.classList.contains('has-results')) {
                    container.classList.remove('has-results');
                    container.innerHTML = '';
                    activeIndex = -1;
                    input.setAttribute('aria-expanded', 'false');
                }
            });
        });

        // Close on Escape (global)
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                searchInputs.forEach(function (input) {
                    var container = getResultsContainer(input);
                    if (container && container.classList.contains('has-results')) {
                        container.classList.remove('has-results');
                        container.innerHTML = '';
                        activeIndex = -1;
                        input.setAttribute('aria-expanded', 'false');
                        input.blur();
                    }
                });
            }
        });
    }

    /**
     * Find the results container for a given input.
     *
     * @param {HTMLElement} input Search input element.
     * @return {HTMLElement|null}
     */
    function getResultsContainer(input) {
        var form = input.closest('.search-panel__form, .search-form, .header-search');
        if (!form) return null;

        return form.querySelector(
            '.live-search-results, ' +
            '#live-search-results, ' +
            '.search-results'
        );
    }

    /**
     * Handle input change with debounce.
     *
     * @param {HTMLElement} input Search input element.
     */
    function onInputChange(input) {
        var term = input.value.trim();

        if (searchTimeout) {
            clearTimeout(searchTimeout);
            searchTimeout = null;
        }

        if (term.length < 2) {
            var container = getResultsContainer(input);
            if (container) {
                container.innerHTML = '';
                container.classList.remove('has-results');
                input.setAttribute('aria-expanded', 'false');
            }
            currentResults = [];
            activeIndex = -1;
            return;
        }

        searchTimeout = setTimeout(function () {
            performSearch(input, term);
        }, 300);
    }

    /**
     * Perform the AJAX search request.
     *
     * @param {HTMLElement} input Search input element.
     * @param {string}      term  Search term.
     */
    function performSearch(input, term) {
        var container = getResultsContainer(input);
        if (!container) return;

        // Show loading state.
        container.innerHTML = '<div class="live-search__status live-search__loading">' +
            '<span class="live-search__spinner"></span> ' +
            'Searching...</div>';
        container.classList.add('has-results', 'is-loading');
        input.setAttribute('aria-expanded', 'true');

        // Cancel any pending requests.
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();

        var url = OpulentiaLiveSearch.ajaxUrl +
            '?action=Opulentia_live_search' +
            '&search=' + encodeURIComponent(term);

        if (OpulentiaLiveSearch.nonce) {
            url += '&nonce=' + encodeURIComponent(OpulentiaLiveSearch.nonce);
        }

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            signal: abortController.signal
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(function (data) {
                renderResults(input, container, data);
            })
            .catch(function (err) {
                if (err.name === 'AbortError') return;
                renderError(container);
            });
    }

    /**
     * Render search results in the container.
     *
     * @param {HTMLElement} input     Search input.
     * @param {HTMLElement} container Results container.
     * @param {Object}      data      Response data from AJAX.
     */
    function renderResults(input, container, data) {
        container.classList.remove('is-loading');
        activeIndex = -1;

        if (!data.success || !data.data || !data.data.results || data.data.results.length === 0) {
            container.innerHTML = '<div class="live-search__status live-search__no-results">' +
                'No results found for "' + escHtml(input.value.trim()) + '".</div>';
            currentResults = [];
            return;
        }

        currentResults = data.data.results;
        var html = '<ul class="live-search__results-list" role="listbox">';

        currentResults.forEach(function (item, index) {
            html += '<li role="option" aria-selected="false" data-index="' + index + '" class="live-search__result-item">';
            html += '<a href="' + escUrl(item.url) + '" class="live-search__result-link" tabindex="-1">';

            if (item.image) {
                html += '<span class="live-search__result-image-wrap">' +
                    '<img src="' + escUrl(item.image) + '" alt="" class="live-search__result-image">' +
                    '</span>';
            }

            html += '<span class="live-search__result-content">';
            html += '<span class="live-search__result-title">' + escHtml(item.title) + '</span>';

            if (item.price) {
                html += '<span class="live-search__result-price">' + item.price + '</span>';
            }

            if (item.excerpt) {
                html += '<span class="live-search__result-excerpt">' + escHtml(item.excerpt) + '</span>';
            }

            html += '<span class="live-search__result-type">' + escHtml(item.type) + '</span>';
            html += '</span>';
            html += '</a>';
            html += '</li>';
        });

        html += '</ul>';
        container.innerHTML = html;
        input.setAttribute('aria-expanded', 'true');
    }

    /**
     * Render error state.
     *
     * @param {HTMLElement} container Results container.
     */
    function renderError(container) {
        container.classList.remove('is-loading');
        container.innerHTML = '<div class="live-search__status live-search__error">' +
            'Something went wrong. Please try again.</div>';
    }

    /**
     * Handle keyboard navigation in search results.
     *
     * @param {HTMLElement} input Search input.
     * @param {Event}       event Keyboard event.
     */
    function onKeyDown(input, event) {
        var container = getResultsContainer(input);
        if (!container || !container.classList.contains('has-results')) {
            return;
        }

        var items = container.querySelectorAll('.live-search__result-item');

        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();
                navigateTo(container, items, activeIndex + 1);
                break;

            case 'ArrowUp':
                event.preventDefault();
                navigateTo(container, items, activeIndex - 1);
                break;

            case 'Enter':
                event.preventDefault();
                if (activeIndex >= 0 && activeIndex < items.length) {
                    var link = items[activeIndex].querySelector('.live-search__result-link');
                    if (link && link.href) {
                        window.location.href = link.href;
                    }
                } else {
                    // Submit the parent form if no item selected.
                    var form = input.closest('form');
                    if (form) form.submit();
                }
                break;

            case 'Escape':
                event.preventDefault();
                container.classList.remove('has-results');
                container.innerHTML = '';
                activeIndex = -1;
                input.setAttribute('aria-expanded', 'false');
                input.blur();
                break;
        }
    }

    /**
     * Navigate to a specific result item.
     *
     * @param {HTMLElement} container Results container.
     * @param {NodeList}    items     Result item nodes.
     * @param {number}      index     Target index.
     */
    function navigateTo(container, items, index) {
        if (items.length === 0) return;

        // Clamp index.
        if (index < 0) index = items.length - 1;
        if (index >= items.length) index = 0;

        // Remove previous selection.
        if (activeIndex >= 0 && activeIndex < items.length) {
            items[activeIndex].classList.remove('is-selected');
            items[activeIndex].setAttribute('aria-selected', 'false');
        }

        activeIndex = index;

        // Add new selection.
        items[activeIndex].classList.add('is-selected');
        items[activeIndex].setAttribute('aria-selected', 'true');

        // Scroll into view if needed.
        var containerRect = container.getBoundingClientRect();
        var itemRect = items[activeIndex].getBoundingClientRect();

        if (itemRect.bottom > containerRect.bottom) {
            items[activeIndex].scrollIntoView({ block: 'nearest' });
        } else if (itemRect.top < containerRect.top) {
            items[activeIndex].scrollIntoView({ block: 'nearest' });
        }

        // Update input value with selected item title.
        if (currentResults[activeIndex]) {
            // Don't change input value — just announce selection for screen readers.
            input.setAttribute('aria-activedescendant', 'live-search-item-' + activeIndex);
        }
    }

    /**
     * Handle input focus — expand results if they exist.
     *
     * @param {HTMLElement} input Search input.
     */
    function onFocus(input) {
        var container = getResultsContainer(input);
        if (container && container.innerHTML.trim() !== '') {
            container.classList.add('has-results');
            input.setAttribute('aria-expanded', 'true');
        }
    }

    /**
     * Escape HTML entities.
     *
     * @param {string} str Input string.
     * @return {string} Escaped string.
     */
    function escHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    /**
     * Escape URL for safe attribute insertion.
     *
     * @param {string} url Input URL.
     * @return {string} Escaped URL.
     */
    function escUrl(url) {
        return url.replace(/"/g, '%22').replace(/'/g, '%27');
    }

    // Initialize on DOM ready.
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
