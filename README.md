Tema para clientes

## Começando

Essas instruções fornecerão uma cópia do projeto completo em execução em sua máquina local para fins de desenvolvimento e teste.

O projeto pode ser construído com npm ou yarn, então escolha uma das abordagens abaixo caso você não tenha nenhuma instalada em seu sistema.

- O **Npm** é distribuído com o Node.js, o que significa que, quando você baixa o Node.js, automaticamente obtém o npm instalado em seu computador. [Download Node.js](https://nodejs.org/en/download/)
- O **Yarn** é um gerenciador de pacotes criado pela equipe do Facebook e parece ser mais rápido que o npm em geral. [Download Yarn](https://awesomeopensource.com/project/elangosundar/awesome-README-templates)

É recomendado a utilização do **NVM** em seu projeto para evitar problemas de versões. Com o **NVM** a gente pode ver as versões do Node, escolher quais queremos instalar ou desisntalar e definir qual queremos usar em cada momento ou projeto. [Download NVM](https://www.treinaweb.com.br/blog/instalando-e-gerenciando-varias-versoes-do-node-js-com-nvm)

- No macOS, voce pode utilizar o código abaixo para acessar a sua instalação do NVM se nao aparecer a sua instalação no repositorio. Após isso, voce pode escolher a versão desejada e realizar os comandos normalmente.

```bash
  1. source ~/.bash_profile
  2. nvm use 20.19.5
```

## Setup

- Para baixar o projeto siga as instruções abaixo:

```bash
  1. git clone git@github.com:Dev-sLeo/lucci-fresh.git
  2. cd lucci-fresh
```

- Instale o Ruby ou RVM e depois o gerenciador de pacote(composer) e por ultimos as gemas:

```bash
  3. composer install
```

- Instale as dependências e inicie o servidor:

```bash
  6. yarn install
  7. yarn start
```

#### Ou

```bash
  6. npm install
  7. npm start
```

- Para compilar os minificados:

```bash
  9. yarn build
```

## Importação com tpl_engine

Colocar na primeira linha do arquivo

```php
global $tpl_engine;
```

para criar arquivos, eles ficam dentro de includes/partials/template ou components.

Para importar os arquivos usar

```php
$tpl_engine->partial('template/pages/{pagina}/{modulo}', [{array}]);
```

Para importar ou adicionar svg, no mesmo caminho mas na pasta svg includes/partials/svg

```php
$tpl_engine->partial('icons/facebook');
```

## Animações de entrada com GSAP

Para aplicar as animações globais, use o atributo `data-animate` no elemento. Também é possível configurar duração, atraso, distância, easing, threshold e root margin via `data-*`.

```html
<section
  class="o-hero"
  data-animate="fade-up"
  data-animate-delay="0.1"
  data-animate-duration="0.9"
  data-animate-distance="32"
  data-animate-ease="power3.out"
  data-animate-threshold="0.15"
  data-animate-root-margin="0px 0px -15% 0px"
>
  <h1 class="o-hero__title">Bem-vindo</h1>
  <p class="o-hero__text">Texto com animação de entrada.</p>
</section>
```

Para repetir a animação ao entrar novamente na viewport, defina `data-animate-once="false"`.

```html
<div class="c-card" data-animate="fade" data-animate-once="false">
  Conteúdo com fade ao entrar e sair da tela.
</div>
```

## Customizações de WooCommerce

Todas as customizações de WooCommerce (checkout, frete, YITH Delivery Date,
traduções, etc.) ficam em [`extension/woocommerce.php`](extension/woocommerce.php).
Antes de mexer em qualquer coisa relacionada a frete, checkout ou data de
entrega, leia [`docs/woocommerce-customizations.md`](docs/woocommerce-customizations.md) —
tem o motivo de cada hook existir e os pontos que já quebraram uma vez com
update de plugin.
