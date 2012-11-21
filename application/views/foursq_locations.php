<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
defined('BASEPATH') OR exit('No direct script access allowed');
$this->output->set_header('Content-Type: application/json; charset=utf-8');

$venueResponse = array();
foreach($result->results as $key){
           
         $venue = array(
            "id" => $key->id,
            "name" => $key->name,
            "address"=> (property_exists($key,"formatted_address")?$key->formatted_address:$key->vicinity),
            "lat"=> $key->geometry->location->lat,
            "lng"=>$key->geometry->location->lng,  
             "type"=> $key->types
        );
        $venueResponse[] = $venue;
}

echo json_encode(array('venues'=>$venueResponse));

?>
