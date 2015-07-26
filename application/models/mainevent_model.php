<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');
//version 2
class Mainevent_model extends CI_Model {

    
    function main_event_list_api() {

             $arrdt = array();
		
		     $sql_mainevent = "SELECT `event_id`, `event_name`, `event_image`, `event_description`,`status` FROM `mainevent` WHERE 1 ";
			 
			 $mainevent_res = $this->db->query($sql_mainevent);
        if ($mainevent_res->num_rows() > 0) {

            foreach ($mainevent_res->result() as $row) {
                $arrdt[] = array("event_id" => $row->event_id, "event_name" => $row->event_name, "event_image" => BASE_URL() . $row->event_image, "event_description" => $row->event_description, "status" => $row->status);

                
            }
        } 
		$data = array("status" => "success", "mainevent" => $arrdt);
        return $data;
    }
	
	function sub_event_list_for_api($event_id,$facebook_id) {

        $arrdt = array();
        $sql_mainevent = "SELECT t1.`subevent_id`,t1.`sub_event_name`, t1.`sub_event_image`, t1.`sub_event_description`, t1.`event_name`, t1.`event_id`,t1.`status`,(SELECT t2.`select_status` FROM `user_preference` t2   WHERE 1 and t1.subevent_id=t2.subevent_id and t2.event_id='" . $event_id . "'  and t2.facebook_id='" . $facebook_id . "' )as select_status  FROM `sub_event` t1  WHERE 1 and t1.event_id='" . $event_id . "'";
        $mainevent_res = $this->db->query($sql_mainevent);
        if ($mainevent_res->num_rows() > 0) {

            foreach ($mainevent_res->result() as $row) {
				
				if(is_null($row->select_status)){
					$st ="no";
				}else{
					$st=$row->select_status;
				}
                $arrdt[] = array("subevent_id" => $row->subevent_id, "sub_event_name" => $row->sub_event_name, "sub_event_image" => BASE_URL() . $row->sub_event_image, "sub_event_description" => $row->sub_event_description, "event_name" => $row->event_name, "event_id" => $row->event_id, "select_status" => $st);
                $res = array("status" => "success", "mainevent" => $arrdt);
            }
        } else {
            $res = array("status" => "success", "mainevent" => $arrdt);
        }
        return $res;
    }
	
	function user_select_subevent_submit($facebook_id,$event_id,$sub_event_id,$status) {
        

        $userpreference_data = array(
            'facebook_id' => $facebook_id,
            'event_id' => $event_id,
            'subevent_id' => $sub_event_id,
            'select_status' => $status,
			'finding_people'=>'1',
					
        );

        $sql_userperference = "SELECT facebook_id
		  FROM `user_preference` WHERE 1 and facebook_id='" . $facebook_id . "' and subevent_id='" . $sub_event_id . "' ";
        $userperference_res = $this->db->query($sql_userperference);
        if ($userperference_res->num_rows() > 0) {

            $privilege_row = $userperference_res->row_array();
          
				if ($status == 'no' ) {
                
				$this->db->where('facebook_id', $facebook_id);
                $this->db->where('subevent_id', $sub_event_id);
                $this->db->delete('user_preference');
				
				$this->db->where('facebook_id', $facebook_id);
                $this->db->where('subevent_id', $sub_event_id);
                $this->db->delete('group_user');
								
            } else {
				
                $res = array("status" => "success");
            }
        } 
		else if($status == 'yes') {
			     $this->db->insert('user_preference', $userpreference_data);
				 $preference_id  = mysql_insert_id();
				 
				 $check_facebook_id_exist = "select group_id from tblgroup where  subevent_id='" . $sub_event_id . "'";
        $facebook_id_exist_result = $this->db->query($check_facebook_id_exist);
        $group_row = $facebook_id_exist_result->row_array();

         $group_user =array('preference_id'=>$preference_id,"facebook_id"=>$facebook_id,"group_id"=>$group_row['group_id']);
				 $this->db->insert('group_user', $group_user);
               
		}
		$res = array("status" => "success");
        return $res;
    }
	
