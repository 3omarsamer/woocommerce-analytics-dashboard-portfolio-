<?php
defined('ABSPATH')||exit;
/** Anonymous, fail-open storefront presence counter. No PII or IP addresses are stored. */
final class Azula_Analytics_Presence{
    private const ACTIVE_SECONDS=90;
    private const CLEAN_SECONDS=300;
    private const SCHEMA_OPTION='azula_analytics_presence_schema';
    public static function init():void{
        add_action('wp_enqueue_scripts',[__CLASS__,'assets']);
        add_action('wp_ajax_nopriv_azula_analytics_presence_beat',[__CLASS__,'heartbeat']);
        add_action('wp_ajax_azula_analytics_presence_beat',[__CLASS__,'heartbeat']);
        add_action('wp_ajax_azula_analytics_presence_count',[__CLASS__,'ajax_count']);
    }
    private static function table():string{global $wpdb;return $wpdb->prefix.'azula_analytics_presence';}
    private static function ensure_table():bool{global $wpdb;if(get_option(self::SCHEMA_OPTION)==='1')return true;require_once ABSPATH.'wp-admin/includes/upgrade.php';$table=self::table();$charset=$wpdb->get_charset_collate();dbDelta("CREATE TABLE {$table} (token_hash char(64) NOT NULL,last_seen bigint(20) unsigned NOT NULL,PRIMARY KEY (token_hash),KEY last_seen (last_seen)) {$charset};");$exists=$wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$table));if($exists===$table){update_option(self::SCHEMA_OPTION,'1',false);return true;}return false;}
    public static function assets():void{if(is_admin())return;wp_enqueue_script('azula-presence',AZULA_ANALYTICS_URL.'assets/js/presence.js',[],AZULA_ANALYTICS_VERSION,true);wp_localize_script('azula-presence','AzulaPresence',['ajaxUrl'=>admin_url('admin-ajax.php')]);}
    public static function heartbeat():void{try{if(!self::ensure_table())wp_send_json_error([],503);$token=sanitize_text_field(wp_unslash($_POST['token']??''));if(!preg_match('/^[A-Za-z0-9_-]{20,100}$/',$token))wp_send_json_error([],400);global $wpdb;$table=self::table();$now=time();$hash=hash('sha256',$token);$wpdb->query($wpdb->prepare("INSERT INTO {$table} (token_hash,last_seen) VALUES (%s,%d) ON DUPLICATE KEY UPDATE last_seen=VALUES(last_seen)",$hash,$now));if(wp_rand(1,20)===1)$wpdb->query($wpdb->prepare("DELETE FROM {$table} WHERE last_seen < %d",$now-self::CLEAN_SECONDS));wp_send_json_success();}catch(Throwable $e){wp_send_json_error([],500);}}
    public static function count():int{try{if(!self::ensure_table())return 0;global $wpdb;$table=self::table();$cut=time()-self::ACTIVE_SECONDS;return max(0,(int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE last_seen >= %d",$cut)));}catch(Throwable $e){return 0;}}
    public static function ajax_count():void{if(!current_user_can('view_woocommerce_reports'))wp_send_json_error(['message'=>'Not allowed.'],403);check_ajax_referer('azula_analytics_admin','nonce');wp_send_json_success(['value'=>self::count()]);}
}