# Lucci Fresh – Copilot Instructions

Você é um agente especializado no tema WordPress **Lucci Fresh** (versão 1.1.0), desenvolvido por UpSites / Leonardo Pang. Sempre siga as convenções e padrões abaixo ao criar, editar ou sugerir código.

---

## Stack & Build System

- **PHP 7.4+** + **WordPress** (sem framework PHP externo)
- **Webpack 5** com Babel, Sass, PostCSS/Autoprefixer, BrowserSync
- **ACF Pro** para campos customizados
- **WooCommerce** para produtos (tours)
- **GSAP**, **Splide**, **Swiper**, **AOS** como bibliotecas JS
- Engine de templates: `QDI\WP\Template` via `$tpl_engine->partial()` e `$tpl_engine->svg()`

### Entry Points Webpack

| Bundle                           | Arquivo de entrada             | Saída                       |
| -------------------------------- | ------------------------------ | --------------------------- |
| CSS crítico (inline no `<head>`) | `webpack/css/sass/inline.scss` | `public/css/inline.min.css` |
| CSS principal (defer)            | `webpack/css/sass/main.scss`   | `public/css/main.min.css`   |
| JavaScript principal             | `webpack/js/app.js`            | `public/js/app.min.js`      |

---

## Arquitetura de Arquivos

```
webpack/
  css/sass/
    base/         ← _settings, _var-layout, _var-typography, _functions, _mixins, _media-queries
    modules/
      components/ ← accordion, cards, filters, loading, pagination, slider
      header/
      footer/
      menu/
      pages/
      global/     ← testimonials, shared
      woocommerce/
    vendor/       ← _splide, _swiper, _aos
  js/
    app.js        ← importa e inicializa todos os módulos
    scripts/
      lib/        ← utils.js, dom.js, get-wp-url.js
      slider/     ← hero.js, testimonials.js, product-slider.js

includes/partials/
  components/     ← _accordion.html.php, _select.html.php, _skeleton-post.html.php, etc.
  template/
    header/       ← _header.html.php, _header-top.html.php, _mobile-menu.html.php
    footer/       ← _footer.html.php, _newsletter.html.php, _redes-sociais.html.php
    global/       ← _testimonials.html.php

extension/
  post-types/     ← custom post types
  custom-taxonomy.php
  custom-fields.php
  helpers/        ← funções auxiliares
  ajax/           ← handlers AJAX

acf-json/         ← configuração de campos ACF (versionado)
```

---

## Convenções CSS (BEM-inspired)

| Prefixo      | Uso                       | Exemplo                                     |
| ------------ | ------------------------- | ------------------------------------------- |
| `.o-`        | Objects / layout wrappers | `.o-header`, `.o-footer`                    |
| `.c-`        | Components reutilizáveis  | `.c-button`, `.c-card`, `.c-accordion`      |
| `.u-`        | Utilities                 | `.u-mask-bg`, `.u-header-link`              |
| `.is-`       | States                    | `.is-open`, `.is-active`, `.is-initialized` |
| `--modifier` | BEM modifier              | `.c-button--primary`, `.c-card--featured`   |
| `__element`  | BEM element               | `.o-header__content`, `.c-footer__logo`     |

### Design Tokens (SCSS)

**Cores** (de `_settings.scss` — alinhadas ao Figma):

```scss
// Principais
$color-laranja: #ff7201; // laranja primário (hover/variante)
$color-laranja-cta: #da864d; // laranja CTA — botões "Fazer pedido", "Saiba mais"
$color-verde: #1e593a; // verde escuro secundário (+ variantes 50-900)
$color-verde-medio: #70845f; // verde médio — hero bg, seção testimonials
$color-preto: #1f2818; // texto principal (dark green/black)
$color-beje: #ffedd0; // creme/bege — footer bg, seção about
$color-slate: #414b6c; // texto secundário (datas, meta)
$color-divider: #e8edfe; // divisor em cards de testimonial
```

**Tipografia** (de `_var-typography.scss` — alinhada ao Figma):

```scss
// Fontes
$font-heading: "DM Serif Display", serif; // todos os títulos H1–H4
$font-body: "Manrope", sans-serif; // corpo, UI, nav, botões

// Escala (desktop)
// Hero H1:     62px / line-height 1.2
// Section H2:  48px / line-height normal
// Section H3:  40px / line-height normal
// Body large:  22px / line-height 1.6
// Body:        20px / line-height 1.6–1.8
// UI/Nav:      16px / line-height normal
// Small:       14px / line-height 1.6
// Micro:       12px / line-height 1.6
```

