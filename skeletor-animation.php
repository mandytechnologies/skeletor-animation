<?php

namespace Mandy;

/**
 * Plugin Name:           QB - Block Settings - Animation
 * Plugin URI:            https://github.com/mandytechnologies/skeletor-animation
 * Description:           This plugin enables animated effects for WordPress blocks.
 * Version:               1.0.2
 * Requires PHP:          7.0
 * Requires at least:     6.1.0
 * Tested up to:          6.8.2
 * Author:                Quick Build
 * Author URI:            https://www.quickbuildwebsite.com/
 * License:               GPLv2 or later
 * License URI:           https://www.gnu.org/licenses/
 * Text Domain:           qb-block-settings-animation
 * 
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Enable Animation Settings for blocks.
 */
class SkeletorBlockAnimation {
	/**
	 * Called on `after_setup_theme`
	 *
	 * Bind actions here
	 *
	 * @return void
	 */
	public static function setup() {
		add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_frontend_assets']);
		add_action('enqueue_block_editor_assets', [__CLASS__, 'enqueue_block_editor_assets']);
	}

	public static function enqueue_frontend_assets() {
		$asset = include(__DIR__ . '/build/frontend.asset.php');
		if (!$asset) {
			return;
		}

		$plugin_dir_url = plugin_dir_url(__FILE__);

		wp_enqueue_script(
			'skeletor_animation',
			$plugin_dir_url . 'build/frontend.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_enqueue_style(
			'skeletor_animation',
			$plugin_dir_url . 'build/animation.scss.css',
			[],
			$asset['version']
		);
	}

	/**
	 * Called on `enqueue_block_editor_assets`
	 *
	 * Enqueue the plugin frontend script in the block editor
	 *
	 * @return void
	 */
	public static function enqueue_block_editor_assets() {
		$screen = get_current_screen();
		if (!$screen || !$screen->is_block_editor) {
			return;
		}

		$asset = include(__DIR__ . '/build/index.asset.php');
		$fasset = include(__DIR__ . '/build/frontend.asset.php');

		if (!$asset || !$fasset) {
			return;
		}

		$plugin_dir_url = plugin_dir_url(__FILE__);

		wp_enqueue_script(
			'skeletor_animation_backend',
			$plugin_dir_url . '/build/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_enqueue_style(
			'skeletor_animation',
			$plugin_dir_url . 'build/animation.scss.css',
			[],
			$fasset['version']
		);
	}
}

add_action('after_setup_theme', ['\Mandy\SkeletorBlockAnimation', 'setup']);

define('MANDY_ANIMATION_VERSION', '1.0.2');

require 'plugin-update-checker/plugin-update-checker.php';

$update_checker = \Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/mandytechnologies/skeletor-animation',
	__FILE__,
	'skeletor-animation'
);

require_once( 'includes/class-plugin.php' );
