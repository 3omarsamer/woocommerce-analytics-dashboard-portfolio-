<?php
defined('ABSPATH')||exit;
/** Central Woo Analytics status exclusion policy. All Woo-derived reports must reuse this. */
final class Azula_Analytics_Order_Eligibility{
    /**
     * Effective Woo Analytics exclusions, normalized to wc_order_stats status format.
     * Mirrors Woo Analytics semantics: configured exclusions + auto-draft/trash + filter.
     */
    public static function excluded_statuses():array{
        $configured=(array)get_option('woocommerce_excluded_report_order_statuses',['pending','failed','cancelled']);
        $configured=array_map(static function($s){
            $s=sanitize_key((string)$s);
            return str_starts_with($s,'wc-')?substr($s,3):$s;
        },$configured);
        $excluded=array_values(array_unique(array_merge(['auto-draft','trash'],$configured)));
        $excluded=(array)apply_filters('woocommerce_analytics_excluded_order_statuses',$excluded);
        return array_values(array_unique(array_filter(array_map(static function($s){
            $s=sanitize_key((string)$s);
            if($s==='')return '';
            return str_starts_with($s,'wc-')?$s:'wc-'.$s;
        },$excluded))));
    }

    /** Diagnostic/display helper only; queries should use exclusion semantics directly. */
    public static function included_statuses():array{
        $registered=array_keys(wc_get_order_statuses());
        return array_values(array_diff($registered,self::excluded_statuses()));
    }
}