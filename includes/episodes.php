<?php

namespace transistor\episodes;

add_action( 'init', __NAMESPACE__ . '\\init' );
add_action( 'rest_api_init', __NAMESPACE__ . '\\rest_api_init' );
add_filter( 'the_content', __NAMESPACE__ . '\\the_content' );

function init() {
    register_post_type(
        'transistor-episode',
        array(
            'label' => 'Episodes',
            'labels' => array(
                'name' => 'Episodes',
                'singular_name' => 'Episode',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Episode',
                'edit_item' => 'Edit Episode',
                'new_item' => 'New Episode',
                'view_item' => 'View Episode',
                'view_items' => 'View Episodes',
                'search_items' => 'Search Episodes',
                'not_found' => 'No episodes found',
                'not_found_in_trash' => 'No episodes found in Trash',
                'parent_item_colon' => '',
                'all_items' => 'Episodes',
                'archives' => 'Episode Archives',
                'attributes' => 'Episode Attributes',
                'insert_into_item' => 'Insert into episode',
                'uploaded_to_this_item' => 'Uploaded to this episode',
                'featured_image' => 'Episode Artwork',
                'set_featured_image' => 'Set episode artwork',
                'remove_featured_image' => 'Remove episode artwork',
                'use_featured_image' => 'Use as episode artwork',
                'menu_name' => 'Transistor.fm',
                'filter_items_list' => 'Filter episodes list',
                'items_list_navigation' => 'Episode list navigation',
                'items_list' => 'Episodes list',
                'item_published' => 'Publishing date',
                'item_published_privately' => 'Episode published privately',
                'item_reverted_to_draft' => 'Episode reverted to draft',
                'item_scheduled' => 'Episode scheduled',
                'item_updated' => 'Episode updated',
                'item_link' => 'Episode link',
                'item_link_description' => 'A link to an episode',
            ),
            'description' => '',
            'public' => true,
            'hierarchical' => false,
            'menu_position' => 20,
            'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewBox="0 0 144 144"><path fill="black" d="M72 120c-3 0-5-2-5-5V29c0-2.8 2.2-5 5-5s5 2.2 5 5v86c0 3-2 5-5 5zM52 77H29c-3 0-5-2-5-5s2-5 5-5h23c2.8 0 5 2.2 5 5s-2.2 5-5 5zm63 0H92c-3 0-5-2-5-5s2-5 5-5h23c3 0 5 2 5 5s-2 5-5 5z"/><path fill="black" d="M72 144A72 72 0 1 1 72 0a72 72 0 0 1 0 144zm0-134a62.5 62.5 0 1 0 0 125 62.5 62.5 0 0 0 0-125z"/></svg>' ),
            'supports' => array(
                'title', // title
                'excerpt', // summary
                'editor', // description
                'thumbnail', // image_url
                'author', // author
                'custom-fields',
            ),
            'capabilities' => array(
                'edit_post' => 'edit_post', 
                'read_post' => 'read_post', 
                'delete_post' => 'delete_post', 
                'edit_posts' => 'edit_posts', 
                'edit_others_posts' => 'edit_others_posts', 
                'publish_posts' => '',       
                'read_private_posts' => 'read_private_posts', 
                'create_posts' => '',
            ),
            'has_archive' => true,
            'rewrite' => array(
                'slug' => 'episodes',
                'with_front' => false,
                'feeds' => false,
            ),
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

function episode_published( \WP_REST_Request $request ) {
    // Create the episode post
    $params = $request->get_params();
    $data = (object) $params['data'];
    $id = $data->id;
    // Check to make sure the episode doesn't already exist
    $post = get_post_by_id( $id );
    if( $post ) {
        return $post;
    }
    // Get the show
    $attributes = (object) $data->attributes;
    $relationships = (object) $data->relationships;
    $term_id = \transistor\shows\get_term_by_id( $relationships->show->data->id );
    $post = wp_insert_post( array(
        'post_type' => 'transistor-episode',
        'post_title' => $attributes->title,
        'post_excerpt' => $attributes->summary,
        'post_content' => wpautop( $attributes->description ),
        'post_name' => sanitize_title( $attributes->title ),
        'post_status' => post_status( $attributes->status ),
        'post_date' => $attributes->created_at,
        'post_modified' => $attributes->updated_at,
        'meta_input' => array(
            '_id' => $id,
            '_attributes' => $attributes,
        ),
        'tax_input' => array(
            'transistor-show' => $term_id,
        ),
    ) );
    // Assign the epsisode to the show
    wp_send_json( $post );
}

function post_status( $status ) {
    switch( $status ) {
        case 'published':
            $status = 'publish';
            break;
        default:
            $status = 'draft';
    }
    return $status;
}

function get_post_by_id( $episode_id ) {
    $posts = new \WP_Query( array(
        'post_type' => 'transistor-episode',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => '_id',
                'compare' => '=',
                'value' => $episode_id,
                'type' => 'NUMERIC',
            ),
        ),
    ) );
    if( ! $posts->have_posts() ) {
        return false;
    }
    $post = (array) $posts->posts[0];
    return $post;
}

function the_content( $content ) {
    global $post;
    $attributes = get_post_meta( $post->ID, '_attributes', true );
    $content .= '<p>' . $attributes->embed_html . '</p>';
    return $content;
}