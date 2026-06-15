<?php

/**
 * WooCommerce - Hooks, filtros e customizações do tema Arterra
 */
defined('ABSPATH') || exit;

if (!class_exists('WooCommerce')) return;

// -----------------------------------------------------------------------------
// Layout & estrutura
// -----------------------------------------------------------------------------

// Remove o wrapper padrão do WC e usa o do tema
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

// Remove o breadcrumb padrão (o tema controla isso nos templates)
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

// Remove a sidebar padrão do WooCommerce
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

// Número de produtos por linha e por página
add_filter('loop_shop_columns', function () {
    return 3;
});

add_filter('loop_shop_per_page', function () {
    return 12;
}, 20);

// -----------------------------------------------------------------------------
// Imagens de produto
// -----------------------------------------------------------------------------

// Tamanho das imagens
add_filter('woocommerce_get_image_size_gallery_thumbnail', function ($size) {
    return [
        'width'  => 100,
        'height' => 100,
        'crop'   => 1,
    ];
});

// -----------------------------------------------------------------------------
// Botão "Adicionar ao carrinho"
// -----------------------------------------------------------------------------

// Texto padrão do botão no loop
add_filter('woocommerce_product_add_to_cart_text', function ($text, $product) {
    if (!$product->is_in_stock()) {
        return __('Indisponível', 'arterra');
    }

    return __('Adicionar ao carrinho', 'arterra');
}, 10, 2);

// Classe CSS do botão "add to cart" no loop
add_filter('woocommerce_loop_add_to_cart_args', function ($args, $product) {
    $args['class'] = implode(' ', array_filter([
        'button',
        'c-btn',
        'c-btn--primary',
        'c-product-card__cta',
        'add_to_cart_button',
        $product->supports('ajax_add_to_cart') ? 'ajax_add_to_cart' : '',
    ]));
    return $args;
}, 10, 2);

// -----------------------------------------------------------------------------
// Checkout
// -----------------------------------------------------------------------------

// Localização padrão (Brasil)
add_filter('default_checkout_billing_country', function () {
    return 'BR';
});
add_filter('default_checkout_shipping_country', function () {
    return 'BR';
});

// Restringe a loja apenas ao Brasil (oculta o seletor de país no Checkout Block)
add_filter('woocommerce_countries', function ($countries) {
    return ['BR' => $countries['BR']];
});
add_filter('woocommerce_shipping_countries', function ($countries) {
    return ['BR' => $countries['BR']];
});

// Campos desnecessários para mercado BR
add_filter('woocommerce_checkout_fields', function ($fields) {

    // Remove campo de empresa (pode ser reativado conforme necessidade)
    // unset($fields['billing']['billing_company']);

    // Remove campo de estado (preenchido automaticamente pelo CEP)
    // unset($fields['billing']['billing_state']);

    return $fields;
});

// -----------------------------------------------------------------------------
// Checkout Blocks (Store API) - campo "Número" e estado fixo SP
// -----------------------------------------------------------------------------

// Registra o campo adicional "Número" (aparece no Checkout Block, em ambos endereços)
add_action('woocommerce_init', function () {
    if (!function_exists('woocommerce_register_additional_checkout_field')) return;

    woocommerce_register_additional_checkout_field([
        'id'       => 'arterra/address_number',
        'label'    => __('Número', 'arterra'),
        'location' => 'address',
        'type'     => 'text',
        'required' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ]);
});

// Salva o "Número" também na linha de endereço (pedidos via Checkout Block)
add_filter('woocommerce_order_formatted_billing_address', function ($address, $order) {
    $number = $order->get_meta('arterra/address_number') ?: $order->get_meta('_billing_number');
    if ($number && !empty($address['address_1'])) {
        $address['address_1'] = trim($address['address_1'] . ', ' . $number);
    }
    return $address;
}, 10, 2);

add_filter('woocommerce_order_formatted_shipping_address', function ($address, $order) {
    $number = $order->get_meta('arterra/address_number') ?: $order->get_meta('_shipping_number');
    if ($number && !empty($address['address_1'])) {
        $address['address_1'] = trim($address['address_1'] . ', ' . $number);
    }
    return $address;
}, 10, 2);

// Estado fixo (São Paulo): oculta o campo no Checkout Block via locale do país
// Renomeia "Código postal" para "CEP" no Checkout Block
add_filter('woocommerce_get_country_locale', function ($locale) {
    $locale['BR']['state']['required'] = false;
    $locale['BR']['state']['hidden']   = true;
    $locale['BR']['postcode']['label'] = __('CEP', 'arterra');
    return $locale;
});

