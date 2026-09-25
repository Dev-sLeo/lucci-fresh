# Customizações de WooCommerce — guia para manutenção futura

Este documento descreve as customizações de WooCommerce feitas no tema (arquivo
[`extension/woocommerce.php`](../extension/woocommerce.php)), o motivo de cada
uma existir, e para onde olhar antes de mexer nelas de novo — seja em um
update de plugin, seja numa feature nova.

> **Regra geral do arquivo**: nenhuma customização aqui edita arquivos de
> plugins de terceiros diretamente. Tudo é feito via hooks (`add_action` /
> `add_filter`) a partir do tema, exatamente como a documentação oficial da
> WooCommerce recomenda para extensões e customizações — ver
> [developer.woocommerce.com/docs](https://developer.woocommerce.com/docs/) →
> seção **Extensions** (boas práticas de desenvolvimento) e **Best Practices**
> (performance). Isso significa que updates de plugin não apagam essas
> correções, mas também significa que **uma mudança de versão pode invalidar
> um hook** (nome de action/filter, id de shipping method, etc.) — é
> exatamente isso que já aconteceu com duas das correções documentadas abaixo.

## Ambiente no momento em que isso foi escrito

| Componente | Versão |
|---|---|
| WooCommerce | 10.8.1 |
| YITH WooCommerce Delivery Date Premium | 2.42.0 |
| Flexible Shipping (Octolize) | 6.12.0 |
| Fluid Checkout | 4.2.7 |

Se qualquer um desses subir de versão major/minor, **revalide os itens
marcados com ⚠️ abaixo** antes de assumir que continuam funcionando — a causa
raiz de mais de um bug documentado aqui foi justamente um id de shipping
method ou nome de hook que mudou entre versões do plugin.

## Documentação oficial validada

A [documentação de desenvolvedor da WooCommerce](https://developer.woocommerce.com/docs/)
foi reestruturada e hoje é organizada em: Getting Started, Features, Theming,
Block Development, Best Practices, Code Snippets, Contribution, Extensions,
API e CLI. Ela é boa para conceitos e boas práticas, mas **não substitui o
código-fonte** para detalhes de implementação de classes como
`WC_Shipping_Method` / `WC_Settings_API` — para isso, a referência é o
[WooCommerce Code Reference](https://woocommerce.github.io/code-reference/)
(gerado via PHPDoc a partir do código-fonte) e, quando a dúvida é sobre uma
extensão de terceiros (YITH, Octolize/Flexible Shipping), **o código-fonte do
próprio plugin instalado em `wp-content/plugins/`** — como fizemos para achar
todas as correções abaixo.

## Índice de customizações por seção

### Layout, produtos e carrinho
Remoção de wrappers/breadcrumb/sidebar padrão do WC, colunas da loja,
tamanho de imagem, texto e classes do botão "Adicionar ao carrinho",
fragmentos AJAX do mini-carrinho. Customização direta e estável — não há
pegadinha de versão aqui, é a forma padrão documentada de sobrescrever
templates/estilos do WooCommerce a partir de um tema.

### Checkout — campos para o mercado brasileiro
Bloco grande do arquivo. Adiciona os campos "Número" e "Bairro" (não
cobertos por nenhum plugin instalado), fixa País=Brasil e Estado=São Paulo
(loja não atende fora de SP), traduz labels para PT-BR, e salva os campos
extras no pedido (`woocommerce_checkout_update_order_meta` +
`woocommerce_order_formatted_*_address`).

⚠️ **Ponto de atenção**: existem DOIS caminhos de checkout paralelos sendo
cobertos ao mesmo tempo — o **Checkout Block / Store API**
(`woocommerce_register_additional_checkout_field`, filtros
`woocommerce_customer_get_*_state`, `woocommerce_checkout_posted_data`) e o
**checkout clássico via Fluid Checkout** (`woocommerce_checkout_fields`,
`fc_hide_optional_fields_skip_list`). Isso não é redundância por engano: o
site já operou nos dois modelos em momentos diferentes desta conversa (ver
histórico), e o Fluid Checkout pode alternar entre eles conforme
configuração. **Ao adicionar um campo de checkout novo, adicione nos dois
lugares**, ou ele só vai aparecer em um dos dois fluxos.

### Desconto Pix, e-mails, Minha Conta
Customizações pontuais e auto-contidas (desconto de 5% no Pix via
`woocommerce_cart_calculate_fees`, remetente/rodapé de e-mail, botão "Repetir
compra" também para pedidos "processing" e também na listagem de pedidos, não
só dentro do pedido). Sem pegadinhas de versão conhecidas.

### Traduções (gettext / gettext_with_context)
Vários filtros `gettext`/`gettext_with_context` traduzindo strings que os
plugins não trazem em pt-BR (Checkout Blocks, WooCommerce core, YITH Delivery
Date, Fluid Checkout). **Padrão a seguir para textos novos em inglês**: nunca
edite o arquivo `.po`/`.mo` do plugin (é apagado em updates) — adicione a
string no `$strings`/`$map` do filtro `gettext` correspondente, ou crie um
novo filtro se for de um domínio ainda não coberto. Referência oficial:
[Text Domain / i18n na documentação da WooCommerce](https://developer.woocommerce.com/docs/)
→ seção Extensions, e a [I18n API do WordPress](https://developer.wordpress.org/plugins/internationalization/).

### AWDP (Advanced Woo Dynamic Pricing) — arredondamento de preço
Corrige um problema de centavos "quebrados" na tabela de preço progressivo,
ativando um filtro `raw_woocommerce_price` só durante a janela de renderização
daquela tabela específica (liga/desliga via prioridade 99/101 nas actions que
o próprio plugin usa para desenhar a tabela). Não afeta preço de carrinho,
checkout ou exibição normal do produto — só aquela tabela.

### Fix de CSS vazando do plugin "Integration Rede/Itaú" (woo-rede)
Não é hook de dados, é CSS: o plugin de pagamento carrega uma folha de estilo
globalmente em `/wp-admin` (sem checar a tela atual) com uma regra que quebra
QUALQUER tela de configurações baseada em `.form-table` — inclusive a do
Flexible Shipping e da YITH Delivery Date. Neutralizamos a regra fora da tela
própria do gateway via `wp_add_inline_style` com prioridade 999 em
`admin_enqueue_scripts`. **Se o problema visual voltar depois de um update do
woo-rede**, é porque o handle do style mudou de nome — confira
`wp_style_is('lkn-integration-rede-for-woocommerce', 'enqueued')` contra o
que o plugin realmente registra na nova versão.

## YITH WooCommerce Delivery Date × Flexible Shipping × Local Pickup nativo

> ⚠️ **Desligado desde 2026-09** — a loja decidiu não usar mais data/
> transportadora de entrega no checkout. A integração toda (a causa raiz de
> um bug real: `validate_checkout_width_delivery_date()` do plugin disparava
> `wc_add_notice(..., 'error')` sempre que o carrinho precisava de frete e o
> método escolhido tinha "Processing Method" configurado, mesmo em uso
> normal — a notice ficava presa na sessão e reaparecia
> (`.woocommerce-NoticeGroup-updateOrderReview`, com scroll) em toda
> atualização do carrinho) foi removida via `remove_action()` em
> `extension/woocommerce.php` (seção "Desliga o YITH WooCommerce Delivery
> Date no checkout"). O resto desta seção documenta a integração como ela
> era ANTES dessa remoção — histórico útil se a loja decidir usar o plugin de
> novo no futuro, mas não reflete o comportamento atual do checkout.

Esta é a seção mais frágil do arquivo — três correções empilhadas, cada uma
resolvendo uma causa raiz diferente do mesmo sintoma ("campo de data de
entrega não aparece, ou aparece vazio sem calendário"). Documentado em detalhe
porque **qualquer futura mudança de método de frete na loja provavelmente vai
esbarrar aqui de novo**.

### 1. "Processing Method" ausente para métodos com regras (Flexible Shipping)

A integração nativa da YITH
(`includes/integrations/class.yith-wc-flexible-shipping-integration.php`)
tenta adicionar os campos "Processing Method" / "Set as required" no hook
`woocommerce_settings_api_form_fields_flexible_shipping_info` — mas o id real
do método na versão instalada da Octolize é **`flexible_shipping`** (sem
sufixo). Confirmado empiricamente: o atributo `name` do `<select>` renderizado
na tela de edição do método é `woocommerce_flexible_shipping_select_process_method`,
o que só é possível se `$this->id === 'flexible_shipping'`
(ver `WC_Settings_API::get_field_key()` no
[Code Reference](https://woocommerce.github.io/code-reference/)).

**Correção**: registramos os mesmos dois campos no hook certo
(`woocommerce_settings_api_form_fields_flexible_shipping`). Depois disso, o
merchant configura o "Processing Method" normalmente em *WooCommerce →
Configurações → Entrega → [zona] → [nome do método]*, igual a qualquer outro
método de frete.

⚠️ **Se atualizar o Flexible Shipping/Octolize**: confirme que o id do método
continua sendo `flexible_shipping` — abra a tela de edição do método, inspecione
o `name` de qualquer campo nativo do formulário (ex.: "Method title") no
HTML; o prefixo antes do nome do campo é o id real.

### 2. Local Pickup nativo do WooCommerce (`pickup_location`) não tem tela de configuração clássica

A partir da versão em que o WooCommerce passou a ter retirada em múltiplos
endereços nativamente (`Automattic\WooCommerce\Blocks\Shipping\PickupLocation`),
a tela de admin desse método é **100% React** — `admin_options()` não chama
`generate_settings_html()`, então não existe hook
`woocommerce_settings_api_form_fields_pickup_location` para adicionar nada.
A YITH também não tem nenhuma integração própria para esse id (confirmamos:
zero ocorrências de `pickup_location` no código do plugin).

**Correção**: mapeamento via filtro `ywcdd_get_shipping_method_option`, ligando
cada índice de endereço de retirada (`pickup_location:0`, `pickup_location:1`,
...) a um "Processing Method" já cadastrado em *WooCommerce → Data de Entrega
→ Processing Methods*. O mapeamento **não fica em ID fixo no código** (isso
quebraria ao migrar entre local/staging/produção, onde o ID do post do
Processing Method muda) — fica salvo na option `lucci_pickup_location_
processing_methods`, editável em **Configurações → Retirada + Data de
Entrega** (tela registrada em `extension/woocommerce.php`, função
`lucci_render_pickup_processing_methods_page`). A tela lista os endereços de
retirada reais (lidos de `get_option('pickup_location_pickup_locations')`) e
os Processing Methods reais do ambiente atual (`yith_proc_method`), então
funciona em qualquer ambiente sem editar PHP. **Ao adicionar um novo endereço
de retirada**, basta abrir essa tela de novo — o novo endereço aparece
automaticamente na lista para receber um Processing Method.

### 3. Calendário aparece vazio mesmo com tudo configurado (cache)

Mesmo com os dois itens acima corretos, o campo de data pode aparecer só com
a caixa (sem o calendário jQuery UI abrir), mesmo esperando bastante. Causa
raiz: `YITH_Delivery_Date_Calendar::is_holiday()`
(`includes/class.yith-delivery-date-calendar.php`) faz **uma consulta SQL por
dia do calendário** (até 30 por requisição, sem nenhum cache), medida em
~18–19s neste ambiente. Isso não é responsabilidade nossa consertar dentro do
plugin, mas dá pra evitar que o mesmo cálculo se repita:

- **Cache de resultado**: interceptamos `wp_ajax_update_datepicker` (prioridade
  1, antes do handler da própria YITH) e cacheamos a resposta JSON completa
  por 6h, por combinação `carrier_id + processing_method_id + dia`. A
  primeira pessoa do dia ainda espera o tempo real do plugin; todo mundo
  depois responde em menos de 1s.
- **Bug de corrida separado**: no Fluid Checkout, o evento que o YITH usa pra
  saber "o método mudou" pode disparar cedo demais (antes do cliente escolher
  o frete), fazendo o plugin achar que "nada mudou" e nunca inicializar o
  `.datepicker()` do jQuery UI — o campo então fica com o HTML certo, mas sem
  calendário algum, para sempre. Corrigido no JS do tema
  ([`webpack/js/scripts/deliveryDateReposition.js`](../webpack/js/scripts/deliveryDateReposition.js)):
  detectamos esse estado "meio-renderizado" (conteúdo presente, mas sem a
  classe `hasDatepicker`) e forçamos o evento `init-delivery-fields` (o mesmo
  que o próprio plugin dispara depois de um recálculo bem-sucedido), uma vez
  por render.

⚠️ **Se o tempo de resposta real (não em cache) mudar muito** (pra melhor ou
pra pior), ajuste o `HOUR_IN_SECONDS` do cache e reavalie se o "auto-reparo"
do JS ainda é necessário — ele foi desenhado como rede de segurança e não
deveria causar nenhum efeito colateral mesmo se o bug de corrida do plugin for
corrigido em uma versão futura (a condição `hasDatepicker` simplesmente nunca
seria verdadeira, e o `ensureDatepickerInitialized()` não faria nada).

## Checklist para a próxima feature/update de WooCommerce

1. Antes de mexer num shipping method, **confirme o id real** inspecionando o
   `name` de um campo nativo na tela de edição — não confie no nome do plugin
   ou no slug da URL.
2. Antes de assumir que um hook da YITH (ou de qualquer plugin de terceiros)
   está sendo usado, **grep no código do plugin instalado** por esse nome de
   hook — a documentação online desses plugins costuma estar desatualizada
   em relação à versão real instalada.
3. Para dúvidas de API/classes core do WooCommerce, use o
   [Code Reference](https://woocommerce.github.io/code-reference/) como fonte
   primária; a [documentação de conceitos](https://developer.woocommerce.com/docs/)
   é melhor para "como fazer X da forma recomendada" do que para "qual é a
   assinatura exata desse método".
4. Nunca edite arquivos dentro de `wp-content/plugins/` — tudo cabe em hooks
   a partir do tema (`extension/woocommerce.php` ou um novo arquivo em
   `extension/` se crescer demais).
5. Depois de qualquer mudança em frete/checkout, teste manualmente
   selecionando cada método de entrega cadastrado e confirmando que o campo
   de data (se aplicável) aparece com calendário funcional — os bugs aqui
   documentados só aparecem na hora de escolher o método, não em nenhuma
   validação estática de configuração.

## Checkout v2 — checkout custom nativo (substitui o Fluid Checkout)

Existe um segundo checkout, construído inteiramente com hooks nativos do
WooCommerce (sem Fluid Checkout), com layout próprio do tema baseado no
Figma. Ele fica atrás de uma feature flag e ainda não é o padrão em produção.

- **Flag**: `luccifresh_new_checkout_enabled()` em `extension/woocommerce.php`,
  controlada pela opção **WooCommerce → Ajustes → Avançado → "Checkout novo
  (Lucci Fresh)"** (checkbox "Ativar checkout novo", salva como a opção
  `luccifresh_new_checkout_enabled`). Uma constante `LUCCIFRESH_NEW_CHECKOUT_ENABLED`
  em `wp-config.php` continua funcionando como override manual (útil para
  debug local), mas o jeito normal de ligar/desligar é pelo painel. Com a
  opção desligada, nada muda — o Fluid Checkout continua sendo o checkout
  real de produção. **Lembre-se de desativar o plugin Fluid Checkout em
  Plugins antes de ligar essa opção** (ver descoberta #3 abaixo).
- **Template**: `woocommerce/checkout/form-checkout.php` decide entre o
  markup antigo (`p-checkout`) e o novo (`p-checkout-v2`, via
  `$tpl_engine->partial('template/pages/checkout/wizard')`).
- **Parciais** em `includes/partials/template/pages/checkout/`: `wizard`
  (shell + colunas), `progress` (indicador de 4 etapas), `step-dados`,
  `step-entrega`, `step-pagamento`, `step-revisao`, `order-summary`.
- **Cabeçalho/rodapé "distraction-free"**: o Figma mostra a página de
  checkout SEM o menu principal e SEM o rodapé completo (só uma barra final
  com "Lucci Fresh • Refeições feitas com amor" + link de privacidade/
  WhatsApp). `header.php`/`footer.php` checam
  `luccifresh_is_new_checkout_page()` (helper em `extension/woocommerce.php`)
  e, quando verdadeiro, trocam os parciais completos
  (`template/header/header`, `template/footer/footer` + `newsletter` +
  `mobile-menu`) pelos mínimos (`template/header/header-checkout`,
  `template/footer/footer-checkout`) - a faixa de entrega
  (`template/header/header-top`) continua igual, é o mesmo componente do
  site inteiro. **Isso só afeta a página real de Checkout com a opção
  ligada** - todas as outras páginas (inclusive Carrinho) continuam com o
  header/footer completos normalmente.
- **Um único form, 4 "steps" visuais**: é o mesmo `<form name="checkout">`
  do WooCommerce inteiro; os steps só controlam visibilidade
  (`webpack/js/scripts/checkoutSteps.js`, classe `.is-active` +
  `data-checkout-step`). Isso significa que **nenhum campo, validação ou
  gateway precisou ser reimplementado** — CPF, número, bairro, frete YITH,
  Pix/Rede/COD continuam exatamente como documentados nas seções acima.
- **Campos por step**: divididos manualmente via
  `luccifresh_render_billing_field_group()` (mesmo `extension/woocommerce.php`),
  que renderiza um subconjunto de `WC()->checkout()->get_checkout_fields('billing')`
  com `woocommerce_form_field()` — não duplica `required`/labels/priority,
  só decide em qual step cada campo aparece.
- **Método de frete movido para o step "Entrega"**: por padrão o WooCommerce
  imprime a lista de métodos de frete dentro do resumo do pedido
  (`review-order.php`). O Figma pede essa lista junto ao endereço, então
  `step-entrega.html.php` chama `wc_cart_totals_shipping_html()`
  diretamente. Para não duplicar a lista (dois grupos de radio com o mesmo
  `name` brigando pela seleção), `woocommerce/checkout/review-order.php` é um
  override do template core do WooCommerce que **omite esse bloco quando a
  flag está ligada** — leia o comentário no topo do arquivo antes de mexer,
  porque é fácil reintroduzir a duplicação sem perceber.
- **Pagamento**: `step-pagamento.html.php` só chama
  `do_action('woocommerce_checkout_payment')` (hook padrão) — o visual de
  "3 cards" é CSS puro sobre o `ul.wc_payment_methods` nativo
  (`_checkout-v2.scss`). O toggle de campos por gateway ao trocar o radio já
  vem do JS core do WooCommerce (`assets/js/frontend/checkout.js`).
- **Revisão**: não tem template PHP próprio — `checkoutSteps.js` lê os
  valores já digitados nos steps anteriores via DOM (`fieldValue()`) e
  preenche o resumo. O botão "Confirmar pedido" só clica no `#place_order`
  real, renderizado pelo `checkout/payment.php` padrão do WooCommerce dentro
  do step de Pagamento — o submit e toda a validação de servidor continuam
  sendo o fluxo padrão do WooCommerce.
- ⚠️ **Sem JS**: `checkoutSteps.js` adiciona a classe `js-stepped` no form;
  o CSS só esconde steps não-ativos quando essa classe existe
  (`_checkout-v2.scss`). Se o JS falhar ao carregar, todos os steps ficam
  visíveis e o checkout continua completável — verificar isso continua
  funcionando antes de qualquer mudança no bundle JS do checkout.
- **Antes de trocar a flag para produção**: seguir o plano de testes salvo
  em `C:\Users\leona\.claude\plans\clever-frolicking-crescent.md` (Pix,
  cartão Rede crédito/débito, COD, os 3 métodos de frete incluindo
  YITH Delivery Date, mobile, e regressão do checkout antigo com a flag
  desligada).
- **Tela "Aguardando Pix" (`woocommerce/checkout/thankyou.php`)**: quando o
  pedido é pago com Pix e ainda não foi pago (`$order->needs_payment()`), a
  página de pedido recebido mostra o layout do Figma (node 2106:1420) em
  vez do thank-you padrão do WooCommerce - qualquer outro caso (outro
  gateway, Pix já pago, checkout antigo) continua idêntico ao core. O QR
  Code, o código "copia e cola" e as instruções são o **widget real** do
  plugin Payment Gateway Pix for WooCommerce
  (`LknPaymentPixForWoocommercePix::showPix()`, hook
  `woocommerce_order_details_after_order_table`) - só é chamado dentro do
  cartão novo, não foi recriado. ⚠️ O texto do Figma promete uma contagem
  regressiva ("Pague em até 15:00") e atualização automática da página -
  esse gateway específico (chave Pix estática, sem webhook bancário) **não
  tem nenhuma das duas coisas** (o próprio plugin avisa que a confirmação é
  manual, por comprovante). O texto foi ajustado para não prometer algo que
  o sistema não cumpre - se um dia trocarem para um gateway Pix com webhook
  (ex.: C6/Sicoob/Cielo, já existem classes prontas no plugin), vale
  reavaliar se dá pra reativar essas duas promessas.

### ⚠️ Descobertas de validação (testadas ao vivo, importantes para manutenção)

1. **O nome certo do template é `checkout/form-checkout.php`, não
   `checkout/checkout.php`.** O arquivo antigo do tema
   (`woocommerce/checkout/checkout.php`) nunca foi carregado pelo
   WooCommerce - o nome do template usado pelo shortcode
   `[woocommerce_checkout]` é `checkout/form-checkout.php` (ver
   `wp-content/plugins/woocommerce/templates/checkout/form-checkout.php`).
   O arquivo foi renomeado nesta implementação; se algum dia reaparecer um
   `woocommerce/checkout/checkout.php` no tema, ele é código morto.
2. **A página "Checkout" está salva com o Checkout Block (Store API), não
   o shortcode clássico.** Confirmado ao desativar o Fluid Checkout
   temporariamente em ambiente local: sem o Fluid Checkout, a página cai no
   bloco nativo do WooCommerce (React), não no HTML clássico. É por isso
   que a flag do checkout novo precisa forçar o shortcode via `the_content`
   (ver acima) em vez de só trocar o template.
3. **O Fluid Checkout não tem um "desligar tudo" único.** O filtro
   `fc_enable_checkout_page_template` só desliga a substituição de template
   de página inteira; a feature principal (`FluidCheckout_Steps`, em
   `inc/checkout-steps.php`) reescreve o checkout clássico inteiro
   (billing/shipping/payment, progress bar, sub-steps) via hooks
   incondicionais no construtor da classe, sem filtro de disable próprio.
   **Na prática, para o checkout novo aparecer de verdade, o plugin Fluid
   Checkout precisa estar DESATIVADO no ambiente** (não basta a flag +
   filtros) - atualizar o plano de rollout: a flag por si só não é
   suficiente, é preciso desativar o plugin junto (o que é esperado, já que
   o objetivo final é justamente parar de depender dele).
4. **`woocommerce_checkout_payment()` já vem ligada por padrão dentro do
   hook `woocommerce_checkout_order_review`** (prioridade 20, ver
   `wc-template-hooks.php` do WooCommerce core) - ou seja, por padrão a
   lista de gateways já aparece dentro do resumo do pedido. Como
   `step-pagamento.html.php` chama `woocommerce_checkout_payment()` de novo
   explicitamente (pra colocar no lugar certo do layout), é necessário um
   `remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20)`
   (feito em `extension/woocommerce.php`, só quando a flag está ligada) -
   sem isso, dois grupos de radio `name="payment_method"` ficam na mesma
   página, com risco real de o pedido ser enviado com o gateway errado.
5. **Pendência de teste antes de produção**: com o novo checkout ligado, o
   JS do woo-rede (`jquery.card.js` / `wooRedeDebit.js`) lançou um erro no
   console (`Cannot read properties of undefined (reading 'getAttribute')`)
   ao inicializar - o plugin espera os campos de cartão em uma posição/
   ordem específica no DOM no load da página. Como o step de Pagamento
   agora fica fora do fluxo de posição padrão (dentro de `#customer_details`
   em vez do local tradicional), isso precisa ser testado manualmente:
   selecionar "Cartão" no step 3 e confirmar que o formulário de
   número/validade/CVV do Rede realmente aparece e funciona antes de
   liberar a flag em produção.
