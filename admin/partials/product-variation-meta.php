<?php
/**
 * Add extra field for product variation
 *
 * @link       https://storepro.io/
 * @since      1.0.0
 * @package    ai-product-content-creator-for-woocommerce
 */
if (!defined('ABSPATH')) {
    die; // Exit if accessed directly
}
// $description = $variation_data['_variation_description'][0];
$title = get_the_title($variation);
$excerpt = get_the_excerpt($variation);

?>

<div class="spwai-variation-meta-box spwai-meta-box">
    <h2>AI Product Content Generate</h2>
    <label for="spwai-var-prompt" class="spwai-prompt-label">Product Keywords</label>
    <input type="text" class="spwai-prompt" value="<?php echo esc_attr($title . ' (' . $excerpt . ')'); ?>" />

    <div class="spwai-button-container">
        <button type="button" class="spwai-generate" data-loop="<?php echo esc_attr($loop); ?>">Generate</button>
        <!-- Add this loader element in your HTML -->
        <div class="spwai-loader" style="display: none;">
            <img src="<?php echo esc_url(SPWAI_URL . 'admin/images/loading.gif'); ?>" alt="Loading...">
        </div>
    </div>
    <!-- Show Error Message -->
    <div class="spwai-error-message" id="spwai-var-error-message"></div>

    <div class="spwai-output">
        <label><b>Generated Outputs</b></label>
    </div>

    <!-- Output Data -->
    <div class="spwai-output">
        <label for="spwai-var-description_<?php echo esc_attr($loop); ?>">Description:</label>
        <textarea id="spwai-var-description_<?php echo esc_attr($loop); ?>" class="spwai-description" name="spwai_var_description[<?php echo esc_attr($loop); ?>]" rows="4"></textarea>
    </div>

    <!-- Apply Button -->
    <div class="spwai-output">
        <button type="button" data-loop="<?php echo esc_attr($loop); ?>" class="spwai-apply button button-primary button-large">Save Data</button>
    </div>
</div>
