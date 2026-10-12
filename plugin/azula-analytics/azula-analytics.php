<?php
/**
 * Plugin Name: Azula Analytics
 * Description: Read-only, HPOS-compatible WooCommerce analytics dashboard (portfolio edition).
 * Version: 0.5.3
 */
defined('ABSPATH')||exit;
define('AZULA_ANALYTICS_VERSION','0.5.3');define('AZULA_ANALYTICS_DIR',plugin_dir_path(__FILE__));define('AZULA_ANALYTICS_URL',plugin_dir_url(__FILE__));
require_once AZULA_ANALYTICS_DIR.'includes/class-date-range.php';require_once AZULA_ANALYTICS_DIR.'includes/class-order-eligibility.php';require_once AZULA_ANALYTICS_DIR.'includes/class-product-classifier.php';require_once AZULA_ANALYTICS_DIR.'includes/class-woo-data.php';require_once AZULA_ANALYTICS_DIR.'includes/class-ga4-data.php';require_once AZULA_ANALYTICS_DIR.'includes/class-admin.php';require_once AZULA_ANALYTICS_DIR.'includes/class-presence.php';require_once AZULA_ANALYTICS_DIR.'includes/class-checkout-compat.php';
add_action('plugins_loaded',static function(){
    $host=strtolower((string)wp_parse_url(home_url('/'),PHP_URL_HOST));
    $production=defined('AZULA_ANALYTICS_ALLOWED_HOST') && $host===strtolower((string)AZULA_ANALYTICS_ALLOWED_HOST);
    if(!$production||!class_exists('WooCommerce'))return;
    Azula_Analytics_Admin::init();
    Azula_Analytics_Presence::init();
    Azula_Analytics_Checkout_Compat::init();
});