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

    public function index($lat, $long, $radius = 300) {
        $this->load->library("GooglePlaces");

        $params = array(
            "location" => $lat . "," . $long,
            "sensor" => "false",
            "key" => $this->GOOGLE_API_KEY,
            "radius" => $radius
        );

        $data["result"] = $this->googleplaces->nearby_places($params);
        $this->load->view("foursq_locations", $data);
    }

    private function add_location($locationid, $location_name, $locaiton_address, $lat, $lng) {

        try {
            if (!empty($locationid) || !empty($location_name) || !empty($locaiton_address)) {
                $this->load->database();
                $sql = "SELECT count(*) rowexist,locationid,API_ID from locations where API_ID='{$locationid}'";
                $result = $this->db->query($sql);
                $row = $result->row();
                if ($row->rowexist > 0)
                    return $row->locationid;
                else {
                    $param = array("API_ID" => $locationid, "locationName" => $location_name,
                        "locationAddress" => $locaiton_address,
                        "LocationLat" => $lat, "LocationLong" => $lng);
                    $row = $this->db->insert("locations", $param);
                    if ($this->db->affected_rows() > 0)
                        return $locationid;
                }
            }
        } catch (Exception $ex) {
            die($ex);
        }
        return false;
    }

    /*
     * Method to accept checkin data.
     */

    public function checkin() {
        $this->load->database();
        $error = "";
        $resp = array();
        $userid = $this->input->post("user_id");
        $locationid = $this->input->post("location_id");
        $locationName = $this->input->post("location_name");
        $locationAddress = $this->input->post("location_address");
        $locationLat = $this->input->post("location_lat");
        $locationLng = $this->input->post("location_lng");

        try {
            if ($userid || $locationid || $locationName) {
                $get_user_sql = $this->db->get_where("users", array("user_id" => $userid));
                $rows = $get_user_sql->row();
                if ($rows) {
                    $userid = $rows->user_id;
                    $location_id = $this->add_location($locationid, $locationName, $locationAddress, $locationLat, $locationLng);
                    //handle updating number of checkins. Get current count and update by 1
                    $checkin_count = $this->db->get_where("locations", array("API_ID" => $location_id));
                    $resp["status"] = "true";
                    $resp["checkin"]["userid"] = $userid;
                    if ($checkin_count->num_rows() > 0) {
                        $checkin_count = $checkin_count->row();
                        $nbr_checkins = $checkin_count->LocationCheckins + 1;
                        $this->db->update("locations", array("locationcheckins" => $nbr_checkins), array("locationid" => $location_id, "API_ID" => $checkin_count->API_ID, "LocationRatings" => $checkin_count->LocationRatings, "LocationUseRatings" => $checkin_count->LocationUseRatings));
                        //set response status if successful
                        $resp["status"] = "true";
                        $resp["checkin"]["location"] = $checkin_count;
                    }else{
                        $resp["checkin"]["location"] = "";
                    }
                } else {
                    //handle situation where a user doesn't not exist
                    $error = "User is not registered or does not exist";
                    $resp["status"] = "NOT_USER";
                    $resp["error"] = $error;
                }
            } else {
                $error = "One or more required parameters missing";
                $resp["status"] = "MISSING_PARAMETER";
            }
        } catch (Exception $ex) {
            $error = $ex->getMessage();
            $resp["status"] = "EXCEPTION_ERROR";
            $resp["error"] = $error;
        }

        $data["checkin"] = json_encode($resp);
        $this->load->view("checkin", $data);
    }

    public function fetch_location($locationid) {
        $this->load->database();
        $resp = array();
        $this->db->select("*");
        $this->db->from("locations");
        //$this->db->join("locationcheckin","locations.locationid=locationcheckin.locationid","left");
        $this->db->where("API_ID", $locationid);
        $result = $this->db->get();

        $this->db->select("locationcheckin.*");
        $this->db->from("locationcheckin");
        $this->db->join("locations", "locationcheckin.locationid=locations.locationid", "right");
        $this->db->where("API_ID", $locationid);
        $checkin = $this->db->get();

        $this->db->select("locationreview.*");
        $this->db->from("locationreview");
        $this->db->join("locations", "locations.locationid=locationreview.locationid");
        $this->db->where("locations.API_ID", $locationid);
        $reviews = $this->db->get();

        $resp["status"] = "true";
        $resp["location"] = $result->row();
        $resp["checkin"] = $checkin->result_array();
        $resp["reviews"] = $reviews->result_array();
        $data["location"] = $resp;
        $this->load->view("location", $data);
    }

    public function add_review() {
        $userid = $this->input->post("userid");
        $locationid = $this->input->post("locationid");
        $reviewText = $this->input->post("reviewText");
        $floatRating = $this->input->post("fltRating");

        $response = array();

        if ($userid || $locationid && ($reviewText || $floatRating )) {

            $insertavg = $this->insert_review($locationid, $userid, $reviewText, $floatRating);

            if ($insertavg != null || $insertavg > -1) {
                $response["success"] = "true";
                $response["result"] = (int) $insertavg;
            }
        } else {
            $response["success"] = "false";
        }

        $data["review"] = json_encode($response);
        $this->load->view("add_review", $data);
    }

    private function insert_review($locationid, $userid, $review, $rating) {
        $this->load->database();
        $resp = array();
        $param = array("LocationId" => $locationid, "UserId" => $userid, "DateTime" => time(), "ReviewText" => $review, "ReviewRating" => $rating);
        $this->db->insert("locationreview", $param);
        if ($this->db->affected_rows() > 0) {
            $this->db->select("AVG(ReviewRating) as Average");
            $this->db->from("locationreview");
            $this->db->where(array("LocationId" => $locationid, "ReviewRating >" => 0.0));
            $result = $this->db->get()->row();

            if ($result->Average != null) {
                $param = array("LocationRatings" => $result->Average, "LocationUseRatings" => 1);
                $this->db->where("locationid", $locationid);
                $this->db->update("locations", $param);
                //handle response
                return $result->Average;
            }

            return -1;
        }
    }

}

?>
