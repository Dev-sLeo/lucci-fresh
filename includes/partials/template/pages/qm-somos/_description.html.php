<?php
// includes/partials/template/pages/qm-somos/_description.html.php
global $tpl_engine;

$desc_block = get_field('description') ?? [];
$texto      = $desc_block['texto']   ?? '';
$citacao    = $desc_block['citacao'] ?? '';

if (!$texto && !$citacao) return;
?>
<section class="s-qs-about">
  <div class="s-container">

    <?php if ($texto) : ?>
      <div class="s-qs-about__texto">
        <?= wp_kses_post($texto) ?>
      </div>
    <?php endif; ?>

    <?php if ($citacao) : ?>
      <blockquote class="s-qs-about__quote">
        <span class="s-qs-about__quote-mark" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" width="41" height="30" viewBox="0 0 41 30" fill="none">
            <path d="M9.16076 30C6.40509 30 4.17075 29.0476 2.45777 27.1429C0.819255 25.1648 0 22.6007 0 19.4506C0 15.348 1.22888 11.5385 3.68665 8.02197C6.14441 4.43223 9.198 1.75824 12.8474 0L13.5177 1.31868C11.9537 2.49084 10.5758 3.99267 9.3842 5.82417C8.19255 7.65567 7.33606 9.92674 6.81471 12.6374L9.16076 13.1868C11.7675 13.7729 13.7784 14.8718 15.1935 16.4835C16.683 18.022 17.4278 19.8901 17.4278 22.0879C17.4278 24.4322 16.6085 26.337 14.97 27.8022C13.406 29.2674 11.4696 30 9.16076 30ZM32.733 30C29.9773 30 27.743 29.0476 26.03 27.1429C24.3915 25.1648 23.5722 22.6007 23.5722 19.4506C23.5722 15.348 24.8011 11.5385 27.2589 8.02197C29.7166 4.43223 32.7702 1.75824 36.4196 0L37.0899 1.31868C35.5259 2.49084 34.148 3.99267 32.9564 5.82417C31.7648 7.65567 30.9083 9.92674 30.3869 12.6374L32.733 13.1868C35.3397 13.7729 37.3506 14.8718 38.7657 16.4835C40.2552 18.022 41 19.8901 41 22.0879C41 24.4322 40.1807 26.337 38.5422 27.8022C36.9782 29.2674 35.0418 30 32.733 30Z" fill="#1F2818" />
          </svg>
        </span>
        <p class="s-qs-about__quote-text"><?= esc_html($citacao) ?></p>
      </blockquote>
    <?php endif; ?>

  </div>
</section><!-- /.s-qs-about -->