<?php
/**
 * Multan Tech Accessories — AdSense AdSlot Component
 * Controlled by admin settings (adsense_enabled, adsense_publisher_id)
 * Features reserved min-height to guarantee 0 Cumulative Layout Shift (CLS)
 * @var string $slotId
 * @var string $format ('rectangle'|'banner')
 */
$enabled = (bool)mta_setting('adsense_enabled', '0');
$pubId = (string)mta_setting('adsense_publisher_id', '');
$format = $format ?? 'rectangle';
$minHeight = $format === 'banner' ? '90px' : '280px';

if (!$enabled || empty($pubId)) return;
?>
<div class="ad-slot-container" style="margin: 2.5rem 0; text-align: center;">
  <span style="display: block; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 0.35rem;">
    Advertisement
  </span>
  <div style="min-height: <?= $minHeight ?>; background: var(--surface-2); border: 1px dashed var(--border); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; overflow: hidden;">
    <!-- Google AdSense Tag -->
    <ins class="adsbygoogle"
         style="display:block; text-align:center;"
         data-ad-client="<?= e($pubId) ?>"
         data-ad-slot="<?= e($slotId ?? '1234567890') ?>"
         data-ad-format="auto"
         data-full-width-responsive="true"></ins>
    <script>
      (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
  </div>
</div>
