<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LocationSearch
 *
 * @author ndy40
 */
class LocationSearch extends CI_Controller {
    //put your code here
    
    public  function index($lat,$long,$limit=10){
        $url = "https://api.foursquare.com/v2/venues/explore";
        $this->load->library("FourSquare");
        
        $this->foursquare->url = $url;
        $params = array(
                "ll"=>$lat.",".$long,
                "limit" => $limit
                //"access_token" => "UGNFSU3SJYW5SNBQ2LLJIP3PUX3LS0IFNXN1F1II5VKC2NWI"
                );
        $this->foursquare->limit = $limit;
        $data["result"] = $this->foursquare->venue_explore($params); 
        
        $this->load->view("foursq_locations",$data);
        
        
    }
}

?>
