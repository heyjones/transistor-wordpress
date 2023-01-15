<?php

namespace transistor\shows;

add_action( 'init', __NAMESPACE__ . '\\init' );

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
    // Get the show(s) the site owner would like to sync
    $shows = get_option( 'transistor_shows' );
    if( ! $shows ) {
        return;
    }
    // Get the existing show(s)
    $terms = get_terms(
        array(
            'taxonomy' => 'transistor-show',
            'fields' => 'ids',
            'hide_empty' => false,
            'meta_query' => array(
                array(
                    'key' => '_id',
                    'compare' => 'IN',
                    'value' => $shows,
                    'type' => 'NUMERIC',
                )
            )
        )
    );
    // Identify which show(s) need(s) to be created
    $shows = array_diff( $shows, $terms );
    if( ! $shows ) {
        return;
    }
    $transistor_api_key = get_option( 'transistor_api_key' );
    // Create the show(s)
    foreach( (array) $shows as $show ) {
        // Get the term details
        $request = wp_remote_get(
            'https://api.transistor.fm/v1/shows/' . $show,
            array(
                'headers' => array(
                    'x-api-key' => $transistor_api_key,
                )
            )
        );
        if( is_wp_error( $request ) || 200 !== wp_remote_retrieve_response_code( $request ) ) {
            continue;
        }
        $response = json_decode( wp_remote_retrieve_body( $request ) );
        $show = $response->data;
        // Create the term
        $term = wp_insert_term(
            $show->attributes->title,
            'transistor-show',
            array(
                'slug' => $show->attributes->slug,
            )
        );
        if( ! is_wp_error( $term ) ) {
            add_term_meta(
                $term['term_id'],
                '_id',
                $show->id,
                true
            );
            add_term_meta(
                $term['term_id'],
                '_attributes',
                $show->attributes,
                true
            );
        }
    }
}