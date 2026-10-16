export const TYPE_COLORS = {
    destination: '#d4a017',
    club: '#0b1f3a',
    shop: '#1a7a4c',
    accommodation: '#2563eb',
    shelter: '#b45309',
    program: '#c026d3',
};

export const FALLBACK_COLOR = '#0b1f3a';

const TYPE_ORDER = {
    destination: 0,
    accommodation: 1,
    shelter: 2,
    program: 3,
    club: 4,
    shop: 5,
};

export function parseJson(value, fallback) {
    try {
        return JSON.parse(value || '');
    } catch {
        return fallback;
    }
}

export function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

export function waitForLeaflet(callback) {
    if (typeof window.L !== 'undefined') {
        callback();

        return;
    }

    let attempts = 0;
    const timer = window.setInterval(() => {
        attempts += 1;

        if (typeof window.L !== 'undefined') {
            window.clearInterval(timer);
            callback();
        } else if (attempts > 120) {
            window.clearInterval(timer);
        }
    }, 50);
}

export function createBaseLayers(L) {
    const streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        keepBuffer: 4,
        attribution: '&copy; OpenStreetMap',
    });

    const satellite = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
            maxZoom: 18,
            keepBuffer: 4,
            attribution: 'Tiles &copy; Esri',
        },
    );

    return { streets, satellite };
}

export function colorFor(type) {
    return TYPE_COLORS[type] || FALLBACK_COLOR;
}

export function markerIcon(L, marker, active = false) {
    const label = escapeHtml(marker.label || '');

    return L.divIcon({
        className: `geo-pin${active ? ' is-active' : ''}`,
        html: `
            <div class="geo-pin__wrap" style="--pin-color:${colorFor(marker.type)}">
                <span class="geo-pin__dot"></span>
                <span class="geo-pin__label">${label}</span>
            </div>
        `,
        iconSize: [0, 0],
        iconAnchor: [0, 0],
    });
}

export function clusterIcon(L, members) {
    const types = new Set(members.map((member) => member.type));
    const color = types.size === 1 ? colorFor(members[0].type) : FALLBACK_COLOR;
    const count = members.length;
    const size = count > 9 ? 36 : 32;

    return L.divIcon({
        className: 'geo-cluster-wrap',
        html: `<div class="geo-cluster${count > 9 ? ' geo-cluster--lg' : ''}" style="--pin-color:${color}"><span>${count.toLocaleString('fa-IR')}</span></div>`,
        iconSize: [size, size],
        iconAnchor: [size / 2, size / 2],
    });
}

export function popupHtml(marker, typeLabels) {
    const typeLabel = typeLabels[marker.type] || marker.type;
    const badges = (marker.badges || [])
        .map((badge) => `<span class="geo-popup__badge">${escapeHtml(badge)}</span>`)
        .join('');
    const summary = marker.summary ? `<p class="geo-popup__summary">${escapeHtml(marker.summary)}</p>` : '';
    const meta = marker.meta ? `<p class="geo-popup__meta">${escapeHtml(marker.meta)}</p>` : '';
    const link = marker.url
        ? `<a class="geo-popup__link" href="${escapeHtml(marker.url)}">مشاهده جزئیات</a>`
        : '';
    const cover = marker.cover
        ? `<img class="geo-popup__cover" src="${escapeHtml(marker.cover)}" alt="" loading="lazy" referrerpolicy="no-referrer">`
        : '';

    return `
        <div class="geo-popup" dir="rtl">
            ${cover}
            <div class="geo-popup__body">
                <div class="geo-popup__badges">${badges}</div>
                <strong class="geo-popup__title">${escapeHtml(marker.label)}</strong>
                <span class="geo-popup__type">${escapeHtml(typeLabel)}${marker.subtitle ? ` · ${escapeHtml(marker.subtitle)}` : ''}</span>
                ${summary}
                ${meta}
                ${link}
            </div>
        </div>
    `;
}

/**
 * Remove a popup cover that failed to load so no broken-image icon shows.
 */
export function hideBrokenPopupImages(popup) {
    popup
        .getElement()
        ?.querySelectorAll('img')
        .forEach((img) => {
            img.addEventListener('error', () => img.remove(), { once: true });
        });
}

export function sortMarkers(markers) {
    return [...markers].sort((a, b) => {
        const typeDelta = (TYPE_ORDER[a.type] ?? 99) - (TYPE_ORDER[b.type] ?? 99);

        if (typeDelta !== 0) {
            return typeDelta;
        }

        const featuredDelta = Number(Boolean(b.featured)) - Number(Boolean(a.featured));

        if (featuredDelta !== 0) {
            return featuredDelta;
        }

        return Number(b.sort || 0) - Number(a.sort || 0);
    });
}

export function initSimpleMap(root) {
    const L = window.L;
    const canvas = root.querySelector('[data-geo-map-canvas]');

    if (!canvas) {
        return;
    }

    canvas.setAttribute('dir', 'ltr');
    root.dataset.geoMapReady = '1';

    const markers = parseJson(root.dataset.markers, []);
    const typeLabels = parseJson(root.dataset.typeLabels, {});
    const { streets } = createBaseLayers(L);

    const map = L.map(canvas, { zoomControl: true, attributionControl: true }).setView(
        [Number(root.dataset.centerLat), Number(root.dataset.centerLng)],
        Number(root.dataset.zoom || 8),
    );

    streets.addTo(map);

    const bounds = [];

    markers.forEach((marker) => {
        const pin = L.marker([marker.lat, marker.lng], {
            icon: markerIcon(L, marker),
            keyboard: true,
            title: marker.label,
        }).addTo(map);

        pin.bindPopup(popupHtml(marker, typeLabels), { maxWidth: 280, className: 'geo-popup-frame' });
        bounds.push([marker.lat, marker.lng]);
    });

    map.on('popupopen', (event) => hideBrokenPopupImages(event.popup));

    const fit = () => {
        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [36, 36], animate: false });
        } else if (bounds.length === 1) {
            map.setView(bounds[0], Number(root.dataset.zoom || 11), { animate: false });
        }
    };

    if (typeof ResizeObserver === 'function') {
        let fitted = false;

        new ResizeObserver(() => {
            map.invalidateSize({ animate: false, pan: false });

            const size = map.getSize();

            if (!fitted && size.x > 0 && size.y > 0) {
                fitted = true;
                fit();
            }
        }).observe(canvas);
    } else {
        fit();
    }
}
