<?php global $tpl_engine; ?>
<div class="o-splide splide s-container">
  <div class="splide__track">
    <ul class="splide__list">
      <?php foreach ($testimonials as $card): ?>
        <li class="splide__slide">

        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php $tpl_engine->partial('components/slider/arrows') ?>
</div>