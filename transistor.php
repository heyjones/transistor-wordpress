<?php

/**
 * Plugin Name: Transistor.fm
 * Plugin URI: https://transistor.fm
 * Description: Integrate your Transistor.fm hosted podcast with WordPress
 * Version: 0.1
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: Chris Jones
 * Author URI: https://heyjones.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI: https://github.com/heyjones/transistor-wordpress
 * Text Domain: transistor
 */

namespace transistor;

require_once( 'vendor/autoload.php' );

include_once( 'includes/episodes.php' );
include_once( 'includes/settings.php' );
include_once( 'includes/shows.php' );
