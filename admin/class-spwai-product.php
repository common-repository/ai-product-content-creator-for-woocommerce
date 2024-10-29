<?php

/**
 * Product related functionalities in admin end  
 *
 * @link       https://storepro.io/
 * @since      1.0.0
 * @package    ai-product-content-creator-for-woocommerce
 */
class Spwai_Product
{

    /**
     * Add product metabox for extra fields 
     */
    public function add_product_meta_box()
    {
        add_meta_box('spwai-product-metabox', 'AI Product Content Generate', array($this, 'product_meta_box_content'), 'product', 'normal', 'high');
    }

    /**
     * product_meta_box_content
     */
    public function product_meta_box_content($post)
    {
        require_once(SPWAI_PATH . 'admin/partials/product-metabox.php');
    }

    /**
     * Add extra fields in Variation product
     */
    public function add_variation_meta($loop, $variation_data, $variation)
    {
        include(SPWAI_PATH . 'admin/partials/product-variation-meta.php');
    }

    /**
     * AJAX Handling: Generate text from openai
     */
    public function generate_text()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'spwai_nonce')) {
            die('Permission check failed');
        }

        // Validate and sanitize prompt
        if (!isset($_POST['prompt'])) {
            die('Prompt not provided');
        }
        $prompt = sanitize_text_field(wp_unslash($_POST['prompt']));

        // Validate and sanitize field
        if (!isset($_POST['field'])) {
            die('Field not provided');
        }
        $field = sanitize_text_field(wp_unslash($_POST['field']));

        // Make a request to ChatGPT API and get the response
        $result = Spwai_Openai::generate_text_from_openai($prompt, $field);

        // Return the generated description
        echo wp_json_encode($result);
        die();
    }

    /**
     * AJAX Handling: Save product data that generated with openai
     */
    public function save_product_data()
    {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'spwai_nonce')) {
            die('Permission check failed');
        }

        // default response
        $result['status'] = 'failed';
        $result['message'] = 'Invalid request!';

        // Process update for main product
        if (isset($_POST['product_id'])) {
            $product_id = intval($_POST['product_id']);

            // Validate and sanitize fields
            if (!isset($_POST['fields']) || !is_array($_POST['fields'])) {
                die('Fields not provided or invalid');
            }
            $fields = array_map(function($field) {
                return is_array($field) ? array_map('sanitize_text_field', $field) : sanitize_text_field($field);
            }, wp_unslash($_POST['fields']));

            if (!empty($product_id) && !empty($fields)) {
                // Set update data as an array
                $update_data = [];
                foreach ($fields as $field => $value) {
                    if (!empty(trim($value))) {
                        if ($field === 'title') {
                            $update_data['post_title'] = sanitize_text_field(trim($value));
                        } else if ($field === 'description') {
                            $update_data['post_content'] = sanitize_textarea_field(trim($value));
                        } else if ($field === 'shortdescription') {
                            $update_data['post_excerpt'] = sanitize_textarea_field(trim($value));
                        }
                    }
                }
                // Update WooCommerce product data
                if (!empty($update_data)) {
                    $update_data['ID'] = $product_id;
                    wp_update_post($update_data);
                    $result['status'] = 'success';
                    $result['message'] = 'Updated Successfully';
                } else {
                    $result['message'] = 'No values in Generated fields!';
                }
            }
        } 
        // Process update for variation
        else if (isset($_POST['variation_id'])) {
            $variation_id = intval($_POST['variation_id']);

            // Validate and sanitize description
            if (!isset($_POST['description'])) {
                die('Description not provided');
            }
            $description = sanitize_textarea_field(trim(wp_unslash($_POST['description'])));

            if (!empty($variation_id) && !empty($description)) {
                // Update the variation description
                update_post_meta($variation_id, '_variation_description', $description);
                $result['status'] = 'success';
                $result['message'] = 'Updated Successfully';
            }
        }
        // Send a response
        echo wp_json_encode($result);
        die();
    }
}
