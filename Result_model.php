<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Result_model extends CI_Model
{
    
    function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }
    public function check_already_declare($numbers,$game_id,$game_type,$sdate)
    { 
        date_default_timezone_set('Asia/Kolkata');
        $day = date('l', strtotime($sdate));
        $wh =" game_id ='".$game_id."' AND reg_date ='".$sdate."' AND game_day='".$day."'";
         return $already_exit = $this->db->where($wh)->get("declare_result")->num_rows();
    }
    
    public function insert_open_declare_result($numbers,$game_id,$game_type,$sdate)
    {
        $day = date('l', strtotime($sdate));
        $wh =" game_id ='".$game_id."' AND reg_date ='".$sdate."' AND game_day='".$day."'";
        $already_exit = $this->db->where($wh)->get("declare_result")->num_rows();
        if($already_exit > 0)
        {
            echo 0;
        }else{
                $len = strlen($numbers);
                $sum = 0; 
                for($i=0;$i < $len;$i++)
                {
        		    $no = $numbers[$i];
        		    $sum = $sum + $numbers[$i];
        		}
        		    $zz = (string)$sum;$len = strlen($zz);
                    $final = $len == 1 ?  $final = $zz[0]: $final = $zz[1];
                    $day = date('l', strtotime($sdate));
                    
                    $where = 'game_id ="'.$game_id.'" AND day_name="'.$day.'" ';
    			    $count = $this->db->where($where)->get('game_content')->num_rows();
    			    if($count > 0){
    			    $gcid = $this->db->where($where)->get('game_content')->row()->id;    
    			    }else{
    			        $gcid = "";
    			    }
                    $insert = array("game_id"=>$game_id,"game_content_id"=>$gcid,"game_type"=>$game_type,"game_day"=>$day,"open_digit"=>$final,
                              "open_panna"=>$numbers,"number"=>$numbers,"final"=>$numbers."x".$final,"status"=>1,"day"=>date('l'),"month"=>date('F'),"year"=>date('Y'),
                              "create_date"=>date('Y-m-d H:i:sa'),"create_time"=>date('Y-m-d h:i:s'),"reg_date"=>$sdate);
                    $result = $this->db->insert('declare_result', $insert); 
                    $last_id = $this->db->insert_id();
                    // NOTIFICATION CODE
                    $game_type = "Open";
                    $last_data = $this->db->where("id",$last_id)->get("declare_result")->row();
                    $result = $game_type =="Open" ?  $last_data->open_panna."-".$last_data->open_digit : $last_data->close_digit."-".$last_data->close_panna;
                    $body = $result;
                    $game_wh = "id ='".$last_data->game_id."'";
                    $title = $this->db->where($game_wh)->get("game")->row()->name;
                    $users =  $this->db->where("status",1)->get("user")->result();
                    foreach($users as $user)
                    {
                        $user_ids[] = $user->user_id;
                    }
                    $user_id = implode(",",$user_ids);
                   $this->load->model('Agent_model');    // NOTIFICATION CALL HERE
                   $noti = $this->Agent_model->notification($user_id,$body,$title);  // NOTIFICATION OVER HERE
                   $noti = "";
                   $this->open_result_declare($game_id,$sdate);
            if($result)
            {   
                echo 2;
            
            }else{
                echo "fail";
            }
        }
    }
    
    public function open_result_notification($last_open_result_id)
    {
        $count = $this->db->get("result")->num_rows();
        if($count > 0)
        {   
            $game_type = "Open";
            $last_data = $this->db->where("id",$last_open_result_id)->get("declare_result")->row();
            $result = $game_type =="Open" ?  $last_data->open_panna."-".$last_data->open_digit : $last_data->close_digit."-".$last_data->close_panna;
            $body = $result;
            $game_wh = "id ='".$last_data->game_id."'";
            //$title = $this->Common_model->Name("game",$game_wh,"name",""); // tablename,where,fieldname,return value blank or - etc
            $title = $this->db->where($game_wh)->get("game")->row()->name;
            $users =  $this->db->where("status",1)->get("user")->result();
            foreach($users as $user)
            {
                $user_ids[] = $user->user_id;
            }
           $user_id = implode(",",$user_ids);
           $this->load->model('Agent_model');
           $this->Agent_model->notification($user_id,$body,$title);
           
           return true;
        }else{
            return 0;
        }
        
       
    }
    
    public function open_result_declare($game_id,$sdate)
    {
        $this->load->model("Agent_model");
        
        date_default_timezone_set("Asia/Kolkata");
        $day = date('l', strtotime($sdate));
        $today = $sdate;
        $array_winner = array();  // For Half Sangam
        $full_sangam_array = array(); // For Full Sangam
        $red_jodi_win_array = array(); // For Red Jodi
        $jodi_win_array = array(); // For Jodi Win Array
        $close_single_digit_win = array(); // for Close Single
        $open_digit_win = array();  // for Open Digit 
        $patti_win = array(); // Single,Double Triple Patti
        $close_patti_win = array(); // Close Single ,Double,Triple Patti  
        $result_where ='game_id ="'.$game_id.'" AND game_day ="'.$day.'" AND reg_date ="'.$today.'"';
        $result_cnt = $this->db->where($result_where)->get('declare_result')->num_rows(); 
        if($result_cnt > 0)
        {
            $result = $this->db->where($result_where)->get('declare_result')->row();
            $game_schedule = $this->db->where('id',$result->game_content_id)->get('game_content')->row();
            $played_where = 'game_content_id ="'.$result->game_content_id.'" AND game_day ="'.$result->game_day.'" AND reg_date="'.$today.'" AND status="1"';
            $played_cnt = $this->db->where($played_where)->get('game_played')->num_rows();
            
            if($played_cnt > 0)
            {
                $prize_cnt = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->num_rows();
                $prize_data = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->row(); 
                $prize_amount = $prize_data->amount;  // amount which is multiple by with total amount into Points 
                    
                $played_data = $this->db->where($played_where)->get('game_played')->result();
                foreach($played_data as $played)
                {
                   $array_winner = array();  // For Half Sangam
                   $full_sangam_array = array(); // For Full Sangam
                   $red_jodi_win_array = array(); // For Red Jodi
                   $jodi_win_array = array(); // For Jodi Win Array
                   $close_single_digit_win = array(); // for Close Single
                   $open_digit_win = array();  // for Open Digit 
                   $patti_win = array(); // Single,Double Triple Patti
                   $close_patti_win = array(); // Close Single ,Double,Triple Patti
                   $cname = $this->db->where('category_id',$played->category_id)->get("category")->row()->cname;
                   if($cname =="Single Digit")
                   {
                       $played_type = $played->game_type; 
                       if($result->game_type =="Open" && $played_type =="Open")
                       {
                            $open_digit_number = explode(",",$played->numbers);   
                            $open_digit_amts = explode(",",$played->amount);   
                            $open_digit_count = count($open_digit_number);
                            for($open_digit =0; $open_digit < $open_digit_count; $open_digit++)
                            {
                            	$single_digit = $result->open_digit;
                            	if($single_digit == $open_digit_number[$open_digit])
                                { 
                                    $wincode = $this->db->order_by('id',"desc")->limit(1)->get('winner_list')->row()->wincode;
                                    $wincode_no = empty($wincode) ? '1000000' : $wincode + 1;
                                    $prize_data = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->row(); 
                                    $prize_amount = $prize_data->amount; $prize_point = $prize_data->points;
                                    $final_points = ($open_digit_amts[$open_digit] * $prize_point) / $prize_amount;
                                    $result_number = $single_digit;
                                 // insert into winner list   
                                   
                                   $array = array('result_id'=>$result->id,"wincode"=>$wincode_no,"result_number"=>$result_number,"prize_id"=>$prize_data->id,"game_type"=>$played->game_type,
                                   "game_day"=>$result->game_day,"number"=>$open_digit_number[$open_digit],"amount"=>$open_digit_amts[$open_digit],"points"=>$final_points,"status"=>1,"reg_date"=>$sdate);
                                  $ins = $this->insert_winner_list($played->id,$array);
                                
                                   if($ins)
                                   {
    	                               $open_digit_win[] = "win";
    	                                /* USER WALLLET UPDATE*/
                                        $users = $this->db->where('user_id',$played->user_id)->get('user')->row();
                                        $walletArr = array("result_id"=>$result->id,"game_type"=>"Open","game_id"=>$result->game_id,"amount"=>$final_points,"user_id"=> $played->user_id,
                                        "mobile"=>$users->mobile,"type"=>"WinGame","status"=>1,"create_date"=>date('Y-m-d h:i:sa'),"reg_date"=>$sdate,"category_id"=>$played->category_id,
                                        "game_played_id"=>$played->id,"ticket_number"=>$played->ticket_number,"win_number"=>$open_digit_number[$open_digit]);
                                        $insert_new = $this->db->insert("wallet_transaction",$walletArr);
                                        
                                        $this->Agent_model->update_wallet($played->user_id,$final_points);
                                   }
                                }else{
                                        $open_digit_win[] = "lose";
                                    }
                            } // For Loop Here 
                         if(in_array("win",$open_digit_win))
                         {
                            $this->db->where("id",$played->id)->update("game_played",array("status" => 3));
                         }else{
                             $this->db->where("id",$played->id)->update("game_played",array("status" => 2));
                         }
                    } // Both Game Type Same
                }//Single If Over Here...
                
                if($cname =="Single Pana" || $cname =="Double Pana" || $cname =="Triple Pana")
                {       
                     $played_type = $played->game_type;
                     if($result->game_type =="Open" && $played_type =="Open")
                     {    
                        $all_patti_numbers = explode(",",$played->numbers);   $all_patti_amts = explode(",",$played->amount);   
                        $all_patti_count = count($all_patti_numbers);
                        for($patti =0; $patti < $all_patti_count; $patti++){
                            $all_patti = $result->open_panna;
                            if($all_patti == $all_patti_numbers[$patti])
                            {       
                                 $wincode = $this->db->order_by('id',"desc")->limit(1)->get('winner_list')->row()->wincode;
                                 $wincode_no = empty($wincode) ? '1000000' : $wincode + 1;
                                 $prize_data = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->row(); 
                                 $prize_amount = $prize_data->amount; $prize_point = $prize_data->points;
                                 $final_points = ($all_patti_amts[$patti] * $prize_point) / $prize_amount;
                                 $result_number = $all_patti;
                                
                                $array = array('result_id'=>$result->id,"wincode"=>$wincode_no,"result_number"=>$result_number,"prize_id"=>$prize_data->id,"game_type"=>$played->game_type,
                                "game_day"=>$result->game_day,"number"=>$all_patti_numbers[$patti],"amount"=>$all_patti_amts[$patti],"points"=>$final_points,"status"=>1,"reg_date"=>$sdate);
                                //$ins = $this->db->insert("winner_list",$insert);
                                $ins = $this->insert_winner_list($played->id,$array);
	                            if($ins)
	                            {
	                                $json['status'] =1; $json['msg'] ="Winner Added"; $patti_win[] = "win";
                                          
                                    /* USER WALLLET UPDATE*/
                                    $users = $this->db->where('user_id',$played->user_id)->get('user')->row();
                                    $walletArr = array("result_id"=>$result->id,"game_type"=>"Open","game_id"=>$result->game_id,"amount"=>$final_points,"user_id"=> $played->user_id,
                                    "mobile"=>$users->mobile,"type"=>"WinGame","status"=>1,"create_date"=>date('Y-m-d h:i:sa'),"reg_date"=>$sdate,"category_id"=>$played->category_id,
                                    "game_played_id"=>$played->id,"ticket_number"=>$played->ticket_number,"win_number"=>$all_patti_numbers[$patti]);
                                    
                                    $insert_new = $this->db->insert("wallet_transaction",$walletArr);
                                    $this->Agent_model->update_wallet($played->user_id,$final_points);
                                    /*USER CODE OVER*/
	                            }else{
	                                    $json['status'] =0;
                                        $json['msg'] ="Something Went Wrong ";
                                }
                        }else{
                                $patti_win[] = "lose";
                            }
                        }// For Loop Over Here
                        if(in_array("win",$patti_win)){
                             $this->db->where("id",$played->id)->update("game_played",array("status" => 3));
                        }else{
                             $this->db->where("id",$played->id)->update("game_played",array("status" => 2));
                        }
                    }  
                }
             } //FOR LOOP OVER
                    
                    // Winning Prize Not Added Or Not Found.. Points Not Found ..
            }else{
                    $json['status'] = 0;
                    $json['msg'] = "No Player Join Game";
            }
        }else{
                $json['status'] = 0;
                $json['msg'] = "No Result Declared Today";
        }
    
        return true;
    }

    
    // CLOSE RESULT MODULES
   public function insert_close_declare_result($numbers,$game_id,$game_type,$sdate)
   {
        $where = "game_id ='".$game_id."' AND reg_date='".$sdate."'";
        $row_cnt = $this->db->where($where)->get("declare_result")->num_rows();
        if($row_cnt > 0)
        {
            $len = strlen($numbers);
			$sum = 0;
			for($i=0;$i < $len;$i++){
			      $no = $numbers[$i];
			      $sum = $sum + $numbers[$i];
			   }
        	$game_type =$game_type; $zz = (string)$sum;
        	$len = strlen($zz);
        	$final = $len == 1 ?  $final = $zz[0]: $final = $zz[1];
            $where = "game_id ='".$game_id."' AND reg_date='".$sdate."'";
            $last_result = $this->db->where($where)->get("declare_result")->row();
            $result_val = $last_result->open_panna."-".$last_result->open_digit.$final."-".$numbers;
            $updateArr = array('game_type'=>$game_type,"close_digit"=>$final,"close_panna"=>$numbers,"update_status"=>1,"final"=>$result_val);
            $game_id = $last_result->game_id;
            $para2 = $last_result->id;
            $this->NewResultDeleteWallet($para2,"Open");
            $this->NewResultDeleteWallet($para2,"Close");
            $result_update = $this->db->where('id',$para2)->update('declare_result', $updateArr);
            if($result_update)
            {
                $open_result = $this->NewRedeclaredOpenResult($game_id,$sdate);
		        $close_result = $this->NewCloseResult($game_id,$sdate);
		        $game_type = $game_type;
		        $last_data = $this->db->where("id",$para2)->get("declare_result")->row();
                    $result = $game_type =="Open" ?  $last_data->open_panna."-".$last_data->open_digit : $last_data->open_panna."-".$last_data->open_digit."".$last_data->close_digit."-".$last_data->close_panna;
                    $body = $result;
                    $game_wh = "id ='".$last_data->game_id."'";
                    $title = $this->db->where($game_wh)->get("game")->row()->name;
                    $users =  $this->db->where("status",1)->get("user")->result();
                    foreach($users as $user)
                    {
                        $user_ids[] = $user->user_id;
                    }
                    $user_id = implode(",",$user_ids);
                   $this->load->model('Agent_model');    // NOTIFICATION CALL HERE
                   $noti = $this->Agent_model->notification($user_id,$body,$title);  // NOTIFICATION OVER HERE
                   $noti = "";
                   echo  2;
            }else{
                        echo "fail";
            }
            
        }
    }
    
   public function NewResultDeleteWallet($result_id,$game_type)  // close
   {    
       $run =0;
       date_default_timezone_set('Asia/Kolkata');
       $fetch = $this->db->where("id",$result_id)->get("declare_result")->row();
       $game_id = $fetch->game_id;
      // $game_day = date('l', strtotime($sdate));
       $game_content_id = $this->db->where("game_id",$game_id)->where("day_name",$fetch->game_day)->get("game_content")->row()->id;
       $today =$fetch->reg_date;
       $winner_where = " result_id='".$result_id."' AND game_type ='".$game_type."' AND game_content_id = '".$game_content_id."' AND reg_date = '".$today."'";
       $check = $this->db->where($winner_where)->get("winner_list")->num_rows();
       if($check  > 0)
       {    
           $windata = $this->db->where($winner_where)->get("winner_list")->result();
           foreach($windata as $val)
           {
                $users = $this->db->where("user_id",$val->user_id)->get("user")->row();
                $user_wallet = $users->wallet;
                $update_wallet =  ($user_wallet - $val->points);
                $this->db->where("user_id",$val->user_id)->update("user",array("wallet"=>$update_wallet));
           }
            $today =$fetch->reg_date;
           $day = $fetch->game_day;
           $played_where ="game_id ='".$game_id."' AND reg_date ='".$today."'
           AND game_day ='".$day."' AND game_type='".$game_type."'";
           
           $this->db->where($played_where)->update("game_played",array("status" =>1));
            $run = $this->db->where($winner_where)->delete("winner_list");
            if($run)
            {
                //echo '<script>alert("DELETE RUN")</script>';
            }else{
              // echo '<script>alert("Failed ")</script>';
            }
            
       }else{
           $game_id = $fetch->game_id;
           $today =$fetch->reg_date;
           $day = $fetch->game_day;
           $played_where ="game_id ='".$game_id."' AND reg_date ='".$today."'
           AND game_day ='".$day."' AND game_type='".$game_type."'";
           $this->db->where($played_where)->update("game_played",array("status" =>1));
       }
       
       
      
       $wallet_where = " result_id='".$result_id."' AND game_type ='".$game_type."' AND game_id = '".$game_id."' AND reg_date = '".$today."'";
       $check = $this->db->where($wallet_where)->get("wallet_transaction")->num_rows();
       if($check  > 0)
       {    
           $run = $this->db->where($wallet_where)->delete("wallet_transaction");
       }
       if($run){
           return true;
       }else{
           return false;
       }
       
   }
   
   public function NewRedeclaredOpenResult($game_id,$sdate)
   {
      date_default_timezone_set("Asia/Kolkata");
      $day = date('l', strtotime($sdate));
      $today = $sdate;
 
     $result_where ='game_id ="'.$game_id.'" AND game_day ="'.$day.'" AND reg_date ="'.$today.'"';
     $result_cnt = $this->db->where($result_where)->get('declare_result')->num_rows(); 
       
        if($result_cnt > 0){
            $result = $this->db->where($result_where)->get('declare_result')->row();
            $game_schedule = $this->db->where('id',$result->game_content_id)->get('game_content')->row();
            $played_where = 'game_content_id ="'.$result->game_content_id.'" AND game_day ="'.$result->game_day.'" AND reg_date="'.$today.'" AND status="1"';
             $played_cnt = $this->db->where($played_where)->get('game_played')->num_rows();
           
            if($played_cnt > 0)
            {
                $prize_cnt = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->num_rows();
                $prize_data = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->row(); 
                $prize_amount = $prize_data->amount;  // amount which is multiple by with total amount into Points 
                    $played_data = $this->db->where($played_where)->get('game_played')->result();
                    foreach($played_data as $played){
                        
                       $array_winner = array();  // For Half Sangam
                       $full_sangam_array = array(); // For Full Sangam
                       $red_jodi_win_array = array(); // For Red Jodi
                       $jodi_win_array = array(); // For Jodi Win Array
                       $close_single_digit_win = array(); // for Close Single
                       $open_digit_win = array();  // for Open Digit 
                       $patti_win = array(); // Single,Double Triple Patti
                       $close_patti_win = array(); // Close Single ,Double,Triple Patti
                         $cname = $this->db->where('category_id',$played->category_id)->get("category")->row()->cname;
                         if($cname =="Single Digit")
                         {
                            $played_type = $played->game_type; 
                            if($result->game_type =="Close" && $played_type =="Open")
                            {
                                $open_digit_number = explode(",",$played->numbers);   
                                $open_digit_amts = explode(",",$played->amount);   
                                $open_digit_count = count($open_digit_number);
                                for($open_digit =0; $open_digit < $open_digit_count; $open_digit++){
                            	   $single_digit = $result->open_digit;
                            	  
                                    if($single_digit == $open_digit_number[$open_digit])
                                    { 
                                        $wincode = $this->db->order_by('id',"desc")->limit(1)->get('winner_list')->row()->wincode;
                                        $wincode_no = empty($wincode) ? '1000000' : $wincode + 1;
                                        
                                        $prize_data = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->row(); 
                                        $prize_amount = $prize_data->amount; $prize_point = $prize_data->points;
                                        $final_points = ($open_digit_amts[$open_digit] * $prize_point) / $prize_amount;
                                        $result_number = $single_digit;
                                        
                                        $array= array("result_id"=>$result->id,"game_type"=>$played->game_type,"wincode"=>$wincode_no,"result_number"=>$result_number,
                                                "prize_id"=>$prize_data->id,"game_day"=>$result->game_day,"number"=>$open_digit_number[$open_digit],
                                                "amount"=>$open_digit_amts[$open_digit],"points"=>$final_points,"reg_date"=>$sdate);
                                        
                                        $ins = $this->insert_winner_list($played->id,$array);
        	                        
        	                            if($ins){
    	                                       $open_digit_win[] = "win";
	                                           /* USER WALLLET UPDATE*/
	                                           $users = $this->db->where('user_id',$played->user_id)->get('user')->row();
	                                           $walletArr = array("result_id"=>$result->id,"game_type"=>"Open","game_id"=>$result->game_id,
	                                           "category_id"=>$played->category_id,"game_played_id"=>$played->id,"ticket_number"=>$played->ticket_number,
	                                           "win_number"=>$open_digit_number[$open_digit],"amount"=>$final_points,"user_id"=>$played->user_id,
	                                           "mobile"=>$users->mobile,"type"=>"WinGame","status","create_date","reg_date");
    	                                        $insert_new = $this->db->insert("wallet_transaction",$walletArr);
            	                               
            	                                $this->load->model("Agent_model"); 
            	                                $this->Agent_model->update_wallet($played->user_id,$final_points);
            	                                /*USER CODE OVER*/
        	                            }else{  
        	                                    $json['status'] =0;
    	                                        $json['msg'] ="Something Went Wrong ";
    	                                     }
                                            
                                    }else{
                                        $open_digit_win[] = "lose";
                                    }
                                } // For Loop Here 
                                if(in_array("win",$open_digit_win)){
                                     $this->db->where("id",$played->id)->update("game_played",array("status" => 3));
                                }else{
                                     $this->db->where("id",$played->id)->update("game_played",array("status" => 2));
                                }
                        	} // Both Game Type Same 
                         }//Single If Over Here...
                            if($cname =="Single Pana" || $cname =="Double Pana" || $cname =="Triple Pana")
                            {       
                                 $played_type = $played->game_type;
                                 if($result->game_type =="Close" && $played_type =="Open")
                                 {    
                                    $all_patti_numbers = explode(",",$played->numbers);   
                                    $all_patti_amts = explode(",",$played->amount);   
                                    $all_patti_count = count($all_patti_numbers);
                                    for($patti =0; $patti < $all_patti_count; $patti++){
                                        $all_patti = $result->open_panna;
                                        if($all_patti == $all_patti_numbers[$patti])
                                        {       
                                             $wincode = $this->db->order_by('id',"desc")->limit(1)->get('winner_list')->row()->wincode;
                                                $wincode_no = empty($wincode) ? '1000000' : $wincode + 1;
                                                 
                                                $prize_data = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->row(); 
                                                $prize_amount = $prize_data->amount; $prize_point = $prize_data->points;
                                                $final_points = ($all_patti_amts[$patti] * $prize_point) / $prize_amount;
                                              
                                                $result_number = $all_patti;
                                              
                                                $array= array("result_id"=>$result->id,"game_type"=>$played->game_type,"wincode"=>$wincode_no,"result_number"=>$result_number,
                                                "prize_id"=>$prize_data->id,"game_day"=>$result->game_day,"number"=>$all_patti_numbers[$patti],
                                                "amount"=>$all_patti_amts[$patti],"points"=>$final_points,"reg_date"=>$sdate);
                                        
                                                $ins = $this->insert_winner_list($played->id,$array);
                                        
                	                            if($ins){
                	                                      $json['status'] =1;
            	                                          $json['msg'] ="Winner Added";
            	                                          $patti_win[] = "win";
            	                                         
            	                                          $users = $this->db->where('user_id',$played->user_id)->get('user')->row();
            	                                          
            	                                          $WalletArr = array("result_id"=>$result->id,"game_type"=>"Open","game_id"=>$result->game_id,"amount"=>$final_points,
            	                                          "user_id"=>$played->user_id,"mobile"=>$users->mobile,"type"=>"WinGame","status"=>1,"create_date"=>date('Y-m-d h:i:sa'),
            	                                          "reg_date"=>$sdate,"category_id"=>$played->category_id,"game_played_id"=> $played->id,"ticket_number"=>$played->ticket_number,
            	                                          "win_number"=>$all_patti_numbers[$patti]);
            	                                            
            	                                            $insert_new = $this->db->insert("wallet_transaction",$WalletArr);
                        	                                $this->load->model("Agent_model"); 
            	                                            $this->Agent_model->update_wallet($played->user_id,$final_points);  /* USER WALLLET UPDATE*/
                	                                         /*USER CODE OVER*/
                	                            }else{
                	                                    $json['status'] =0;
            	                                        $json['msg'] ="Something Went Wrong ";
            	                                        
                	                            }
                                        
                                    }else{
                                            $patti_win[] = "lose";
                                        }
                                    }// For Loop Over Here
                                    if(in_array("win",$patti_win)){
                                         $this->db->where("id",$played->id)->update("game_played",array("status" => 3));
                                    }else{
                                         $this->db->where("id",$played->id)->update("game_played",array("status" => 2));
                                    }
                        }  
                            }
                        $json['status'] = 1;
                        $json['msg'] = "Winner Added";
                    } //FOR LOOP OVER
                    
                    // Winning Prize Not Added Or Not Found.. Points Not Found ..
            }else{
                    $json['status'] = 0;
                    $json['msg'] = "No Player Join Game";
            }
        }else{
                $json['status'] = 0;
                $json['msg'] = "No Result Declared Tday";
        }
        return true;
       // echo json_encode($json);
   }  // close

   public function NewCloseResult($game_id,$sdate)
   {
        date_default_timezone_set("Asia/Kolkata");
        $day = date('l', strtotime($sdate));
        $today = $sdate;
   
    $result_where ='game_type ="Close" AND game_id ="'.$game_id.'" AND game_day ="'.$day.'" AND reg_date ="'.$today.'"';
    $result_cnt = $this->db->where($result_where)->get('declare_result')->num_rows();
    if($result_cnt > 0)
    {
        $result = $this->db->where($result_where)->get('declare_result')->row();
            $game_schedule = $this->db->where('id',$result->game_content_id)->get('game_content')->row();
            $played_where = 'game_content_id ="'.$result->game_content_id.'" AND game_day ="'.$result->game_day.'" AND reg_date="'.$today.'" AND status="1"';
            $played_cnt = $this->db->where($played_where)->get('game_played')->num_rows();
            if($played_cnt > 0)
            {
                    $played_data = $this->db->where($played_where)->get('game_played')->result();
                    foreach($played_data as $played){
                        $prize_data = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->row(); 
                        $prize_amount = $prize_data->amount;  // amount which is multiple by with total amount into Points 
                         $array_winner = array();  // For Half Sangam
                         $full_sangam_array = array(); // For Full Sangam
                           $red_jodi_win_array = array(); // For Red Jodi
                           $jodi_win_array = array(); // For Jodi Win Array
                           $close_single_digit_win = array(); // for Close Single
                           $open_digit_win = array();  // for Open Digit 
                           $patti_win = array(); // Single,Double Triple Patti
                           $close_patti_win = array(); // Close Single ,Double,Triple Patti
                         $cname = $this->db->where('category_id',$played->category_id)->get("category")->row()->cname;
                       if($cname =="Half Sangam"){
                       if($result->game_type =="Close"){
                            $numbers = explode(",",$played->numbers);   
                            $amts = explode(",",$played->amount);   
                            $num_count = count($numbers);
                          
                            for($j=0;$j < $num_count; $j++){
                                
                                $open_panna = $result->open_panna."-".$result->close_digit;
                                if($open_panna == $numbers[$j])
                                {
                                    $wincode = $this->db->order_by('id',"desc")->limit(1)->get('winner_list')->row()->wincode;
                                    $wincode_no = empty($wincode) ? '1000000' : $wincode + 1;
                                 
                                    $prize_data = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->row(); 
                                    $prize_amount = $prize_data->amount;  // amount which is multiple by with total amount into Points 
                                        
                                    $prize_point = $prize_data->points;
                                    $final_points = ($amts[$j] * $prize_point) / $prize_amount;
                                        $result_number = $result->open_panna."-".$result->close_digit;
                                       $array= array("result_id"=>$result->id,"game_type"=>$played->game_type,"wincode"=>$wincode_no,"result_number"=>$result_number,
                                                "prize_id"=>$prize_data->id,"game_day"=>$result->game_day,"number"=>$numbers[$j],
                                                "amount"=>$amts[$j],"points"=>$final_points,"reg_date"=>$sdate); 
                                        $ins =  $this->insert_winner_list($played->id,$array);
        	                            
        	                            if($ins){
        	                                     $json['status'] =1;
    	                                         $json['msg'] ="Winner Added";
    	                                         $users = $this->db->where('user_id',$played->user_id)->get('user')->row();
    	                                         
    	                                         $WalletArr = array("result_id"=>$result->id,"game_type"=>"Close","game_id"=>$result->game_id,"amount"=>$final_points,
                                                "user_id"=>$played->user_id,"mobile"=>$users->mobile,"type"=>"WinGame","status"=>1,"create_date"=>date('Y-m-d h:i:sa'),
                                                "reg_date"=>$sdate,"category_id"=>$played->category_id,"game_played_id"=> $played->id,"ticket_number"=>$played->ticket_number,
                                                "win_number"=>$numbers[$j]);
                                                
            	                                $insert_new = $this->db->insert("wallet_transaction",$WalletArr);
            	                                $this->load->model("Agent_model"); 
                                                $this->Agent_model->update_wallet($played->user_id,$final_points);  /* USER WALLLET UPDATE*/
    	                                         /*USER CODE OVER*/
    	                                }else{
        	                                    $json['status'] =0;
    	                                        $json['msg'] ="Something Went Wrong ";
    	                                    }
            	                            
        	                              $array_winner[] = "win";  
                                   
                                }else{
                                    $array_winner[] = "lose";
                                   
                                }  // open panna match here 
                                
                                $close_panna = $result->open_digit."-".$result->close_panna;
                               if($close_panna == $numbers[$j])
                                {
                                    $wincode = $this->db->order_by('id',"desc")->limit(1)->get('winner_list')->row()->wincode;
                                    $wincode_no = empty($wincode) ? '1000000' : $wincode + 1;
                                      
                                        $prize_data = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->row(); 
                                        $prize_amount = $prize_data->amount;  // amount which is multiple by with total amount into Points 
                                        $prize_point = $prize_data->points;
                                    $final_points = ($amts[$j] * $prize_point) / $prize_amount;
                                        //$final_points = $prize_amount * $amts[$j];
                                        $result_number =$result->close_panna."-".$result->open_digit;
                                        $insert['result_id'] = $result->id;
                                        $insert['game_id'] =$played->game_id;
                                        $insert['wincode'] =$wincode_no;$insert['result_number'] =$result_number;
                                        $insert['ticket_no'] =$played->ticket_number; $insert['prize_id'] =$prize_data->id; 
        	                            $insert['played_id'] =$played->id; $insert['game_content_id'] = $played->game_content_id;
        	                            $insert['category_id'] =$played->category_id; $insert['user_id'] = $played->user_id; 
        	                            $insert['game_type'] =$played->game_type; $insert['game_day'] = $result->game_day; 
        	                            $insert['number'] =$numbers[$j]; $insert['amount'] = $amts[$j]; 
        	                            $insert['points'] = $final_points;$insert['status'] = 1;
        	                            $insert['day'] = date('l');$insert['month'] = date('F');
        	                            $insert['year'] = date('Y');$insert['create_date'] = date('Y-m-d h:i:sa');
        	                            $insert['create_time'] =date('Y-m-d H:i:sa');$insert['reg_date'] =$sdate;
        	                            
        	                            $array= array("result_id"=>$result->id,"game_type"=>$played->game_type,"wincode"=>$wincode_no,"result_number"=>$result_number,
                                                "prize_id"=>$prize_data->id,"game_day"=>$result->game_day,"number"=>$numbers[$j],
                                                "amount"=>$amts[$j],"points"=>$final_points,"reg_date"=>$sdate);
        	                            $ins = $this->insert_winner_list($played->id,$array);
        	                            if($ins){
        	                               
        	                                     $json['status'] =1;
    	                                         $json['msg'] ="Winner Added";
    	                                         $array_winner[] = "win";
    	                                          /* USER WALLLET UPDATE*/
    	                                          $users = $this->db->where('user_id',$played->user_id)->get('user')->row();
    	                                         
    	                                          $WalletArr = array("result_id"=>$result->id,"game_type"=>"Close","game_id"=>$result->game_id,"amount"=>$final_points,
                                                  "user_id"=>$played->user_id,"mobile"=>$users->mobile,"type"=>"WinGame","status"=>1,"create_date"=>date('Y-m-d h:i:sa'),
                                                  "reg_date"=>$sdate,"category_id"=>$played->category_id,"game_played_id"=> $played->id,"ticket_number"=>$played->ticket_number,
                                                  "win_number"=>$numbers[$j]);
                                                 
            	                                $insert_new = $this->db->insert("wallet_transaction",$WalletArr);
            	                                $this->load->model("Agent_model"); 
                                                $this->Agent_model->update_wallet($played->user_id,$final_points);  /* USER WALLLET UPDATE*/
    	                                        /*USER CODE OVER*/
        	                            }else{
        	                                    $json['status'] =0;
    	                                        $json['msg'] ="Something Went Wrong ";
    	                                }
            	                      
                                }else{
                                    
                                    $array_winner[] = "lose";
                                }  // close panna match first over here
                                
                            }  // for loop over here
                           
                            if(in_array("win",$array_winner))
                            {
                                $this->db->where('id',$played->id)->update("game_played",array("status"=> 3));
                            }else{
                                $this->db->where('id',$played->id)->update("game_played",array("status"=> 2));
                            }
                        } // Open Close Type 
                       }elseif($cname =="Full Sangam"){
                           
                           if($result->game_type =="Close"){
                            $full_sangam_numbers = explode(",",$played->numbers);   
                            $full_sangam_amts = explode(",",$played->amount);   
                            $full_sangam_count = count($full_sangam_numbers);
                          
                           for($k=0;$k < $full_sangam_count; $k++){
                                
                                $full_sangam = $result->open_panna."-".$result->close_panna;
                                if($full_sangam == $full_sangam_numbers[$k])
                                {
                                    $wincode = $this->db->order_by('id',"desc")->limit(1)->get('winner_list')->row()->wincode;
                                    $wincode_no = empty($wincode) ? '1000000' : $wincode + 1;
                                      
                                   $prize_amount = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->row()->amount;
                                   
                                   $prize_point = $prize_data->points;
                                    $final_points = ($full_sangam_amts[$k] * $prize_point) / $prize_amount;
                                   //$final_points = $prize_amount * $full_sangam_amts[$k];
                                   $result_number = $full_sangam;
                                    $array= array("result_id"=>$result->id,"game_type"=>$played->game_type,"wincode"=>$wincode_no,"result_number"=>$result_number,
                                            "prize_id"=>$prize_data->id,"game_day"=>$result->game_day,"number"=>$full_sangam_numbers[$k],
                                            "amount"=>$full_sangam_amts[$k],"points"=>$final_points,"reg_date"=>$sdate);
                                    
                                    $ins = $this->insert_winner_list($played->id,$array);
    	                            if($ins){
    	                                      $json['status'] =1;
	                                          $json['msg'] ="Winner Added";
	                                        $full_sangam_array[] ="win";
	                                        /* USER WALLLET UPDATE*/
	                                        $users = $this->db->where('user_id',$played->user_id)->get('user')->row();
	                                        
	                                        $WalletArr = array("result_id"=>$result->id,"game_type"=>"Close","game_id"=>$result->game_id,"amount"=>$final_points,
                                            "user_id"=>$played->user_id,"mobile"=>$users->mobile,"type"=>"WinGame","status"=>1,"create_date"=>date('Y-m-d h:i:sa'),
                                            "reg_date"=>$sdate,"category_id"=>$played->category_id,"game_played_id"=> $played->id,"ticket_number"=>$played->ticket_number,
                                            "win_number"=>$full_sangam_numbers[$k]);
                                           
    	                                    $insert_new = $this->db->insert("wallet_transaction",$WalletArr);
        	                               
        	                                $this->load->model("Agent_model"); 
                                            $this->Agent_model->update_wallet($played->user_id,$final_points);  /* USER WALLLET UPDATE*/
	                                         /*USER CODE OVER*/
    	                            }else{
    	                                    $json['status'] =0;
	                                        $json['msg'] ="Something Went Wrong ";
	                                        
    	                            }
            	                            
        	                                
                                   
                                }else{
                                     $full_sangam_array[] ="lose";
                                }  // open panna match here 
                             
                               
                            }  // for loop over here
                            
                            if(in_array("win",$full_sangam_array)){
                               $this->db->where("id",$played->id)->update("game_played",array("status"=>3));
                            }else{
                                $this->db->where("id",$played->id)->update("game_played",array("status"=>2));
                            }
                           
                        }   // OpenClose Type 
                       }elseif($cname =="Red Jodi"){
                          
                           if($result->game_type =="Close")
                           {    
                               $red_jodi_numbers = explode(",",$played->numbers);   
                               $red_jodi_amts = explode(",",$played->amount);   
                               $red_jodi_count = count($red_jodi_numbers);
                               for($red =0; $red < $red_jodi_count; $red++)
                               {
                                    $red_jodi = $result->open_digit.$result->close_digit;
                                    if($red_jodi == $red_jodi_numbers[$red])
                                    {
                                       $wincode = $this->db->order_by('id',"desc")->limit(1)->get('winner_list')->row()->wincode;
                                        $wincode_no = empty($wincode) ? '1000000' : $wincode + 1;
                                         
                                       $prize_data = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->row(); 
                                       $prize_amount = $prize_data->amount;  // amount which is multiple by with total amount into Points 
                                            
                                       $prize_point = $prize_data->points;
                                    $final_points = ($red_jodi_amts[$red] * $prize_point) / $prize_amount;
                                            //$final_points = $prize_amount * $red_jodi_amts[$red];
                                            $result_number = $red_jodi;
                                            
                                            $array= array("result_id"=>$result->id,"game_type"=>$played->game_type,"wincode"=>$wincode_no,"result_number"=>$result_number,
                                            "prize_id"=>$prize_data->id,"game_day"=>$result->game_day,"number"=>$red_jodi_numbers[$red],
                                            "amount"=>$red_jodi_amts[$red],"points"=>$final_points,"reg_date"=>$sdate);
                                           
                                            $ins = $this->insert_winner_list($played->id,$array);
            	                            if($ins)
            	                            {
    	                                      $json['status'] =1; $json['msg'] ="Winner Added";
	                                          $red_jodi_win_array[] ="win";
	                                        
	                                          $users = $this->db->where('user_id',$played->user_id)->get('user')->row();
	                                           
	                                           $WalletArr = array("result_id"=>$result->id,"game_type"=>"Close","game_id"=>$result->game_id,"amount"=>$final_points,
                                               "user_id"=>$played->user_id,"mobile"=>$users->mobile,"type"=>"WinGame","status"=>1,"create_date"=>date('Y-m-d h:i:sa'),
                                               "reg_date"=>$sdate,"category_id"=>$played->category_id,"game_played_id"=> $played->id,"ticket_number"=>$played->ticket_number,
                                               "win_number"=>$red_jodi_numbers[$red]);
                                               $insert_new = $this->db->insert("wallet_transaction",$WalletArr);
                                           
        	                                    $this->load->model("Agent_model"); 
                                                $this->Agent_model->update_wallet($played->user_id,$final_points);  /* USER WALLLET UPDATE*/
                                                 /*USER CODE OVER*/
            	                            }else{
            	                                    $json['status'] =0;
        	                                        $json['msg'] ="Something Went Wrong ";
        	                                }
                	                        
                                    }else{
                                         $red_jodi_win_array[] = "lose";
                                    }  // open panna match here 
                              } // for loop Close Here
                              
                              if(in_array("win",$red_jodi_win_array)){
                                  $this->db->where("id",$played->id)->update("game_played",array("status" => 3));
                              }else{
                                  $this->db->where("id",$played->id)->update("game_played",array("status" => 2));
                              }
                            }   // OpenClose Type 
                            
                       }elseif($cname =="Jodi"){
                         
                           if($result->game_type =="Close")
                           {   $jodi_numbers = explode(",",$played->numbers);   
                               $jodi_amts = explode(",",$played->amount);   
                               $jodi_count = count($jodi_numbers);
                               for($jodi =0; $jodi < $jodi_count; $jodi++)
                               {
                                    $jodi_match = $result->open_digit.$result->close_digit;
                                    if($jodi_match == $jodi_numbers[$jodi])
                                    {   
                                        $wincode = $this->db->order_by('id',"desc")->limit(1)->get('winner_list')->row()->wincode;
                                        $wincode_no = empty($wincode) ? '1000000' : $wincode + 1;
                                          
                                        $prize_data = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->row(); 
                                        $prize_amount = $prize_data->amount;  // amount which is multiple by with total amount into Points        
                                        
                                        $prize_point = $prize_data->points;
                                    $final_points = ($jodi_amts[$jodi] * $prize_point) / $prize_amount;
                                        //$final_points = $prize_amount * $jodi_amts[$jodi];
                                        $result_number = $jodi_match;
                                        $array= array("result_id"=>$result->id,"game_type"=>$played->game_type,"wincode"=>$wincode_no,"result_number"=>$result_number,
                                        "prize_id"=>$prize_data->id,"game_day"=>$result->game_day,"number"=>$jodi_numbers[$jodi],
                                        "amount"=>$jodi_amts[$jodi],"points"=>$final_points,"reg_date"=>$sdate);
                                        $ins = $this->insert_winner_list($played->id,$array);

        	                            if($ins){
        	                                      $json['status'] =1;
    	                                          $json['msg'] ="Winner Added";
    	                                          $jodi_win_array[] = "win";
    	                                          /* USER WALLLET UPDATE*/
    	                                          $users = $this->db->where('user_id',$played->user_id)->get('user')->row();
    	                                          
    	                                          $WalletArr = array("result_id"=>$result->id,"game_type"=>"Close","game_id"=>$result->game_id,"amount"=>$final_points,
                                                    "user_id"=>$played->user_id,"mobile"=>$users->mobile,"type"=>"WinGame","status"=>1,"create_date"=>date('Y-m-d h:i:sa'),
                                                    "reg_date"=>$sdate,"category_id"=>$played->category_id,"game_played_id"=> $played->id,"ticket_number"=>$played->ticket_number,
                                                    "win_number"=>$jodi_numbers[$jodi]);
                                                    $insert_new = $this->db->insert("wallet_transaction",$WalletArr);
                                                    $this->load->model("Agent_model"); 
                                                    $this->Agent_model->update_wallet($played->user_id,$final_points);  /* USER WALLLET UPDATE*/
                                                     /*USER CODE OVER*/
            	                            }else{
            	                                    $json['status'] =0;
        	                                        $json['msg'] ="Something Went Wrong ";
        	                                 }
                	                        
                                    }else{
                                        $jodi_win_array[] = "lose";
                                    }  // open panna match here 
                               }// For Close Here
                               if(in_array("win",$jodi_win_array)){
                                   $this->db->where('id',$played->id)->update("game_played",array("status" =>3));
                               }else{
                                   $this->db->where('id',$played->id)->update("game_played",array("status" =>2));
                               }
                            }   // OpenClose Type 
                       }else{
                           $cname = $this->db->where('category_id',$played->category_id)->get("category")->row()->cname;
                            $played_type = $played->game_type;
                            if($cname =="Single Digit" && $result->game_type =="Close" && $played_type =="Close")
                            {
                            
                               $single_digit_numbers = explode(",",$played->numbers);   
                                $single_digit_amts = explode(",",$played->amount);   
                                $single_digit_count = count($single_digit_numbers);
                                for($single_digit = 0; $single_digit < $single_digit_count; $single_digit++)
                                {
                                    $single_digit_close = $result->close_digit;
                                    if($single_digit_close == $single_digit_numbers[$single_digit])
                                    {   
                                        $wincode = $this->db->order_by('id',"desc")->limit(1)->get('winner_list')->row()->wincode;
                                        $wincode_no = empty($wincode) ? '1000000' : $wincode + 1;
                                         
                                        $prize_data = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->row(); 
                                        $prize_amount = $prize_data->amount;  // amount which is multiple by with total amount into Points 
                                        $prize_point = $prize_data->points;
                                        $final_points = ($single_digit_amts[$single_digit] * $prize_point) / $prize_amount;
                                        
                                        $result_number = $single_digit_close;
                                        $array= array("result_id"=>$result->id,"game_type"=>$played->game_type,"wincode"=>$wincode_no,"result_number"=>$result_number,
                                                "prize_id"=>$prize_data->id,"game_day"=>$result->game_day,"number"=>$single_digit_numbers[$single_digit],
                                                "amount"=>$single_digit_amts[$single_digit],"points"=>$final_points,"reg_date"=>$sdate);
        	                            $ins = $this->insert_winner_list($played->id,$array);
        	                            if($ins){
        	                                      $json['status'] =1;
    	                                          $json['msg'] ="Winner Added";
    	                                          $close_single_digit_win[] = "win";
    	                                          /* USER WALLLET UPDATE*/
    	                                          $users = $this->db->where('user_id',$played->user_id)->get('user')->row();
    	                                          
    	                                          $WalletArr = array("result_id"=>$result->id,"game_type"=>"Close","game_id"=>$result->game_id,"amount"=>$final_points,
                                                    "user_id"=>$played->user_id,"mobile"=>$users->mobile,"type"=>"WinGame","status"=>1,"create_date"=>date('Y-m-d h:i:sa'),
                                                    "reg_date"=>$sdate,"category_id"=>$played->category_id,"game_played_id"=> $played->id,"ticket_number"=>$played->ticket_number,
                                                    "win_number"=>$single_digit_numbers[$single_digit]);
                                                    $insert_new = $this->db->insert("wallet_transaction",$WalletArr);
                                                    $this->load->model("Agent_model"); 
                                                    $this->Agent_model->update_wallet($played->user_id,$final_points);  /* USER WALLLET UPDATE*/
    	                                           /*USER CODE OVER*/
        	                            }else{
        	                                    $json['status'] =0;
    	                                        $json['msg'] ="Something Went Wrong ";
    	                               }
                                        
                                    }else{
                                        $close_single_digit_win[] = "lose";
                                    }
                                }  // For Loop Close Here
                                
                                if(in_array("win",$close_single_digit_win)){
                                    $this->db->where("id",$played->id)->update("game_played",array("status" => 3));
                                }else{
                                    $this->db->where("id",$played->id)->update("game_played",array("status" => 2));
                                }
                            
                            } // Single Digit Logic Over here
                            if($cname =="Single Pana" || $cname =="Double Pana" || $cname =="Triple Pana")
                            {
    	                        $played_type = $played->game_type;
                                if($result->game_type =="Close" && $played_type =="Close")
                                {   
                                    $close_patti_numbers = explode(",",$played->numbers);   
                                    $close_patti_amts = explode(",",$played->amount);   
                                    $close_patti_count = count($close_patti_numbers);
                                    for($close_patti =0; $close_patti < $close_patti_count; $close_patti++)
                                    {
                                        $all_patti = $result->close_panna;
                                        if($all_patti == $close_patti_numbers[$close_patti])
                                        {
                                            $wincode = $this->db->order_by('id',"desc")->limit(1)->get('winner_list')->row()->wincode;
                                                $wincode_no = empty($wincode) ? '1000000' : $wincode + 1;
                                                
                                            $prize_data = $this->db->where('game_id',$played->game_id)->where('category_id',$played->category_id)->get('winner_prize')->row(); 
                                            $prize_amount = $prize_data->amount;  // amount which is multiple by with total amount into Points 
                                            $prize_point = $prize_data->points;
                                    $final_points = ($close_patti_amts[$close_patti] * $prize_point) / $prize_amount;
                                               // $final_points = $prize_amount * $close_patti_amts[$close_patti];
                                                $result_number = $all_patti;
                                                
                                                $array= array("result_id"=>$result->id,"game_type"=>$played->game_type,"wincode"=>$wincode_no,"result_number"=>$result_number,
                                                        "prize_id"=>$prize_data->id,"game_day"=>$result->game_day,"number"=>$close_patti_numbers[$close_patti],
                                                        "amount"=>$close_patti_amts[$close_patti],"points"=>$final_points,"reg_date"=>$sdate);
                                             
                                               $ins = $this->insert_winner_list($played->id,$array);
                	                            if($ins){
                	                                      $json['status'] =1;
            	                                          $json['msg'] ="Winner Added";
            	                                          $close_patti_win[] ="win";
            	                                          /* USER WALLLET UPDATE*/
            	                                          $users = $this->db->where('user_id',$played->user_id)->get('user')->row();
            	                                          
            	                                          $WalletArr = array("result_id"=>$result->id,"game_type"=>"Close","game_id"=>$result->game_id,"amount"=>$final_points,
                                                           "user_id"=>$played->user_id,"mobile"=>$users->mobile,"type"=>"WinGame","status"=>1,"create_date"=>date('Y-m-d h:i:sa'),
                                                           "reg_date"=>$sdate,"category_id"=>$played->category_id,"game_played_id"=> $played->id,"ticket_number"=>$played->ticket_number,
                                                           "win_number"=>$close_patti_numbers[$close_patti]);
                                                           $insert_new = $this->db->insert("wallet_transaction",$WalletArr);
                                                           $this->load->model("Agent_model"); 
                                                           $this->Agent_model->update_wallet($played->user_id,$final_points);  /* USER WALLLET UPDATE*/
                                                          /*USER CODE OVER*/
                	                            }else{
                	                                    $json['status'] =0;
            	                                        $json['msg'] ="Something Went Wrong ";
            	                                        
                	                            }
                                         
                                        }else{
                                            $close_patti_win[] ="lose";
                                    } //
                                    } // For Loop Close Here
                                    if(in_array("win",$close_patti_win)){
                                         $this->db->where("id",$played->id)->update("game_played",array("status" => 3));
                                    }else{
                                         $this->db->where("id",$played->id)->update("game_played",array("status" => 2));
                                    }
                            } // Single Double Triple Patti If Close Here 
                            }
                                 $json['status'] =1;
                                 $json['msg'] ="Winner Added"; 
	                     }
                        $json['status'] = 1;
                        $json['msg'] = "Winner Added";
                    } //FOR LOOP OVER
                    // Winning Prize Not Added Or Not Found.. Points Not Found ..
            }else{
                    $json['status'] = 0;
                    $json['msg'] = "No Player Join Game";
                    //No Player Join This Game
            }
    }else{
            return "fail";
    }   
    return true;
   }
    
   public function insert_winner_list($played_id,$array)
   {
        $played = $this->db->where("id",$played_id)->get("game_played")-row();
        $field = array("game_id"=>$played->game_id,"ticket_no"=>$played->ticket_number,"played_id"=>$played->id,"game_content_id"=>$played->game_content_id,
                        "category_id"=>$played->category_id,"user_id"=>$played->user_id,"day"=>date('l'),
                        "month"=>date('F'),"year"=>date('Y'),"create_date"=> date('Y-m-d h:i:sa'),"create_time"=>date('Y-m-d H:i:sa'));
        $insert = array_merge($array,$field);
        $ins = $this->db->insert("winner_list",$insert);
         //return $this->db->insert_id();
        echo empty($ins) ? 0 : 1; 
        
    }
    
   
}

?>