<?php

/* WhatsApp Notifications WHMCS Module By Vishal Hotkar

 * WA Client - https://notify.designfactri.biz

 * Version 2.0.4

 * */

class waclientAPI {
    private $user_token; // USER API TOKEN
    private $user_key; //USER API KEY
    private $country_code="91";//Default Country Code Bangladesh //880 with out +
    protected $url='https://notify.designfactri.biz/api/create-message?';// ALWAYS USE THIS LINK TO CALL API SERVICE
    
    public $msgType="text";// Message type sms/voice/unicode/flash/music/mms/whatsapp
    public $route=0;// Your Routing Path Default 0
    public $file=false;// File URL for voice or whatsapp. Default not set
    public $scheduledate=false;//Date and Time to send message (YYYY-MM-DD HH:mm:ss) Default not use
    public $duration=false;//Duration of your voice message in seconds (required for voice)
    public $language=false;//Language of voice message (required for text-to-speach)

    /**
     * To Find your api details please log and go into https://notify.designfactri.biz/
     */
    /**
     * Call to site
     */
    private function Call($params){
        if($params){ 
            $params = str_replace(" ", '%20', $params);
            $curl_handle=curl_init();
            curl_setopt($curl_handle,CURLOPT_URL,$this->url.$params);
            curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
            curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
            curl_exec($curl_handle);
            curl_close($curl_handle);
        }else{
            return false;
        }
    }

    /**
     * Set user Credentials
     * @return boolen
     */
    public function setUser($key,$token){
        if($key && $token){
            $this->user_key=$key;
            $this->user_token=$token;
            return true;
        }else{
            return false;
        }
    }


    /**
     * Set Default Routing
     * @return boolen
     */
    public function RouteNumber($number){
        if($number){
            $explode=str_split($number);
            if($explode[0]=="+"){
                unset($explode[0]);
                $number=implode("",$explode);
            }else{
                if($explode[0]==0){
                    unset($explode[0]);
                    $number=implode("",$explode);
                }
                $number=$this->country_code.$number;
            }
            return $number;
        }else{
            return false;
        }
    }

    /**
     * Check avalible credit balance
     * @return array
     */
    public function CheckBalance($json=FALSE){
        $param='action=check-balance&api_key='.$this->user_key.'&apitoken='.$this->user_token;
        if($result=$this->Call($param)){
            if($json===FALSE){
                $c=json_decode($result,true);
                if($c['balance'] !="error"){
                    return false;
                }else{
                    return $c;
                }
            }else{
                return $result;
            }
        }else{
            return false;
        }
    }

    /**
     * Check SMS status
     * group_id = The group_id returned by send sms request
     * @return array
     */
    public function CheckStatus($group_id,$json=FALSE){
        if($group_id){
            $param="&groupstatus&apikey=".$this->user_key."&apitoken=".$this->user_token."&groupid=".$group_id;
            if($res=$this->Call($param)){
                if($json===FALSE){
                    $c=json_decode($res);//You can also use direct json by call json as true
                    if($c['status']=="error"){
                        return false;
                    }else{
                        return $c;
                    }
                }else{
                    return $res;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    /**
     * Send Message
     * @return boolen
     */
    public function SendMessage($Mobile,$TEXT,$invoice_pdf_file,$invoiceid,$json=FALSE){
        
        $TEXT=json_decode($TEXT);
        $TEXT=urlencode($TEXT);
        $TEXT = html_entity_decode($TEXT);

        if($this->user_key !="" && $this->user_token !=""){
            if($Mobile){
                if($TEXT){
    
                    $param='number='.$Mobile.'&type='.$this->msgType.'&instance_id='.$this->user_token.'&access_token='.$this->user_key;

                    if($invoice_pdf_file){
                        $filename = basename($invoice_pdf_file);
                        $param.='&message='.$TEXT.'&type=media&media_url='.$invoice_pdf_file.'&filename='.$filename.'';
                    }else{
                        if($this->msgType=="text" || $this->msgType=="unicode"){
                            $param.='&message='.$TEXT;
                        }
                    }

                    if($this->scheduledate!=false){
                        $param.='&scheduledate='.$this->scheduledate;
                    }
                    if($this->language!=false){
                        $param.='&language='.$this->language;
                    }
                    if($res=$this->Call($param,$Mobile,$TEXT,$invoice_pdf_file,$invoiceid)){
                        if($json !=FALSE){
                            return $res;
                        }else{
                            $c=json_decode($res);
                            return $c;
                        }
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}
?>