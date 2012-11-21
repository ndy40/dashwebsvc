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
    private $GOOGLE_API_KEY = "AIzaSyAMJsi6uzlG_l26K50mweEajBxUDgD9Pr0";
    
    public  function index($lat,$long,$radius = 300){
        $this->load->library("GooglePlaces");
               
        $params = array(
                "location"=>$lat.",".$long,
                "sensor" => "false",
                "key" => $this->GOOGLE_API_KEY,
                "radius" => $radius                
                );
        
        $data["result"] = $this->googleplaces->nearby_places($params); 
        $this->load->view("foursq_locations",$data);
        
        
    }
    
    
}

?>
