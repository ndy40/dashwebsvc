<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserAccount
 *
 * @author ndy40
 */
class UserAccount extends CI_Controller {
    
    private $error = array();
    private $resp = array(); // response array that will contain all the response data including status of request
    
    public function register_user(){
        try{
         
        $firstname = $this->input->post("firstname");
        $surname = $this->input->post("surname");
        $email = $this->input->post("email");
        $username = $this->input->post("username");
        $password = $this->input->post("password");
        $error = NULL;
        $resp = array();
        if($firstname || $surname || $email || $username){
            //load the database
            $this->load->database();
            $sql = "SELECT count(*) as usercount FROM users WHERE email like '{$email}'";
            $user_exist_rs = $this->db->query($sql);
            $row = $user_exist_rs->row();
            if($row->usercount > 0){
                $error = "User with the email already exist.";
                $resp["status"] = "USER_EXIST";
            }else{
                $sql = "INSERT INTO users( username,email,password,firstname,surname) Value(".
                        $this->db->escape($username).","
                        .$this->db->escape($email).",". $this->db->escape($password)."," .
                        $this->db->escape($firstname).","
                        .$this->db->escape($surname) .")";
                
                $result = $this->db->query($sql);
                if($this->db->affected_rows()){
                    $resp["status"] = "true";
                    $resp["userdata"] = array("userid"=>$this->db->insert_id(),"username"=> $username,"email"=>$email);
                }
                
            }
            
        }else{
            $error = "One or more required data is missing.Firstname, lastname, surname and email must be provided";
            $resp["status"] = "MISSING_PARAMETER";
        }
        
        }catch(Exception $ex){
            $error = $ex->getMessage();
            $resp["status"] = "EXCEPTION_ERROR";
        }
        if(!empty($error) && $resp["status"] != "true"){
            $resp["error"]= $error;
        }
        
        $data["data"] = json_encode($resp);
        $this->load->view("register_user",$data);
        
    }
    
    public function update_user(){
        
    }
    
    public function follow_user(){
        $this->load->database();
        $userid = $this->input->post("userid");
        $followId= $this->input->post("followid");
        
        $resp = array();
        if($userid && $followId && ($userid != $followId) ){
            $param = array("userid"=> (int)$userid,"following"=> (int)$followId);
            $this->db->insert("Friendlist",$param);
            if($this->db->affected_rows() > 0){
                $resp["status"] = "true";                
            }else{
                $resp["status"] = "false";                
                $resp["error"] = "An error occured.";
            }
            
        }else{
            $resp["status"] = "false";
            $resp["error"] = "Missing or inappropriate parameter passed";
            
        }
        
        $data["follow"] = json_encode($resp);
        $this->load->view("follow_user",$data);
        
        
    }
    
    public function fetch_user($userid,$get_followers = false,$get_following = false){
        $this->load->database();
        $resp = array();
        $this->db->select("*");
        $this->db->from("users");
        $this->db->where("user_id",$userid);
        $user = $this->db->get()->row();        
        $resp["user"] = array("userid"=>$user->user_id, "username"=>$user->UserName,"firstname"=>$user->Firstname,"lastname"=>$user->Lastname);
        
        if($user != null){
            if($get_followers){
                $this->db->select("users.*");
                $this->db->from("users");
                $this->db->join("Friendlist","Friendlist.following = users.user_id");
                $this->db->where("Friendlist.userid",$user->user_id);
                
            }
        }
        
        
    }
}

?>
