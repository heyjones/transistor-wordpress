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
}

function get_term_by_id( $show_id ) {
    $terms = new \WP_Term_Query( array(
        'taxonomy' => 'transistor-show',
        'number' => 1,
        'hide_empty' => false,
        'meta_query' => array(
            array(
                'key' => '_id',
                'compare' => '=',
                'value' => $show_id,
                'type' => 'NUMERIC',
            ),
        ),
    ) );
    if( empty( $terms->terms ) ) {
        return false;
    }
    $term = (array) $terms->terms[0];
    return $term;
}

function get_term( $show ) {
    $term = get_term_by_id( $show->id );
    if( ! $term ) {
        $term = insert_term( $show );
    }
    return $term;
}

function insert_term( $show ) {
    $term = wp_insert_term(
        $show->attributes->title,
        'transistor-show',
        array(
            'description' => $show->attributes->description,
            'slug' => $show->attributes->slug,
        )
    );
    if( is_wp_error( $term ) ) {
        return;
    }
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

function get_webhook( $event_name, $show_id ) {
    $term = get_term_by_id( $show_id );
    $webhook = get_term_meta( $term['term_id'], '_episode_published', true );
    if( $webhook ) {
        return $webhook;
    }
    $transistor_api_key = get_option( 'transistor_api_key' );
    $request = wp_remote_post(
        'https://api.transistor.fm/v1/webhooks',
        array(
            'headers' => array(
                'x-api-key' => $transistor_api_key,
            ),
            'body' => array(
                'event_name' => $event_name,
                'show_id' => $show_id,
                'url' => get_rest_url( null, '/transistor/v1/episode_published/' ),
            ),
        )
    );
    if( ! is_wp_error( $request ) || 200 === wp_remote_retrieve_response_code( $request ) ) {
        $response = json_decode( wp_remote_retrieve_body( $request ) );
        $webhook = $response->data;
        add_term_meta(
            $term['term_id'],
            '_' . $event_name,
            $webhook->id,
            true
        );
    }
}