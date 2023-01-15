<?php

namespace transistor\shows;

add_action( 'init', __NAMESPACE__ . '\\init' );
add_action( 'rest_api_init', __NAMESPACE__ . '\\rest_api_init' );

function init() {
    register_taxonomy(
        'transistor-show',
        'transistor-episode',
        array(
            'labels' => array(
                'name' => 'Shows',
                'singular_name' => 'Show',
            ),
            'description' => '',
            'public' => true,
            'show_ui' => false,
            'show_in_menu' => false,
            'show_in_nav_menus' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'meta_box_cb' => false,
            'capabilities' => array(
                'manage_terms' => '',
                'edit_terms' => '',
                'delete_terms' => '',
                'assign_terms' => 'edit_posts'
            ),
            'rewrite' => array(
                'slug' => 'shows',
                'with_front' => false,
            )
        )
    );
}

function rest_api_init() {
    register_rest_route(
        'transistor/v1',
        '/episode_published/',
        array(
            'methods' => \WP_REST_Server::EDITABLE,
            'callback' => __NAMESPACE__ . '\\episode_published',
        )
    );
}

function episode_published( $data ) {
    // Create the episode post
}