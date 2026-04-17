<?php
/**
 * My Account Page - Área do cliente
 */
defined('ABSPATH') || exit;

$current_user = wp_get_current_user();
?>

<div class="p-my-account">
  <div class="s-container">

    <?php do_action('woocommerce_before_account_navigation'); ?>

    <nav class="p-my-account__nav woocommerce-MyAccount-navigation">
      <ul>
        <?php foreach (wc_get_account_menu_items() as $endpoint => $label) : ?>
          <li class="<?= wc_get_account_menu_item_classes($endpoint); ?>">
            <a href="<?= esc_url(wc_get_account_endpoint_url($endpoint)); ?>">
              <?= esc_html($label); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <?php do_action('woocommerce_after_account_navigation'); ?>

    <div class="p-my-account__content woocommerce-MyAccount-content">
      <?php do_action('woocommerce_account_content'); ?>
    </div>

  </div>
</div>
