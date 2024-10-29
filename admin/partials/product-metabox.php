<?php
/**
 * Add Metabox for product
 *
 * @link       https://storepro.io/
 * @since      1.0.0
 * @package    ai-product-content-creator-for-woocommerce
 */
if (!defined('ABSPATH')) {
    die; // Exit if accessed directly
}
// global $post;
$product_title = get_the_title($post->ID);
?>
<div class="spwai-meta-box">
    <label for="spwai-prompt" class="spwai-prompt-label">Product Keywords</label>
    <input type="text" id="spwai-prompt" class="spwai-prompt" name="spwai_prompt" value="<?php echo esc_attr($product_title); ?>" />

    <div class="spwai-button-container">
        <button type="button" id="spwai-generate" class="spwai-generate">Generate</button>
        <!-- Add this loader element in your HTML -->
        <div id="spwai-loader" class="spwai-loader" style="display: none;">
            <img src="<?php echo esc_url(SPWAI_URL . 'admin/images/loading.gif'); ?>" alt="Loading...">
        </div>
    </div>
    <!-- Show Error Message -->
    <div class="spwai-error-message" id="spwai-error-message"></div>

    <div class="spwai-output">
        <label><b>Generated Outputs</b></label>
    </div>

    <!-- Output Data -->
    <div class="spwai-output">
        <input type="checkbox" id="spwai-check-title" checked />
        <label for="spwai-check-title">Title:</label>
        <input type="text" id="spwai-title" name="spwai_title" />
    </div>
    <div class="spwai-output">
        <input type="checkbox" id="spwai-check-description" checked />
        <label for="spwai-check-description">Description:</label>
        <textarea id="spwai-description" name="spwai_description" rows="8"></textarea>
    </div>
    <div class="spwai-output">
        <input type="checkbox" id="spwai-check-shortdescription" checked />
        <label for="spwai-check-shortdescription">Short Description:</label>
        <textarea id="spwai-shortdescription" name="spwai_shortdescription" rows="5"></textarea>
    </div>
    <input type="hidden" name="spwai_nonce" id="spwai-nonce" value="<?php echo esc_attr(wp_create_nonce('spwai_nonce')); ?>" />

    <!-- Apply Button -->
    <div class="spwai-output">
        <button type="button" id="spwai-apply" class="button button-primary button-large">Save Generated Values</button>
    </div>
</div>
