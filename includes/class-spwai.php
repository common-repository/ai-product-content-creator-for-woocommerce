<?php

/**
 * The file that defines the core plugin class
 *
 * @link              https://storepro.io/
 * @since             1.0.0
 * @package           ai-product-content-creator-for-woocommerce
 */

class Spwai
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Spwai_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;
	protected $plugin_base;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		$this->version = SPWAI_VERSION;
		$this->plugin_name = SPWAI_NAME;
		$this->plugin_base = SPWAI_BASE;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Spwai_Loader. Orchestrates the hooks of the plugin.
	 * - Spwai_i18n. Defines internationalization functionality.
	 * - Spwai_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters
		 */
		require_once SPWAI_PATH . 'includes/class-spwai-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 */
		require_once SPWAI_PATH . 'includes/class-spwai-i18n.php';

		/**
		 * The class responsible for defining actions in admin area.
		 */
		require_once SPWAI_PATH . 'admin/class-spwai-admin.php';
		require_once SPWAI_PATH . 'admin/class-spwai-product.php';
		require_once SPWAI_PATH . 'admin/class-spwai-settings.php';
		require_once SPWAI_PATH . 'includes/class-spwai-openai.php';

		$this->loader = new Spwai_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Spwai_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Spwai_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$sp_admin = new Spwai_Admin($this->get_plugin_name(), $this->get_version());
		$sp_product = new Spwai_Product();
		$sp_settings = new Spwai_Settings();
		// Admin
		$this->loader->add_action('admin_enqueue_scripts', $sp_admin, 'enqueue_scripts');
		$this->loader->add_filter('plugin_action_links_' . $this->plugin_base, $sp_admin, 'settings_link');

		// settings page
		$this->loader->add_action('admin_menu', $sp_settings, 'add_admin_menu');
		$this->loader->add_action('admin_init', $sp_settings, 'settings_init');

		// Product
		$this->loader->add_action('add_meta_boxes', $sp_product, 'add_product_meta_box');
		$this->loader->add_action('woocommerce_product_after_variable_attributes', $sp_product, 'add_variation_meta', 10, 3);
		$this->loader->add_action('wp_ajax_spwai_generate_text', $sp_product, 'generate_text');
		$this->loader->add_action('wp_ajax_spwai_save_product_data', $sp_product, 'save_product_data');
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Spwai_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
