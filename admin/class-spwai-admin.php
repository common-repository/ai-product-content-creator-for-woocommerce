<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://storepro.io/
 * @since      1.0.0
 * @package    ai-product-content-creator-for-woocommerce
 */
class Spwai_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets and scripts for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        // admin styles
        wp_enqueue_style($this->plugin_name, SPWAI_URL . 'admin/css/admin.css', array(), $this->version, 'all');

        // admin scripts
        wp_enqueue_script($this->plugin_name, SPWAI_URL . 'admin/js/admin.js', array('jquery'), $this->version, true);
    }

    /**
     * Add Link to settings page in plugin list page 
     */
    public function settings_link($links)
    {
        $url = esc_url(get_admin_url() . "admin.php?page=" . SPWAI_NAME);
        $settings_link = '<a href="' . $url . '">' . __('Settings', 'ai-product-content-creator-for-woocommerce') . '</a>';
        array_push($links, $settings_link);
        return $links;
    }
}
