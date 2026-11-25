/*
 * Reusable map location picker.
 * Uses Google Maps when a browser key is supplied, otherwise Leaflet + OpenStreetMap.
 *
 * DeliveryMap.init({
 *   el, provider: 'google'|'osm', googleKey, center: {lat,lng}, zoom,
 *   initial: {lat,lng}|null, startPoint: {lat,lng,name}|null,
 *   searchInput, searchButton, locateButton, onPick(lat, lng)
 * }) -> Promise<{ place(lat,lng,fire), setView(lat,lng) }>
 */
(function () {
    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            var s = document.createElement('script');
            s.src = src; s.async = true; s.onload = resolve; s.onerror = reject;
            document.head.appendChild(s);
        });
    }
    function loadCss(href) {
        var l = document.createElement('link');
        l.rel = 'stylesheet'; l.href = href;
        document.head.appendChild(l);
    }

    function wireLocate(opts, api) {
        if (!opts.locateButton) return;
        opts.locateButton.addEventListener('click', function () {
            if (!navigator.geolocation) { alert('Your browser does not support location access.'); return; }
            navigator.geolocation.getCurrentPosition(function (pos) {
                api.setView(pos.coords.latitude, pos.coords.longitude);
                api.place(pos.coords.latitude, pos.coords.longitude, true);
            }, function () {
                alert('Could not get your location. Please allow location access or pin it on the map.');
            }, { enableHighAccuracy: true, timeout: 10000 });
        });
    }

    async function initLeaflet(opts) {
        if (!window.L) {
            loadCss('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
            await loadScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
        }
        var map = L.map(opts.el).setView([opts.center.lat, opts.center.lng], opts.zoom || 12);
        // On touch screens one finger should scroll the page, two fingers move/zoom the map.
        if (L.Browser.mobile) { map.dragging.disable(); }
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19, attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        if (opts.startPoint) {
            L.circleMarker([opts.startPoint.lat, opts.startPoint.lng], { radius: 8, color: '#047857', fillColor: '#10b981', fillOpacity: 0.9 })
                .addTo(map).bindTooltip(opts.startPoint.name || 'Shop', { permanent: false });
        }

        var marker = null;
        var api = {
            setView: function (lat, lng) { map.setView([lat, lng], 16); },
            place: function (lat, lng, fire) {
                if (marker) { marker.setLatLng([lat, lng]); }
                else {
                    marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                    marker.on('dragend', function () { var p = marker.getLatLng(); opts.onPick(p.lat, p.lng); });
                }
                if (fire) opts.onPick(lat, lng);
            }
        };
        map.on('click', function (e) { api.place(e.latlng.lat, e.latlng.lng, true); });
        if (opts.initial) api.place(opts.initial.lat, opts.initial.lng, false);

        async function search() {
            var q = (opts.searchInput.value || '').trim();
            if (!q) return;
            try {
                var res = await fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&countrycodes=lk&q=' + encodeURIComponent(q));
                var data = await res.json();
                if (!data.length) { alert('Location not found. Try a nearby town or pin it on the map.'); return; }
                var lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
                api.setView(lat, lng);
                api.place(lat, lng, true);
            } catch (e) { alert('Search failed. Please pin your location on the map.'); }
        }
        if (opts.searchInput) {
            opts.searchInput.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); search(); } });
        }
        if (opts.searchButton) opts.searchButton.addEventListener('click', search);
        wireLocate(opts, api);
        return api;
    }

    async function initGoogle(opts) {
        if (!window.google || !window.google.maps) {
            await new Promise(function (resolve, reject) {
                window.__dmGoogleReady = resolve;
                var s = document.createElement('script');
                s.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(opts.googleKey) + '&libraries=places&callback=__dmGoogleReady';
                s.async = true; s.onerror = reject;
                document.head.appendChild(s);
            });
        }
        var map = new google.maps.Map(opts.el, {
            center: opts.center, zoom: opts.zoom || 12, streetViewControl: false, mapTypeControl: false, gestureHandling: 'cooperative'
        });
        if (opts.startPoint) {
            new google.maps.Marker({
                position: opts.startPoint, map: map, title: opts.startPoint.name || 'Shop',
                icon: { path: google.maps.SymbolPath.CIRCLE, scale: 8, fillColor: '#10b981', fillOpacity: 0.9, strokeColor: '#047857', strokeWeight: 2 }
            });
        }
        var marker = null;
        var api = {
            setView: function (lat, lng) { map.setCenter({ lat: lat, lng: lng }); map.setZoom(16); },
            place: function (lat, lng, fire) {
                var pos = { lat: lat, lng: lng };
                if (marker) { marker.setPosition(pos); }
                else {
                    marker = new google.maps.Marker({ position: pos, map: map, draggable: true });
                    marker.addListener('dragend', function () { var p = marker.getPosition(); opts.onPick(p.lat(), p.lng()); });
                }
                if (fire) opts.onPick(lat, lng);
            }
        };
        map.addListener('click', function (e) { api.place(e.latLng.lat(), e.latLng.lng(), true); });
        if (opts.initial) api.place(opts.initial.lat, opts.initial.lng, false);

        if (opts.searchInput) {
            var ac = new google.maps.places.Autocomplete(opts.searchInput, { componentRestrictions: { country: 'lk' }, fields: ['geometry'] });
            ac.addListener('place_changed', function () {
                var p = ac.getPlace();
                if (!p.geometry) return;
                var lat = p.geometry.location.lat(), lng = p.geometry.location.lng();
                api.setView(lat, lng);
                api.place(lat, lng, true);
            });
            opts.searchInput.addEventListener('keydown', function (e) { if (e.key === 'Enter') e.preventDefault(); });
        }
        wireLocate(opts, api);
        return api;
    }

    window.DeliveryMap = {
        init: function (opts) {
            return opts.provider === 'google' && opts.googleKey ? initGoogle(opts) : initLeaflet(opts);
        }
    };
})();
