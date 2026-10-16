import {
    clusterIcon,
    colorFor,
    createBaseLayers,
    escapeHtml,
    hideBrokenPopupImages,
    markerIcon,
    parseJson,
    popupHtml,
    sortMarkers,
} from './geo-map';

/** Zoom level from which every marker is drawn individually. */
const SINGLE_MARKER_ZOOM = 14;
/** Grid cell size (px) used to group nearby markers into clusters. */
const CLUSTER_CELL_SIZE = 56;
const DESKTOP_QUERY = '(min-width: 1024px)';

function cardHtml(marker, typeLabel) {
    const badges = (marker.badges || [])
        .slice(0, 2)
        .map((badge) => `<span class="map-card__badge">${escapeHtml(badge)}</span>`)
        .join('');
    const initial = escapeHtml(Array.from(marker.label || '؟')[0]);
    const image = marker.cover
        ? `<img src="${escapeHtml(marker.cover)}" alt="" loading="lazy" decoding="async" referrerpolicy="no-referrer">`
        : '';

    return `
        <article class="map-card" data-map-card="${escapeHtml(marker.id)}" tabindex="0">
            <div class="map-card__media" style="--pin-color:${colorFor(marker.type)}">
                <span class="map-card__initial" aria-hidden="true">${initial}</span>
                ${image}
            </div>
            <div class="map-card__body">
                <div class="map-card__top">
                    <span class="map-card__type" style="--pin-color:${colorFor(marker.type)}">${escapeHtml(typeLabel)}</span>
                    ${badges}
                </div>
                <h3 class="map-card__title">${escapeHtml(marker.label)}</h3>
                ${marker.subtitle ? `<p class="map-card__subtitle">${escapeHtml(marker.subtitle)}</p>` : ''}
                ${marker.meta ? `<p class="map-card__meta">${escapeHtml(marker.meta)}</p>` : ''}
                <div class="map-card__actions">
                    <button type="button" class="map-card__focus" data-map-focus="${escapeHtml(marker.id)}">نمایش روی نقشه</button>
                    ${marker.url ? `<a href="${escapeHtml(marker.url)}" class="map-card__link">جزئیات</a>` : ''}
                </div>
            </div>
        </article>
    `;
}

