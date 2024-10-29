<?php
/** 
 * Plugin Name:       AI Product Content Creator For Woocommerce
 * Description:       Generate product descriptions, titles, and short descriptions effortlessly using OpenAI API for your WooCommerce products. Additionally, it supports variation description generation.
 * Version:           1.0.0
 * Author:            StorePro
 * Author URI:        https://storepro.io/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ai-product-content-creator-for-woocommerce
 * Domain Path:       /languages
 * 
 * @link              https://storepro.io/
 * @since             1.0.0
 * @package           ai-product-content-creator-for-woocommerce
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

if (!defined('SPWAI_BASE')) {
    define('SPWAI_BASE', plugin_basename(__FILE__));
    define('SPWAI_NAME', 'ai-product-content-creator-for-woocommerce');
    define('SPWAI_VERSION', '1.0.0');
    define('SPWAI_PATH', plugin_dir_path(__FILE__));
    define('SPWAI_URL', plugin_dir_url(__FILE__));
}

// Plugin activation function
register_activation_hook(__FILE__, 'spwai_activate');
function spwai_activate() {
    require_once SPWAI_PATH . 'includes/class-spwai-activator.php';
    Spwai_Activator::activate();
}

// Plugin deactivation function
register_deactivation_hook(__FILE__, 'spwai_deactivate');
function spwai_deactivate() {
    require_once SPWAI_PATH . 'includes/class-spwai-deactivator.php';
    Spwai_Deactivator::deactivate();
}

require_once SPWAI_PATH . 'includes/class-spwai.php';

// Execute Plugin
function spwai_run_plugin() {
    $plugin = new Spwai();
    $plugin->run();
}

spwai_run_plugin();
