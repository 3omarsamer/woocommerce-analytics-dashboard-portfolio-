<?php
defined('ABSPATH')||exit;

/**
 * Read-only Google Analytics Data API client for Azula Analytics.
 * Authentication is independent of Site Kit and uses the configured service account.
 */
final class Azula_Analytics_GA4_Data{
    private const SCOPE='https://www.googleapis.com/auth/analytics.readonly';
    private const TOKEN_URL='https://oauth2.googleapis.com/token';
    private const API_ROOT='https://analyticsdata.googleapis.com/v1beta/properties/';
    private const REALTIME_TTL=45;
    private const REPORT_TTL=600;

    public function live_visitors():array{
        $property=$this->property_id();
        if(!$property)return $this->error('GA4 property is not configured.');
        $key='az_ga4_rt_'.md5($property);
        $cached=get_transient($key);
        if(is_array($cached))return $cached;
        $result=$this->request('runRealtimeReport',['metrics'=>[['name'=>'activeUsers']]]);
        if(!$result['ok'])return $result;
        $value=$this->first_metric($result['data']);
        if($value===null)return $this->error('GA4 realtime response did not contain activeUsers.');
        $out=['ok'=>true,'value'=>(int)$value];
        set_transient($key,$out,self::REALTIME_TTL);
        return $out;
    }

    public function total_visitors(array $range):array{
        $property=$this->property_id();
        if(!$property)return $this->error('GA4 property is not configured.');
        $from=$range['date_from'];$to=$range['date_to'];
        $key='az_ga4_u_'.md5($property.'|'.$from.'|'.$to);
        $cached=get_transient($key);
        if(is_array($cached))return $cached;
        $result=$this->request('runReport',[
            'dateRanges'=>[['startDate'=>$from,'endDate'=>$to]],
            'metrics'=>[['name'=>'totalUsers']],
        ]);
        if(!$result['ok'])return $result;
        $value=$this->first_metric($result['data']);
        if($value===null)$value=0;
        $out=['ok'=>true,'value'=>(int)$value,'timezone'=>$this->timezone($result['data'])];
        set_transient($key,$out,self::REPORT_TTL);
        return $out;
    }

    public function sessions(array $range):array{
        $property=$this->property_id();
        if(!$property)return $this->error('GA4 property is not configured.');
        $from=$range['date_from'];$to=$range['date_to'];
        $key='az_ga4_s_'.md5($property.'|'.$from.'|'.$to);
        $cached=get_transient($key);
        if(is_array($cached))return $cached;
        $result=$this->request('runReport',[
            'dateRanges'=>[['startDate'=>$from,'endDate'=>$to]],
            'metrics'=>[['name'=>'sessions']],
        ]);
        if(!$result['ok'])return $result;
        $value=$this->first_metric($result['data']);
        if($value===null)$value=0;
        $out=['ok'=>true,'value'=>(int)$value,'timezone'=>$this->timezone($result['data'])];
        set_transient($key,$out,self::REPORT_TTL);
        return $out;
    }

    public function funnel(array $range):array{
        $property=$this->property_id();
        if(!$property)return $this->error('GA4 property is not configured.');
        $from=$range['date_from'];$to=$range['date_to'];
        $key='az_ga4_f_'.md5($property.'|'.$from.'|'.$to);
        $cached=get_transient($key);if(is_array($cached))return $cached;
        $total=$this->sessions($range);if(!$total['ok'])return $total;
        $steps=[];$timezone=(string)($total['timezone']??'');
        foreach(['add_to_cart','begin_checkout','purchase'] as $event){
            $result=$this->request('runReport',[
                'dateRanges'=>[['startDate'=>$from,'endDate'=>$to]],
                'metrics'=>[['name'=>'sessions']],
                'dimensionFilter'=>['filter'=>['fieldName'=>'eventName','stringFilter'=>['matchType'=>'EXACT','value'=>$event,'caseSensitive'=>true]]],
            ]);
            if(!$result['ok'])return $result;
            $value=$this->first_metric($result['data']);$steps[$event]=(int)($value??0);
            if($timezone==='')$timezone=$this->timezone($result['data']);
        }
        $out=['ok'=>true,'sessions'=>(int)$total['value'],'add_to_cart'=>$steps['add_to_cart'],'begin_checkout'=>$steps['begin_checkout'],'purchase'=>$steps['purchase'],'timezone'=>$timezone];
        set_transient($key,$out,self::REPORT_TTL);return $out;
    }

