<?php
// includes/partials/template/pages/contact/_contact.html.php
global $tpl_engine;

$contato    = get_field('content') ?? [];
$eyebrow    = $contato['eyebrow']   ?? __('Contato', 'lucci-fresh');
$titulo     = $contato['title']     ?? __('Entre em contato com a gente', 'lucci-fresh');
$form_title = $contato['title_form'] ?? __('Preencha o formulário abaixo', 'lucci-fresh');
$form_sc    = $contato['form']      ?? '';

// Grupos com text + url
$telefone = $contato['telefone'] ?? [];
$tel_text = is_array($telefone) ? ($telefone['text'] ?? '11 96075-2237') : $telefone;
$tel_url  = is_array($telefone) ? ($telefone['url']  ?? 'tel:+5511960752237') : '';

$email_f    = $contato['email'] ?? [];
$email_text = is_array($email_f) ? ($email_f['text'] ?? 'luccifresh.saudavel@gmail.com') : $email_f;
$email_url  = is_array($email_f) ? ($email_f['url']  ?? 'mailto:luccifresh.saudavel@gmail.com') : '';

$endereco_f    = $contato['endereco'] ?? [];
$endereco_text = is_array($endereco_f) ? ($endereco_f['text'] ?? 'Rua Américo Vespucci, 646, Vila Prudente – 03135-010') : $endereco_f;
$endereco_url  = is_array($endereco_f) ? ($endereco_f['url']  ?? '') : '';

$endereco_2    = $contato['endereco_2'] ?? [];
$endereco_2_text = is_array($endereco_2) ? ($endereco_2['text'] ?? 'Rua Américo Vespucci, 646, Vila Prudente – 03135-010') : $endereco_2;
$endereco_2_url  = is_array($endereco_2) ? ($endereco_2['url']  ?? '') : '';

$horario = $contato['horario'] ?? 'Segunda à sexta das 10h às 18h30 · Sábado das 10h às 15h';
?>
<section class="s-ct-contact">
  <div class="s-container">
    <div class="s-ct-contact__inner">

      <!-- Coluna de informações -->
      <div class="s-ct-contact__info">

        <div class="s-ct-contact__header">
          <?php if ($eyebrow) : ?>
            <h1 class="s-ct-contact__eyebrow"><?= esc_html($eyebrow) ?></h1>
          <?php endif; ?>
          <h2 class="s-ct-contact__title"><?= esc_html($titulo) ?></h2>
        </div>

        <ul class="s-ct-contact__items">

          <?php if ($tel_text) : ?>
            <li class="s-ct-contact__item">
              <span class="s-ct-contact__icon" aria-hidden="true">
                <?php $tpl_engine->svg('icons/contact/telefone') ?>
              </span>
              <?php if ($tel_url) : ?>
                <a class="s-ct-contact__item-text" href="<?= esc_url($tel_url) ?>"><?= esc_html($tel_text) ?></a>
              <?php else : ?>
                <p class="s-ct-contact__item-text"><?= esc_html($tel_text) ?></p>
              <?php endif; ?>
            </li>
          <?php endif; ?>

          <?php if ($email_text) : ?>
            <li class="s-ct-contact__item">
              <span class="s-ct-contact__icon" aria-hidden="true">
                <?php $tpl_engine->svg('icons/contact/email') ?>
              </span>
              <?php if ($email_url) : ?>
                <a class="s-ct-contact__item-text" href="<?= esc_url($email_url) ?>"><?= esc_html($email_text) ?></a>
              <?php else : ?>
                <p class="s-ct-contact__item-text"><?= esc_html($email_text) ?></p>
              <?php endif; ?>
            </li>
          <?php endif; ?>

          <?php if ($endereco_text) : ?>
            <li class="s-ct-contact__item">
              <span class="s-ct-contact__icon" aria-hidden="true">
                <?php $tpl_engine->svg('icons/contact/adress') ?>
              </span>
              <?php if ($endereco_url) : ?>
                <a class="s-ct-contact__item-text" href="<?= esc_url($endereco_url) ?>"><?= esc_html($endereco_text) ?></a>
              <?php else : ?>
                <p class="s-ct-contact__item-text"><?= esc_html($endereco_text) ?></p>
              <?php endif; ?>
            </li>
          <?php endif; ?>

          <?php if ($endereco_2_text) : ?>
            <li class="s-ct-contact__item">
              <span class="s-ct-contact__icon" aria-hidden="true">
                <?php $tpl_engine->svg('icons/contact/adress') ?>
              </span>
              <?php if ($endereco_2_url) : ?>
                <a class="s-ct-contact__item-text" href="<?= esc_url($endereco_2_url) ?>"><?= esc_html($endereco_2_text) ?></a>
              <?php else : ?>
                <p class="s-ct-contact__item-text"><?= esc_html($endereco_2_text) ?></p>
              <?php endif; ?>
            </li>
          <?php endif; ?>

          <?php if ($horario) : ?>
            <li class="s-ct-contact__item">
              <span class="s-ct-contact__icon" aria-hidden="true">
                <?php $tpl_engine->svg('icons/contact/horario') ?>
              </span>
              <p class="s-ct-contact__item-text"><?= esc_html($horario) ?></p>
            </li>
          <?php endif; ?>

        </ul>
      </div>

      <!-- Coluna do formulário -->
      <div class="s-ct-contact__form-wrap">
        <h2 class="s-ct-contact__form-title"><?= esc_html($form_title) ?></h2>

        <?php if ($form_sc) : ?>
          <div class="s-ct-contact__cf7">
            <?= do_shortcode($form_sc) ?>
          </div>
        <?php else : ?>
          <form class="s-ct-contact__form js-contact-form" novalidate>
            <?php wp_nonce_field('contact_form_nonce', 'contact_nonce') ?>

            <div class="s-ct-contact__fields">
              <input
                class="s-ct-contact__field"
                type="text"
                name="name"
                placeholder="<?= esc_attr__('Nome', 'lucci-fresh') ?>"
                required>

              <input
                class="s-ct-contact__field"
                type="email"
                name="email"
                placeholder="<?= esc_attr__('E-mail', 'lucci-fresh') ?>"
                required>

              <input
                class="s-ct-contact__field"
                type="tel"
                name="telefone"
                placeholder="<?= esc_attr__('Telefone', 'lucci-fresh') ?>">

              <input
                class="s-ct-contact__field"
                type="text"
                name="assunto"
                placeholder="<?= esc_attr__('Assunto', 'lucci-fresh') ?>">

              <textarea
                class="s-ct-contact__field"
                name="message"
                placeholder="<?= esc_attr__('Mensagem', 'lucci-fresh') ?>"
                required></textarea>
            </div>

            <div class="s-ct-contact__feedback js-contact-feedback" aria-live="polite" hidden></div>

            <button type="submit" class="c-btn c-btn--primary s-ct-contact__submit">
              <?= esc_html__('Enviar', 'lucci-fresh') ?>
            </button>
          </form>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section><!-- /.s-ct-contact -->