<?php
defined('ABSPATH')||exit;
/** Production begin_checkout compatibility. Commerce must never depend on this class. */
final class Azula_Analytics_Checkout_Compat{
    public static function init():void{add_action('wp_enqueue_scripts',[__CLASS__,'enqueue'],99);}
    private static function production():bool{$host=strtolower((string)wp_parse_url(home_url('/'),PHP_URL_HOST));return defined('AZULA_ANALYTICS_ALLOWED_HOST') && $host===strtolower((string)AZULA_ANALYTICS_ALLOWED_HOST);}
    public static function enqueue():void{
        if(!self::production()||!function_exists('is_checkout')||!is_checkout()||is_order_received_page()||is_wc_endpoint_url('order-pay'))return;
        if(!function_exists('WC')||!WC()->cart||WC()->cart->is_empty())return;
        $hash=(string)WC()->cart->get_cart_hash();if($hash==='')return;
        wp_enqueue_script('azula-begin-checkout',AZULA_ANALYTICS_URL.'assets/js/begin-checkout.js',[],AZULA_ANALYTICS_VERSION,true);
        wp_add_inline_script('azula-begin-checkout','window.AzulaBeginCheckout='.wp_json_encode(['episode'=>hash('sha256',$hash)]).';','before');
    }
}