**Border Radius:**

```scss
$radius-pill: 600px; // botões CTA
$radius-card: 16px; // cards de produto e testimonial
$radius-image: 24px; // imagens grandes (about, loja)
$radius-tag: 8px; // labels de categoria
```

**Breakpoints** (mobile-first, de `_var-layout.scss`):

```scss
$media-query-mobile: 360px;
$media-query-tablet: 720px;
$media-query-desktop-low: 1024px;
$media-query-desktop: 1280px;
$media-query-desktop-high: 1440px;
```

**Grid:**

- 12 colunas, gutter 16px
- Container max-width: 1220px (desktop), 1000px (desktop-low), 640px (tablet)

**Funções SCSS personalizadas:**

```scss
cvl($color, $amount)  // color variant light
cvd($color, $amount)  // color variant dark
cvt($color, $amount)  // color variant transparent
```

---

## Seções da Home Page (front-page.php)

Mapeamento das seções do layout Figma → partials PHP:

| #   | Seção                                                          | Partial                                  | Bg        |
| --- | -------------------------------------------------------------- | ---------------------------------------- | --------- |
| 1   | **Top Bar** — aviso de entrega/frete                           | `template/header/_header-top.html.php`   | `#DA864D` |
| 2   | **Header** — logo, nav, social                                 | `template/header/_header.html.php`       | branco    |
| 3   | **Hero** — headline + CTA + imagem produto                     | `template/global/_hero.html.php`         | `#70845F` |
| 4   | **Categorias** — "Refeições feitas com amor" slider horizontal | `components/_category-slider.html.php`   | branco    |
| 5   | **Benefícios** — "Responsabilidade & qualidade" ícones         | `components/_benefits.html.php`          | branco    |
| 6   | **Mais Pedidos** — slider de produtos em destaque              | `components/_featured-products.html.php` | branco    |
| 7   | **Produto por categoria** — grid com filtro (ex: Pizzas)       | `components/_product-grid.html.php`      | branco    |
| 8   | **Nossa História** — texto + foto família + CTA                | `template/global/_about.html.php`        | `#FFEDD0` |
| 9   | **Loja Física** — fotos + mapa                                 | `template/global/_store.html.php`        | `#DA864D` |
| 10  | **Depoimentos** — carousel reviews Google                      | `template/global/_testimonials.html.php` | `#70845F` |
| 11  | **Footer**                                                     | `template/footer/_footer.html.php`       | `#FFEDD0` |

### Botões (padrão Figma)

```html
<!-- Primário (laranja pill) -->
<a class="c-btn c-btn--primary" href="#">Fazer pedido</a>

<!-- Secundário (branco pill) -->
<a class="c-btn c-btn--secondary" href="#">R$ 15,80</a>
```

```scss
.c-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 24px 20px;
  border-radius: $radius-pill;
  font-family: $font-body;
  font-weight: 600;
  font-size: 20px;
  line-height: normal;
  white-space: nowrap;
  text-decoration: none;

  &--primary {
    background: $color-laranja-cta; // #DA864D
    color: #fff;
  }

  &--secondary {
    background: #fff;
    color: $color-preto;
  }
}
```

### Card de Depoimento (padrão Figma)

```html
<div class="c-testimonial">
  <div class="c-testimonial__author">
    <div class="c-testimonial__avatar"><!-- img --></div>
    <p class="c-testimonial__name">Filipe Coelho</p>
    <p class="c-testimonial__date">4 meses atrás</p>
  </div>
  <div class="c-testimonial__divider"></div>
  <div class="c-testimonial__content">
    <div class="c-testimonial__header">
      <span class="c-testimonial__quote">"</span>
      <div class="c-testimonial__stars"><!-- 5 estrelas --></div>
    </div>
    <p class="c-testimonial__text">
      As marmitas são muito boas mesmo, vale super a pena!
    </p>
  </div>
</div>
```

```scss
.c-testimonial {
  background: #fff;
  border-radius: $radius-card; // 16px
  padding: 64px;
  gap: 48px;
  // ...
}
```

### Card de Categoria (padrão Figma)

