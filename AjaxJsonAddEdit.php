<?php

public function AjaxAddPet()
{
    extract($_POST);
    
    
    $userid = $_SESSION['user_id'];
    $pet_name = $_POST['pet_name'];
    $breed_id = $_POST['breed_id'];
    $pet_weight = $_POST['pet_weight'];
    $gender = $_POST['gender'];
    
    $type_id= 1;
    
    $petcount = $this->db->where("client_id",$userid)->where('name',$pet_name)->get("tbl_pet")->num_rows();
    if($petcount > 0)
    {
        $json = array("status"=>0,"msg"=>"Pet Already Exist");
    }else{
            date_default_timezone_set('Asia/Kolkata');
            $array = array("client_id"=>$userid,"breed_id"=>$breed_id,"name"=>$pet_name,"weight"=>$pet_weight,"gender"=>$gender,"type_id"=>$type_id,
            "status"=>1,"create_by"=>$userid,"reg_date"=>date("Y-m-d"),"create_time"=>date('Y-m-d H:i:s'));
            
            $insert = $this->db->insert("tbl_pet",$array);
            $last_id = $this->db->insert_id();
            if($insert)
            {
                $this->load->model("Pet_model");
                $fetch = $this->db->where("client_id",$userid)->where("status",1)->order_by('pet_id','desc')->get("tbl_pet")->result();
                foreach($fetch as $plist)
                {
                    $breedName = $this->Pet_model->BreedName($plist->breed_id);
                    $petLogo  = base_url().'uploads/pet_profile/'.$plist->profile;
                    $petdata[] = array("pet_id"=>$plist->pet_id,"name"=>$plist->name,"breedName"=>$breedName,"petlogo"=>$petLogo);
                }
                $json = array("status"=>1,"msg"=>"Pets Added Successfully","data"=>$petdata,"id"=>$last_id);
            }else{
                $json = array("status"=>0,"msg"=>"Something Went Wrong");
            }
    }
    echo json_encode($json);
}
?>