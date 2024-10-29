<?php

/**
 * Openai api handling
 *
 * @link       https://storepro.io/
 * @since      1.0.0
 * @package    ai-product-content-creator-for-woocommerce
 */
class Spwai_Openai
{
    const SPWAI_EP_CHAT = 'https://api.openai.com/v1/chat/completions'; // chat endpoint
    
    /**
     * Generate text from openai
     */
    public static function generate_text_from_openai($prompt, $field)
    {
        switch ($field) {
            case 'title':
                $system_message = "Craft a compelling, concise, SEO-optimized product title in English based on user input, using relevant keywords for enhanced search visibility. Maximum Limit: 70 characters.";
                break;
            case 'description':
                $system_message = "Craft a compelling, concise, SEO-optimized product description in English based on user input, using relevant keywords for enhanced search visibility. Include specific details, features, and benefits, as well as the value it offers to the customer. Maximum Limit: 800 characters. Include Minimum 2 paragraphs";
                break;
            case 'shortdescription':
                $system_message = "Craft a compelling, concise, SEO-optimized product short description in English based on user input, using relevant keywords for enhanced search visibility. Include specific details, features, and benefits. Maximum Limit: 400 characters.";
                break;
            case 'var-description':
                $system_message = "Craft a compelling, concise, SEO-optimized product short description in English based on user input, using relevant keywords for enhanced search visibility. Include specific details, features, and benefits. Maximum Limit: 400 characters.";
                break;
            default:
                $system_message = 'Respond as "INVALID INPUT"';
                break;
        }

        $result = ['status' => 'failed'];
        
        // Data to be sent to the API
        $data = array(
            'model' => sanitize_text_field(trim(get_option('spwai_model'))), // Specify the GPT-3.5-turbo model
            'messages' => array(
                array('role' => 'system', 'content' => sanitize_text_field($system_message)),
                array('role' => 'user', 'content' => sanitize_text_field($prompt))
            )
        );
        
        // Setup headers
        $headers = array(
            'Authorization' => 'Bearer ' . sanitize_text_field(trim(get_option('spwai_api_key'))),
            'Content-Type' => 'application/json'
        );

        // Make the API request
        $response = wp_remote_post(self::SPWAI_EP_CHAT, array(
            'method' => 'POST',
            'headers' => $headers,
            'body' => wp_json_encode($data),
            'data_format' => 'body',
            'timeout' => 60
        ));

        // Check if the API request was successful
        if (is_wp_error($response)) {
            $result['message'] = 'Failed to connect OpenAI API: ' . $response->get_error_message() . ' Please try again.';
            return $result;
        }

        // Access response code
        $response_code = wp_remote_retrieve_response_code($response);
        // Decode the response body
        $response_body = json_decode(wp_remote_retrieve_body($response), true);

        // Check if the response contains the expected data
        if (!isset($response_body['choices'][0]['message']['content'])) {

            $settings_page = admin_url('admin.php?page='.SPWAI_NAME);
            // check errors in response
            switch ($response_code) {
                case '401':
                    $result['message'] = 'API Error: Invalid Authentication. Possible due to Incorrect API key provided. Please verify your API key <a href="' . esc_url($settings_page) . '" target="_blank">here</a>.';
                    break;
                case '429':
                    $result['message'] = 'API Error: Possible due to exceeded your current quota, please check your plan and billing details. You can check your usage <a href="https://platform.openai.com/account/usage" target="_blank">here</a>.';
                    break;
                case '500':
                    $result['message'] = 'API Error: The OpenAI server had an error while processing your request. please try again later'; //Check the <a href="https://status.openai.com/" target="_blank">status page</a>.
                    break;
                case '503':
                    $result['message'] = 'API Error: The OpenAI engine is currently overloaded, please try again later';
                    break;

                default:
                    $result['message'] = 'Error: No Response from OpenAI. (It may be server or internet connection issues, or an incorrect API key provided. Please check your <a href="' . esc_url($settings_page) . '" target="_blank">Settings</a>.)';
                    break;
            }
            return $result;
        }

        // Extract the generated text
        $generated_text = sanitize_text_field(trim($response_body['choices'][0]['message']['content']));
        $generated_text = trim($generated_text, '"'); // remove double quotes from start and end

        $result['status'] = 'success';
        $result['message'] = $generated_text;
        return $result;
    }
}
