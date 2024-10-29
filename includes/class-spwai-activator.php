<?php

/**
 * Fired during plugin activation
 *
 * @link       https://storepro.io/
 * @since      1.0.0
 * @package    spwooai
 */
class Spwai_Activator
{
    /**
     * @since    1.0.0
     */
    public static function activate()
    {
        // create custom table
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'spwai_token';

        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));

        if ($table_exists != $table_name) {
            require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
            $create_sql = "CREATE TABLE `$table_name` (
                          `id` INT NOT NULL AUTO_INCREMENT,
                          `model` varchar(50) NOT NULL,
                          `year` SMALLINT NOT NULL,
                          `month` SMALLINT NOT NULL,
                          `prompt_tokens` INT NOT NULL,
                          `completion_tokens` INT NOT NULL,
                          `total_tokens` INT NOT NULL,
                           PRIMARY KEY(id),
                           UNIQUE KEY unique_model_month_year (model, year, month)
                          ) $charset_collate;";

            dbDelta($create_sql);
        }
    }
}
