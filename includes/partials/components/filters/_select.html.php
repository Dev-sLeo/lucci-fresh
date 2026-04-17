<?php global $tpl_engine; ?>
<?php
$icon    = $icon ?? '';
$label   = $label ?? 'Selecione';
$name    = $nome ?? 'select_field';
$options = $options ?? [];
?>

<div class="c-select" data-filter="<?= esc_attr($name); ?>">

  <div class="c-select__control">
    <span class="c-select__icon">
      <?= $icon ? $tpl_engine->svg('filters/' . $icon) : '' ?>
    </span>

    <span class="c-select__label"><?= esc_html($label); ?></span>

    <span class="c-select__arrow">
      <?php $tpl_engine->svg('filters/arrow'); ?>
    </span>
  </div>

  <div class="c-select__dropdown">
    <?php foreach ($options as $opt): ?>

      <?php
      // Se for array → usa label e value
      if (is_array($opt)) {
        $opt_label = $opt['label'] ?? '';
        $opt_value = $opt['value'] ?? sanitize_title($opt_label);
      } else {
        // Se for string → label = value
        $opt_label = $opt;
        $opt_value = sanitize_title($opt);
      }
      ?>

      <button
        class="c-select__option"
        data-value="<?= esc_attr($opt_value); ?>">
        <?= esc_html($opt_label); ?>
      </button>

    <?php endforeach; ?>
  </div>

</div>