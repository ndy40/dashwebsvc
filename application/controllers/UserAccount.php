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
}

?>
