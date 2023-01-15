<?php

namespace transistor\settings;

add_action( 'admin_init', __NAMESPACE__ . '\\admin_init' );
add_action( 'admin_menu', __NAMESPACE__ . '\\admin_menu' );

function admin_init() {
    register_setting(
        'transistor',
        'transistor_api_key',
        array(
            'type' => 'string',
            'description' => 'The Transistor API uses API keys to authenticate all requests.',
            'show_in_rest' => false,
        )
    );
	add_settings_section(
		'transistor_api',
		'API Access',
		__NAMESPACE__ . '\\settings_section',
        'transistor'
	);
	add_settings_field(
		'transistor-api-key',
        'Your API Key',
        __NAMESPACE__ . '\\settings_field',
        'transistor',
        'transistor_api',
    );
}

function admin_menu() {
	add_submenu_page(
        'edit.php?post_type=transistor-episode',
		'Transistor.fm',
		'Settings',
		'manage_options',
		'transistor',
		__NAMESPACE__ . '\\menu_page',
        99,
	);
}

function menu_page( $args ) {

    ?>
    <div class="wrap">
		<h1>
            <?php echo esc_html( get_admin_page_title() ); ?>
        </h1>
		<form action="options.php" method="post">
			<?php

			settings_fields( 'transistor' );
            do_settings_sections( 'transistor' );
            submit_button( 'Save Settings' );

			?>
		</form>
	</div>
    <?php

}

function settings_section() {
    
    ?>
    <p>Developers can view and manage podcasts, episodes, and private podcast subscribers using our JSON API. Get started by using your API Key and browsing our <a href="https://developers.transistor.fm/" target="_blank">API documentation</a>.</p>
    <?php

}

function settings_field() {
    $transistor_api_key = get_option( 'transistor_api_key' );

    ?>
	<input type="text" name="transistor_api_key" value="<?php echo $transistor_api_key; ?>" size="25" required>
	<?php

}