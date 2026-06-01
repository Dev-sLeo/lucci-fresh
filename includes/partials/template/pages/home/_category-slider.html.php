<?php
// includes/partials/template/pages/home/_category-slider.html.php
global $tpl_engine;

$terms = get_terms([
  'taxonomy'   => 'product_cat',
  'hide_empty' => true,
  'orderby'    => 'menu_order',
  'order'      => 'ASC',
  'parent'     => 0,
  'exclude'    => [get_term_by('slug', 'sem-categoria', 'product_cat')->term_id ?? 0],
]);

if (is_wp_error($terms) || empty($terms)) return;
?>

<section class="s-category-slider">
  <div class="s-container">
    <div class="s-category-slider__header">
      <div class="s-category-slider__heading">
        <p class="s-category-slider__eyebrow">
          <?= esc_html__('Categorias', 'lucci-fresh') ?>
        </p>
        <h2 class="s-category-slider__title">
          <?= esc_html__('Refeições feitas com amor', 'lucci-fresh') ?>
        </h2>
      </div>
    </div>

    <div class="s-category-slider__track">
      <button
        class="s-category-slider__prev js-category-prev"
        aria-label="<?= esc_attr__('Categoria anterior', 'lucci-fresh') ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="23" viewBox="0 0 12 23" fill="none">
          <path d="M11.0835 22.0833L0.750163 11.4167L11.0835 0.75" stroke="#FD8426" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>

      <div class="swiper js-category-slider">
        <div class="swiper-wrapper">
          <?php foreach ($terms as $term) :
            $thumbnail_id  = get_term_meta($term->term_id, 'thumbnail_id', true);
            $thumbnail_src = $thumbnail_id
              ? wp_get_attachment_image_src($thumbnail_id, 'large')
              : null;
            $img_url  = $thumbnail_src ? $thumbnail_src[0] : '';
            $term_url = get_term_link($term);
          ?>
            <div class="swiper-slide">
              <a href="<?= esc_url($term_url) ?>" class="c-category-card">
                <?php if ($img_url) : ?>
                  <img
                    class="c-category-card__image"
                    src="<?= esc_url($img_url) ?>"
                    alt="<?= esc_attr($term->name) ?>"
                    loading="lazy"
                    decoding="async">
                <?php else : ?>
                  <div class="c-category-card__image c-category-card__image--placeholder"></div>
                <?php endif; ?>

                <div class="c-category-card__label">
                  <span class="c-category-card__name"><?= esc_html($term->name) ?></span>
                  <span class="c-category-card__arrow" aria-hidden="true">
                    <?php $tpl_engine->svg('icons/arrow-link') ?>
                  </span>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <button
        class="s-category-slider__next js-category-next"
        aria-label="<?= esc_attr__('Próxima categoria', 'lucci-fresh') ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="23" viewBox="0 0 12 23" fill="none">
          <path d="M0.75 22.0833L11.0833 11.4167L0.75 0.75" stroke="#FD8426" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>
    </div>
  </div>
</section>