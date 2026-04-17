<?php
global $tpl_engine;
$block = get_field('depoimentos');

$title               = $block['titulo'] ?? '';
$veja_todos          = $block['veja_todos'] ?? '';
$cards               = $block['cards'] ?? [];
$numeros_avaliacoes  = $block['numeros_de_avaliacoes'] ?? '';
$google_rating       = $block['estrelas_google'] ?? '';
?>

<section class="o-testimonials single-section">
  <div class="s-container">

    <div class="o-testimonials__header">
      <h2 class="o-testimonials__title" data-animate="fade-down" data-animate-delay="0.1">
        <?= $block['titulo']; ?>
      </h2>
      <a href="<?= $block['veja_todos']; ?>" class="o-testimonials__link" data-animate="fade-up" data-animate-delay="0.2">Ver todos</a>
    </div>

    <?php if (!empty($cards)) : ?>
      <div class="o-testimonials__list swiper" data-animate="fade-up" data-animate-delay="0.2">
        <div class="swiper-wrapper">

          <?php foreach ($cards as $card) :
            $stars       = $card['estrelas'] ?? 0;
            $photo       = $card['foto'] ?? null;
            $text        = $card['descricao'] ?? '';
            $name        = $card['nome'] ?? '';
            $role        = $card['descricao_nome'] ?? '';
            $desc_title  = $card['description_title'] ?? '';
          ?>
            <div class="swiper-slide">
              <article class="c-testimonial">

                <div class="c-testimonial__details">
                  <?php if ($photo) : ?>
                    <div class="c-testimonial__avatar">
                      <?= wp_get_attachment_image($photo['ID'], 'thumbnail', false, ['loading' => 'lazy']) ?>
                    </div>
                  <?php endif; ?>
                  <div class="c-testimonial__meta">
                    <?php if ($name) : ?><p class="c-testimonial__name"><?= esc_html($name); ?></p><?php endif; ?>
                    <?php if ($role) : ?><p class="c-testimonial__role"><?= esc_html($role); ?></p><?php endif; ?>
                  </div>
                </div>

                <div class="c-testimonial__divider" aria-hidden="true"></div>

                <div class="c-testimonial__content">
                  <div class="c-testimonial__top">
                    <span class="c-testimonial__quote" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="33" height="28" viewBox="0 0 33 28" fill="none">
                        <path d="M13.9057 14.1538V28H0V24.4103C0 18.8034 0.415094 14.6325 1.24528 11.8974C2.14465 9.09402 4.46226 5.1282 8.19811 0L14.1132 3.28205C11 8.54701 9.20126 12.1709 8.71698 14.1538H13.9057ZM32.7924 14.1538V28H18.8868V24.4103C18.8868 18.8034 19.3019 14.6325 20.1321 11.8974C21.0314 9.09402 23.3491 5.1282 27.0849 0L33 3.28205C29.8868 8.54701 28.0881 12.1709 27.6038 14.1538H32.7924Z" fill="#425D33" />
                      </svg></span>
                    <div class="c-testimonial__rating">
                      <?= render_stars($stars); ?>
                    </div>
                  </div>

                  <div class="c-testimonial__body">
                    <?php if ($desc_title) : ?>
                      <p class="c-testimonial__desc-title"><?= esc_html($desc_title) ?></p>
                    <?php endif; ?>

                    <?php if ($text) : ?>
                      <div class="c-testimonial__text"><?= wp_kses_post(wpautop($text)); ?></div>
                    <?php endif; ?>
                  </div>
                </div>

              </article>
            </div>
          <?php endforeach; ?>

        </div>

        <div class="o-testimonials__controls">
          <button class="o-testimonials__nav o-testimonials__nav--prev" type="button" aria-label="<?php echo esc_attr__('Anterior', 'textdomain'); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10" fill="none">
              <path d="M4.75 8.75L0.75 4.75L4.75 0.75" stroke="#FD8426" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
          <div class="swiper-pagination o-testimonials__pagination"></div>
          <button class="o-testimonials__nav o-testimonials__nav--next" type="button" aria-label="<?php echo esc_attr__('Próximo', 'textdomain'); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10" fill="none">
              <path d="M0.75 8.75L4.75 4.75L0.75 0.75" stroke="#FD8426" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
        </div>

      </div>
    <?php endif; ?>

    <div class="o-testimonials__bottom" data-animate="fade-up" data-animate-delay="0.25">
      <div class="o-testimonials__bottom-left">
        <div class="o-home-testimonials__logo">
          <img src="<?= get_stylesheet_directory_uri() ?>/public/image/logo-tripadvisor.webp" alt="Tripadvisor Logo">
        </div>
      </div>
      <div class="o-testimonials__bottom-right">
        <?php if ($google_rating !== '' && $google_rating !== null) : ?>
          <div class="o-testimonials__google">
            <span class="o-testimonials__google-score">
              <?= esc_html(number_format((float) $google_rating, 1, '.', '')); ?>
            </span>
            <?= render_stars($google_rating); ?>
          </div>
        <?php endif; ?>
        <?php if ($numeros_avaliacoes) : ?>
          <div class="o-testimonials__numbers">
            <?= esc_html($numeros_avaliacoes); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</section>