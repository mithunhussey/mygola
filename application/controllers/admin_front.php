<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_front extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('loginapi_model');
        $this->loginapi_model->header();
    }

    //sign up new facebook user api
    public function sign_up() {

        if (isset($_POST["facebook_id"]) && isset($_POST["gender"]) && isset($_POST["email_id"])  && isset($_POST["device_id"]) && isset($_POST["image_url"]) && isset($_POST["zipcode"]) && isset($_POST["os_type"]) && isset($_POST["name"])) {

            $facebook_id = $_POST['facebook_id'];
            $gender = $_POST['gender'];
            $email_id = $_POST['email_id'];
            $device_id = $_POST['device_id'];
            $image_url = $_POST['image_url'];
            $zipcode = $_POST['zipcode'];
            $device_type = $_POST['os_type'];
            $name = $_POST['name'];
			


            if ($facebook_id != '' && $email_id != '' && $gender != ''  && $device_id != '' && $image_url != '' && $zipcode != '' && $device_type != '' && $name != '') {


                $response = $this->loginapi_model->loginmodel_check($facebook_id, $email_id, $gender, $device_id, $image_url, $zipcode, $device_type, $name);
            } else {
                $response = array("status" => "failed" ,"desc"=>"2");
            }
        } else {
            $response = array("status" => "failed","desc"=>"1");
        }
        print_r(json_encode($response));
		exit;
    }

   

    

    //logout api 
    function logout_api() {

        if (isset($_POST["facebook_id"]) && isset($_POST["device_id"]) && isset($_POST["os_type"])) {

            $this->load->model('api/v2/loginapi_model');
            $facebook_id = $_POST['facebook_id'];
            $device_id = $_POST['device_id'];
            $device_type = $_POST['os_type'];

            if ($facebook_id != '' && $device_id != '' && $device_type != '') {

                $response = $this->loginapi_model->logout_api_saving($facebook_id, $device_id, $device_type);
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
