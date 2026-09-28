<?php
/**
 * صفحات مصدر صور المنتجات (نتيجة البحث عن كل منتج باسمه).
 *
 * لكل SKU قائمة صفحات مرتبة: الصفحة الرسمية للعلامة أولاً ثم صفحة المنتج في متاجر تركية كبرى
 * (A101، Migros). أداة «الصور الرسمية» تجرّبها بالترتيب وتأخذ صورة المنتج من أول صفحة تعمل،
 * ثم تلجأ إلى فهرسة موقع العلامة للمنتجات غير المذكورة هنا.
 *
 * لإضافة أو تصحيح مصدر: أضف الرابط هنا، أو الصقه يدوياً في الأداة (المنتجات ← الصور الرسمية).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

return array(
	/* ============================ إيتي ============================ */
	'ETI-BRW-INT' => array(
		'https://www.etietieti.com/eti-browni-intense-cikolata-kapli-krema-dolgulu-kek-2',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-browni-intense-cikolatali-kek-3x50-g_p-17002566',
	),
	'ETI-BRW-GLD' => array(
		'https://www.etietieti.com/eti-browni-gold-cikolata-soslu-cikolatali-kek',
	),
	'ETI-BRW-MIN' => array(
		'https://www.etietieti.com/eti-browni-intense-mini-cikolatali-kek',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-browni-intense-cikolatali-mini-kek-10x16-g_p-17002100',
	),
	'ETI-POP-CHO' => array(
		'https://www.etietieti.com/eti-cikolatali-popkek',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-popkek-cikolatali-kek-60-g_p-17002101',
	),
	'ETI-POP-BAN' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/eti-popkek-muzlu-kek-60-g_p-17000180',
		'https://www.migros.com.tr/hemen/eti-popkek-muzlu-kek-60-g-p-4dc3e6',
	),
	'ETI-POP-LEM' => array(
		'https://www.etietieti.com/eti-popkek-mini-limonlu',
	),
	'ETI-POP-MCH' => array(
		'https://www.etietieti.com/eti-mini-cikolatali-popkek',
	),
	'ETI-TOP-COC' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/eti-topkek-kek-kakaolu-35-g_p-17000048',
	),
	'ETI-TOP-BAN' => array(
		'https://www.migros.com.tr/eti-topkek-muzlu-kek-40-g-p-4dec1a',
	),
	'ETI-TOP-LEM' => array(
		'https://www.etietieti.com/eti-topkek-limonlu',
	),
	'ETI-TOP-FRU' => array(
		'https://www.etietieti.com/eti-topkek-meyveli',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-topkek-meyveli-kek-40-g_p-17000043',
		'https://www.migros.com.tr/eti-topkek-meyveli-40-g-p-4dc3d2',
	),
	'ETI-TOP-MOZ' => array(
		'https://www.etietieti.com/eti-topkek-mozaik',
	),
	'ETI-TOP-STR' => array(
		'https://www.etietieti.com/eti-topkek-cilekli',
	),
	'ETI-TOP-HAZ' => array(
		'https://www.etietieti.com/eti-topkek-findikli-kakaolu',
	),
	'ETI-PAY-FRU' => array(
		'https://www.etietieti.com/eti-meyveli-paykek',
		'https://www.migros.com.tr/eti-paykek-meyveli-kek-200-g-p-4dc3db',
		'https://www.a101.com.tr/market/eti-paykek-meyveli-kek-200-g_p-17000042',
	),
	'ETI-PAY-MOS' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/eti-paykek-mozaik-200-g_p-17001278',
		'https://www.migros.com.tr/eti-paykek-mozaik-kek-200-g-p-4dc3dd',
	),
	'ETI-PAY-COC' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/eti-paykek-kakaolu-kek-200-g_p-17000046',
		'https://www.migros.com.tr/eti-paykek-kakaolu-kek-200-g-p-4dc3d5',
	),
	'ETI-TAR-RAS' => array(
		'https://www.etietieti.com/eti-tartini-frambuazli-turta',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-tartini-frambuazli-turta-171-g_p-17001171',
		'https://www.migros.com.tr/eti-tartini-frambuazli-turta-171-g-p-4dc3f6',
	),
	'ETI-TAR-APR' => array(
		'https://www.migros.com.tr/eti-tartini-kayisili-turta-180-gr-p-4dce5b',
	),
	'ETI-TUT-COC' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/eti-tutku-cikolata-dolgulu-biskuvi-100-gr_p-17000110',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-tutku-cikolata-dolgulu-biskuvi-210-g_p-17001879',
	),
	'ETI-TUT-BLD' => array(
		'https://www.etietieti.com/eti-tutku-bold-kakao-kremali-mozaik-biskuvi',
	),
	'ETI-CIN-ORA' => array(
		'https://www.etietieti.com/eti-cin-portakal-joleli-biskuvi',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-cin-portakal-joleli-biskuvi-5x25-g_p-17000009',
		'https://www.migros.com.tr/eti-cin-portakalli-biskuvi-5-x-25-g-p-6afa5e',
	),
	'ETI-CIN-STR' => array(
		'https://www.etietieti.com/eti-cin-cilek-joleli-biskuvi',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-cin-cilek-joleli-biskuvi-13x25-gr_p-17000436',
		'https://www.migros.com.tr/eti-cin-lokmalik-cilekli-114-g-p-6af81f',
	),
	'ETI-BUR-CLS' => array(
		'https://www.etietieti.com/eti-burcak-tam-bugday-unlu-biskuvi',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-burcak-bugdayli-biskuvi-3x131-g_p-17001575',
	),
	'ETI-BUR-MLK' => array(
		'https://www.etietieti.com/eti-burcak-sutlu-cikolatali-biskuvi',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-burcak-sutlu-cikolatali-biskuvi-114-g_p-17001723',
		'https://www.migros.com.tr/eti-burcak-sutlu-cikolatali-biskuvi-114-g-p-6b1d9b',
	),
	'ETI-BUR-DRK' => array(
		'https://www.etietieti.com/burcak-bitter-cikolatali-biskuvi',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-burcak-bitter-cikolatali-biskuvi-114-g_p-17002436',
		'https://www.migros.com.tr/eti-burcak-bitter-cikolatali-biskuvi-114-g-p-6b1ab7',
	),
	'ETI-FRM-CHO' => array(
		'https://www.etietieti.com/eti-form-bitter-cikolata-kapli-lifli-biskuvi',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-form-bitter-cikolatali-light-biskuvi-50-g_p-17001466',
		'https://www.migros.com.tr/eti-form-bitter-cikolata-kapli-lifli-biskuvi-50-g-p-6afa8c',
	),
	'ETI-FRM-BRN' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/eti-form-kepekli-biskuvi-5x45-g_p-17000026',
	),
	'ETI-POT-CLS' => array(
		'https://www.etietieti.com/eti-petit-beurre-biskuvi',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-petit-beurre-petibor-biskuvi-200-g_p-17003441',
		'https://www.migros.com.tr/eti-petit-beurre-biskuvi-1000-g-p-6af6e4',
	),
	'ETI-NEG-CLS' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/eti-negro-kremali-kakaolu-biskuvi-110-g_p-17002216',
		'https://www.migros.com.tr/hemen/eti-negro-kakaolu-kremali-biskuvi-110-g-p-6af783',
	),
	'ETI-NEG-BLD' => array(
		'https://www.etietieti.com/eti-negro-bold-cikolatali-kremali-biskuvi',
		'https://www.etietieti.com/eti-negro-bold-kakaolu-kremali-biskuvi-',
	),
	'ETI-BEN-MLK' => array(
		'https://etietieti.com/eti-benimo-bitter-cikolatali-sutlu-kaplamali-hindistan-cevizli-marshmallowlu-biskuvi',
		'https://www.migros.com.tr/eti-benimo-lokmalik-cikolatali-biskuvi-80-g-p-6b1547',
	),
	'ETI-HOS-DRK' => array(
		'https://www.migros.com.tr/eti-hosbes-bitter-cikolata-kremali-gofret-142-g-p-4dce81',
	),
	'ETI-HOS-STR' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/eti-hosbes-cilek-kremali-gofret-142-g_p-17000754',
		'https://www.migros.com.tr/eti-hosbes-cilek-kremali-gofret-142-g-p-6d4116',
	),
	'ETI-HOS-MCO' => array(
		'https://www.etietieti.com/eti-hosbes-sutlu-kakaolu-kremali-gofret',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-hosbes-kakao-kremali-gofret-142-g_p-17001810',
		'https://www.migros.com.tr/eti-hosbes-kakao-kremali-gofret-142-g-p-6d413b',
	),
	'ETI-GOF-CHO' => array(
		'https://www.etietieti.com/eti-cikolatali-gofret',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-sutlu-cikolatali-gofret-34-g_p-17002415',
		'https://www.migros.com.tr/eti-sutlu-cikolatali-gofret-34-g-p-6d5f11',
	),
	'ETI-KAR-GOF' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/eti-karam-gurme-bitter-cikolatali-gofret-50-gr_p-17001636',
		'https://www.migros.com.tr/eti-karam-gurme-bitter-cikolatali-gofret-50-g-p-6d43f1',
	),
	'ETI-CRX-CLS' => array(
		'https://www.etietieti.com/eti-crax-sade-cubuk-kraker',
		'https://www.migros.com.tr/eti-crax-cubuk-kraker-40-g-p-6af799',
	),
	'ETI-CRX-CHE' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/crax-peynirli-cubuk-kraker-80-g_p-17002537',
		'https://www.migros.com.tr/eti-crax-peynirli-cubuk-kraker-175-g-p-6af934',
	),
	'ETI-SUS-CBK' => array(
		'https://www.etietieti.com/eti-susamli-cubuk-kraker',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-susamli-cubuk-kraker-56-g_p-17000287',
		'https://www.migros.com.tr/eti-susamli-cubuk-kraker-120-g-p-6b12c0',
	),
	'ETI-CRX-SPI' => array(
		'https://www.etietieti.com/eti-crax-baharatli-cubuk-kraker',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-crax-baharatli-cubuk-kraker-80-g_p-17002536',
		'https://www.migros.com.tr/eti-crax-baharatli-cubuk-kraker-80-g-p-6af9c6',
	),
	'ETI-CRX-HOT' => array(
		'https://www.etietieti.com/eti-crax-acili-cubuk-kraker',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-crax-acili-kraker-80-g-_p-17002853',
	),
	'ETI-GNG-ORG' => array(
		'https://www.migros.com.tr/eti-gong-pops-misir-ve-pirinc-patlagi-sade-80g-p-4da880',
	),
	'ETI-GNG-SPI' => array(
		'https://www.etietieti.com/eti-gong-pops-baharat-cesnili',
		'https://www.migros.com.tr/eti-gong-pops-baharat-ces-misir-ve-pirinc-patlagi-80g-p-4da881',
	),
	'ETI-BLK-CLS' => array(
		'https://www.migros.com.tr/eti-balik-kraker-40-g-p-6af929',
		'https://toptan.migros.com.tr/eti-balik-kraker-40-g-p-07010601',
	),
	'ETI-KRM-DRK' => array(
		'https://www.etietieti.com/eti-karam-54-kakaolu-bitter-cikolata',
		'https://www.migros.com.tr/eti-karam-54-kakaolu-bitter-cikolata-60-g-p-6b5dd5',
	),
	'ETI-KRM-ORA' => array(
		'https://www.migros.com.tr/eti-karam-54-kakaolu-bademli-portakalli-bitter-cikolata-60-g-p-6b5dd3',
		'https://www.migros.com.tr/eti-karam-bitter-bademli-portakalli-kare-cikolata-70-g-p-6b536f',
	),
	'ETI-KRM-PIS' => array(
		'https://www.etietieti.com/eti-karam-icibol-antep-fistikli',
		'https://www.migros.com.tr/eti-karam-antep-fistikli-bitter-cikolata-28-g-p-6b5312',
	),
	'ETI-KRM-HAZ' => array(
		'https://www.migros.com.tr/eti-karam-icibol-findikli-28-g-p-6b6677',
	),
	'ETI-CNG-BAR' => array(
		'https://www.etietieti.com/eti-canga',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-canga-bar-yer-fistikli-karamelli-cikolata-45-g_p-17001422',
		'https://www.migros.com.tr/eti-canga-45-g-p-6b5391',
	),
	'ETI-WNT-BAR' => array(
		'https://www.etietieti.com/eti-wanted-karamelli',
		'https://www.migros.com.tr/eti-wanted-sutlu-cikolata-kapli-misir-ve-bugday-gevrekli-karamelli-bar-32-g-p-6b47d1',
	),
	'ETI-PTT-MLK' => array(
		'https://www.etietieti.com/eti-petito-bol-sutlu-mini-cikolata-8li',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-petito-mini-sutlu-cikolata-32-g_p-27000207',
		'https://www.migros.com.tr/eti-petito-bol-sutlu-cikolata-8-g-p-6b60a7',
	),
	'ETI-PTT-GOF' => array(
		'https://www.etietieti.com/eti-petito-gofret',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-petito-gofret-kakaolu-26-g_p-17002488',
		'https://www.migros.com.tr/eti-petito-bol-sutlu-kremali-sade-kakaolu-gofret-26-g-p-6d65e2',
	),
	'ETI-PTT-WHT' => array(
		'https://www.etietieti.com/eti-petito-ayicik-beyaz-ve-bitter-cikolata-desenli-bol-sutlu-cikolata',
	),
	'ETI-PUF-MAR' => array(
		'https://www.etietieti.com/eti-puf-kakao-granul-kaplamali-marshmallow-biskuvi',
		'https://www.a101.com.tr/kapida/atistirmalik/eti-puf-biskuvi-kakaolu-marshmallow-18-g_p-17000017',
	),
	'ETI-LFL-CHO' => array(
		'https://www.etietieti.com/eti-lifalif-bitter-cikolatali-yulaf-bar',
		'https://www.migros.com.tr/eti-lifalif-bitter-cikolatali-yulaf-bar-35-g-p-6d41ad',
	),
	'ETI-LFL-FRU' => array(
		'https://www.etietieti.com/lifalif-kirmizi-meyveli-yulaf-bar',
		'https://www.migros.com.tr/eti-lifalif-kirmizi-meyveli-yulaf-bar-35-g-p-6d4775',
	),
	/* ============================ أولكر ============================ */
	'ULK-DAN-BCO' => array(
		'https://www.migros.com.tr/ulker-dankek-kakaolu-baton-kek-200-g-p-6b14e9',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-dankek-kakaolu-kek-200-g_p-17002759',
	),
	'ULK-DAN-BFR' => array(
		'https://www.migros.com.tr/ulker-dankek-meyveli-baton-kek-200-g-p-6b14e8',
	),
	'ULK-DAN-LCO' => array(
		'https://www.migros.com.tr/ulker-dankek-lokmalik-kakaolu-kek-160-g-p-6b1d48',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-dankek-lokmalik-kakaolu-kek-160-g_p-17000400',
	),
	'ULK-DAN-LRA' => array(
		'https://www.migros.com.tr/ulker-dankek-lokmalik-uzumlu-kek-160-g-p-6b1d47',
		'https://www.a101.com.tr/market/ulker-dankek-lokmalik-uzumlu-kek-160-g_p-17001914',
	),
	'ULK-KEK-COC' => array(
		'https://www.migros.com.tr/kekstra-konfeti-muffin-kek-kakaolu-38-g-p-4dad0d',
	),
	'ULK-KEK-STR' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-kekstra-cilekli-kek-35-gr_p-17001271',
		'https://www.migros.com.tr/ulker-kekstra-jolebol-cilekli-joleli-kek-35-g-p-4dd253',
	),
	'ULK-RUL-BAN' => array(
		'https://www.migros.com.tr/ulker-dankek-muzlu-rulo-pasta-235-g-p-6b14e6',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-dankek-rulo-muzlu-pasta-235-g_p-17000015',
	),
	'ULK-RUL-STR' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-dankek-cilekli-rulo-pasta-235-g_p-17000445',
		'https://www.migros.com.tr/dankek-rulo-pasta-cilekli-235-g-p-4dc466',
	),
	'ULK-RUL-COC' => array(
		'https://www.migros.com.tr/ulker-dankek-cikolatali-rulo-pasta-235-g-p-6b14e5',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-dankek-cikolatali-rulo-pasta-235-g_p-17000008',
	),
	'ULK-BSK-COC' => array(
		'https://www.migros.com.tr/ulker-biskrem-kakaolu-200-g-p-6b1cce',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-biskrem-kakaolu-kremali-dolgulu-biskuvi-200-g_p-17001953',
	),
	'ULK-BSK-CHR' => array(
		'https://www.migros.com.tr/biskrem-visneli-cikolatali-90-g-p-6afff2',
		'https://www.a101.com.tr/market/ulker-visneli-biskrem-90-gr/',
	),
	'ULK-BSK-EXT' => array(
		'https://www.migros.com.tr/ulker-biskrem-extra-kakaolu-krema-dolgulu-biskuvi-184-g-p-6b160e',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-biskrem-extra-kakaolu-krema-dolgulu-biskuvi-184-g_p-17002472',
	),
	'ULK-BSK-MIN' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-biskrem-kakaolu-krema-dolgulu-biskuvi-40-g_p-17003743',
	),
	'ULK-CKP-COC' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-cokoprens-cikolata-kremali-sandvic-biskuvi-10x30-g_p-17001253',
		'https://www.migros.com.tr/cokoprens-300-g-p-6b16ed',
	),
	'ULK-CKP-MIN' => array(
		'https://www.migros.com.tr/cokoprens-atistirmalik-81-g-p-6b1cbc',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-cokoprens-cikolata-kremali-mini-biskuvi-3x81-g_p-17001243',
	),
	'ULK-HAN-CHD' => array(
		'https://www.migros.com.tr/ulker-hanimeller-cokodamla-kakaolu-damla-biskuvi-150-g-p-6b160c',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-hanimeller-cokodamla-kurabiye-150-g_p-17000176',
	),
	'ULK-HAN-HAZ' => array(
		'https://www.migros.com.tr/ulker-hanimeller-findikli-rulo-82-g-p-6afb1e',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-hanimeller-findikli-kurabiye-3x82-g_p-17003265',
	),
	'ULK-HAN-BTR' => array(
		'https://www.migros.com.tr/ulker-hanimeller-tereyagli-kurabiye-152-g-p-6af796',
	),
	'ULK-HAN-MIX' => array(
		'https://www.migros.com.tr/ulker-hanimeller-karisik-tatli-kurabiye-170g-p-6b1606',
		'https://www.migros.com.tr/ulker-hanimeller-asorti-karisik-tatli-kurabiye-150-g-p-6b160b',
	),
	'ULK-POT-CLS' => array(
		'https://www.migros.com.tr/ulker-potibor-biskuvi-175-g-p-6afb1c',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-potibor-biskuvi-175-g_p-17000360',
	),
	'ULK-RON-BAN' => array(
		'https://www.migros.com.tr/ulker-kremali-rondo-muzlu-biskuvi-61-g-p-6af884',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-rondo-muz-kremali-biskuvi-61-g_p-17003225',
	),
	'ULK-RON-STR' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-rondo-cilek-kremali-biskuvi-61-g-_p-17002600',
		'https://www.migros.com.tr/ulker-rondo-cilekli-kremali-biskuvi-76-g-p-6afb41',
	),
	'ULK-RON-COC' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-rondo-kakaolu-kremali-biskuvi-4x61-g_p-17003427',
	),
	'ULK-HAL-CLS' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-halley-sutlu-cikolatali-biskuvi-8x30-g_p-17002269',
		'https://www.migros.com.tr/ulker-halley-cikolata-kaplamali-sandvic-biskuvi-8li-240-g-p-6b1bdc',
	),
	'ULK-HAL-MIN' => array(
		'https://www.migros.com.tr/ulker-halley-mini-66-g-p-6b1c8e',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-halley-cikolata-kapli-marshmallowlu-mini-biskuvi-3x66-g_p-17002090',
	),
	'ULK-PRB-COC' => array(
		'https://www.migros.com.tr/ulker-probis-kakaolu-ve-muzlu-proteinli-biskuvi-75-g-p-6b1229',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-probis-proteinli-biskuvi-75-g_p-17002328',
	),
	'ULK-PRB-MLK' => array(
		'https://www.migros.com.tr/ulker-probis-10lu-280-g-p-6b16f1',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-probis-kremali-biskuvi-28x10-g_p-17002275',
	),
	'ULK-FNG-CLS' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-finger-biskuvi-5x150-g_p-17000238',
		'https://www.migros.com.tr/ulker-finger-biskuvi-750-g-p-6afa9d',
	),
	'ULK-IKR-COC' => array(
		'https://www.migros.com.tr/ikram-kremali-biskuvi-cikolatali-84-g-p-6afb35',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-ikram-cikolata-kremali-biskuvi-3x84-g_p-17001873',
	),
	'ULK-IKR-HAZ' => array(
		'https://www.migros.com.tr/ikram-kremali-biskuvi-findikli-84-g-p-6afb36',
		'https://www.a101.com.tr/market/ulker-ikram-findik-kremali-biskuvi-3x84-g_p-17003241',
	),
	'ULK-DID-MLK' => array(
		'https://www.migros.com.tr/dido-sutlu-cikolatali-gofret-35-g-p-6d4ca9',
		'https://www.migros.com.tr/ulker-dido-sutlu-cikolatali-gofret-3lu-paket-105-g-p-6b648c',
	),
	'ULK-GOF-CLS' => array(
		'https://www.migros.com.tr/ulker-cikolatali-gofret-36-g-p-6d5ee4',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-cikolatali-gofret-36-g_p-17002360',
	),
	'ULK-GOF-HAZ' => array(
		'https://www.migros.com.tr/ulker-extra-sutlu-cikolata-kapli-findikli-gofret-45-g-p-6b56d3',
	),
	'ULK-KKT-CLS' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-kat-kat-tat-kakaolu-findik-kremali-milfoy-25-g_p-17002451',
		'https://www.migros.com.tr/ulker-kat-kat-tat-25-g-p-6d4106',
	),
	'ULK-9KT-CLS' => array(
		'https://www.migros.com.tr/ulker-9-kat-tat-findikli-gofret-39-g-p-6d40c8',
		'https://www.migros.com.tr/ulker-9-kat-tat-ince-findikli-114-g-p-6d416c',
	),
	'ULK-CRZ-POP' => array(
		'https://www.migros.com.tr/cerezza-popcorn-patlamis-misir-super-boy-80-g-p-4da913',
		'https://www.a101.com.tr/kapida/atistirmalik/cerezza-patlamis-misir-80-g_p-15000693',
	),
	'ULK-CRZ-HOT' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/cerezza-misir-acili-cips-180-g_p-15001194',
	),
	'ULK-CRZ-ONI' => array(
		'https://www.migros.com.tr/cerezza-sinema-peynir-sogan-aromali-misir-cerezi-super-boy-117-g-p-4d8a74',
	),
	'ULK-CRZ-CKT' => array(
		'https://www.migros.com.tr/cerezza-kokteyl-karisik-misir-cerezi-super-boy-117-g-p-4da420',
		'https://www.a101.com.tr/kapida/atistirmalik/cerezza-kokteyl-misir-cipsi-117-g_p-13002769',
	),
	'ULK-CZI-CHE' => array(
		'https://www.migros.com.tr/ulker-cizi-peynirlikraker70g-p-6b127a',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-cizi-peynirli-kraker-4x70-g_p-17002015',
	),
	'ULK-KRS-CLS' => array(
		'https://www.migros.com.tr/ulker-krispi-baharatli-cubuk-kraker-43-g-p-6b1d07',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-krispi-baharatli-cubuk-kraker-43-g-_p-17002281',
	),
	'ULK-CBK-SLT' => array(
		'https://www.migros.com.tr/ulker-tuzlu-cubuk-kraker-80-g-p-6af7a2',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-cubuk-kraker-40-g_p-17002267',
	),
	'ULK-TAC-CLS' => array(
		'https://www.migros.com.tr/ulker-tac-kraker-76-g-p-6b153c',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-tuzlu-tac-kraker-3x76-g_p-17002642',
	),
	'ULK-SUS-CBK' => array(
		'https://www.migros.com.tr/ulker-susamli-cubuk-kraker-70-g-p-6afd85',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-susamli-kraker-70-g-_p-17002597',
	),
	'ULK-ALB-CLS' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-albeni-40-g_p-17000192',
		'https://www.migros.com.tr/albeni-buyuk-boy-52-g-p-6b530c',
	),
	'ULK-ALB-MIN' => array(
		'https://www.migros.com.tr/ulker-albeni-atistirmalik-cikolatali-72-g-p-6b1cc3',
	),
	'ULK-MET-CLS' => array(
		'https://www.migros.com.tr/ulker-metro-sutlu-cikolatali-kapli-karamel-nugali-bar-36-g-p-6b608e',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-metro-cikolata-nugali-bar-36-g_p-27000761',
	),
	'ULK-MET-MIN' => array(
		'https://www.migros.com.tr/ulker-metro-mini-coklu-paket-102-g-p-6b55a1',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-metro-mini-coklu-cikolata-102-g_p-27001045',
	),
	'ULK-LAV-CLS' => array(
		'https://www.migros.com.tr/ulker-laviva-dolgulu-ve-biskuvili-cikolata-35-g-p-6b46cc',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-laviva-biskuvili-bar-cikolata-35-g_p-27000060',
	),
	'ULK-CKN-CLS' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-cokonat-cikolatali-findikli-gofret-33-g_p-17000191',
		'https://www.migros.com.tr/ulker-cokonat-3lu-99-g-p-6d437e',
	),
	'ULK-CST-COC' => array(
		'https://www.migros.com.tr/ulker-coco-star-hindistan-cevizli-bar-25-g-p-6b5270',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-cocostar-hindistan-cevizli-bar-cikolata-25-g_p-27001164',
	),
	'ULK-CKN-PIS' => array(
		'https://www.migros.com.tr/ulker-cokonat-antep-fistikli-30-g-p-6b489b',
	),
	'ULK-YUP-FRU' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-yupo-jelly-meyve-bahceleri-yumusak-seker-24-g_p-27001170',
		'https://www.migros.com.tr/ulker-yupo-dino-meyveli-eksi-jelly-73-g-p-6b6e48',
	),
	'ULK-TAB-MLK' => array(
		'https://www.migros.com.tr/ulker-tablet-cikolata-sutlu-80-g-p-6b4540',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-sutlu-cikolata-60-g_p-27000755',
	),
	'ULK-TAB-PIS' => array(
		'https://www.migros.com.tr/ulker-butun-antep-fistikli-sutlu-kare-cikolata-65-g-p-6b56c8',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-antep-fistikli-cikolata-65-g_p-27001142',
	),
	'ULK-CKM-CLS' => array(
		'https://www.migros.com.tr/ulker-cokomel-marshmallow-sade-36-g-p-6b6b02',
		'https://www.a101.com.tr/kapida/atistirmalik/ulker-cokomel-sade-marshmallowlu-biskuvi-22-g_p-17003228',
	),
	/* ============================ بونوتشي ============================ */
	'BON-VAN-COC' => array(
		'https://www.a101.com.tr/market/bonucci-vanilya-kremali-kek-kakaolu-40-g/',
	),
	'BON-MRS-CHO' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/bonucci-cikolata-kaplamali-marshmallowlu-kek-8x23-g-_p-17003703',
	),
	'BON-SND-CHO' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/bonucci-marshmallowlu-sandvic-kek-23-g_p-17003540',
	),
	'BON-PIS-CRM' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/bonucci-antep-fistikli-kek-65-g_p-17003511',
	),
	'BON-CRM-RUL' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/bonucci-cremoso-kakaolu-rulo-kek-280-g_p-17003469',
	),
	'BON-DLC-RUL' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/bonucci-dolce-role-kakao-kremali-rulo-kek-50-g_p-17003709',
	),
	'BON-DUB-CHO' => array(
		'https://www.a101.com.tr/kapida/atistirmalik/bonucci-kadayifli-dubai-cikolatasi-35-g_p-27002437',
	),
);