    public function sessions_timeline(array $range):array{
        $property=$this->property_id();
        if(!$property)return $this->error('GA4 property is not configured.');
        $from=$range['date_from'];$to=$range['date_to'];
        $key='az_ga4_st_'.md5($property.'|'.$from.'|'.$to);
        $cached=get_transient($key);
        if(is_array($cached))return $cached;
        $result=$this->request('runReport',[
            'dateRanges'=>[['startDate'=>$from,'endDate'=>$to]],
            'dimensions'=>[['name'=>'date']],
            'metrics'=>[['name'=>'sessions']],
            'orderBys'=>[['dimension'=>['dimensionName'=>'date'],'desc'=>false]],
            'limit'=>'10000',
        ]);
        if(!$result['ok'])return $result;
        $found=[];
        foreach(($result['data']['rows']??[]) as $row){
            $raw=(string)($row['dimensionValues'][0]['value']??'');
            $value=$row['metricValues'][0]['value']??null;
            if(preg_match('/^\d{8}$/',$raw)&&is_numeric($value)){
                $date=substr($raw,0,4).'-'.substr($raw,4,2).'-'.substr($raw,6,2);
                $found[$date]=(int)$value;
            }
        }
        $rows=[];
        try{$cursor=new DateTimeImmutable($from);$end=new DateTimeImmutable($to);}catch(Throwable $e){return $this->error('Invalid GA4 report date range.');}
        while($cursor<=$end){$date=$cursor->format('Y-m-d');$rows[]=['date'=>$date,'sessions'=>$found[$date]??0];$cursor=$cursor->modify('+1 day');}
        $out=['ok'=>true,'rows'=>$rows,'timezone'=>$this->timezone($result['data'])];
        set_transient($key,$out,self::REPORT_TTL);
        return $out;
    }

    private function request(string $method,array $body):array{
        $property=$this->property_id();
        $token=$this->access_token();
        if(!$token['ok'])return $token;
        $url=self::API_ROOT.rawurlencode($property).':'.$method;
        $response=wp_remote_post($url,[
            'timeout'=>20,'redirection'=>0,
            'headers'=>['Authorization'=>'Bearer '.$token['token'],'Content-Type'=>'application/json'],
            'body'=>wp_json_encode($body),
        ]);
        unset($token);
        if(is_wp_error($response))return $this->error('Google Analytics is temporarily unavailable.');
        $status=(int)wp_remote_retrieve_response_code($response);
        $raw=(string)wp_remote_retrieve_body($response);
        $data=json_decode($raw,true);unset($raw);
        if($status<200||$status>=300){
            $message=is_array($data)?(string)($data['error']['message']??'Google Analytics API request failed.'):'Google Analytics API request failed.';
            return $this->error($message,$status);
        }
        if(!is_array($data))return $this->error('Google Analytics returned an invalid response.');
        return ['ok'=>true,'data'=>$data];
    }

    private function access_token():array{
        if(!defined('AZULA_ANALYTICS_GA4_CREDENTIALS'))return $this->error('GA4 credentials are not configured.');
        $path=(string)AZULA_ANALYTICS_GA4_CREDENTIALS;
        if(!$path||!is_readable($path))return $this->error('GA4 credential file is unavailable.');
        $raw=file_get_contents($path);if($raw===false)return $this->error('GA4 credential file could not be read.');
        $credentials=json_decode($raw,true);unset($raw);
        if(!is_array($credentials)||($credentials['type']??'')!=='service_account'||empty($credentials['client_email'])||empty($credentials['private_key']))return $this->error('GA4 service-account credential is invalid.');
        $now=time();
        $header=$this->b64(wp_json_encode(['alg'=>'RS256','typ'=>'JWT']));
        $claims=$this->b64(wp_json_encode(['iss'=>(string)$credentials['client_email'],'scope'=>self::SCOPE,'aud'=>self::TOKEN_URL,'iat'=>$now,'exp'=>$now+3600]));
        $unsigned=$header.'.'.$claims;$signature='';
        $signed=openssl_sign($unsigned,$signature,(string)$credentials['private_key'],OPENSSL_ALGO_SHA256);
        unset($credentials);
        if(!$signed)return $this->error('GA4 service-account authentication failed.');
        $assertion=$unsigned.'.'.$this->b64($signature);unset($unsigned,$signature);
        $response=wp_remote_post(self::TOKEN_URL,['timeout'=>20,'redirection'=>0,'headers'=>['Content-Type'=>'application/x-www-form-urlencoded'],'body'=>['grant_type'=>'urn:ietf:params:oauth:grant-type:jwt-bearer','assertion'=>$assertion]]);unset($assertion);
        if(is_wp_error($response))return $this->error('Google authentication is temporarily unavailable.');
        $status=(int)wp_remote_retrieve_response_code($response);$raw=(string)wp_remote_retrieve_body($response);$data=json_decode($raw,true);unset($raw);
        if($status<200||$status>=300||!is_array($data)||empty($data['access_token']))return $this->error(is_array($data)?(string)($data['error_description']??'Google authentication failed.'):'Google authentication failed.',$status);
        return ['ok'=>true,'token'=>(string)$data['access_token']];
    }

    private function property_id():string{return defined('AZULA_ANALYTICS_GA4_PROPERTY_ID')?preg_replace('/\D+/','',(string)AZULA_ANALYTICS_GA4_PROPERTY_ID):'';}
    private function first_metric(array $data):?float{$v=$data['rows'][0]['metricValues'][0]['value']??null;return is_numeric($v)?(float)$v:null;}
    private function timezone(array $data):string{return sanitize_text_field((string)($data['metadata']['timeZone']??''));}
    private function b64(string $value):string{return rtrim(strtr(base64_encode($value),'+/','-_'),'=');}
    private function error(string $message,int $status=0):array{return ['ok'=>false,'error'=>sanitize_text_field($message),'status'=>$status];}
}