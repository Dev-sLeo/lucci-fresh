<?php
global $tpl_engine;
get_header();
?>
<div class='c-404'>
	<div class="c-wrapper">
		<section class='s-container'>
			<div class='c-404__container'>
				<div class='c-404__content'>
					<h1 class="title__super">404</h1>
					<h2 class="title__normal">Página não encontrada</h2>
					<p class="text__normal">A página que você procurava não está disponível. Sentimos muito pelo inconveniente.</p>
					<a href="<?= site_url() ?>" class="button button__blue"><span class="o-btn-default__span">Voltar para home</span></a>
				</div>
			</div>
		</section>
	</div>
</div>
<?php get_footer(); ?>