	//listing of group conformed date and place Api.
    function list_group($preference_id,$facebook_id) {


        $arrdt = array();
        $venue = array();
        $act_date = array();

        $sql_mainevent = "SELECT tblgroup.`group_id`,event_date,`place_id`, event_date as select_date, `preference_id`, tblgroup.`subevent_id`, sub_event.`sub_event_name`, sub_event.`sub_event_image`,`group_name` 
FROM `tblgroup`
inner join sub_event on sub_event.subevent_id=tblgroup.subevent_id
inner join group_user on group_user.group_id=tblgroup.group_id WHERE 1 and  preference_id='" . $preference_id . "' and facebook_id='" . $facebook_id . "'";
        $mainevent_res = $this->db->query($sql_mainevent);
        if ($mainevent_res->num_rows() > 0) {

            foreach ($mainevent_res->result() as $row) {

                //place conversion


                $sql_place = "SELECT place_id,`place_name`,`place_phone`, `place_address`, `place_des`, `price`, `stars`, `review`,geo_loc,img_url,review_source FROM `place` WHERE 1 and place_id='" . $row->place_id . "'";


                $place_res = $this->db->query($sql_place);
                if ($place_res->num_rows() > 0) {

                    foreach ($place_res->result() as $rows) {
                        $geo_loc = $rows->geo_loc;
                        $geo_loc_arr = explode(',', $geo_loc);

                        //babson lat and lon
                        $lng1 = -71.2669113;
                        $lat1 = 42.2995602;
                       
                        $babson_distance = $this->distance_calculation($lat1, $lng1, $geo_loc_arr[0], $geo_loc_arr[1]);

                        //from zipcode get lat and long and calculate distance between the place


                       // $zip_lat = $this->get_lat_lon_from_zipcode($this->get_zipcode_from_user_table($facebook_id));
                        $zipcode_dist = $this->distance_calculation(12.9200, 77.6200, $geo_loc_arr[0], $geo_loc_arr[1]);



                        $venue[] = array("place_id" => $rows->place_id, "place_name" => $rows->place_name, "place_address" => $rows->place_address, "place_des" => $rows->place_des, "place_phone" => $rows->place_phone, "img_url" => base_url() . $rows->img_url, "price" => $rows->price, "stars" => $rows->stars, "review" => $rows->review, "distance_babson" => $babson_distance, "distance_house" => $zipcode_dist,"review_source" => $rows->review_source,"latitude"=>$geo_loc_arr[0],"longitude"=>$geo_loc_arr[1]);
                    }
                }


               
                $user = array();

                // user list who are invited

                $sql_user = "SELECT group_user.facebook_id,tbluser.image_url,tbluser.name,tbluser.user_info FROM `group_user` inner join tbluser on tbluser.facebook_id=group_user.facebook_id WHERE 1 and group_user.group_id='" . $row->group_id . "' and  group_user.facebook_id!='".$facebook_id."'";

                $user_res = $this->db->query($sql_user);
                if ($user_res->num_rows() > 0) {

                    foreach ($user_res->result() as $rows_user) {

                        $user[] = array("facebook_id" => $rows_user->facebook_id, "name" => $rows_user->name, "image_url" => $rows_user->image_url, "user_info" => $rows_user->user_info);
                    }
                }


                $arrdt[] = array("group_id" => $row->group_id, "place" => $venue, "date" => $row->event_date, "sub_event_name" => $row->sub_event_name, "sub_event_image" => BASE_URL() . $row->sub_event_image, "preference_id" => $row->preference_id, "subevent_id" => $row->subevent_id, "group_name" => $row->group_name, "user" => $user);

                $res = array("status" => "success", "group" => $arrdt);
            }
        } else {
            $res = array("status" => "success", "group" => $arrdt);
        }
        return $res;
    }
	
	 // calculate distance in miles
    function distance_calculation($lat1,$lng1,$lat2,$lng2) {

        $earthRadius = 3958.75;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $dist = $earthRadius * $c;
        return $dist;
    }

    // getting lat and lon  from zipcode 
    function get_lat_lon_from_zipcode($zip) {
        $url = "http://maps.googleapis.com/maps/api/geocode/json?address=
" . urlencode($zip) . "&sensor=false";
        $result_string = file_get_contents($url);
        $result = json_decode($result_string, true);
        $result1[] = $result['results'][0];
        $result2[] = $result1[0]['geometry'];
        $result3[] = $result2[0]['location'];
        return $result3[0];
    }
	function get_zipcode_from_user_table($facebook_id) {

        $check_facebook_id_exist = "select zipcode from tbluser where facebook_id='" . $facebook_id . "'";
        $facebook_id_exist_result = $this->db->query($check_facebook_id_exist);
        $row = $facebook_id_exist_result->row_array();

        return $row['zipcode'];
    }
	function final_conform_save($preference_id,$facebook_id, $status)
	{
		$is_going_array = array("is_going" =>$status);
		
		$this->db->where('preference_id', $preference_id);
        $this->db->where('facebook_id', $facebook_id);
        $this->db->update('user_preference', $is_going_array);
		
		$this->db->where('preference_id', $preference_id);
        $this->db->where('facebook_id', $facebook_id);
        $this->db->update('group_user', $is_going_array);
		
		return true;
		
	}

}
