<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Loginapi_model extends CI_Model {

    // sign up
    function loginmodel_check($facebook_id, $email_id, $gender, $device_id, $image_url, $zipcode, $device_type, $name) {

        $check_facebook_id_exist = "select count(facebook_id) as num,facebook_id  from tbluser where facebook_id='" . $facebook_id . "'";
        $facebook_id_exist_result = $this->db->query($check_facebook_id_exist);
        $row = $facebook_id_exist_result->row_array();
        if ($row['num'] == 1) {

            
			
			//check device id 
            $device_sql = "select facebook_id,device_id,fldid  from tbldevice where 1 and device_id='" . $device_id . "'";
            $device_sql_result = $this->db->query($device_sql);
			$device_array = array(
                    'facebook_id' => $facebook_id,
                    'device_id' => $device_id,
                    'device_type' => $device_type,
                    'logout_status' => 'N'
                );
            if ($device_sql_result->num_rows() > 0) {
               foreach($device_sql_result->result_array() as $device_type){ 
                $this->db->where('fldid', $device_type['fldid']);
                $this->db->update('tbldevice', $device_array);
			   }
            }else{
				$this->db->insert('tbldevice', $device_array);
			}
			
			$response = array("status" => "success", "facebook_id" => $facebook_id , "is_exists" => "yes","image"=>$image_url,"name"=>$name);
            
			// end of device id checking
			
			
        } else {

            $datatable2 = array(
                'facebook_id' => $facebook_id,
                'email_id' => $email_id,
                'gender' => $gender,
                'image_url' => $image_url,
                'zipcode' => $zipcode,
                'name' => $name
            );


            $this->db->insert('tbluser', $datatable2);
            $user_id = mysql_insert_id();

           

            //adding device detail in devicetable
            $device_array = array(
                'facebook_id' => $facebook_id,
                'device_id' => $device_id,
                'device_type' => $device_type,
                'logout_status' => 'N'
            );
			//check device id and user id exists
			$sql_check_device_type="SELECT `fldid` FROM `tbldevice` WHERE 1 AND `device_id`='".$device_id."'";
			
			$device_type = $this->db->query($sql_check_device_type);
			
			if ($device_type->num_rows() > 0) {
            	foreach($device_type->result_array() as $device_type){
				
				$this->db->where('fldid', $device_type['fldid']);
				$this->db->update('tbldevice', $device_array);
				
				}
			}else{
			
            	
				$this->db->insert('tbldevice', $device_array);
			
			
			}
            
            
			$response = array("status" => "success", "facebook_id" => $facebook_id,"is_exists" => "no","image"=>$image_url,"name"=>$name);
        }
        return $response;
		
    }
	
	function logout_api_saving($facebook_id, $device_id, $device_type) {

        $device_sql = "select facebook_id,device_id,device_type  from tbldevice where facebook_id='" . $facebook_id . "' and device_id='" . $device_id . "' and device_type='" . $device_type . "'";
        $device_sql_result = $this->db->query($device_sql);
        if ($device_sql_result->num_rows() > 0) {

            $this->db->where('facebook_id', $facebook_id);
            //$this->db->where('device_id', $device_id);
            //$this->db->where('device_type', $device_type);
            $this->db->delete('tbldevice');

            $res = array("status" => "success");
        } else {
            $res = array("status" => "failed");
        }
        return $res;
    }

    //common header for api 
    function header() {

        return header('Content-Type: application/json');
    }

}
