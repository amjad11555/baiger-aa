/**
 * «العروض والإشعارات»: اختيار الرسالة، النسخ، والإرسال لكل زبون عبر واتساب.
 */
(function () {
	'use strict';

	var root = document.querySelector('.zd-eng');
	if (!root) { return; }
	var text = root.querySelector('[data-zd-msg-text]');
	var msgs = {};
	try { msgs = JSON.parse((document.getElementById('zd-eng-msgs') || {}).textContent || '{}'); } catch (e) { msgs = {}; }

	// اختيار الرسالة.
	root.querySelectorAll('[data-zd-msg]').forEach(function (b) {
		b.addEventListener('click', function () {
			var m = msgs[b.getAttribute('data-zd-msg')];
			if (m && text) { text.value = m; markSent(); }
		});
	});

	// نسخ.
	var copyBtn = root.querySelector('[data-zd-copy]');
	var copied = root.querySelector('[data-zd-copied]');
	if (copyBtn && text) {
		copyBtn.addEventListener('click', function () {
			var plain = text.value.replace(/\{الاسم\}/g, '').replace(/\{المحل\}/g, '').replace(/مرحباً\s+،/g, 'مرحباً،');
			var done = function () { if (copied) { copied.textContent = 'تم النسخ ✓'; setTimeout(function () { copied.textContent = ''; }, 2500); } };
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(plain).then(done, function () { text.select(); document.execCommand('copy'); done(); });
			} else { text.select(); document.execCommand('copy'); done(); }
		});
	}

	// تذكّر من أُرسلت له هذه الرسالة (في هذا المتصفح).
	function key() {
		var s = text ? text.value : '';
		var h = 0;
		for (var i = 0; i < s.length; i++) { h = ((h << 5) - h + s.charCodeAt(i)) | 0; }
		return 'zd_eng_sent_' + h;
	}
	function sentList() { try { return JSON.parse(localStorage.getItem(key()) || '[]'); } catch (e) { return []; } }
	function markSent() {
		var list = sentList();
		root.querySelectorAll('[data-zd-send]').forEach(function (b) {
			var on = list.indexOf(b.getAttribute('data-wa')) > -1;
			b.textContent = on ? 'أُرسلت ✓' : 'إرسال';
			b.classList.toggle('is-sent', on);
		});
	}
	root.addEventListener('click', function (e) {
		var b = e.target.closest('[data-zd-send]');
		if (!b || !text) { return; }
		var msg = text.value.replace(/\{الاسم\}/g, b.getAttribute('data-name') || '').replace(/\{المحل\}/g, b.getAttribute('data-shop') || '');
		window.open('https://wa.me/' + b.getAttribute('data-wa') + '?text=' + encodeURIComponent(msg), '_blank', 'noopener');
		var list = sentList();
		if (list.indexOf(b.getAttribute('data-wa')) === -1) { list.push(b.getAttribute('data-wa')); }
		try { localStorage.setItem(key(), JSON.stringify(list)); } catch (err) { /* لا شيء */ }
		markSent();
	});
	if (text) { text.addEventListener('input', markSent); }
	markSent();

	// بحث في الزبائن.
	var q = root.querySelector('[data-zd-eng-q]');
	if (q) {
		q.addEventListener('input', function () {
			var term = q.value.trim().toLowerCase();
			root.querySelectorAll('[data-zd-eng-list] li').forEach(function (li) {
				li.hidden = !!term && li.getAttribute('data-search').indexOf(term) === -1;
			});
		});
	}

	// حقل المقدار يظهر فقط مع الخصم التلقائي.
	var type = root.querySelector('[data-zd-eng-type]');
	var amount = root.querySelector('[data-zd-eng-amount]');
	function syncAmount() { if (type && amount) { amount.hidden = type.value === 'none'; amount.max = type.value === 'percent' ? '90' : ''; } }
	if (type) { type.addEventListener('change', syncAmount); syncAmount(); }
})();
