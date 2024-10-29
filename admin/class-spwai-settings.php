<?php

/**
 * Settings page 
 *
 * @link       https://storepro.io/
 * @since      1.0.0
 * @package    ai-product-content-creator-for-woocommerce
 */
class Spwai_Settings
{

    /**
     * Add Settings menu
     */
    public function add_admin_menu()
    {
        add_menu_page('AI Content Creator Settings', 'AI Content Creator', 'manage_options', SPWAI_NAME, array($this, 'render_settings_page'), '', 56);
    }

    /**
     * Settings initiation
     */
    public function settings_init() {
        add_settings_section('spwai_api_section', 'API Settings', array($this, 'api_section_callback'), 'spwai_settings');

        add_settings_field('spwai_api_key', 'OpenAI API Key', array($this, 'api_key_callback'), 'spwai_settings', 'spwai_api_section');
        add_settings_field('spwai_model', 'Select Model', array($this, 'model_callback'), 'spwai_settings', 'spwai_api_section');

        register_setting('spwai_settings', 'spwai_api_key', array($this, 'sanitize_api_key'));
        register_setting('spwai_settings', 'spwai_model', array($this, 'sanitize_model'));
    }

    public function api_section_callback() {
        echo 'Configure your OpenAI API settings below.';
    }

    public function api_key_callback() {
        $api_key = get_option('spwai_api_key');
        echo '<input type="text" name="spwai_api_key" required value="' . esc_attr($api_key) . '" />';
        echo '<a class="spwai-help-link" href="https://platform.openai.com/api-keys" target="_blank">Get Your API Key</a>';
    }

    public function model_callback() {
        $model = get_option('spwai_model');
        $models = array(
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            'gpt-3.5-turbo-1106' => 'GPT-3.5 Turbo 1106',
            'gpt-4' => 'GPT-4', 
            'gpt-4-turbo-preview' => 'GPT-4 Turbo'
        );

        echo '<select name="spwai_model">';
        foreach ($models as $key => $option) {
            $selected = selected($model, $key, false);
            echo '<option value="' . esc_attr($key) . '" ' . esc_attr($selected) . '>' . esc_html($option) . '</option>';
        }
        echo '</select>';
        
        echo '<p class="description">Default model set as "GPT-3.5 Turbo", which is the cost-effective choice.</p>';
    }

    public function sanitize_api_key($input) {
        return sanitize_text_field($input);
    }

    public function sanitize_model($input) {
        return sanitize_text_field($input);
    }

    /**
     * Render the settings page
     */
    public function render_settings_page() {

        // Display any settings errors or messages
        settings_errors();
        ?>
    <div class="spwai-settings">
        <div class="wrap">
            <h2>Woocommerce Product Content Creator Settings</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('spwai_settings');
                do_settings_sections('spwai_settings');
                submit_button();
                ?>
            </form>
        </div>
        <span class="spwai-by-text">
            <a href="http://storepro.io/" target="_blank">
            <img src="<?php echo esc_url(SPWAI_URL . 'admin/images/storepro-logo.png'); ?>" alt="StorePro Logo">
            </a>
        </span>
    </div>
    <?php
}
}
