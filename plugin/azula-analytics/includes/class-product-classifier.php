<?php
defined('ABSPATH')||exit;
/** Read-only analytics classification. Never mutates product taxonomy. */
final class Azula_Analytics_Product_Classifier{
private const TYPE_MAP=[
310=>'Leggings',351=>'Leggings',313=>'Pants',309=>'Pants',311=>'Shorts',318=>'Shorts',317=>'Sports Bras',
316=>'Tops',315=>'Tops',308=>'Tops',320=>'Hoodies & Jackets',307=>'Hoodies & Jackets',345=>'Swimwear',347=>'Swimwear',
325=>'Footwear',324=>'Footwear',323=>'Footwear',352=>'Footwear',349=>'Footwear',350=>'Footwear',321=>'Socks',
322=>'Accessories',329=>'Accessories',330=>'Accessories'];
private const TYPE_OVERRIDES=[2874=>'Hoodies & Jackets',4613=>'Leggings',13406=>'Pants',13417=>'Pants',3261=>'Tops',5765=>'Tops',5891=>'Tops'];
private const BRAND_MAP=[83=>'Alphalete',49=>'AYBL',104=>'Alo',327=>'Alo',328=>'Alo',58=>'BO+TEE',35=>'Dfyne',62=>'Gymshark',306=>'Gymshark',312=>'Gymshark',110=>'Oace',24=>'Oner Active',40=>'Teveo',79=>'YoungLA'];
private const MEN_TAG=54;private const WOMEN_TAG=22;
private array $cat_cache=[],$tag_cache=[];
private function term_ids(int $product_id,string $tax):array{$cache=$tax==='product_cat'?'cat_cache':'tag_cache';if(isset($this->{$cache}[$product_id]))return$this->{$cache}[$product_id];$ids=wp_get_post_terms($product_id,$tax,['fields'=>'ids']);if(is_wp_error($ids))$ids=[];return$this->{$cache}[$product_id]=array_map('intval',$ids);}
public function category(int $product_id):string{if(isset(self::TYPE_OVERRIDES[$product_id]))return self::TYPE_OVERRIDES[$product_id];$b=[];foreach($this->term_ids($product_id,'product_cat')as$id)if(isset(self::TYPE_MAP[$id]))$b[self::TYPE_MAP[$id]]=true;$b=array_keys($b);if(!$b)return'Unclassified';if(count($b)===1)return$b[0];
// Approved specificity reductions only. Any remaining cross-type conflict is deliberately Unclassified.
if(in_array('Sports Bras',$b,true)&&in_array('Tops',$b,true))$b=array_values(array_diff($b,['Tops']));
if(in_array('Socks',$b,true))$b=array_values(array_diff($b,['Footwear','Accessories']));
if(in_array('Hoodies & Jackets',$b,true)&&in_array('Tops',$b,true))$b=array_values(array_diff($b,['Tops']));
return count($b)===1?$b[0]:'Unclassified';}
public function brand(int $product_id):string{$b=[];foreach($this->term_ids($product_id,'product_cat')as$id)if(isset(self::BRAND_MAP[$id]))$b[self::BRAND_MAP[$id]]=true;$b=array_keys($b);return count($b)===1?$b[0]:'Unclassified';}
public function gender(int $product_id):string{$ids=$this->term_ids($product_id,'product_tag');$m=in_array(self::MEN_TAG,$ids,true);$w=in_array(self::WOMEN_TAG,$ids,true);if($m&&$w)return'Unisex';if($m)return'Men';if($w)return'Women';return'Unclassified';}
public static function type_map():array{return self::TYPE_MAP;}public static function overrides():array{return self::TYPE_OVERRIDES;}
}