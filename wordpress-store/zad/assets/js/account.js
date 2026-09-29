/**
 * حساب التاجر: التبويبات، واختيار المنطقة، وتحديد موقع المحل على الخريطة.
 */
(function () {
	'use strict';

	var doc = document;
	var A = window.ZAD_ACCOUNT || {};
	var ISTANBUL = [41.02, 28.96];

	/* التبويبات: «حساب جديد» و«لدي حساب» */
	var auth = doc.querySelector('[data-zd-auth]');
	var mapReady = [];
	function showTab(name, focus) {
		if (!auth) { return; }
		auth.querySelectorAll('[data-zd-auth-tab]').forEach(function (t) {
			var on = t.getAttribute('data-zd-auth-tab') === name;
			t.classList.toggle('is-active', on);
			t.setAttribute('aria-selected', on ? 'true' : 'false');
			t.setAttribute('tabindex', on ? '0' : '-1');
			var panel = doc.getElementById(t.getAttribute('aria-controls'));
			if (panel) { panel.hidden = !on; }
			if (on && focus) { t.focus(); }
		});
		if (name === 'register') { mapReady.forEach(function (fn) { fn(); }); }
		try { history.replaceState(null, '', updateQuery('tab', name)); } catch (e) { /* لا شيء */ }
	}
	function updateQuery(key, val) {
		var u = new URL(window.location.href);
		u.searchParams.set(key, val);
		return u.pathname + u.search + u.hash;
	}
	if (auth) {
		var tabs = Array.prototype.slice.call(auth.querySelectorAll('[data-zd-auth-tab]'));
		tabs.forEach(function (t) {
			t.setAttribute('tabindex', t.classList.contains('is-active') ? '0' : '-1');
			t.addEventListener('click', function (e) {
				e.preventDefault();
				showTab(t.getAttribute('data-zd-auth-tab'));
			});
			t.addEventListener('keydown', function (e) {
				var i = tabs.indexOf(t);
				// في RTL السهم الأيسر يعني التالي.
				if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
					e.preventDefault();
					var dir = (e.key === 'ArrowLeft') === (doc.dir === 'rtl' || doc.documentElement.dir === 'rtl') ? 1 : -1;
					var next = tabs[(i + dir + tabs.length) % tabs.length];
					showTab(next.getAttribute('data-zd-auth-tab'), true);
				}
			});
		});
	}

	/* حقول المحل: المنطقة + الخريطة */
	doc.querySelectorAll('[data-zd-register]').forEach(function (form) {
		var district = form.querySelector('[data-zd-district]');
		var cityBox = form.querySelector('[data-zd-city]');
		var cityInput = cityBox ? cityBox.querySelector('input') : null;
		var mapEl = form.querySelector('[data-zd-map]');
		var latIn = form.querySelector('[data-zd-lat]');
		var lngIn = form.querySelector('[data-zd-lng]');
		var status = form.querySelector('[data-zd-loc-status]');
		var gps = form.querySelector('[data-zd-gps]');
		var locate = form.querySelector('[data-zd-locate]');
		var map = null;
		var marker = null;

		function hasPin() { return latIn && latIn.value !== '' && lngIn.value !== ''; }
		function say(text, isError) {
			if (!status) { return; }
			status.textContent = text;
			status.classList.toggle('is-error', !!isError);
			status.classList.toggle('is-ok', !isError && hasPin());
		}
		function districtCenter() {
			var d = district && A.districts ? A.districts[district.value] : null;
			return d ? d : null;
		}
		function pinIcon() {
			return window.L.divIcon({ className: 'zd-pin', html: '<span></span>', iconSize: [30, 40], iconAnchor: [15, 38] });
		}
		function setPin(lat, lng, zoom, note) {
			latIn.value = (+lat).toFixed(6);
			lngIn.value = (+lng).toFixed(6);
			if (map) {
				if (!marker) {
					marker = window.L.marker([lat, lng], { draggable: true, icon: pinIcon(), keyboard: false }).addTo(map);
					marker.on('dragend', function () {
						var p = marker.getLatLng();
						latIn.value = p.lat.toFixed(6);
						lngIn.value = p.lng.toFixed(6);
						say('تم تعديل موقع المحل ✓');
					});
				} else {
					marker.setLatLng([lat, lng]);
				}
				map.setView([lat, lng], zoom || Math.max(map.getZoom(), 16));
			}
			if (locate) { locate.classList.remove('has-error'); }
			say(note || 'تم تحديد موقع المحل ✓ يمكنك سحب الدبوس لتدقيقه.');
		}

		function initMap() {
			if (map || !mapEl || !window.L || mapEl.offsetParent === null) {
				if (map) { map.invalidateSize(); }
				return;
			}
			var start = hasPin() ? [+latIn.value, +lngIn.value] : (districtCenter() || ISTANBUL);
			var zoom = hasPin() ? 16 : (districtCenter() ? 13 : 10);
			map = window.L.map(mapEl, { scrollWheelZoom: false }).setView(start, zoom);
			map.attributionControl.setPrefix('<a href="https://leafletjs.com">Leaflet</a>');
			window.L.tileLayer(A.tiles || 'https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: A.attr || '' }).addTo(map);
			if (hasPin()) { setPin(+latIn.value, +lngIn.value, 16, 'تم تحديد موقع المحل ✓'); }
			map.on('click', function (e) { setPin(e.latlng.lat, e.latlng.lng, null); });
		}
		mapReady.push(initMap);
		initMap();

		if (district) {
			district.addEventListener('change', function () {
				var other = district.value === 'other';
				if (cityBox) { cityBox.hidden = !other; }
				if (cityInput) { cityInput.required = other; }
				var c = districtCenter();
				if (map && c && !hasPin()) { map.setView(c, other ? 6 : 14); }
			});
			if (cityInput) { cityInput.required = district.value === 'other'; }
		}

		if (gps) {
			if (!('geolocation' in navigator)) { gps.hidden = true; }
			gps.addEventListener('click', function () {
				initMap();
				say('جارِ تحديد موقعك…');
				gps.disabled = true;
				navigator.geolocation.getCurrentPosition(function (pos) {
					gps.disabled = false;
					setPin(pos.coords.latitude, pos.coords.longitude, 17, 'تم تحديد موقعك الحالي ✓ تأكد أنه مكان المحل، واسحب الدبوس إن لزم.');
				}, function () {
					gps.disabled = false;
					say('تعذّر تحديد موقعك (قد يكون الإذن مرفوضاً). المس مكان المحل على الخريطة بدلاً من ذلك.', true);
				}, { enableHighAccuracy: true, timeout: 12000, maximumAge: 60000 });
			});
		}

		form.addEventListener('submit', function (e) {
			if (locate && !hasPin()) {
				e.preventDefault();
				locate.classList.add('has-error');
				say('حدّد موقع المحل على الخريطة قبل المتابعة: اضغط «موقعي الحالي» أو المس مكانه على الخريطة.', true);
				locate.scrollIntoView({ behavior: 'smooth', block: 'center' });
				if (gps && !gps.hidden) { gps.focus({ preventScroll: true }); }
			}
		});
	});
})();
