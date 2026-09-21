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

// O campo país é ocultado via CSS (.wc-block-components-address-form__country { display:none })
// NÃO restringimos woocommerce_countries para não quebrar plugins de frete/cálculo de endereço

// Não pré-selecionar nenhum método de entrega: o cliente deve escolher ativamente
add_filter('woocommerce_shipping_chosen_method', function ($default, $rates, $chosen_method) {
    return $chosen_method ? $default : '';
}, 10, 3);

// Em toda nova visita ao carrinho/checkout, limpa qualquer método de entrega
// já escolhido (inclusive retirada na loja) para que nada venha pré-selecionado
// nem seja somado ao total antes do cliente escolher manualmente.
// Não afeta a escolha real: cliques no checkout acontecem via Store API/REST,
// que não passa por este hook (apenas o carregamento normal da página passa).
add_action('template_redirect', function () {
    if (!(is_cart() || is_checkout())) return;
    if (!WC()->session) return;

    WC()->session->set('chosen_shipping_methods', []);
    WC()->session->set('shipping_method_counts', []);
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
    $locale['BR']['state']['required'] = true;
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
        $fields['billing']['billing_address_2']['required']    = false;
    }
    if (isset($fields['shipping']['shipping_address_2'])) {
        $fields['shipping']['shipping_address_2']['priority'] = 110; // por último
        $fields['shipping']['shipping_address_2']['required'] = false;
    }
    if (isset($fields['billing']['billing_city'])) {
        $fields['billing']['billing_city']['label']    = __('Cidade', 'arterra');
        $fields['billing']['billing_city']['class']    = ['form-row-wide'];
        $fields['billing']['billing_city']['priority'] = 100;
    }
    if (isset($fields['shipping']['shipping_city'])) {
        $fields['shipping']['shipping_city']['class']    = ['form-row-wide'];
        $fields['shipping']['shipping_city']['priority'] = 100;
    }
    if (isset($fields['billing']['billing_postcode'])) {
        $fields['billing']['billing_postcode']['label']       = __('CEP', 'arterra');
        $fields['billing']['billing_postcode']['placeholder'] = '00000-000';
        $fields['billing']['billing_postcode']['class']       = ['form-row-first'];
        $fields['billing']['billing_postcode']['priority']    = 80;
    }
    if (isset($fields['shipping']['shipping_postcode'])) {
        $fields['shipping']['shipping_postcode']['class']    = ['form-row-first'];
        $fields['shipping']['shipping_postcode']['priority'] = 80;
    }
    // Bairro: nenhum plugin instalado registra esse campo, então criamos o nosso
    $fields['billing']['billing_neighborhood'] = [
        'label'       => __('Bairro', 'arterra'),
        'placeholder' => '',
        'required'    => true,
        'class'       => ['form-row-last'],
        'priority'    => 81, // ao lado do CEP
    ];
    if (isset($fields['shipping']['shipping_address_1'])) {
        $fields['shipping']['shipping_neighborhood'] = [
            'label'       => __('Bairro', 'arterra'),
            'placeholder' => '',
            'required'    => true,
            'class'       => ['form-row-last'],
            'priority'    => 81,
        ];
    }
    if (isset($fields['billing']['billing_phone'])) {
        $fields['billing']['billing_phone']['label']       = __('Celular', 'arterra');
        $fields['billing']['billing_phone']['placeholder'] = '(00) 00000-0000';
        $fields['billing']['billing_phone']['required']    = true;
        $fields['billing']['billing_phone']['class']       = ['form-row-last'];
    }
    if (isset($fields['shipping']['shipping_phone'])) {
        $fields['shipping']['shipping_phone']['class'] = ['form-row-last'];
    }
    if (isset($fields['billing']['billing_cpf'])) {
        $fields['billing']['billing_cpf']['class']    = ['form-row-first'];
        $fields['billing']['billing_cpf']['required'] = true;
    }
    if (isset($fields['order']['order_comments'])) {
        $fields['order']['order_comments']['label'] = __('Observações para entrega', 'arterra');
    }

    // Endereço | Número lado a lado
    if (isset($fields['billing']['billing_address_1'])) {
        $fields['billing']['billing_address_1']['class']    = ['form-row-first'];
        $fields['billing']['billing_address_1']['priority'] = 90;
    }
    if (isset($fields['shipping']['shipping_address_1'])) {
        $fields['shipping']['shipping_address_1']['class']    = ['form-row-first'];
        $fields['shipping']['shipping_address_1']['priority'] = 90;
    }

    // Adiciona campo "Número" do endereço
    $fields['billing']['billing_number'] = [
        'label'       => __('Número', 'arterra'),
        'placeholder' => __('Nº', 'arterra'),
        'required'    => true,
        'class'       => ['form-row-last'],
        'priority'    => 91, // logo após o endereço
    ];

    if (isset($fields['shipping']['shipping_address_1'])) {
        $fields['shipping']['shipping_number'] = [
            'label'       => __('Número', 'arterra'),
            'placeholder' => __('Nº', 'arterra'),
            'required'    => true,
            'class'       => ['form-row-last'],
            'priority'    => 91,
        ];
    }

    // País e Estado fixos (Brasil / São Paulo): ocultos, pois não há escolha real
    if (isset($fields['billing']['billing_country'])) {
        $fields['billing']['billing_country']['class'] = ['form-row-wide', 'fc-hidden-field'];
    }
    if (isset($fields['shipping']['shipping_country'])) {
        $fields['shipping']['shipping_country']['class'] = ['form-row-wide', 'fc-hidden-field'];
    }
    if (isset($fields['billing']['billing_state'])) {
        $fields['billing']['billing_state']['type']     = 'hidden';
        $fields['billing']['billing_state']['default']  = 'SP';
        $fields['billing']['billing_state']['required'] = false;
        $fields['billing']['billing_state']['class']     = ['form-row-wide', 'fc-hidden-field'];
    }
    if (isset($fields['shipping']['shipping_state'])) {
        $fields['shipping']['shipping_state']['type']     = 'hidden';
        $fields['shipping']['shipping_state']['default']  = 'SP';
        $fields['shipping']['shipping_state']['required'] = false;
        $fields['shipping']['shipping_state']['class']     = ['form-row-wide', 'fc-hidden-field'];
    }

    return $fields;
});

