<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GooglePlaces
 *
 * @author ndy40
 */
class GooglePlaces {
    
    private $URL = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?";
    private $curl_instance;
    
    public function __construct() {
        $this->curl_instance = curl_init();
    }

        public function nearby_places($params=array("")){
            try{
            if(is_array($params) && !empty($params)){
                $counter = 0;
                foreach($params as $key => $value){
                    $this->URL .= ($counter != 0)? "&".$key."=".$value: $key ."=".$value;
                    ++$counter;
                }
            curl_setopt($this->curl_instance, CURLOPT_URL, $this->URL);
            curl_setopt($this->curl_instance, CURLOPT_HEADER, (int) 0);
            curl_setopt($this->curl_instance, CURLOPT_HTTPGET, TRUE);
            curl_setopt($this->curl_instance, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curl_instance, CURLOPT_SSL_VERIFYPEER,FALSE);
            
            $result = curl_exec($this->curl_instance);
                                  
            return json_decode($result);
                
            }
            }catch(Exception $ex){
                
            }
   }
}

?>
