/**
 * لوحة «الزبائن»: الخريطة، والتصفية حسب المنطقة والحالة، والبحث، والترتيب.
 */
(function () {
	'use strict';

	var root = document.querySelector('.zd-cus');
	var dataEl = document.getElementById('zd-cus-data');
	if (!root || !dataEl) { return; }
	var DATA = {};
	try { DATA = JSON.parse(dataEl.textContent || '{}'); } catch (e) { DATA = {}; }

	var tbody = root.querySelector('[data-zd-rows]');
	var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
	var q = root.querySelector('[data-zd-q]');
	var areaSel = root.querySelector('[data-zd-area-select]');
	var statusSel = root.querySelector('[data-zd-status]');
	var sortSel = root.querySelector('[data-zd-sort]');
	var countEl = root.querySelector('[data-zd-count]');
	var noneEl = root.querySelector('[data-zd-none]');
	var bars = Array.prototype.slice.call(root.querySelectorAll('[data-zd-area]'));

	function esc(s) {
		return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; });
	}

	/* الخريطة */
	var map = null;
	var markers = {};
	var mapEl = root.querySelector('[data-zd-cus-map]');
	if (mapEl && window.L) {
		map = window.L.map(mapEl, { scrollWheelZoom: false }).setView([41.02, 28.96], 10);
		map.attributionControl.setPrefix('<a href="https://leafletjs.com">Leaflet</a>');
		window.L.tileLayer(DATA.tiles || 'https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
		}).addTo(map);
		var pts = [];
		(DATA.pins || []).forEach(function (p) {
			var icon = window.L.divIcon({ className: 'zd-cus-pin' + (p.o ? '' : ' is-never'), html: '<span></span>', iconSize: [16, 16], iconAnchor: [8, 8] });
			var m = window.L.marker([p.lat, p.lng], { icon: icon, title: p.s, keyboard: false }).addTo(map);
			m.bindPopup('<b>' + esc(p.s) + '</b>' + esc(p.n) + (p.d ? ' · ' + esc(p.d) : '') + '<br>' +
				(p.o ? esc(p.o) + ' طلبية' : 'لم يطلب بعد') +
				(p.w ? ' · <a href="' + esc(p.w) + '" target="_blank" rel="noopener">واتساب</a>' : '') +
				' · <a href="https://www.google.com/maps?q=' + p.lat + ',' + p.lng + '" target="_blank" rel="noopener">خرائط Google</a>');
			m.on('click', function () { highlightRow(p.id); });
			markers[p.id] = m;
			pts.push([p.lat, p.lng]);
		});
		// العرض الأول على محلات إسطنبول، حتى لا يصغّرها محل واحد في مدينة أخرى.
		var inCity = pts.filter(function (p) { return p[0] > 40.75 && p[0] < 41.6 && p[1] > 27.9 && p[1] < 29.95; });
		var first = inCity.length ? inCity : pts;
		if (first.length > 1) { map.fitBounds(first, { padding: [30, 30], maxZoom: 14 }); }
		else if (first.length === 1) { map.setView(first[0], 15); }
		var legend = document.createElement('p');
		legend.className = 'zd-cus__legend';
		legend.innerHTML = '<span><i></i>طلب مرة على الأقل</span><span><i class="is-never"></i>لم يطلب بعد</span>';
		mapEl.parentNode.appendChild(legend);
	}

	function highlightRow(id) {
		rows.forEach(function (r) { r.classList.toggle('is-focus', r.getAttribute('data-id') === String(id)); });
	}

	/* التصفية */
	function apply() {
		var term = (q && q.value || '').trim().toLowerCase();
		var area = areaSel ? areaSel.value : '';
		var st = statusSel ? statusSel.value : '';
		var shown = 0;
		var visibleIds = {};
		rows.forEach(function (r) {
			var ok = (!area || r.getAttribute('data-area') === area) &&
				(!st || (' ' + r.getAttribute('data-status') + ' ').indexOf(' ' + st + ' ') > -1) &&
				(!term || r.getAttribute('data-search').indexOf(term) > -1);
			r.hidden = !ok;
			if (ok) { shown++; visibleIds[r.getAttribute('data-id')] = true; }
		});
		if (countEl) { countEl.textContent = shown + ' زبون'; }
		if (noneEl) { noneEl.hidden = shown > 0; }
		bars.forEach(function (b) { b.classList.toggle('is-active', !!area && b.getAttribute('data-zd-area') === area); });
		if (map) {
			var pts = [];
			Object.keys(markers).forEach(function (id) {
				var on = !!visibleIds[id];
				if (on && !map.hasLayer(markers[id])) { markers[id].addTo(map); }
				if (!on && map.hasLayer(markers[id])) { map.removeLayer(markers[id]); }
				if (on) { pts.push(markers[id].getLatLng()); }
			});
			if ((area || st || term) && pts.length) { map.fitBounds(pts, { padding: [30, 30], maxZoom: 15 }); }
		}
	}
	function sort() {
		var key = sortSel ? sortSel.value : 'reg';
		rows.sort(function (a, b) { return parseFloat(b.getAttribute('data-' + key)) - parseFloat(a.getAttribute('data-' + key)); });
		rows.forEach(function (r) { tbody.appendChild(r); });
	}

	if (q) { q.addEventListener('input', apply); }
	if (areaSel) { areaSel.addEventListener('change', apply); }
	if (statusSel) { statusSel.addEventListener('change', apply); }
	if (sortSel) { sortSel.addEventListener('change', sort); }
	bars.forEach(function (b) {
		b.addEventListener('click', function () {
			var v = b.getAttribute('data-zd-area');
			areaSel.value = areaSel.value === v ? '' : v;
			apply();
			root.querySelector('.zd-cus__list').scrollIntoView({ behavior: 'smooth', block: 'start' });
		});
	});
	root.addEventListener('click', function (e) {
		var quick = e.target.closest('[data-zd-quick]');
		if (quick && statusSel) {
			statusSel.value = quick.getAttribute('data-zd-quick');
			apply();
			root.querySelector('.zd-cus__list').scrollIntoView({ behavior: 'smooth', block: 'start' });
			return;
		}
		var focus = e.target.closest('[data-zd-focus]');
		if (focus && map) {
			var id = focus.getAttribute('data-zd-focus');
			var m = markers[id];
			if (m) {
				mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
				map.setView(m.getLatLng(), 16);
				m.openPopup();
				highlightRow(id);
			}
		}
	});
})();
