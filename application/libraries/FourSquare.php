<?php if(!defined("BASEPATH")) exit("No direct script access allowed");

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FourSquare
 *
 * @author ndy40
 */
class FourSquare {

    //put your code here
    private $client_id = "FCSXEB2YT1JSURENFDAVHEDRSM4KEFLBWPIZUQVHNRCDAEWP";
    private $client_secrete = "VATJ3UKZX3NPZJHL2RYYTN1QHFLXFH45MRFMIK2A5O52LGV3";
    public $url;
    public $limit = 10;
    private $curl_instance;

    

        
    public function venue_explore($params) {
        try {
            if (empty($params)){
                throw new InvalidArgumentException("error: empty parameter supplied!!");
            }else if(is_array($params)) {
                if (strpos($this->url, "?") == FALSE)
                    $this->url .= "?";
                
                $counter = 0;
                
                foreach ($params as $key => $value) {
                    $this->url .= ($counter != 0)? "&".$key."=".$value: $key ."=".$value;
                    ++$counter;
                }
                if (!in_array("client_id", $params))
                    $this->url .= "&client_id=" . $this->client_id;
                if (!in_array("client_secret", $params))
                    $this->url .= "&client_secret=" . $this->client_secrete;
                $this->url .= "v=20121105&radius=300";
            }
            $this->curl_instance = curl_init($this->url);
            curl_setopt($this->curl_instance, CURLOPT_HEADER, (int) 0);
            curl_setopt($this->curl_instance, CURLOPT_HTTPGET, TRUE);
            curl_setopt($this->curl_instance, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($this->curl_instance);
            return json_decode($result,TRUE);
        } 
        catch (Exception $ex) {
            die($ex);
        }
    }    

}

?>
