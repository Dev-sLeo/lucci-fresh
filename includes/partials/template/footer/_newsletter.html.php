<?php
$module = get_field('newsletter', 'tema');
$module = is_array($module) ? $module : [];

if (!$module) return;
?>
<section class="o-newsletter">
  <div class="s-container">
    <div class="o-newsletter__container" data-animate="fade-down" data-animate-delay="0.1"
      data-animate-duration="0.9">
      <div class="o-newsletter__left">
        <h2 class="o-newsletter__eyebrow subtitle"><?php echo esc_html($module['eyebrow'] ?? ''); ?></h2>
        <h3 class="o-newsletter__title"><?php echo esc_html($module['title'] ?? ''); ?></h3>
        <p class="o-newsletter__description"><?php echo esc_html($module['description'] ?? ''); ?></p>
      </div>
      <div class="o-newsletter__right">
        <p class="o-newsletter-form__title"><?php echo esc_html($module['title_form'] ?? ''); ?></p>
        <?= do_shortcode($module['form'] ?? '') ?>
      </div>
    </div>
  </div>
</section>