export function initExplorer(root) {
    const L = window.L;
    const canvas = root.querySelector('[data-map-canvas]');
    const panel = root.querySelector('[data-map-panel]');
    const listEl = root.querySelector('[data-map-list]');

    if (!canvas || !listEl || !panel) {
        return;
    }

    const countEl = root.querySelector('[data-map-count]');
    const emptyEl = root.querySelector('[data-map-empty]');
    const searchInput = root.querySelector('[data-map-search]');
    const sortSelect = root.querySelector('[data-map-sort]');
    const layerButtons = [...root.querySelectorAll('[data-map-layer]')];
    const locateBtn = root.querySelector('[data-map-locate]');
    const fitBtn = root.querySelector('[data-map-fit]');
    const baseButtons = [...root.querySelectorAll('[data-map-base]')];
    const mobileToggle = root.querySelector('[data-map-mobile-toggle]');
    const desktop = window.matchMedia(DESKTOP_QUERY);

    canvas.setAttribute('dir', 'ltr');
    root.dataset.mapExplorerReady = '1';

    const typeLabels = parseJson(root.dataset.typeLabels, {});
    const allMarkers = sortMarkers(parseJson(root.dataset.markers, []));
    const markersById = new Map(allMarkers.map((marker) => [marker.id, marker]));
    const activeTypes = new Set(
        layerButtons.length ? layerButtons.map((button) => button.dataset.mapLayer) : allMarkers.map((m) => m.type),
    );

    let searchTerm = '';
    let sortMode = sortSelect?.value || 'featured';
    let selectedId = null;
    let pendingFlyId = null;
    let pendingPopupId = null;
    let hasInitialFit = false;
    let locationMarker = null;
    let listFrame = null;
    let popupTimer = null;

    const { streets, satellite } = createBaseLayers(L);
    let activeBase = streets;

    const map = L.map(canvas, {
        zoomControl: false,
        zoomSnap: 0.5,
        zoomDelta: 1,
        wheelPxPerZoomLevel: 90,
    }).setView([Number(root.dataset.centerLat || 30.2839), Number(root.dataset.centerLng || 57.0834)], 8);

    L.control.zoom({ position: 'bottomleft', zoomInTitle: 'بزرگ‌نمایی', zoomOutTitle: 'کوچک‌نمایی' }).addTo(map);
    activeBase.addTo(map);

    const markerLayer = L.layerGroup().addTo(map);
    const pins = new Map();

    /** One DOM card per marker, created once and only toggled afterwards. */
    listEl.innerHTML = allMarkers
        .map((marker) => cardHtml(marker, typeLabels[marker.type] || marker.type))
        .join('');

    const cards = new Map();
    listEl.querySelectorAll('[data-map-card]').forEach((el) => cards.set(el.dataset.mapCard, el));

    /** Failed cover images fall back to the colored initial. */
    listEl.addEventListener(
        'error',
        (event) => {
            if (event.target instanceof HTMLImageElement) {
                event.target.remove();
            }
        },
        true,
    );

    function isMapVisible() {
        return desktop.matches || root.classList.contains('show-map');
    }

    function matchesFilters(marker) {
        if (!activeTypes.has(marker.type)) {
            return false;
        }

        if (!searchTerm) {
            return true;
        }

        return [marker.label, marker.subtitle, marker.summary, marker.meta, ...(marker.badges || [])]
            .filter(Boolean)
            .join(' ')
            .toLowerCase()
            .includes(searchTerm);
    }

    function filteredMarkers() {
        return allMarkers.filter(matchesFilters);
    }

    function orderedMarkers(items) {
        if (sortMode === 'name') {
            return [...items].sort((a, b) => a.label.localeCompare(b.label, 'fa'));
        }

        if (sortMode === 'nearest') {
            const center = map.getCenter();

            return [...items].sort((a, b) => center.distanceTo([a.lat, a.lng]) - center.distanceTo([b.lat, b.lng]));
        }

        return items;
    }

    function updateList({ resetScroll = false, reorder = false } = {}) {
        const filtered = filteredMarkers();
        const bounds = hasInitialFit && isMapVisible() ? map.getBounds() : null;
        const visible = bounds ? filtered.filter((marker) => bounds.contains([marker.lat, marker.lng])) : filtered;
        const visibleIds = new Set(visible.map((marker) => marker.id));

        cards.forEach((el, id) => {
            el.hidden = !visibleIds.has(id);
        });

        if (reorder || sortMode === 'nearest') {
            const fragment = document.createDocumentFragment();
            orderedMarkers(allMarkers).forEach((marker) => fragment.appendChild(cards.get(marker.id)));
            listEl.appendChild(fragment);
        }

        if (countEl) {
            countEl.textContent = `${visible.length.toLocaleString('fa-IR')} مورد`;
        }

        emptyEl?.classList.toggle('hidden', visible.length > 0);

        if (resetScroll) {
            panel.scrollTop = 0;
        }
    }

    function scheduleListUpdate() {
        if (listFrame !== null) {
            return;
        }

        listFrame = window.requestAnimationFrame(() => {
            listFrame = null;
            updateList();
        });
    }

    function focusMembers(members) {
        const bounds = L.latLngBounds(members.map((member) => [member.lat, member.lng]));

        if (bounds.getSouthWest().equals(bounds.getNorthEast())) {
            map.setView(bounds.getCenter(), Math.min(map.getZoom() + 3, 16));

            return;
        }

        map.fitBounds(bounds, { padding: [70, 70], maxZoom: 15 });
    }

    function addSinglePin(marker) {
        const pin = L.marker([marker.lat, marker.lng], {
            icon: markerIcon(L, marker, marker.id === selectedId),
            keyboard: true,
            title: marker.label,
        });

        pin.bindPopup(popupHtml(marker, typeLabels), {
            maxWidth: 290,
            minWidth: 220,
            className: 'geo-popup-frame',
            autoPanPadding: [24, 70],
        });
        pin.on('click', () => selectMarker(marker.id, { scrollCard: true }));
        pin.addTo(markerLayer);
        pins.set(marker.id, pin);
    }

    function addCluster(members) {
        const lat = members.reduce((sum, member) => sum + member.lat, 0) / members.length;
        const lng = members.reduce((sum, member) => sum + member.lng, 0) / members.length;
        const cluster = L.marker([lat, lng], {
            icon: clusterIcon(L, members),
            keyboard: true,
            title: `${members.length} مورد`,
        });

        cluster.on('click', () => focusMembers(members));
        cluster.addTo(markerLayer);
    }

    function renderMarkers() {
        markerLayer.clearLayers();
        pins.clear();

        const items = filteredMarkers();
        const zoom = map.getZoom();

        if (zoom >= SINGLE_MARKER_ZOOM) {
            items.forEach(addSinglePin);

            return;
        }

        const cells = new Map();

        items.forEach((marker) => {
            const point = map.project([marker.lat, marker.lng], zoom);
            const key = `${Math.floor(point.x / CLUSTER_CELL_SIZE)}:${Math.floor(point.y / CLUSTER_CELL_SIZE)}`;

            if (!cells.has(key)) {
                cells.set(key, []);
            }

            cells.get(key).push(marker);
        });

        cells.forEach((members) => (members.length === 1 ? addSinglePin(members[0]) : addCluster(members)));
    }

    function selectMarker(id, { scrollCard = false } = {}) {
        if (selectedId) {
            pins.get(selectedId)?.getElement()?.classList.remove('is-active');
            cards.get(selectedId)?.classList.remove('is-active');
        }

        selectedId = id;
        pins.get(id)?.getElement()?.classList.add('is-active');

        const card = cards.get(id);
        card?.classList.add('is-active');

        if (scrollCard && card && !card.hidden) {
            card.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });
        }
    }

    function openPendingPopup() {
        window.clearTimeout(popupTimer);

        if (pendingPopupId) {
            const id = pendingPopupId;
            pendingPopupId = null;
            pins.get(id)?.openPopup();
        }
    }

    function flyToMarker(id) {
        const marker = markersById.get(id);

        if (!marker) {
            return;
        }

        pendingPopupId = id;
        map.flyTo([marker.lat, marker.lng], Math.max(map.getZoom(), SINGLE_MARKER_ZOOM), { duration: 0.6 });
        window.clearTimeout(popupTimer);
        popupTimer = window.setTimeout(openPendingPopup, 1100);
    }

    function fitAll({ animate = true } = {}) {
        const items = filteredMarkers();

        if (items.length === 0) {
            return;
        }

        const bounds = L.latLngBounds(items.map((item) => [item.lat, item.lng]));
        map.fitBounds(bounds, { padding: [48, 48], maxZoom: 12, animate });
    }

    function setMobileMapOpen(open) {
        root.classList.toggle('show-map', open);

        if (mobileToggle) {
            mobileToggle.textContent = open ? 'بازگشت به لیست' : 'مشاهده نقشه';
        }

        updateList({ resetScroll: true });
    }

    layerButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const type = button.dataset.mapLayer;

            if (activeTypes.has(type)) {
                if (activeTypes.size === 1) {
                    return;
                }

                activeTypes.delete(type);
            } else {
                activeTypes.add(type);
            }

            const isActive = activeTypes.has(type);
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', String(isActive));
            renderMarkers();
            updateList({ resetScroll: true });
        });
    });

    let searchTimer = null;
    searchInput?.addEventListener('input', (event) => {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(() => {
            searchTerm = String(event.target.value || '')
                .trim()
                .toLowerCase();
            renderMarkers();
            updateList({ resetScroll: true });
        }, 140);
    });

    sortSelect?.addEventListener('change', (event) => {
        sortMode = event.target.value;
        updateList({ reorder: true, resetScroll: true });
    });

    function handleCardActivation(event) {
        const card = event.target.closest('[data-map-card]');

        if (!card || event.target.closest('a')) {
            return;
        }

        const id = card.dataset.mapCard;
        selectMarker(id);

        if (!isMapVisible()) {
            pendingFlyId = id;
            setMobileMapOpen(true);

            return;
        }

        flyToMarker(id);
    }

    listEl.addEventListener('click', handleCardActivation);
    listEl.addEventListener('keydown', (event) => {
        if ((event.key === 'Enter' || event.key === ' ') && event.target.matches('[data-map-card]')) {
            event.preventDefault();
            handleCardActivation(event);
        }
    });

    locateBtn?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            return;
        }

        const label = locateBtn.textContent;
        locateBtn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            ({ coords }) => {
                locationMarker?.remove();
                locationMarker = L.circleMarker([coords.latitude, coords.longitude], {
                    radius: 8,
                    color: '#fff',
                    weight: 3,
                    fillColor: '#2563eb',
                    fillOpacity: 1,
                }).addTo(map);
                map.flyTo([coords.latitude, coords.longitude], 12, { duration: 0.6 });
                locateBtn.disabled = false;
            },
            () => {
                locateBtn.textContent = 'دسترسی به موقعیت ممکن نشد';
                locateBtn.disabled = false;
                window.setTimeout(() => (locateBtn.textContent = label), 2500);
            },
            { enableHighAccuracy: true, timeout: 8000 },
        );
    });

    fitBtn?.addEventListener('click', () => {
        if (isMapVisible()) {
            fitAll();
        }
    });

    baseButtons.forEach((button) => {
        button.addEventListener('click', () => {
            map.removeLayer(activeBase);
            activeBase = button.dataset.mapBase === 'satellite' ? satellite : streets;
            activeBase.addTo(map);
            baseButtons.forEach((item) => item.classList.toggle('is-active', item === button));
        });
    });

    mobileToggle?.addEventListener('click', () => setMobileMapOpen(!root.classList.contains('show-map')));

    map.on('zoomend', renderMarkers);
    map.on('moveend', () => {
        openPendingPopup();
        scheduleListUpdate();
    });
    map.on('popupopen', (event) => hideBrokenPopupImages(event.popup));

    /**
     * The only place that tells Leaflet about size changes. Runs once the map
     * has real dimensions, which also covers the hidden-on-mobile case.
     */
    function handleResize() {
        map.invalidateSize({ animate: false, pan: false });

        const size = map.getSize();

        if (size.x === 0 || size.y === 0) {
            return;
        }

        if (!hasInitialFit) {
            hasInitialFit = true;
            fitAll({ animate: false });
            renderMarkers();
            updateList();
        } else {
            scheduleListUpdate();
        }

        if (pendingFlyId) {
            const id = pendingFlyId;
            pendingFlyId = null;
            flyToMarker(id);
        }
    }

    if (typeof ResizeObserver === 'function') {
        new ResizeObserver(handleResize).observe(canvas);
    } else {
        window.addEventListener('resize', handleResize);
        window.setTimeout(handleResize, 0);
    }

    desktop.addEventListener('change', () => updateList({ resetScroll: true }));

    renderMarkers();
    updateList();
}