// Reordena e renomeia campos de cobrança para PT-BR
add_filter('woocommerce_checkout_fields', function ($fields) {
    if (isset($fields['billing']['billing_first_name'])) {
        $fields['billing']['billing_first_name']['label'] = __('Nome', 'arterra');
    }
    if (isset($fields['billing']['billing_last_name'])) {
        $fields['billing']['billing_last_name']['label'] = __('Sobrenome', 'arterra');
    }
    if (isset($fields['billing']['billing_address_1'])) {
        $fields['billing']['billing_address_1']['label']       = __('Endereço', 'arterra');
        $fields['billing']['billing_address_1']['placeholder'] = __('Rua, Av., número...', 'arterra');
    }
    if (isset($fields['billing']['billing_address_2'])) {
        $fields['billing']['billing_address_2']['label']       = __('Complemento', 'arterra');
        $fields['billing']['billing_address_2']['placeholder'] = __('Apto, bloco, sala...', 'arterra');
        $fields['billing']['billing_address_2']['priority']    = 110; // por último
    }
    if (isset($fields['shipping']['shipping_address_2'])) {
        $fields['shipping']['shipping_address_2']['priority'] = 110; // por último
    }
    if (isset($fields['billing']['billing_city'])) {
        $fields['billing']['billing_city']['label'] = __('Cidade', 'arterra');
    }
    if (isset($fields['billing']['billing_postcode'])) {
        $fields['billing']['billing_postcode']['label']       = __('CEP', 'arterra');
        $fields['billing']['billing_postcode']['placeholder'] = '00000-000';
    }
    if (isset($fields['billing']['billing_phone'])) {
        $fields['billing']['billing_phone']['label']       = __('Celular', 'arterra');
        $fields['billing']['billing_phone']['placeholder'] = '(00) 00000-0000';
        $fields['billing']['billing_phone']['required']    = true;
    }

    // Adiciona campo "Número" do endereço
    $fields['billing']['billing_number'] = [
        'label'       => __('Número', 'arterra'),
        'placeholder' => __('Nº', 'arterra'),
        'required'    => true,
        'class'       => ['form-row-wide'],
        'priority'    => 51, // logo após o endereço
    ];

    if (isset($fields['shipping']['shipping_address_1'])) {
        $fields['shipping']['shipping_number'] = [
            'label'       => __('Número', 'arterra'),
            'placeholder' => __('Nº', 'arterra'),
            'required'    => true,
            'class'       => ['form-row-wide'],
            'priority'    => 51,
        ];
    }

    // Estado fixo: São Paulo (entrega somente nesse estado)
    if (isset($fields['billing']['billing_state'])) {
        $fields['billing']['billing_state']['type']     = 'hidden';
        $fields['billing']['billing_state']['default']  = 'SP';
        $fields['billing']['billing_state']['required'] = false;
    }
    if (isset($fields['shipping']['shipping_state'])) {
        $fields['shipping']['shipping_state']['type']     = 'hidden';
        $fields['shipping']['shipping_state']['default']  = 'SP';
        $fields['shipping']['shipping_state']['required'] = false;
    }

    return $fields;
});

// Salva o campo "Número" no pedido
add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
    if (!empty($_POST['billing_number'])) {
        update_post_meta($order_id, '_billing_number', sanitize_text_field(wp_unslash($_POST['billing_number'])));
    }
    if (!empty($_POST['shipping_number'])) {
        update_post_meta($order_id, '_shipping_number', sanitize_text_field(wp_unslash($_POST['shipping_number'])));
    }
});

// Exibe o campo "Número" junto ao endereço (admin, emails, detalhes do pedido)
add_filter('woocommerce_order_formatted_billing_address', function ($address, $order) {
    $number = $order->get_meta('_billing_number');
    if ($number) {
        $address['address_1'] = trim($address['address_1'] . ', ' . $number);
    }
    return $address;
}, 10, 2);

add_filter('woocommerce_order_formatted_shipping_address', function ($address, $order) {
    $number = $order->get_meta('_shipping_number');
    if ($number) {
        $address['address_1'] = trim($address['address_1'] . ', ' . $number);
    }
    return $address;
}, 10, 2);

// Estado fixo: força São Paulo independentemente do envio
add_filter('default_checkout_billing_state', function () {
    return 'SP';
});
add_filter('default_checkout_shipping_state', function () {
    return 'SP';
});

// Garante SP no processamento do pedido, mesmo com campo oculto
add_filter('woocommerce_checkout_posted_data', function ($data) {
    $data['billing_state']  = 'SP';
    $data['shipping_state'] = 'SP';
    return $data;
});

// -----------------------------------------------------------------------------
// Emails transacionais
// -----------------------------------------------------------------------------

// Personaliza o "from name" dos e-mails do WooCommerce
add_filter('woocommerce_email_from_name', function ($from_name) {
    return get_bloginfo('name');
}, 10);

// Footer text dos e-mails
add_filter('woocommerce_email_footer_text', function () {
    return sprintf(
        /* translators: %s: store name */
        __('Você recebeu este e-mail porque realizou uma compra em %s.', 'arterra'),
        get_bloginfo('name')
    );
});

// -----------------------------------------------------------------------------
// Mini-cart / fragmentos
// -----------------------------------------------------------------------------

