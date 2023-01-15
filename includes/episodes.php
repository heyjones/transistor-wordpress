<?php

namespace transistor\episodes;

add_action( 'init', __NAMESPACE__ . '\\init' );

function init() {

    /**
     * Post Types
     * 
     * /#Episode
     */
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
            'taxonomies' => array(
                'transistor-show',
                'transistor-keywords',
                'transistor-type',
            )
        )
    );

    /**
     * Post Meta
     */
    register_post_meta(
        'transistor-episode',
        'duration',
        array(
            'type' => 'integer',
            'description' => 'Duration of episode in seconds',
            'single' => true,
        )
    );

    // Transcript
    register_post_meta(
        'transistor-episode',
        'audio_url',
        array(
            'type' => 'string',
            'description' => 'URL to an episode\'s new audio file',
            'single' => true,
        )
    );

    // Transcript
    register_post_meta(
        'transistor-episode',
        'transcript',
        array(
            'type' => 'string',
            'description' => 'Full text of the episode transcript',
            'single' => true,
        )
    );

}