// Complemento (address_2) é opcional: evita que o Fluid Checkout colapse o campo
// atrás de um link "Adicionar apartamento... (opcional)". Sem isso, o campo some/
// reaparece a cada recálculo de frete (o HTML do endereço é regerado via AJAX),
// dando a impressão de que o formulário "quebra" quando o frete é calculado.
add_filter('fc_hide_optional_fields_skip_list', function ($skip_list) {
    $skip_list[] = 'address_2';
    $skip_list[] = 'billing_address_2';
    $skip_list[] = 'shipping_address_2';
    return $skip_list;
});

// Salva os campos "Número" e "Bairro" no pedido
add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
    if (!empty($_POST['billing_number'])) {
        update_post_meta($order_id, '_billing_number', sanitize_text_field(wp_unslash($_POST['billing_number'])));
    }
    if (!empty($_POST['shipping_number'])) {
        update_post_meta($order_id, '_shipping_number', sanitize_text_field(wp_unslash($_POST['shipping_number'])));
    }
    if (!empty($_POST['billing_neighborhood'])) {
        update_post_meta($order_id, '_billing_neighborhood', sanitize_text_field(wp_unslash($_POST['billing_neighborhood'])));
    }
    if (!empty($_POST['shipping_neighborhood'])) {
        update_post_meta($order_id, '_shipping_neighborhood', sanitize_text_field(wp_unslash($_POST['shipping_neighborhood'])));
    }
});

// Exibe "Número" e "Bairro" junto ao endereço (admin, emails, detalhes do pedido)
add_filter('woocommerce_order_formatted_billing_address', function ($address, $order) {
    $number = $order->get_meta('_billing_number');
    if ($number) {
        $address['address_1'] = trim($address['address_1'] . ', ' . $number);
    }
    $neighborhood = $order->get_meta('_billing_neighborhood');
    if ($neighborhood) {
        $address['address_2'] = trim($address['address_2'] . ($address['address_2'] ? ' - ' : '') . 'Bairro: ' . $neighborhood);
    }
    return $address;
}, 10, 2);

