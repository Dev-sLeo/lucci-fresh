<?php

class Main_Mega_Walker extends Walker_Nav_Menu
{
	private array $opened_li_depth = [];
	private $current_item = null;
	private bool $mega_active = false;

	private function normalize_image_html($img, string $size = 'thumbnail', array $attrs = []): string
	{
		$attrs = array_merge([
			'loading'  => 'lazy',
			'decoding' => 'async',
		], $attrs);

		if (is_array($img) && !empty($img['ID'])) {
			return wp_get_attachment_image($img['ID'], $size, false, $attrs);
		}

		if (is_numeric($img)) {
			return wp_get_attachment_image($img, $size, false, $attrs);
		}

		return '';
	}

	private function has_mega_children(array $children): bool
	{
		if (!function_exists('get_field')) return false;

		foreach ($children as $child) {
			if ((bool) get_field('ativar_mega_menu', $child->ID)) {
				return true;
			}
		}

		return false;
	}

	private function render_mega_card(object $child): string
	{
		if (!function_exists('get_field')) return '';

		$mega_data = get_field('mega_menu', $child->ID);
		if (empty($mega_data)) return '';

		$img  = $mega_data['imagem'] ?? null;
		$desc = $mega_data['description'] ?? '';

		$img_html = $this->normalize_image_html($img, 'large');

		$svg_arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="5" height="8" viewBox="0 0 5 8" fill="none"><path d="M1.00006 6.65686L3.82848 3.82843L1.00006 1" stroke="#0056FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

		$out = "<li class='c-mega__list-item c-mega__list-item--card'>";

		if ($img_html) {
			$out .= "<div class='c-mega__card-media'>{$img_html}</div>";
		}

		$out .= "<div class='c-mega__card-content'>";
		$out .= "<span class='c-mega__card-title'>" . esc_html($child->title) . "</span>";

		if ($desc) {
			$out .= "<p class='c-mega__card-desc'>" . esc_html($desc) . "</p>";
		}

		$out .= "<a class='c-mega__card-link' href='" . esc_url($child->url) . "'>Saiba mais{$svg_arrow}</a>";
		$out .= "</div>";
		$out .= "</li>";

		return $out;
	}

	private function render_mega_dropdown(array $children): string
	{
		$out  = "<div class='c-main-menu__dropdown c-main-menu__dropdown--mega'>";
		$out .= "<ul class='c-mega__list'>";

		foreach ($children as $child) {
			$ativar = function_exists('get_field') && (bool) get_field('ativar_mega_menu', $child->ID);

			if ($ativar) {
				$card = $this->render_mega_card($child);

				if ($card) {
					$out .= $card;
					continue;
				}
			}

			$out .= "<li class='c-mega__list-item'>";
			$out .= "<a class='c-mega__list-link' href='" . esc_url($child->url) . "'>";
			$out .= "<span class='c-mega__list-text'>" . esc_html($child->title) . "</span>";
			$out .= "</a>";
			$out .= "</li>";
		}

		$out .= "</ul>";
		$out .= "</div>";

		return $out;
	}

	public function display_element($element, &$children_elements, $max_depth, $depth = 0, $args = [], &$output = '')
	{
		if (!$element) return;

		$id_field = $this->db_fields['id'];
		$element_id = $element->$id_field;

		$args[0]->has_children = (isset($children_elements[$element_id]) && is_array($children_elements[$element_id]));

		if ($depth === 0) {
			$element->children_items = $children_elements[$element_id] ?? [];
		}

		parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
	}

	public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
	{
		if ($this->mega_active && $depth > 0) return;

		$item_classes = implode(' ', (array) $item->classes);
		$has_children = !empty($args->has_children);

		if ($depth === 0) {
			$this->current_item = $item;

			$output .= "<li class='{$item_classes} c-main-menu__item'>";
			$this->opened_li_depth[$depth] = true;

			$output .= "<div class='c-main-menu__item-head'>";

			if (!empty($item->url)) {
				$output .= "<a class='c-main-menu__link' href='" . esc_url($item->url) . "'>";
				$output .= "<span>" . esc_html($item->title) . "</span>";
				$output .= "</a>";
			} else {
				$output .= "<span class='c-main-menu__link'><span>" . esc_html($item->title) . "</span></span>";
			}

			if ($has_children) {
				$svg_chevron = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="8" viewBox="0 0 13 8" fill="none"><path d="M0.999998 0.999998L6.11778 6.11778L11.2356 0.999998" stroke="#0056FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
				$output .= "<button class='c-main-menu__toggle' type='button'>{$svg_chevron}</button>";
			}

			$output .= "</div>";
			return;
		}

		$output .= "<li class='{$item_classes}'>";
		$this->opened_li_depth[$depth] = true;

		$output .= "<a href='" . esc_url($item->url) . "'>";
		$output .= esc_html($item->title);
		$output .= "</a>";
	}

	public function start_lvl(&$output, $depth = 0, $args = [])
	{
		if ($this->mega_active) return;

		if ($depth !== 0) {
			$output .= "<ul class='sub-menu'>";
			return;
		}

		$children = $this->current_item->children_items ?? [];

		if (!empty($children) && $this->has_mega_children($children)) {
			$this->mega_active = true;
			$output .= $this->render_mega_dropdown($children);
			return;
		}

		$output .= "<ul class='sub-menu'>";
	}

	public function end_lvl(&$output, $depth = 0, $args = [])
	{
		if ($this->mega_active) {
			if ($depth === 0) {
				$this->mega_active = false;
			}
			return;
		}

		$output .= "</ul>";
	}

	public function end_el(&$output, $item, $depth = 0, $args = [])
	{
		if ($this->mega_active && $depth > 0) return;

		if (!empty($this->opened_li_depth[$depth])) {
			$output .= "</li>";
			unset($this->opened_li_depth[$depth]);
		}
	}
}
