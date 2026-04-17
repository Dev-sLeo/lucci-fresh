<?php

function tc_general_theme_settings($wp_customize) {
    $section = 'cps_general_theme_settings';
    $key = 'cps_general_theme_settings';

    $wp_customize->add_section($section, array(
        'title' => 'Configurações gerais',
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'description' => 'Configurações gerais do tema'
    ));

    // $wp_customize->add_setting($key . '[big_highlight]', array(
    //     'default' => '',
    //     'capability' => 'edit_theme_options'
    // ));

    // $wp_customize->add_control($key . '[big_highlight]', array(
    //     'label'      => 'Destaque grande ativado?',
    //     'section'    => $section,
    //     'settings'   => $key . '[big_highlight]',
    //     'type'       => 'select',
    //     'choices'    => array(
    //         'yes' => 'Sim',
    //         'no' => 'Não',
    //     ),
    // ));
}

// add_action('customize_register', 'tc_general_theme_settings');
