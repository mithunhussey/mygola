<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mainevent_api extends CI_Controller {

    function __construct() {
        parent::__construct();
		$this->load->model('loginapi_model');
        $this->loginapi_model->header();
    }

    // Main event List Api
    function mainevent_list() {

        $this->load->model("mainevent_model");
        $response = $this->mainevent_model->main_event_list_api();
            
        
        print_r(json_encode($response));
		exit;
    }
	function subevent_list() {
        if (isset($_POST["event_id"]) && isset($_POST["facebook_id"])) {
            

            
            $event_id = $_POST['event_id'];
            $facebook_id = $_POST['facebook_id'];
			
            if ($event_id != '') {

                $this->load->model("mainevent_model");
                $response = $this->mainevent_model->sub_event_list_for_api($event_id,$facebook_id);
            } else {
                $response = array("status" => "failed");
            }
        } else {
            $response = array("status" => "failed");
        }
        print_r(json_encode($response));
		exit;
    }
	
	function subevent_submit() {

        if (isset($_POST["facebook_id"]) && isset($_POST["sub_event_id"]) && isset($_POST["event_id"]) && isset($_POST["select_status"])) {

            $facebook_id = $_POST['facebook_id'];
            $event_id = $_POST['event_id'];
            $sub_event_id = $_POST['sub_event_id'];
            $status = $_POST['select_status'];
            if ($facebook_id != '' && $event_id != '' && $status != '' && $sub_event_id != '') {

                $this->load->model("mainevent_model");
                $response = $this->mainevent_model->user_select_subevent_submit($facebook_id,$event_id,$sub_event_id,$status);
            } else {
                $response = array("status" => "failed");
            }
        } else {
            $response = array("status" => "failed");
        }
        print_r(json_encode($response));
		exit;
    }
	
	 function list_group() {

       
        if (isset($_POST["facebook_id"]) && isset($_POST["preference_id"])) {
            $preference_id = $_POST['preference_id'];
            $facebook_id = $_POST['facebook_id'];
            

            if ($preference_id != '' && $facebook_id != '' ) {
                $this->load->model('mainevent_model');
                $dt = $this->mainevent_model->list_group($preference_id, $facebook_id);
            } else {
                $dt = array("status" => "failed");
            }
        } else {
            $dt = array("status" => "failed");
        }


        print_r(json_encode($dt));
		exit;
    }
	function final_conform() {
        
        if (isset($_POST["facebook_id"]) && isset($_POST["preference_id"]) && isset($_POST["status"])) {
            $preference_id = $_POST['preference_id'];
            $facebook_id = $_POST['facebook_id'];
            $status = $_POST['status'];
          

            if ($preference_id != '' && $facebook_id != '' && $status != '') {
                $this->load->model('mainevent_model');
                $dt = $this->mainevent_model->final_conform_save($preference_id,$facebook_id,$status);
                $dt = array("status" => "success");
            } else {
                $dt = array("status" => "failed");
            }
        } else {
            $dt = array("status" => "failed");
        }
		print_r(json_encode($dt));
		exit;
	 }
	 
	 function selected_subevent() {


        if (isset($_POST["facebook_id"])) {


            $facebook_id = $_POST['facebook_id'];
            
            if ($facebook_id != '' ) {

                $this->load->model("Subevent");
                $response = $this->Subevent->initial_selected_subevent($facebook_id);
            } else {
                $response = array("status" => "failed");
            }
        } else {
            $response = array("status" => "failed");
        }
        print_r(json_encode($response));
		exit;
    }

}