add_filter('woocommerce_order_formatted_shipping_address', function ($address, $order) {
    $number = $order->get_meta('_shipping_number');
    if ($number) {
        $address['address_1'] = trim($address['address_1'] . ', ' . $number);
    }
    $neighborhood = $order->get_meta('_shipping_neighborhood');
    if ($neighborhood) {
        $address['address_2'] = trim($address['address_2'] . ($address['address_2'] ? ' - ' : '') . 'Bairro: ' . $neighborhood);
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

// Garante SP no objeto WC_Customer durante o cálculo de frete (Store API / Checkout Block)
// Sem isso, o campo state oculto nunca é enviado e plugins de frete por distância falham
add_filter('woocommerce_customer_get_billing_state', function ($state) {
    return $state ?: 'SP';
});
add_filter('woocommerce_customer_get_shipping_state', function ($state) {
    return $state ?: 'SP';
});

// Garante SP no processamento do pedido, mesmo com campo oculto
add_filter('woocommerce_checkout_posted_data', function ($data) {
    $data['billing_state']  = 'SP';
    $data['shipping_state'] = 'SP';
    return $data;
});

// -----------------------------------------------------------------------------
// Desconto de 5% para pagamento via Pix
// -----------------------------------------------------------------------------

add_action('woocommerce_cart_calculate_fees', function ($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;
    if (!$cart || $cart->is_empty()) return;

    $chosen_method = WC()->session ? WC()->session->get('chosen_payment_method') : '';
    if (!$chosen_method || stripos($chosen_method, 'pix') === false) return;

    $discount = -1 * ($cart->get_subtotal() * 0.05);

    $cart->add_fee(__('Desconto Pix (5%)', 'arterra'), $discount, false);
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
// Minha conta
// -----------------------------------------------------------------------------

// Habilita o botão nativo "Repetir compra" também para pedidos em processamento,
// além dos já concluídos, na lista de pedidos em Minha Conta.
add_filter('woocommerce_valid_order_statuses_for_order_again', function ($statuses) {
    $statuses[] = 'processing';
    return array_unique($statuses);
});

// O WooCommerce só mostra "Repetir compra" dentro do pedido (hook
// woocommerce_order_again_button), não na listagem de "Meus pedidos". Aqui
// adicionamos a mesma ação na listagem, reaproveitando a mesma URL e as
// mesmas regras de elegibilidade de status usadas no botão do pedido.
add_filter('woocommerce_my_account_my_orders_actions', function ($actions, $order) {
    $statuses_for_reordering = apply_filters('woocommerce_valid_order_statuses_for_order_again', ['completed']);

    if ($order->has_status($statuses_for_reordering)) {
        $actions['order-again'] = [
            'url'        => wp_nonce_url(add_query_arg('order_again', $order->get_id(), wc_get_cart_url()), 'woocommerce-order_again'),
            'name'       => __('Order again', 'woocommerce'),
            /* translators: %s: order number */
            'aria-label' => sprintf(__('Order again number %s', 'woocommerce'), $order->get_order_number()),
        ];
    }

    return $actions;
}, 10, 2);

// Remove o item "Downloads" do menu de Minha Conta
add_filter('woocommerce_account_menu_items', function ($items) {
    unset($items['downloads']);
    return $items;
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
// Fluid Checkout: nome do pacote de entrega ("Remessa" -> "Taxa de entrega")
// -----------------------------------------------------------------------------

add_filter('gettext_with_context', function ($translated, $text, $context, $domain) {
    if ($domain === 'fluid-checkout' && $text === 'Shipping' && $context === 'shipping packages') {
        return 'Taxa de entrega';
    }
    return $translated;
}, 10, 4);

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

// -----------------------------------------------------------------------------
// Tabela de preços por quantidade (Advanced Woo Dynamic Pricing) - arredondamento
// -----------------------------------------------------------------------------

/**
 * Os percentuais de desconto cadastrados no plugin não formam uma progressão
 * exata de preços "redondos" (ex.: 19,90 / 19,75 / 19,50). Arredondando ao
 * centavo, o preço exibido na tabela de quantidade fica a 1-2 centavos do
 * valor pretendido em alguns degraus. Arredondamos para o múltiplo de 0,05
 * mais próximo somente durante a renderização dessa tabela (sem afetar
 * preços do carrinho, checkout ou exibição normal do produto).
 *
 * O plugin renderiza a tabela através de uma única action, definida pela
 * opção "awdp_table_position" (ver class-awdp-front-end.php), sempre com
 * prioridade 100. Habilitamos o arredondamento só nessa janela (99 a 101).
 */
if (!function_exists('arterra_round_awdp_pricing_table_price')) {
    function arterra_round_awdp_pricing_table_price($price, $original_price = null)
    {
        $step = 0.05;
        return round($price / $step) * $step;
    }
}

add_action('plugins_loaded', function () {
    if (!class_exists('AWDP_Discount')) return;

    $awdp_table_hooks = [
        'woocommerce_before_single_product',
        'woocommerce_before_single_product_summary',
        'woocommerce_single_product_summary',
        'woocommerce_before_add_to_cart_form',
        'woocommerce_before_variations_form',
        'woocommerce_before_add_to_cart_button',
        'woocommerce_after_add_to_cart_button',
        'woocommerce_after_variations_form',
        'woocommerce_after_add_to_cart_form',
        'woocommerce_product_meta_start',
        'woocommerce_product_meta_end',
        'woocommerce_after_single_product_summary',
        'woocommerce_after_single_product',
    ];

    foreach ($awdp_table_hooks as $hook) {
        add_action($hook, function () {
            add_filter('raw_woocommerce_price', 'arterra_round_awdp_pricing_table_price', 10, 2);
        }, 99);

        add_action($hook, function () {
            remove_filter('raw_woocommerce_price', 'arterra_round_awdp_pricing_table_price', 10);
        }, 101);
    }
}, 20);

// -----------------------------------------------------------------------------
// Fix: YITH WooCommerce Delivery Date x Flexible Shipping
// -----------------------------------------------------------------------------
// O YITH Delivery Date só exibe os campos de data/hora no checkout se o método
// de envio escolhido tiver "Método de processamento" configurado. Para o
// Flexible Shipping, o YITH adiciona esses dois campos extras (select_process_method
// e set_method_as_mandatory) via `woocommerce_settings_api_form_fields_flexible_shipping_info`,
// mas esse hook é o da tela de configurações globais do plugin (id
// `flexible_shipping_info`), não o da tela de edição de cada instância de método
// por zona de entrega (id real: `flexible_shipping`). Como o merchant configura
// o método na tela por zona, os campos nunca aparecem lá e nunca são salvos em
// `woocommerce_flexible_shipping_<instance_id>_settings` — que é exatamente a
// option que o YITH lê em `get_woocommerce_shipping_option()`. Resultado: o
// processing method fica sempre vazio e os campos de data somem no checkout.
// Corrige registrando os mesmos campos no hook do id real do método.
add_filter('woocommerce_settings_api_form_fields_flexible_shipping', function ($form_fields) {
    if (!function_exists('YITH_Delivery_Date_Processing_Method')) {
        return $form_fields;
    }

    $form_fields['select_process_method'] = [
        'title'   => __('Processing Method', 'yith-woocommerce-delivery-date'),
        'type'    => 'select',
        'default' => '',
        'class'   => 'ywcdd_processing_method wc-enhanced-select',
        'options' => YITH_Delivery_Date_Processing_Method()->get_formatted_processing_method(),
    ];

    $form_fields['set_method_as_mandatory'] = [
        'title'       => __('Set as required', 'yith-woocommerce-delivery-date'),
        'type'        => 'checkbox',
        'default'     => 'no',
        'class'       => 'ywcdd_set_mandatory',
        'description' => __('If enabled, customers must select a date for the delivery', 'yith-woocommerce-delivery-date'),
    ];

    return $form_fields;
}, 99);

// -----------------------------------------------------------------------------
// Fix: CSS do plugin "Integration Rede Itaú for WooCommerce" (woo-rede)
// vazando para outras telas do admin
// -----------------------------------------------------------------------------
// O CSS admin do plugin (lkn-integration-rede-for-woocommerce-admin.css) é
// carregado em TODAS as páginas do wp-admin (sem checar a tela) e contém a
// regra global `.form-table tbody, .form-table tbody tr td { width: 100%
// !important; }`. `.form-table` é a classe padrão usada pelo WordPress/
// WooCommerce em qualquer tela de configurações (ex.: método de envio do
// Flexible Shipping, YITH Delivery Date etc.), então essa regra força a
// coluna <td> a 100% de largura em telas que nada têm a ver com o plugin,
// espremendo a coluna <th> e quebrando o texto letra por letra.
// Neutralizamos a regra fora da própria tela de configuração do gateway
// Rede/Itaú, sem precisar alterar o arquivo do plugin (que seria sobrescrito
// em updates).
add_action('admin_enqueue_scripts', function () {
    if (!wp_style_is('lkn-integration-rede-for-woocommerce', 'enqueued')) {
        return;
    }

    $page    = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
    $tab     = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : '';
    $section = isset($_GET['section']) ? sanitize_text_field(wp_unslash($_GET['section'])) : '';

    $is_own_screen = 'wc-settings' === $page
        && 'checkout' === $tab
        && (bool) preg_match('/rede|maxipago/i', $section);

    if ($is_own_screen) {
        return;
    }

    wp_add_inline_style(
        'lkn-integration-rede-for-woocommerce',
        '.form-table tbody, .form-table tbody tr td { width: auto !important; }'
    );
}, 999);

// O YITH WooCommerce Delivery Date só traz traduções para pt_PT (Portugal),
// não para pt_BR. Sem tradução pt_BR, o WordPress usa o texto original em
// inglês, e o campo de data de entrega aparece com labels em inglês no meio
// de um checkout todo em português. Traduzimos manualmente as strings do
// campo de data que ficam visíveis no checkout.
add_filter('gettext', function ($translation, $text, $domain) {
    if ('yith-woocommerce-delivery-date' !== $domain) {
        return $translation;
    }

    $strings = [
        'Delivery Date'                            => 'Data de entrega',
        'Select a delivery date'                   => 'Selecione uma data de entrega',
        'Carrier'                                  => 'Transportadora',
        'Select a carrier'                         => 'Selecione uma transportadora',
        'Select Carrier'                           => 'Selecione a transportadora',
        'Time Slot'                                => 'Horário',
        'Select time slot'                         => 'Selecione um horário',
        'Fee'                                      => 'Taxa',
        'Enter a valid date.'                      => 'Informe uma data válida.',
        'Error: the date %s isn\'t available.'     => 'A data %s não está disponível.',
    ];

    return $strings[$text] ?? $translation;
}, 10, 3);

// O formato do horário ("From: 10:00 - To: 21:00") usa _x() com contexto
// próprio, que o filtro `gettext` acima não intercepta — precisa do
// `gettext_with_context`.
add_filter('gettext_with_context', function ($translation, $text, $context, $domain) {
    if ('yith-woocommerce-delivery-date' !== $domain) {
        return $translation;
    }

    if ('From' === $text && 'from time' === $context) {
        return 'De';
    }
    if ('To' === $text && 'to time' === $context) {
        return 'Até';
    }

    return $translation;
}, 10, 4);

// -----------------------------------------------------------------------------
// YITH WooCommerce Delivery Date — compatibilidade com métodos de frete que a
// própria YITH não configura corretamente (Flexible Shipping com regras da
// Octolize e o Local Pickup nativo do WooCommerce). Sem isso, o campo de data
// de entrega nunca aparece para esses métodos, mesmo com tudo mais certo no
// checkout — a YITH nunca recebe um "Processing Method" válido para eles.
//
// "Taxa de Entrega" (Flexible Shipping / regras de distância da Octolize) já
// está coberto pelo filtro `woocommerce_settings_api_form_fields_flexible_
// shipping` mais acima (ver seção "Fix: YITH WooCommerce Delivery Date x
// Flexible Shipping"). Confirmado via teste real (nome do campo no HTML =
// `woocommerce_flexible_shipping_select_process_method`) que esse é o id
// certo — não precisa de nada além disso pra esse método.
// -----------------------------------------------------------------------------

// "Retirar na loja" (Local Pickup nativo do WooCommerce, id "pickup_location")
//
// Esse método não usa o formulário de configurações clássico do WooCommerce —
// a tela de admin dele é 100% React (ver WC_Blocks Shipping\PickupLocation::
// admin_options()) — então não existe hook equivalente a
// `woocommerce_settings_api_form_fields_pickup_location` para a YITH (ou nós)
// adicionar um campo ali. A YITH também não tem nenhuma integração própria
// para esse id (confirmado: nenhuma ocorrência de "pickup_location" no plugin).
//
// Como alternativa, mapeamos cada endereço de retirada (índice 0, 1, 2... na
// ordem em que aparecem em WooCommerce > Configurações > Entrega > Local
// Pickup) a um "Processing Method" já cadastrado em WooCommerce > Data de
// Entrega > Processing Methods. Esse mapeamento fica salvo em uma option do
// banco (não em ID fixo no código, que mudaria entre local/staging/produção)
// e é editado por uma tela própria em Configurações > Retirada + Data de
// Entrega, com um <select> por endereço listando os Processing Methods reais
// já cadastrados no ambiente atual.
const LUCCI_PICKUP_PROCESSING_METHOD_OPTION = 'lucci_pickup_location_processing_methods';

add_filter('ywcdd_get_shipping_method_option', function ($shipping_settings, $shipping_id) {
    if (0 !== strpos((string) $shipping_id, 'pickup_location')) {
        return $shipping_settings;
    }

    $option_name              = str_replace(':', '_', (string) $shipping_id);
    $processing_method_by_loc = get_option(LUCCI_PICKUP_PROCESSING_METHOD_OPTION, []);

    if (empty($processing_method_by_loc[$option_name])) {
        return $shipping_settings;
    }

    return [
        'select_process_method'   => (string) $processing_method_by_loc[$option_name],
        'set_method_as_mandatory' => 'no',
    ];
}, 20, 2);

// Cache: o "Data de entrega" às vezes fica em branco mesmo com tudo
// configurado certo (blocos acima) — o motivo é que a YITH calcula os dias
// disponíveis fazendo até 30 consultas separadas ao banco (uma por dia do
// calendário, em includes/class.yith-delivery-date-calendar.php::is_holiday(),
// sem nenhum cache). Medimos essa chamada em ~18-19s neste ambiente; se o
// hospedeiro tiver latência de banco parecida, ela pode passar do tempo
// máximo de execução do PHP e o pedido simplesmente falha sem aviso — dando
// a impressão de que o calendário "nunca aparece", mesmo esperando bastante.
//
// Como não dá pra alterar o cache dentro do plugin (arquivo de terceiros),
// colocamos um cache de resultado por fora: a mesma combinação de
// transportadora + método de processamento só recalcula essa lista pesada
// uma vez por dia (os dias disponíveis não mudam de uma consulta pra outra
// no mesmo dia); as próximas consultas do dia respondem na hora.
add_action('wp_ajax_update_datepicker', 'arterra_ywcdd_serve_cached_datepicker', 1);
add_action('wp_ajax_nopriv_update_datepicker', 'arterra_ywcdd_serve_cached_datepicker', 1);
function arterra_ywcdd_serve_cached_datepicker()
{
    if (empty($_POST['ywcdd_carrier_id'])) {
        return;
    }

    $carrier_id = (int) $_POST['ywcdd_carrier_id'];
    $process_id = (int) ($_POST['ywcdd_process_id'] ?? 0);
    $cache_key  = 'arterra_ywcdd_dp_' . $carrier_id . '_' . $process_id . '_' . current_time('Y-m-d');

    $cached = get_transient($cache_key);
    if (false !== $cached) {
        wp_send_json($cached);
        // wp_send_json() já chama wp_die(); a linha abaixo nunca roda de
        // fato, mas deixa claro pra quem ler que a execução para aqui.
        return;
    }

    // Cache miss: deixa a YITH calcular normalmente (fluxo original, sem
    // interferência), só capturando o resultado dela pra guardar em cache
    // pro próximo cliente que pedir a mesma combinação hoje.
    ob_start();
    add_action('shutdown', function () use ($cache_key) {
        $output = ob_get_clean();
        $decoded = json_decode($output, true);

        if (JSON_ERROR_NONE === json_last_error() && !empty($decoded['available_days'])) {
            // 6 horas: dá pra ajustar se as regras de calendário/feriado
            // mudarem com mais frequência que isso.
            set_transient($cache_key, $decoded, 6 * HOUR_IN_SECONDS);
        }

        echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }, 0);
}
