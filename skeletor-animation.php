<?php

namespace Mandy;

/*
Plugin Name: Mandy Build Block Settings — Animation
Plugin URI:  https://github.com/mandytechnologies/skeletor-animation
Description: Adds support for block intro animations
Version:     1.0.1
Author:      Mandy Technologies
Author URI:  https://www.mandytechnologies.com/
Text Domain: mandy-build-block-settings-animation
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

define('SKELETOR_ANIMATION_VERSION', '1.0.1');

if (!class_exists('\Skeletor\Plugin_Updater')) {
	require_once(__DIR__ . '/class--plugin-updater.php');
}

$updater = new \Skeletor\Plugin_Updater(
	plugin_basename(__FILE__),
	SKELETOR_ANIMATION_VERSION,
	'https://bitbucket.org/madebymandy/skeletor-animation/raw/HEAD/package.json'
);
