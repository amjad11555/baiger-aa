/**
 * الإشعارات: عدّاد الجرس، ونافذة العرض عند أول دخول، وتنبيه «وصلت أصناف جديدة» للزائر العائد.
 *
 * «آخر اطلاع» يُحفظ في المتصفح، وللزبون المسجّل في حسابه أيضاً (ليبقى صحيحاً على كل أجهزته).
 */
(function () {
	'use strict';

	var N = window.ZAD_NOTIFY;
	if (!N) { return; }
	var doc = document;

	function load(k) { try { return window.localStorage.getItem(k); } catch (e) { return null; } }
	function save(k, v) { try { window.localStorage.setItem(k, v); } catch (e) { /* لا شيء */ } }

	var seen = Math.max(parseInt(load('zd_notif_seen') || '0', 10) || 0, N.seen || 0);
	// أول زيارة: كل الأصناف جديدة على الزائر، فلا معنى لعدّاد «5 جديد» على الجرس.
	// نبدأ العدّ من الآن، ويظهر للزائر العائد ما وصل بعد زيارته الأولى فقط.
	if (!seen && load('zd_notif_seen') === null) {
		seen = N.now;
		save('zd_notif_seen', String(N.now));
	}
	var promoSeen = !N.promo || load('zd_promo_seen') === N.promo || N.promoSeen === N.promo;
	var panel = doc.getElementById('zd-notif');
	var items = panel ? Array.prototype.slice.call(panel.querySelectorAll('[data-zd-t]')) : [];
	var promoCard = panel ? panel.querySelector('[data-zd-notif-promo]') : null;

	function unseenItems() {
		return items.filter(function (el) { return parseInt(el.getAttribute('data-zd-t'), 10) > seen; });
	}
	function paintBadge() {
		var n = unseenItems().length + (N.promo && !promoSeen ? 1 : 0);
		doc.querySelectorAll('[data-zd-notif-count]').forEach(function (b) {
			b.textContent = n > 9 ? '9+' : String(n);
			b.hidden = n === 0;
		});
		doc.querySelectorAll('[data-zd-open="zd-notif"]').forEach(function (btn) {
			btn.setAttribute('aria-label', n ? 'الإشعارات، ' + n + ' جديد' : 'الإشعارات');
		});
	}
	function paintDots() {
		items.forEach(function (el) { el.classList.toggle('is-unseen', parseInt(el.getAttribute('data-zd-t'), 10) > seen); });
		if (promoCard) { promoCard.classList.toggle('is-unseen', !promoSeen); }
	}

	function sync(items, promo) {
		if (!N.user || !N.ajax || !N.nonce || !window.fetch) { return; }
		var body = new URLSearchParams();
		body.set('nonce', N.nonce);
		if (items) { body.set('items', '1'); }
		if (promo && N.promo) { body.set('promo', N.promo); }
		fetch(N.ajax, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }, body: body.toString() }).catch(function () { /* لا شيء */ });
	}
	// اطّلع على الأصناف الجديدة (والعرض أيضاً إن فتح الجرس).
	function markSeen(opts) {
		opts = opts || {};
		seen = N.now;
		save('zd_notif_seen', String(N.now));
		var withPromo = !!(N.promo && opts.promo);
		if (withPromo) { promoSeen = true; save('zd_promo_seen', N.promo); }
		paintBadge();
		sync(true, withPromo);
	}
	// رأى العرض فقط (أُغلقت نافذته أو ضغط زره).
	function markPromoSeen() {
		if (!N.promo) { return; }
		promoSeen = true;
		save('zd_promo_seen', N.promo);
		paintBadge();
		sync(false, true);
	}

	// فتح الجرس: تبقى النقاط ظاهرة في هذه المرة، ويصفر العدّاد.
	doc.addEventListener('click', function (e) {
		// من ضغط رابط العرض في الشريط العلوي رآه فعلاً: لا نافذة له بعد ذلك.
		if (e.target.closest && e.target.closest('[data-zd-announce] a')) { markPromoSeen(); }
		if (e.target.closest && e.target.closest('[data-zd-open="zd-notif"]')) {
			paintDots();
			closeToast();
			markSeen({ promo: true });
		}
	});

	/* تنبيه الأصناف الجديدة للزائر العائد */
	var toast = null;
	function plural(n) {
		if (n === 1) { return 'صنف جديد'; }
		if (n === 2) { return 'صنفان جديدان'; }
		if (n <= 10) { return n + ' أصناف جديدة'; }
		return n + ' صنفاً جديداً';
	}
	function closeToast() {
		if (!toast) { return; }
		toast.classList.remove('is-show');
		var t = toast;
		toast = null;
		setTimeout(function () { t.remove(); }, 300);
	}
	function showToast(n) {
		toast = doc.createElement('div');
		toast.className = 'zd-newtoast';
		toast.setAttribute('role', 'status');
		toast.innerHTML = '<span class="zd-newtoast__dot" aria-hidden="true"></span>' +
			'<p class="zd-newtoast__text"><b>وصل ' + plural(n) + '</b> منذ زيارتك الأخيرة</p>' +
			'<a class="zd-newtoast__go" href="' + N.newUrl + '">اعرضها</a>' +
			'<button type="button" class="zd-newtoast__x" aria-label="إغلاق">×</button>';
		doc.body.appendChild(toast);
		requestAnimationFrame(function () { toast.classList.add('is-show'); });
		toast.querySelector('.zd-newtoast__x').addEventListener('click', function () { markSeen(); closeToast(); });
		toast.querySelector('.zd-newtoast__go').addEventListener('click', function () { markSeen(); });
		setTimeout(closeToast, 12000);
	}

	/* نافذة العرض: مرة واحدة لكل عرض */
	var modal = doc.getElementById('zd-promo');
	var lastFocus = null;
	function focusables() {
		return Array.prototype.slice.call(modal.querySelectorAll('a[href], button:not([disabled])'));
	}
	function closePromo() {
		if (!modal || modal.hidden) { return; }
		modal.classList.remove('is-open');
		doc.body.classList.remove('zd-lock');
		markPromoSeen();
		setTimeout(function () {
			modal.hidden = true;
			if (lastFocus && lastFocus.focus) { lastFocus.focus({ preventScroll: true }); }
			maybeToast();
		}, 220);
	}
	function openPromo() {
		if (!modal) { return; }
		lastFocus = doc.activeElement;
		modal.hidden = false;
		doc.body.classList.add('zd-lock');
		requestAnimationFrame(function () { modal.classList.add('is-open'); });
		setTimeout(function () { var f = modal.querySelector('[data-zd-promo-go]'); if (f) { f.focus({ preventScroll: true }); } }, 80);
	}
	if (modal) {
		modal.addEventListener('click', function (e) {
			if (e.target === modal || e.target.closest('[data-zd-promo-close]')) { closePromo(); }
		});
		var go = modal.querySelector('[data-zd-promo-go]');
		if (go) {
			go.addEventListener('click', markPromoSeen);
		}
		doc.addEventListener('keydown', function (e) {
			if (modal.hidden) { return; }
			if (e.key === 'Escape') { closePromo(); return; }
			if (e.key === 'Tab') {
				var f = focusables();
				if (!f.length) { return; }
				var first = f[0], last = f[f.length - 1];
				if (e.shiftKey && doc.activeElement === first) { e.preventDefault(); last.focus(); }
				else if (!e.shiftKey && doc.activeElement === last) { e.preventDefault(); first.focus(); }
			}
		});
	}

	function maybeToast() {
		// أول زيارة: لا نعرض «منذ زيارتك الأخيرة»، ويكفي عدّاد الجرس.
		var n = unseenItems().length;
		if (N.toast && seen > 0 && n > 0 && !toast) { showToast(n); }
	}

	// نافذة العرض لا تقاطع الزائر فور وصوله: لا تظهر في صفحات المنتج والسلة والدفع والحساب
	// (من يصل من جوجل إلى صنف يراه أولاً)، وفي غيرها بعد 15 ثانية أو بعد تمرير نصف الصفحة تقريباً،
	// ولا تُفتح فوق درج أو نافذة أخرى مفتوحة. العرض ظاهر أصلاً في الشريط العلوي.
	function armPromo() {
		var b = doc.body.classList;
		var quiet = ['single-product', 'woocommerce-cart', 'woocommerce-checkout', 'woocommerce-account'].some(function (c) { return b.contains(c); });
		if (quiet) { setTimeout(maybeToast, 1200); return; }
		var fired = false, timer = 0;
		function fire() {
			if (fired || promoSeen) { return; }
			if (doc.hidden || b.contains('zd-lock')) { timer = setTimeout(fire, 4000); return; }
			fired = true;
			clearTimeout(timer);
			window.removeEventListener('scroll', onScroll);
			openPromo();
		}
		function onScroll() {
			var h = doc.documentElement;
			if (h.scrollHeight > 0 && (window.scrollY + window.innerHeight) / h.scrollHeight > 0.45) { fire(); }
		}
		timer = setTimeout(fire, 15000);
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	paintBadge();
	if (N.popup && !promoSeen && modal) {
		armPromo();
	} else {
		setTimeout(maybeToast, 1200);
	}
})();
