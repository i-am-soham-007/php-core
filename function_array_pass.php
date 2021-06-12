<?php

function GetArr($demo,$id)
{
 $insert = array("id"=>$id,"playid"=>10,"name"=>'demo');
 $merge = array_merge($insert,$demo);
 print_r($merge);
}

$id= 1;
$demo = array("wincode"=>001,"number"=>124,"amount"=>10.00);
GetArr($demo,$id);


$array= array("result_id"=>$result->id,"game_type"=>$played->game_type,"wincode"=>$wincode_no,"result_number"=>$result_number,
"prize_id"=>$prize_data->id,"game_day"=>$result->game_day,"number"=>$open_digit_number[$open_digit],
"amount"=>$open_digit_amts[$open_digit],"points"=>$final_points,"reg_date"=>$sdate);
$this->insert_winner_list($played->id,$array);


$WalletArr = array("result_id"=>$result->id,"game_type"=>"Close","game_id"=>$result->game_id,"amount"=>$final_points,
"user_id"=>$played->user_id,"mobile"=>$users->mobile,"type"=>"WinGame","status"=>1,"create_date"=>date('Y-m-d h:i:sa'),
"reg_date"=>$sdate,"category_id"=>$played->category_id,"game_played_id"=> $played->id,"ticket_number"=>$played->ticket_number,
"win_number"=>$all_patti_numbers[$patti]);
$insert_new = $this->db->insert("wallet_transaction",$WalletArr);
$this->load->model("Agent_model"); 
$this->Agent_model->update_wallet($played->user_id,$final_points);  /* USER WALLLET UPDATE*/







ADMIN  CONTROLLER


public function ResModelClose($numbers,$game_id,$game_type,$sdate)
    {
        $this->load->model("Result_model");
        $val = $this->Result_model->insert_close_declare_result($numbers,$game_id,$game_type,$sdate);
        
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
?>