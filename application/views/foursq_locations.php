<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$venueResponse = array();
foreach($result->response->groups as $key){
    foreach($key->items as $location){
        
         $venue = array(
            "id" => $location->venue->id,
            "name" => $location->venue->name,
            "address"=>$location->venue->location->address,
            "lat"=> $location->venue->location->lat,
            "lng"=>$location->venue->location->lng,
            "distance"=> $location->venue->location->distance,
            "nbrCheckins"=>$location->venue->stats->checkinsCount           
        );
        $venueResponse[] = $venue;
    }
}

echo json_encode(array('venues'=>$venueResponse));

?>