// Garante que os fragmentos AJAX do carrinho estejam habilitados
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    if (!function_exists('WC') || !WC()->cart) return $fragments;

    // Atualiza o badge de quantidade no header
    $count = WC()->cart->get_cart_contents_count();
    $fragments['.o-header__cart-count'] =
        '<span class="o-header__cart-count' . ($count > 0 ? '' : ' is-hidden') . '">' . esc_html($count) . '</span>';

    // Atualiza o conteúdo dinâmico do cart sidebar
    ob_start();
    include PATHS_PARTIALS . '/components/_cart-sidebar-content.html.php';
    $fragments['#cart-sidebar-content'] = ob_get_clean();

    return $fragments;
});

// -----------------------------------------------------------------------------
// Scripts & Estilos
// -----------------------------------------------------------------------------

// Remove o link "Ver carrinho" exibido após adicionar produto
add_filter('woocommerce_loop_add_to_cart_args', function ($args) {
    $args['class'] = str_replace('ajax_add_to_cart', 'ajax_add_to_cart no-view-cart', $args['class'] ?? '');
    return $args;
});
add_filter('woocommerce_add_to_cart_added_to_cart_notification', '__return_false');

add_action('wp_enqueue_scripts', function () {
    // Mantém os estilos do WC descarregados — o tema gerencia o CSS via SASS
    // Para habilitar os estilos padrão, remova estas linhas:
    // wp_dequeue_style('woocommerce-general');
    // wp_dequeue_style('woocommerce-layout');
    // wp_dequeue_style('woocommerce-smallscreen');
}, 99);

// -----------------------------------------------------------------------------
// Traduções de strings do WooCommerce Blocks
// -----------------------------------------------------------------------------

add_filter('gettext', function ($translated, $text, $domain) {
    if ($domain !== 'woo-gutenberg-products-block' && $domain !== 'woocommerce-blocks') {
        return $translated;
    }
    $map = [
        'Add a coupon'  => 'Adicionar cupom',
        'Add coupons'   => 'Adicionar cupom',
        'Add coupon'    => 'Adicionar cupom',
        'Apply coupon'  => 'Aplicar',
    ];
    return $map[$text] ?? $translated;
}, 10, 3);

// -----------------------------------------------------------------------------
// Login com e-mail apenas (sem nome de usuário)
// -----------------------------------------------------------------------------

add_filter('gettext', function ($translated, $text, $domain) {
    if ($domain !== 'woocommerce') {
        return $translated;
    }
    $map = [
        'Username or email address' => 'E-mail',
    ];
    return $map[$text] ?? $translated;
}, 10, 3);

// -----------------------------------------------------------------------------
// Campo "Telefone" do Checkout Block -> "Celular" e obrigatório
// -----------------------------------------------------------------------------

add_filter('gettext', function ($translated, $text, $domain) {
    if ($domain !== 'woocommerce') {
        return $translated;
    }
    $map = [
        'Phone (optional)'  => 'Celular',
        'Phone'             => 'Celular',
        'Postcode / ZIP'    => 'CEP',
        'Postcode / ZIP *'  => 'CEP',
        'ZIP Code'          => 'CEP',
        'Postcode'          => 'CEP',
    ];
    return $map[$text] ?? $translated;
}, 10, 3);

// Marca o campo de telefone como obrigatório no Checkout Block
add_filter('woocommerce_get_country_locale', function ($locale) {
    foreach ($locale as $country => $fields) {
        if (isset($fields['phone'])) {
            $locale[$country]['phone']['required'] = true;
        }
    }
    return $locale;
});

// -----------------------------------------------------------------------------
// Busca de produtos
// -----------------------------------------------------------------------------

// Inclui produtos na busca global do WordPress
add_filter('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query()) return $query;

    if ($query->is_search()) {
        $post_types   = (array) $query->get('post_type');
        $post_types[] = 'product';
        $query->set('post_type', array_unique($post_types));
    }

    return $query;
});

// -----------------------------------------------------------------------------
// Helpers do tema para WooCommerce
// -----------------------------------------------------------------------------

/**
 * Retorna o HTML do preço formatado para exibição no tema.
 */
if (!function_exists('arterra_get_product_price')) {
    function arterra_get_product_price(WC_Product $product): string
    {
        return '<span class="c-product-card__price">' . $product->get_price_html() . '</span>';
    }
}

/**
 * Retorna a URL do carrinho.
 */
if (!function_exists('arterra_cart_url')) {
    function arterra_cart_url(): string
    {
        return function_exists('wc_get_cart_url') ? wc_get_cart_url() : '';
    }
}

/**
 * Retorna a quantidade de itens no carrinho.
 */
if (!function_exists('arterra_cart_count')) {
    function arterra_cart_count(): int
    {
        return function_exists('WC') ? WC()->cart->get_cart_contents_count() : 0;
    }
}
