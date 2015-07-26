<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');
//version 2
class Subevent extends CI_Model {

        //initial selected sub event list api
    function initial_selected_subevent($facebook_id) {
        $arrdt1 = array();
		$arrdt_del=array();
		$arrdt = array();
		
        
		
        $sql_subevent = "SELECT t1.preference_id,t1.facebook_id,t2.`event_name`,t2.`sub_event_name`,t1.`select_status`, t1.`is_going`,t1.subevent_id ,t2.sub_event_image,t2.sub_event_description,t2.status FROM `user_preference` t1 inner join sub_event t2 on t1.subevent_id  =t2.subevent_id WHERE 1 and t1.facebook_id='" . $facebook_id . "' ";
        $subevent_res = $this->db->query($sql_subevent);
        if ($subevent_res->num_rows() > 0) {
            foreach ($subevent_res->result() as $row) {
				
				
               
				
               
				
				
                $arrdt[] = array("preference_id" => $row->preference_id, "facebook_id" => $row->facebook_id, "event_name" => $row->event_name, "sub_event_name" => $row->sub_event_name, "sub_event_image" => BASE_URL() . $row->sub_event_image, "sub_event_description" => $row->sub_event_description, "is_going" => $row->is_going, "select_status" => $row->select_status, "subevent_id" => $row->subevent_id,'status'=>$row->status);
				
            }
        } 
      
            
            $res = array("status" => "success", "subevent" => $arrdt);

        return $res;
    }

    
    
}
