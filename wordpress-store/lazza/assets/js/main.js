/*!
 * Lazza (لذّة) — سكربت الواجهة
 * بدون مكتبات: مزامنة السلة الفورية، الطلب السريع، البحث الحي، الأدراج، العدّاد، التبويبات، النماذج.
 */
(function () {
	'use strict';

	var C = window.LAZZA || {};
	var T = C.i18n || {};
	var doc = document;
	var $ = function (s, r) { return (r || doc).querySelector(s); };
	var $$ = function (s, r) { return Array.prototype.slice.call((r || doc).querySelectorAll(s)); };
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* ------------------------------------------------------------------
	 * أدوات
	 * ---------------------------------------------------------------- */
	function money(n) {
		var c = C.currency || { symbol: '₺', pos: 'right_space', dec: 0, ds: '.', ts: ',' };
		var fixed = Number(n || 0).toFixed(c.dec).split('.');
		var s = fixed[0].replace(/\B(?=(\d{3})+(?!\d))/g, c.ts) + (fixed[1] ? c.ds + fixed[1] : '');
		switch (c.pos) {
			case 'left': return c.symbol + s;
			case 'left_space': return c.symbol + ' ' + s;
			case 'right': return s + c.symbol;
			default: return s + ' ' + c.symbol;
		}
	}

	function normalize(str) {
		return String(str || '')
			.toLowerCase()
			.replace(/[ً-ٰٟـ]/g, '')
			.replace(/[أإآٱ]/g, 'ا')
			.replace(/ى/g, 'ي')
			.replace(/ة/g, 'ه')
			.replace(/ؤ/g, 'و')
			.replace(/ئ/g, 'ي')
			.replace(/ç/g, 'c').replace(/ş/g, 's').replace(/ğ/g, 'g').replace(/ı/g, 'i').replace(/i̇/g, 'i')
			.replace(/ö/g, 'o').replace(/ü/g, 'u')
			.replace(/\s+/g, ' ')
			.trim();
	}

	function debounce(fn, ms) {
		var t;
		return function () {
			var a = arguments, self = this;
			clearTimeout(t);
			t = setTimeout(function () { fn.apply(self, a); }, ms);
		};
	}

	var toastTimer;
	function toast(msg, type) {
		var el = $('.lz-toast');
		if (!el || !msg) { return; }
		el.textContent = msg;
		el.className = 'lz-toast is-show' + (type ? ' is-' + type : '');
		clearTimeout(toastTimer);
		toastTimer = setTimeout(function () { el.classList.remove('is-show'); }, 2600);
	}

	/* ------------------------------------------------------------------
	 * الترويسة: ارتفاعها + التصغير عند التمرير
	 * ---------------------------------------------------------------- */
	var header = $('#lz-header');
	function setHeaderVar() {
		if (header) {
			doc.documentElement.style.setProperty('--lz-header-h', header.offsetHeight + 'px');
		}
	}
	function onScroll() {
		if (!header) { return; }
		var scrolled = window.scrollY > 60;
		if (scrolled !== header.classList.contains('is-scrolled')) {
			header.classList.toggle('is-scrolled', scrolled);
			setHeaderVar();
		}
	}
	setHeaderVar();
	window.addEventListener('resize', debounce(setHeaderVar, 150));
	window.addEventListener('load', setHeaderVar);
	window.addEventListener('scroll', onScroll, { passive: true });

	/* ------------------------------------------------------------------
	 * الأدراج (القائمة + السلة)
	 * ---------------------------------------------------------------- */
	var overlay = $('.lz-overlay');
	var lastFocus = null;

	function openDrawer(id) {
		var d = doc.getElementById(id);
		if (!d) { return; }
		closeDrawers(true);
		lastFocus = doc.activeElement;
		d.classList.add('is-open');
		d.setAttribute('aria-hidden', 'false');
		if (overlay) {
			overlay.hidden = false;
			requestAnimationFrame(function () { overlay.classList.add('is-open'); });
		}
		doc.body.classList.add('lz-lock');
		$$('[data-lz-open="' + id + '"]').forEach(function (b) { b.setAttribute('aria-expanded', 'true'); });
		var focusable = d.querySelector('button, a[href], input');
		if (focusable) { setTimeout(function () { focusable.focus({ preventScroll: true }); }, 60); }
	}

	function closeDrawers(silent) {
		var open = $$('.lz-drawer.is-open');
		if (!open.length) { return; }
		open.forEach(function (d) {
			d.classList.remove('is-open');
			d.setAttribute('aria-hidden', 'true');
		});
		if (overlay) {
			overlay.classList.remove('is-open');
			setTimeout(function () { if (!$('.lz-drawer.is-open')) { overlay.hidden = true; } }, 260);
		}
		doc.body.classList.remove('lz-lock');
		$$('[data-lz-open][aria-expanded="true"]').forEach(function (b) { b.setAttribute('aria-expanded', 'false'); });
		if (!silent && lastFocus && lastFocus.focus) { lastFocus.focus({ preventScroll: true }); }
	}

	doc.addEventListener('click', function (e) {
		var opener = e.target.closest('[data-lz-open]');
		if (opener) {
			e.preventDefault();
			openDrawer(opener.getAttribute('data-lz-open'));
			return;
		}
		if (e.target.closest('[data-lz-close]')) {
			e.preventDefault();
			closeDrawers();
		}
	});

	doc.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') { closeDrawers(); }
		if (e.key === 'Tab') {
			var d = $('.lz-drawer.is-open');
			if (!d) { return; }
			var items = $$('a[href], button:not([disabled]), input, select, textarea', d).filter(function (el) { return el.offsetParent !== null; });
			if (!items.length) { return; }
			var first = items[0], last = items[items.length - 1];
			if (e.shiftKey && doc.activeElement === first) { e.preventDefault(); last.focus(); }
			else if (!e.shiftKey && doc.activeElement === last) { e.preventDefault(); first.focus(); }
		}
	});

	/* ------------------------------------------------------------------
	 * أجزاء السلة (عداد، إجمالي، سلة مصغّرة)
	 * ---------------------------------------------------------------- */
	function applyFragments(fragments) {
		if (!fragments) { return; }
		Object.keys(fragments).forEach(function (sel) {
			$$(sel).forEach(function (el) {
				var tmp = doc.createElement('div');
				tmp.innerHTML = fragments[sel];
				var node = tmp.firstElementChild;
				if (node) { el.parentNode.replaceChild(node, el); }
			});
		});
		$$('.lz-cart-count').forEach(function (el) {
			el.classList.remove('is-bump');
			void el.offsetWidth;
			el.classList.add('is-bump');
		});
		if (window.jQuery) {
			window.jQuery(doc.body).trigger('wc_fragments_refreshed');
		}
	}

	/* ------------------------------------------------------------------
	 * متحكمات السلة (+ / − / إضافة) مع مزامنة مجمّعة
	 * ---------------------------------------------------------------- */
	var pending = {};
	var pendingCount = 0;
	var syncTimer = null;
	var inflight = null;
	var lastMsg = '';

	function controlsFor(id) {
		return $$('.lz-cart-ctl[data-id="' + id + '"]');
	}

	function setControl(el, qty) {
		qty = Math.max(0, Math.min(9999, parseInt(qty, 10) || 0));
		el.setAttribute('data-qty', qty);
		el.classList.toggle('is-active', qty > 0);
		var input = $('.lz-step__input', el);
		if (input && doc.activeElement !== input) { input.value = qty; }
		var row = el.closest('.lz-qo-row');
		if (row) { row.classList.toggle('is-selected', qty > 0); }
	}

	function changeQty(id, qty, name) {
		qty = Math.max(0, Math.min(9999, parseInt(qty, 10) || 0));
		var before = 0;
		controlsFor(id).forEach(function (el) {
			before = parseInt(el.getAttribute('data-qty'), 10) || 0;
			setControl(el, qty);
			el.classList.add('is-dirty');
		});
		if (qty === 0) { lastMsg = T.removed; }
		else if (before === 0) { lastMsg = '✓ ' + T.added + (name ? ' — ' + name : ''); }
		else { lastMsg = (name ? name + ': ' : '') + qty + ' ' + (T.carton || ''); }
		if (!(id in pending)) { pendingCount++; }
		pending[id] = qty;
		updateSummary();
		clearTimeout(syncTimer);
		syncTimer = setTimeout(flush, 450);
	}

	function flush() {
		if (!C.syncUrl || !pendingCount) { return Promise.resolve(); }
		if (inflight) {
			return inflight.then(flush);
		}
		var items = pending;
		pending = {};
		pendingCount = 0;
		var body = new URLSearchParams();
		body.set('items', JSON.stringify(items));
		doc.documentElement.classList.add('lz-syncing');
		inflight = fetch(C.syncUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			body: body.toString()
		})
			.then(function (r) { if (!r.ok) { throw new Error(r.status); } return r.json(); })
			.then(function (data) {
				if (!data || !data.ok) { throw new Error('bad'); }
				applyFragments(data.fragments);
				reconcile(data.items || {}, Object.keys(items));
				if (data.errors && data.errors.length) { toast(data.errors[0], 'error'); }
				else { toast(lastMsg || T.updated, 'ok'); }
			})
			.catch(function () {
				toast(T.error, 'error');
				refreshState();
			})
			.then(function () {
				inflight = null;
				doc.documentElement.classList.remove('lz-syncing');
				updateSummary();
			});
		return inflight;
	}

	// مطابقة الواجهة مع كميات السلة الفعلية على الخادم.
	function reconcile(map, touched) {
		$$('.lz-cart-ctl').forEach(function (el) {
			var id = el.getAttribute('data-id');
			if (id in pending) { return; }
			var serverQty = map[id] ? parseInt(map[id], 10) : 0;
			el.classList.remove('is-dirty');
			if (touched && touched.indexOf(id) === -1 && !(id in map) && (parseInt(el.getAttribute('data-qty'), 10) || 0) === 0) { return; }
			setControl(el, serverQty);
		});
	}

	function refreshState() {
		if (!C.syncUrl) { return; }
		var body = new URLSearchParams();
		body.set('items', '{}');
		fetch(C.syncUrl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }, body: body.toString() })
			.then(function (r) { return r.json(); })
			.then(function (data) {
				if (data && data.ok) {
					applyFragments(data.fragments);
					$$('.lz-cart-ctl').forEach(function (el) {
						var id = el.getAttribute('data-id');
						if (!(id in pending)) { setControl(el, data.items[id] || 0); el.classList.remove('is-dirty'); }
					});
					updateSummary();
				}
			})
			.catch(function () {});
	}

	doc.addEventListener('click', function (e) {
		var add = e.target.closest('.lz-cart-ctl__add');
		var step = e.target.closest('.lz-step');
		if (!add && !step) { return; }
		var ctl = e.target.closest('.lz-cart-ctl');
		if (!ctl) { return; }
		e.preventDefault();
		var id = ctl.getAttribute('data-id');
		var name = ctl.getAttribute('data-name');
		var qty = parseInt(ctl.getAttribute('data-qty'), 10) || 0;
		if (add) {
			changeQty(id, 1, name);
			var plus = $('.lz-step--plus', ctl);
			if (plus) { plus.focus({ preventScroll: true }); }
		} else {
			var next = qty + (parseInt(step.getAttribute('data-step'), 10) || 0);
			changeQty(id, next, name);
			if (next <= 0) {
				var addBtn = $('.lz-cart-ctl__add', ctl);
				if (addBtn) { addBtn.focus({ preventScroll: true }); }
			}
		}
	});

	doc.addEventListener('change', function (e) {
		if (!e.target.classList || !e.target.classList.contains('lz-step__input')) { return; }
		var ctl = e.target.closest('.lz-cart-ctl');
		if (ctl) { changeQty(ctl.getAttribute('data-id'), e.target.value, ctl.getAttribute('data-name')); }
	});

	doc.addEventListener('keydown', function (e) {
		if (e.key === 'Enter' && e.target.classList && e.target.classList.contains('lz-step__input')) {
			e.preventDefault();
			e.target.blur();
		}
	});

	// إزالة منتج من السلة المصغّرة (عبر سكربت ووكومرس) ← تحديث العدّادات.
	if (window.jQuery) {
		window.jQuery(doc.body).on('removed_from_cart updated_cart_totals', function () { refreshState(); });
	}

	/* ------------------------------------------------------------------
	 * أزرار + / − لحقول كمية ووكومرس (صفحة المنتج والسلة)
	 * ---------------------------------------------------------------- */
	$$('.quantity').forEach(function (q) {
		var input = $('input.qty', q);
		if (!input || input.type === 'hidden') {
			$$('.lz-qty-btn', q).forEach(function (b) { b.remove(); });
		}
	});

	var updateCart = debounce(function () {
		var btn = $('.woocommerce-cart-form [name="update_cart"]');
		if (btn) { btn.disabled = false; btn.removeAttribute('aria-disabled'); btn.click(); }
	}, 700);

	doc.addEventListener('click', function (e) {
		var b = e.target.closest('.lz-qty-btn');
		if (!b) { return; }
		var wrap = b.closest('.quantity');
		var input = wrap && $('input.qty', wrap);
		if (!input || input.type === 'hidden' || input.readOnly) { return; }
		e.preventDefault();
		var step = parseFloat(input.step) || 1;
		var min = input.min !== '' ? parseFloat(input.min) : 0;
		var max = input.max !== '' ? parseFloat(input.max) : Infinity;
		var val = parseFloat(input.value) || 0;
		val = b.classList.contains('lz-qty-plus') ? val + step : val - step;
		val = Math.max(min, Math.min(max, val));
		input.value = val;
		input.dispatchEvent(new Event('change', { bubbles: true }));
		if (window.jQuery) { window.jQuery(input).trigger('change'); }
	});

	doc.addEventListener('change', function (e) {
		if (e.target.matches && e.target.matches('.woocommerce-cart-form input.qty')) { updateCart(); }
	});

	/* ------------------------------------------------------------------
	 * البحث الحي
	 * ---------------------------------------------------------------- */
	$$('[data-lz-search]').forEach(function (form) {
		var input = $('.lz-search__input', form);
		var box = $('.lz-search__results', form);
		if (!input || !box || !C.searchUrl) { return; }
		var ctrl = null;
		var active = -1;
		var cache = {};

		function close() {
			box.hidden = true;
			input.setAttribute('aria-expanded', 'false');
			active = -1;
		}

		function render(data, q) {
			var html = '';
			if (!data.items || !data.items.length) {
				html = '<div class="lz-sr__msg">' + escapeHtml(T.noResults || '') + '</div>';
			} else {
				data.items.forEach(function (it, i) {
					html += '<a class="lz-sr__item" role="option" id="lz-sr-' + i + '" href="' + escapeAttr(it.url) + '">' +
						'<span class="lz-sr__art">' + (it.art || '') + '</span>' +
						'<span><span class="lz-sr__name">' + escapeHtml(it.name) + '</span><span class="lz-sr__meta">' + escapeHtml([it.brand, it.pack].filter(Boolean).join(' • ')) + '</span></span>' +
						'<span class="lz-sr__price">' + escapeHtml(it.price || '') + '</span></a>';
				});
				html += '<a class="lz-sr__all" href="' + escapeAttr(data.url) + '">' + escapeHtml(T.viewAll || '') + ' (' + data.total + ')</a>';
			}
			box.innerHTML = html;
			box.hidden = false;
			input.setAttribute('aria-expanded', 'true');
			active = -1;
		}

		var run = debounce(function () {
			var q = input.value.trim();
			if (q.length < 2) { close(); return; }
			if (cache[q]) { render(cache[q], q); return; }
			if (ctrl) { ctrl.abort(); }
			ctrl = window.AbortController ? new AbortController() : null;
			box.innerHTML = '<div class="lz-sr__msg">' + escapeHtml(T.searching || '') + '</div>';
			box.hidden = false;
			var url = C.searchUrl + (C.searchUrl.indexOf('?') > -1 ? '&' : '?') + 'q=' + encodeURIComponent(q);
			fetch(url, { signal: ctrl ? ctrl.signal : undefined, credentials: 'same-origin' })
				.then(function (r) { return r.json(); })
				.then(function (data) { cache[q] = data; if (input.value.trim() === q) { render(data, q); } })
				.catch(function () {});
		}, 220);

		input.addEventListener('input', run);
		input.addEventListener('focus', function () { if (input.value.trim().length >= 2) { run(); } });
		input.addEventListener('keydown', function (e) {
			var items = $$('.lz-sr__item', box);
			if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
				if (!items.length) { return; }
				e.preventDefault();
				active = (active + (e.key === 'ArrowDown' ? 1 : -1) + items.length) % items.length;
				items.forEach(function (it, i) { it.classList.toggle('is-active', i === active); });
				input.setAttribute('aria-activedescendant', items[active].id);
			} else if (e.key === 'Enter' && active > -1 && items[active]) {
				e.preventDefault();
				window.location.href = items[active].href;
			} else if (e.key === 'Escape') {
				close();
			}
		});
		doc.addEventListener('click', function (e) { if (!form.contains(e.target)) { close(); } });
	});

	function escapeHtml(s) {
		return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}
	function escapeAttr(s) { return escapeHtml(s); }

	/* ------------------------------------------------------------------
	 * العدّاد التنازلي للعروض
	 * ---------------------------------------------------------------- */
	$$('[data-lz-countdown]').forEach(function (el) {
		var end = Date.parse(el.getAttribute('data-lz-countdown'));
		if (isNaN(end)) { return; }
		var parts = { d: $('[data-u="d"]', el), h: $('[data-u="h"]', el), m: $('[data-u="m"]', el), s: $('[data-u="s"]', el) };
		function pad(n) { return (n < 10 ? '0' : '') + n; }
		function tick() {
			var diff = Math.max(0, end - Date.now());
			var s = Math.floor(diff / 1000);
			if (parts.d) { parts.d.textContent = Math.floor(s / 86400); }
			if (parts.h) { parts.h.textContent = pad(Math.floor((s % 86400) / 3600)); }
			if (parts.m) { parts.m.textContent = pad(Math.floor((s % 3600) / 60)); }
			if (parts.s) { parts.s.textContent = pad(s % 60); }
			if (diff <= 0) { clearInterval(timer); }
		}
		tick();
		var timer = setInterval(tick, 1000);
	});

	/* ------------------------------------------------------------------
	 * التبويبات
	 * ---------------------------------------------------------------- */
	$$('.lz-tabs').forEach(function (list) {
		var tabs = $$('[role="tab"]', list);
		function select(tab, focus) {
			tabs.forEach(function (t) {
				var on = t === tab;
				t.classList.toggle('is-active', on);
				t.setAttribute('aria-selected', on ? 'true' : 'false');
				t.tabIndex = on ? 0 : -1;
				var panel = doc.getElementById(t.getAttribute('aria-controls'));
				if (panel) { panel.hidden = !on; }
			});
			if (focus) { tab.focus(); }
		}
		tabs.forEach(function (t, i) {
			t.addEventListener('click', function () { select(t); });
			t.addEventListener('keydown', function (e) {
				var dir = { ArrowLeft: 1, ArrowRight: -1, ArrowDown: 1, ArrowUp: -1 }[e.key];
				if (doc.dir === 'ltr' && (e.key === 'ArrowLeft' || e.key === 'ArrowRight')) { dir = -dir; }
				if (dir) { e.preventDefault(); select(tabs[(i + dir + tabs.length) % tabs.length], true); }
			});
		});
	});

	/* ------------------------------------------------------------------
	 * الظهور التدريجي
	 * ---------------------------------------------------------------- */
	var reveals = $$('.lz-reveal');
	if (reveals.length) {
		if (!('IntersectionObserver' in window) || reduceMotion) {
			reveals.forEach(function (el) { el.classList.add('is-in'); });
		} else {
			var io = new IntersectionObserver(function (entries) {
				entries.forEach(function (en) {
					if (en.isIntersecting) {
						var el = en.target;
						var siblings = $$('.lz-reveal', el.parentNode);
						var delay = Math.min(siblings.indexOf(el), 6) * 70;
						el.style.transitionDelay = delay + 'ms';
						el.classList.add('is-in');
						io.unobserve(el);
					}
				});
			}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
			reveals.forEach(function (el) { io.observe(el); });
		}
	}

	/* ------------------------------------------------------------------
	 * نسخ الرابط
	 * ---------------------------------------------------------------- */
	doc.addEventListener('click', function (e) {
		var b = e.target.closest('[data-lz-copy]');
		if (!b) { return; }
		e.preventDefault();
		var text = b.getAttribute('data-lz-copy');
		var done = function () { toast(T.copied, 'ok'); };
		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(text).then(done).catch(fallback);
		} else { fallback(); }
		function fallback() {
			var ta = doc.createElement('textarea');
			ta.value = text; ta.setAttribute('readonly', ''); ta.style.position = 'absolute'; ta.style.left = '-9999px';
			doc.body.appendChild(ta); ta.select();
			try { doc.execCommand('copy'); done(); } catch (err) { /* تجاهل */ }
			doc.body.removeChild(ta);
		}
	});

	/* ------------------------------------------------------------------
	 * صفحة الطلب السريع
	 * ---------------------------------------------------------------- */
	var qo = $('[data-lz-qo]');
	var qoRows = qo ? $$('.lz-qo-row', qo) : [];
	var qoState = { cat: 'all', brand: 'all', sale: false, selected: false, q: '' };

	qoRows.forEach(function (row) { row._s = normalize(row.getAttribute('data-search')); });

	function applyQoFilter() {
		if (!qo) { return; }
		var tokens = normalize(qoState.q).split(' ').filter(Boolean);
		var visible = 0;
		qoRows.forEach(function (row) {
			var ok = true;
			if (qoState.cat !== 'all' && row.getAttribute('data-cat') !== qoState.cat) { ok = false; }
			if (ok && qoState.brand !== 'all' && row.getAttribute('data-brand') !== qoState.brand) { ok = false; }
			if (ok && qoState.sale && row.getAttribute('data-sale') !== '1') { ok = false; }
			if (ok && qoState.selected && !row.classList.contains('is-selected')) { ok = false; }
			if (ok && tokens.length) {
				for (var i = 0; i < tokens.length; i++) {
					if (row._s.indexOf(tokens[i]) === -1) { ok = false; break; }
				}
			}
			row.hidden = !ok;
			if (ok) { visible++; }
		});
		$$('.lz-qo-group', qo).forEach(function (g) {
			g.hidden = !$$('.lz-qo-row', g).some(function (r) { return !r.hidden; });
		});
		var empty = $('[data-lz-qo-empty]', qo);
		if (empty) { empty.hidden = visible > 0; }
	}

	function qoSelected() {
		var out = [];
		qoRows.forEach(function (row) {
			var ctl = $('.lz-cart-ctl', row);
			if (!ctl) { return; }
			var qty = parseInt(ctl.getAttribute('data-qty'), 10) || 0;
			if (qty > 0) {
				out.push({ name: ctl.getAttribute('data-name'), qty: qty, price: parseFloat(ctl.getAttribute('data-price')) || 0 });
			}
		});
		return out;
	}

	function waMessage(sel) {
		var lines = [T.waIntro || ''];
		var total = 0;
		sel.forEach(function (it, i) {
			lines.push((i + 1) + ') ' + it.name + ' — ' + it.qty + ' ' + (T.carton || ''));
			total += it.qty * it.price;
		});
		if (C.showPrices) {
			lines.push('');
			lines.push((T.waTotal || '') + ': ' + money(total));
		}
		lines.push('');
		lines.push(T.waName || '');
		lines.push(T.waAddress || '');
		return lines.join('\n');
	}

	function updateSummary() {
		if (!qo) { return; }
		var sel = qoSelected();
		var cartons = 0, total = 0;
		sel.forEach(function (it) { cartons += it.qty; total += it.qty * it.price; });
		var set = function (s, v) { $$(s).forEach(function (el) { el.textContent = v; }); };
		set('[data-lz-qo-lines]', sel.length);
		set('[data-lz-qo-lines-badge]', sel.length);
		set('[data-lz-qo-cartons]', cartons);
		set('[data-lz-qo-total]', money(total));
		var summary = $('[data-lz-qo-summary]');
		if (summary) { summary.classList.toggle('has-items', sel.length > 0); }
		var wa = $('[data-lz-qo-wa]');
		if (wa && C.wa) {
			wa.href = 'https://wa.me/' + C.wa + '?text=' + encodeURIComponent(waMessage(sel));
		}
		if (qoState.selected) { applyQoFilter(); }
	}

	if (qo) {
		var search = $('[data-lz-qo-search]', qo);
		if (search) {
			search.addEventListener('input', debounce(function () {
				qoState.q = search.value;
				applyQoFilter();
			}, 120));
		}

		$$('.lz-qo-filter', qo).forEach(function (btn) {
			btn.addEventListener('click', function () {
				var f = btn.getAttribute('data-filter');
				if (f === 'all') {
					qoState.cat = 'all'; qoState.sale = false; qoState.selected = false;
				} else if (f.indexOf('cat:') === 0) {
					var cat = f.slice(4);
					qoState.cat = qoState.cat === cat ? 'all' : cat;
				} else if (f === 'sale') {
					qoState.sale = !qoState.sale;
				} else if (f === 'selected') {
					qoState.selected = !qoState.selected;
				}
				$$('.lz-qo-filter', qo).forEach(function (b) {
					var bf = b.getAttribute('data-filter');
					var on = (bf === 'all' && qoState.cat === 'all' && !qoState.sale && !qoState.selected) ||
						(bf === 'cat:' + qoState.cat) || (bf === 'sale' && qoState.sale) || (bf === 'selected' && qoState.selected);
					b.classList.toggle('is-active', on);
					b.setAttribute('aria-pressed', on ? 'true' : 'false');
				});
				applyQoFilter();
				var list = $('.lz-qo-list', qo);
				if (list && list.getBoundingClientRect().top < 0) {
					var bar = $('[data-lz-qo-bar]');
					var offset = (header ? header.offsetHeight : 0) + (bar ? bar.offsetHeight : 0) + 10;
					window.scrollTo({ top: list.getBoundingClientRect().top + window.scrollY - offset, behavior: reduceMotion ? 'auto' : 'smooth' });
				}
			});
		});

		$$('.lz-qo-brand', qo).forEach(function (btn) {
			btn.addEventListener('click', function () {
				qoState.brand = btn.getAttribute('data-brand');
				$$('.lz-qo-brand', qo).forEach(function (b) {
					var on = b === btn;
					b.classList.toggle('is-active', on);
					b.setAttribute('aria-pressed', on ? 'true' : 'false');
				});
				applyQoFilter();
			});
		});

		var printBtn = $('[data-lz-print]', qo);
		if (printBtn) { printBtn.addEventListener('click', function () { window.print(); }); }

		var clearBtn = $('[data-lz-qo-clear]');
		if (clearBtn) {
			clearBtn.addEventListener('click', function () {
				var sel = qoRows.filter(function (r) { return r.classList.contains('is-selected'); });
				if (!sel.length || !window.confirm(T.confirmClear)) { return; }
				sel.forEach(function (row) {
					var ctl = $('.lz-cart-ctl', row);
					if (ctl) { changeQty(ctl.getAttribute('data-id'), 0); }
				});
			});
		}

		var waBtn = $('[data-lz-qo-wa]');
		if (waBtn) {
			waBtn.addEventListener('click', function (e) {
				if (!qoSelected().length) { e.preventDefault(); toast(T.empty, 'error'); }
			});
		}

		var checkoutBtn = $('[data-lz-qo-checkout]');
		if (checkoutBtn) {
			checkoutBtn.addEventListener('click', function (e) {
				e.preventDefault();
				if (!qoSelected().length) { toast(T.empty, 'error'); return; }
				clearTimeout(syncTimer);
				Promise.resolve(flush()).then(function () {
					window.location.href = checkoutBtn.href;
				});
			});
		}

		updateSummary();
	}

	// حفظ أي تغييرات معلّقة قبل مغادرة الصفحة.
	window.addEventListener('pagehide', function () {
		if (!pendingCount || !C.syncUrl || !navigator.sendBeacon) { return; }
		var body = new URLSearchParams();
		body.set('items', JSON.stringify(pending));
		navigator.sendBeacon(C.syncUrl, new Blob([body.toString()], { type: 'application/x-www-form-urlencoded; charset=UTF-8' }));
		pending = {};
		pendingCount = 0;
	});

	/* ------------------------------------------------------------------
	 * النماذج
	 * ---------------------------------------------------------------- */
	var items = $('[data-lz-items]');
	var tpl = $('template[data-lz-item-tpl]');
	var addItemBtn = $('[data-lz-add-item]');
	if (items && tpl && addItemBtn) {
		addItemBtn.addEventListener('click', function () {
			if ($$('[data-lz-item]', items).length >= 30) { return; }
			var node = tpl.content.firstElementChild.cloneNode(true);
			items.appendChild(node);
			var first = $('input', node);
			if (first) { first.focus(); }
		});
		items.addEventListener('click', function (e) {
			var rm = e.target.closest('[data-lz-remove-item]');
			if (!rm) { return; }
			var row = rm.closest('[data-lz-item]');
			var rows = $$('[data-lz-item]', items);
			if (rows.length > 1 && rows[0] !== row) {
				row.remove();
			} else {
				$$('input', row).forEach(function (i) { i.value = ''; });
			}
		});
	}

	$$('[data-lz-file]').forEach(function (input) {
		input.addEventListener('change', function () {
			var label = input.closest('.lz-drop');
			var text = label ? $('[data-lz-file-label]', label) : null;
			var f = input.files && input.files[0];
			if (f && f.size > 5 * 1024 * 1024) {
				input.value = '';
				toast('حجم الصورة أكبر من 5MB', 'error');
				f = null;
			}
			if (label) { label.classList.toggle('has-file', !!f); }
			if (text) { text.textContent = f ? '✓ ' + f.name : 'اضغط لإرفاق صورة (JPG / PNG حتى 5MB)'; }
		});
	});

	$$('[data-lz-form]').forEach(function (form) {
		form.addEventListener('submit', function () {
			if (form.classList.contains('is-sending')) { return; }
			form.classList.add('is-sending');
			var btn = $('button[type="submit"]', form);
			if (btn) { btn.setAttribute('aria-busy', 'true'); }
		});
	});

	// إظهار رسالة النجاح/الخطأ في مكانها بعد إعادة التوجيه.
	if (/[?&]lz_(sent|err)=/.test(window.location.search)) {
		var target = doc.getElementById('lz-form');
		if (target) { setTimeout(function () { target.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' }); }, 120); }
	}
})();