```html
<div class="c-category-card">
  <img class="c-category-card__image" src="..." alt="..." />
  <div class="c-category-card__label">
    <span class="c-category-card__name">Doces</span>
    <!-- ícone seta -->
  </div>
</div>
```

```scss
.c-category-card {
  border-radius: $radius-card; // 16px
  overflow: hidden;
  width: 388px;
  height: 483px;
  // ...
}
```

---

## Padrão de Partial PHP

Todo partial/componente PHP segue este padrão:

```php
<?php
// includes/partials/components/_nome-componente.html.php
global $tpl_engine;

// Variáveis com defaults
$titulo   = $titulo   ?? '';
$items    = $items    ?? [];
$modifier = $modifier ?? '';
?>
<div class="c-nome-componente <?= $modifier ?>">
  <?php foreach ($items as $item): ?>
    <div class="c-nome-componente__item">
      <?= esc_html($item['titulo']) ?>
    </div>
  <?php endforeach; ?>
</div>
```

**Chamada do partial via template engine:**

```php
$tpl_engine->partial('components/nome-componente', [
  'titulo' => 'Texto',
  'items'  => $data,
]);
```

**Renderizar SVG inline:**

```php
$tpl_engine->svg('nome-do-icone'); // de includes/svgs/
```

---

## Padrão de Módulo JavaScript

Todo módulo JS é uma função default exportada e inicializada em `app.js`:

```javascript
// webpack/js/scripts/nomeModulo.js
export default function nomeModulo() {
  document.addEventListener("click", (e) => {
    const target = e.target.closest(".c-nome-componente__trigger");
    if (!target) return;
    // lógica
  });
}
```

```javascript
// webpack/js/app.js
import nomeModulo from "./scripts/nomeModulo";
nomeModulo();
```

**Helpers disponíveis em `lib/`:**

```javascript
import { scrollTo } from "./lib/dom";
import { getWpUrl } from "./lib/get-wp-url";
```

---

## Padrão de Módulo SCSS

Cada componente tem seu próprio arquivo em `webpack/css/sass/modules/components/`:

```scss
// _nome-componente.scss
.c-nome-componente {
  // estilos base (mobile-first)

  &__element {
    // sub-elemento
  }

  &--modifier {
    // variação
  }

  @include mq(tablet) {
    // estilos tablet+
  }

  @include mq(desktop) {
    // estilos desktop+
  }
}
```

Importe no `main.scss` ou `inline.scss` conforme seja crítico ou não:

```scss
@use "modules/components/nome-componente";
```

---

## Constantes PHP Disponíveis

```php
THEMELIB        // /extension
PATHS_INC       // /includes
PATHS_PARTIALS  // /includes/partials
PATHS_SVG       // /includes/svgs
THEME_VERSION   // versão do package.json
ENV             // 'development' | 'production'
```

---

## WooCommerce

- Produtos = Refeições/comidas (`product` post type)
- 3 colunas, 12 itens por página
- **Categorias de produto visíveis no layout:**
  - Marmitas, Lanches, Santa Pizzinha, Doces, Porções, Sucos
- Templates customizados em `woocommerce/`
- Classes dos botões: `c-btn c-btn--primary c-product-card__cta`
- País padrão: Brasil (BR)
- **Header**: Top bar laranja (`#DA864D`) com texto de aviso de entrega
- **Nav**: Home | Produtos (dropdown) | Quem somos | Fale Conosco
- **Social header**: Instagram + Facebook + WhatsApp (ícones redondos)

---

## Regras Gerais

1. **Mobile-first** sempre: escreva estilos base para mobile e use `@include mq(tablet)` / `@include mq(desktop)` para breakpoints maiores.
2. **Escape de output PHP:** use `esc_html()`, `esc_url()`, `esc_attr()` em todo output dinâmico.
3. **Sem inline styles** em PHP/HTML; toda estilização vai em SCSS.
4. **Componentes isolados:** cada componente tem seu próprio arquivo `.html.php` + `.scss` + `.js` (se necessário).
5. **Sem jQuery** nos novos módulos JS; use Vanilla JS / ES6+.
6. **ACF** para todos os campos configuráveis pelo admin; registre em `acf-json/` para versionamento.
7. **Nomes em kebab-case** para classes CSS e arquivos; **camelCase** para funções JS.
8. **Textos** sempre via `__()` / `_e()` com text-domain `lucci-fresh` para suporte a i18n.
