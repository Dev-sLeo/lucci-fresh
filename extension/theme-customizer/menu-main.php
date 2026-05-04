<?php

class Main_Menu_Walker extends Walker_Nav_Menu
{
	private array $opened_li_depth = [];

	public function display_element($element, &$children_elements, $max_depth, $depth = 0, $args = [], &$output = '')
	{
		if (!$element) return;

		$id_field = $this->db_fields['id'];
		$element_id = $element->$id_field;

		$args[0]->has_children = (isset($children_elements[$element_id]) && is_array($children_elements[$element_id]));

		parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
	}

	public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
	{
		$item_classes = implode(' ', (array) $item->classes);
		$has_children = !empty($args->has_children);

		if ($depth === 0) {
			$output .= "<li class='{$item_classes} c-main-menu__item'>";
			$this->opened_li_depth[$depth] = true;

			$output .= "<div class='c-main-menu__item-head'>";

			$icon     = function_exists('get_field') ? get_field('icone', $item->ID) : null;
			$icon_html = $icon ? render_media_image($icon, 'thumbnail') : '';

			if (!empty($item->url)) {
				$output .= "<a class='c-main-menu__link' href='" . esc_url($item->url) . "'>";
				if ($icon_html) $output .= $icon_html;
				$output .= "<span>" . esc_html($item->title) . "</span>";
				$output .= "</a>";
			} else {
				$output .= "<span class='c-main-menu__link'>";
				if ($icon_html) $output .= $icon_html;
				$output .= "<span>" . esc_html($item->title) . "</span></span>";
			}

			if ($has_children) {
				$svg_chevron = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"><g clip-path="url(#clip0_4_27924)"><path d="M4.5 6.75L9 11.25L13.5 6.75" stroke="#DA864D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="clip0_4_27924"><rect width="18" height="18" fill="white"/></clipPath></defs></svg>';
				$output .= "<button class='c-main-menu__toggle' type='button' aria-label='Submenu'>{$svg_chevron}</button>";
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
		if ($depth === 0) {
			$output .= "<div class='c-main-menu__dropdown'>";
		}
		$output .= "<ul class='sub-menu'>";
	}

	public function end_lvl(&$output, $depth = 0, $args = [])
	{
		$output .= "</ul>";
		if ($depth === 0) {
			$output .= "</div>";
		}
	}

	public function end_el(&$output, $item, $depth = 0, $args = [])
	{
		if (!empty($this->opened_li_depth[$depth])) {
			$output .= "</li>";
			unset($this->opened_li_depth[$depth]);
		}
	}
}
