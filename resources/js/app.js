import { initSimpleMap, waitForLeaflet } from './geo-map';
import { initExplorer } from './map-explorer';

function bootGeoMaps() {
    document.querySelectorAll('[data-geo-map]:not([data-geo-map-ready])').forEach(initSimpleMap);
    document.querySelectorAll('[data-map-explorer]:not([data-map-explorer-ready])').forEach(initExplorer);
}

waitForLeaflet(() => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootGeoMaps);
    } else {
        bootGeoMaps();
    }
});
