<?php  $this->load->view("User/common/dashboard_header"); ?>
<?php  $this->load->view("User/common/dashboard_sidebar"); ?>
<style>
@media only screen and (max-width:540px)
{
.section {
  padding: 0px !important;
}

}
.role-status{
    margin-top:30px;
    margin-left:20px;
}
#role-label{
    margin-top:-15px;
   
}

.col.m6.s12.input-field {
    margin-top: -15px;
}

label {
    margin-top: 3px;
}
label#chnge {
    margin-top: -4px;
}

label#chnge2 {
    margin-top: -4px;
}

@media only screen and (min-width: 992px) {
.col.s12.m12.l12.input-field {
    margin-top: 0px;
}

}
img.pet-profile {
        border-radius: 15px;
}
.breed {
        padding-top: 5px;
}
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>theme/admin/app-assets/css/pages/form-select2.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>theme/admin/app-assets/vendors/select2/select2.min.css" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>theme/admin/app-assets/vendors/select2/select2-materialize.css" type="text/css">
<?php
$login = $_SESSION['user_id'];
$rows = $this->db->where("client_id",$login)->get("tbl_client")->row();
$email = !empty($rows->email) ? $rows->email : "";
$phone = !empty($rows->phone_no) ? $rows->phone_no : "";

$userip = $this->input->ip_address();
$ucount = $this->db->where('userip',$userip)->where('status',0)->get("tbl_info")->num_rows();
if($ucount > 0)
{
    $udata = $this->db->where('userip',$userip)->where('status',0)->order_by('id','desc')->limit('1')->get("tbl_info")->row();
    
    $company_id = !empty($udata->company_id) ? $udata->company_id : "";
    $service_id = !empty($udata->service_id)  ? $udata->service_id : "";
    $company_where = 'AND c.company_id ="'.$company_id.'"';
    
    $service_count = $this->db->where("company_id",$company_id)->where("status",1)->get("tbl_service_configuration")->num_rows();
    if($service_count > 0)
    {
        $service_configs ='<option value="">Select Service</option>';
        $service_c_data = $this->db->where("company_id",$company_id)->where("status",1)->get("tbl_service_configuration")->result();
        foreach($service_c_data as $sclist)
        {
            $service_configs .= '<option value="'.$sclist->service_config_id.'">'.$sclist->name.'</option>';
        }
    }else{
        $service_configs .= '<option value="">Select Service</option>';
    }
}else
{
    $company_id ="";
    $service_id ="";
    $company_where = '';
    
    $service_count = $this->db->where("status",1)->get("tbl_service_configuration")->num_rows();
    if($service_count > 0)
    {
        $service_configs ='<option value="">Select Service</option>';
        $service_c_data = $this->db->where("status",1)->get("tbl_service_configuration")->result();
        foreach($service_c_data as $sclist)
        {
            $service_configs .= '<option value="'.$sclist->service_config_id.'">'.$sclist->name.'</option>';
        }
    }else{
        $service_configs .= '<option value="">Select Service</option>';
    }
}


if(!empty($company_id))
{
    $company_count = $this->db->where("company_id",$company_id)->where("status",1)->get("tbl_company")->num_rows();
    if($company_count > 0)
    {
        $companies = '';
        $company_data = $this->db->where("company_id",$company_id)->where("status",1)->get("tbl_company")->result();
        foreach($company_data as $clist)
        {
            $select = ($clist->company_id == $company_id) ? "selected" : "";
            $companies .= '<option value="'.$clist->company_id.'" '.$select.'>'.$clist->name.'</option>';
        }
    }else{
        $companies = '';
    }
}else{
    $company_count = $this->db->where("status",1)->get("tbl_company")->num_rows();
    if($company_count > 0)
    {
        $companies = '';
        $company_data = $this->db->where("status",1)->get("tbl_company")->result();
        foreach($company_data as $clist)
        {
            $select = ($clist->company_id == $company_id) ? "selected" : "";
            $companies .= '<option value="'.$clist->company_id.'" '.$select.'>'.$clist->name.'</option>';
        }
    }else{
        $companies = '';
    }
}


$type_count = $this->db->where("status",1)->get("tbl_animal_type")->num_rows();
if($type_count > 0)
{
    $types = '<option value="">Select PetType</option>';
    $type_data = $this->db->where("status",1)->get("tbl_animal_type")->result();
    foreach($type_data as $tlist)
    {
        $types .= '<option value="'.$tlist->type_id.'">'.$tlist->name.'</option>';
    }
}else{
    $types = '<option value="">Select PetType</option>';
}

$breed_count = $this->db->where("status",1)->get("tbl_breed")->num_rows();
if($breed_count > 0)
{
    $breeds = '<option value="">Select Breed</option>';
    $breed_data = $this->db->where("status",1)->get("tbl_breed")->result();
    foreach($breed_data as $blist)
    {
        $bname = str_replace("'", "`", $blist->name);
        $breeds .= '<option value="'.$blist->breed_id.'"> '.$bname.'</option>';
    }
}else{
    $breeds = '<option value="">Select Breed</option>';
}


$query ='SELECT s.name,s.status as service_status,s.service_id,c.company_id,c.name as company_name, c.username,sc.service_config_id,sc.name as service_config_name,
         sc.service_rate FROM tbl_service as s, tbl_company as c, tbl_service_configuration as sc WHERE s.service_id = sc.service_id AND c.company_id = sc.company_id AND 
         s.status = 1 AND c.status = 1 AND sc.status=1 '.$company_where;
?>
    <!-- BEGIN: Page Main-->
    <div id="main">
      <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="col s12 m12 l12">
                        <div id="Form-advance" class="card card card-default scrollspy">
                            <div class="card-content">
                                <div class="error">
                                    <?php  
                                        if(isset($error)){ echo $error; }
                                        echo $this->session->flashdata('message');
                                        unset($_SESSION['message']);
                                    ?>
                                </div>
                                <h4 class="card-title">Appointment</h4>
                                <br>
                                <form action="" method="POST" id="appointment-form" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="input-field col m6 l6 s12">
                                            <select class="select2 browser-default" name="company_id" id="company_id" form="appointment-form">
                                                <?php echo $companies;?>
                                            </select>
                                            <label>Company</label>
                                        </div>
                                        <div class="input-field col m6 l6 s12" id="service-update">
                                            <select class="select2 browser-default" name="service_id" id="service_id" form="appointment-form">
                                                <?php echo $service_configs;?>
                                            </select>
                                            <label>Select Services</label>
                                        </div>
                                        <div class="input-field col m6 s12">
                                            <input type="text" class="datepicker" name="drop_off" id="drop_off" form="appointment-form">
                                            <label id="chnge">When would you like to drop off ?</label>
                                        </div>
                                        <div class="input-field col m6 s12">
                                            <div class="row">
                                                <div class="col s12 m12 l12 input-field">
                                                    <label>Between what times ? </label>
                                                </div>
                                                <div class="col m6 s12 input-field">
                                                    <input type="text" name="dftime" class="timepicker" form="appointment-form">
                                                </div>
                                                <div class="col m6 s12 input-field">
                                                    <input type="text" name="dstime" class="timepicker" form="appointment-form">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="input-field col m6 s12">
                                            <input type="text" class="datepicker" name="pickup" id="pickup" form="appointment-form">
                                            <label id="chnge2">When would you like to Pickup ?</label>
                                        </div>
                                        <div class="input-field col m6 s12">
                                            <div class="row">
                                               <div class="col s12 m12 l12 input-field">
                                                    <label>Between what times ? </label>
                                                </div>
                                                <div class="col m6 s12 input-field">
                                                    <input type="text" name="pftime" class="timepicker" form="appointment-form">
                                                </div>
                                                <div class="col m6 s12 input-field">
                                                    <input type="text" name="pstime" class="timepicker" form="appointment-form">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="update-pets">
                                        <?php
                                            $login = $_SESSION['user_id'];
                                            $count = $this->db->where("client_id",$login)->where("status","1")->get("tbl_pet")->num_rows();
                                            if($count > 0)
                                            {
                                                $result = $this->db->where("client_id",$login)->where("status","1")->get("tbl_pet")->result();
                                                foreach($result as $val)
                                                {
                                                    $this->load->model("Pet_model");
                                                    $petName = !empty($val->name) ? $val->name : "NA";
                                                    $breedName = $this->Pet_model->BreedName($val->breed_id);
                                                    
                                                    if(!empty($val->profile))
                                                    {
                                                        $petLogo  = base_url().'uploads/pet_profile/'.$val->profile;    
                                                    }else{
                                                        $petLogo  = base_url().'uploads/pet_profile/'.$val->profile;    
                                                    }
                                                    
                                                  //  $icon = '<i class="material-icons dp48">pets</i>';
                                                    
                                                    echo '<div class="col s12 m12 l12" id="pet_id'.$val->pet_id.'">';
                                                    echo '<div class="col s12 m2 l2">';
                                                    echo '<label><input type="checkbox" name="pet_id[]" value="'.$val->pet_id.'" form="appointment-form" checked><span></span></label>';
                                                    echo '<img src="'.$petLogo.'" height="50" width="50" class="pet-profile">';
                                                    echo '</div>';
                                                    echo '<div class="col s12 l2 m2">';
                                                    echo '<div class="petname"><b>'.$petName.'</b></div>';    
                                                    echo '<div class="breed">'.$breedName.'</div>';
                                                    echo '</div>';
                                                    echo '<div class="col s12 l3 m3">';
                                                    echo '<div class="pet_edit_btn"><a href="javascript:void(0);" class="button" data-pet_id="'.$val->pet_id.'" id="edit_pet_button">EDIT</a></div>';
                                                    echo '</div>';
                                                    echo '</div>';
                                                }
                                            }
                                            
                                        ?> 
                                        
                                    </div>
                                    <div class="row">
                                        <br>
                                        <div class="col m12 l12 s12">
                                            <div class="row edit_wrapper">
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <br>
                                        <div class="col m2 l2 s12" id="add_pet_btn_show">
                                            <!--<a href="javascript:void(0);" class="add_button"><i class="material-icons">add_box</i>Add Pet</a>-->
                                            <a href="javascript:void(0);" class="add_button" title="Add field">AddPet</a>
                                        </div>
                                        
                                        <div class="col m12 l12 s12">
                                            <div class="row field_wrapper">
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <button class="btn cyan waves-effect waves-light right" type="submit" name="submit" form="appointment-form">Book
                                                <i class="material-icons right">send</i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  </div>
  </div>
</div>

    <!-- MODAl -->
<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<?php  $this->load->view("User/common/dashboard_footer"); ?>

<!-- MODAl -->
 <script src="<?php echo base_url();?>theme/admin/app-assets/vendors/select2/select2.full.min.js"></script>
<script src="<?php echo base_url();?>theme/admin/app-assets/js/scripts/form-select2.min.js"></script>


<script>
 $(document).ready(function(){
    setTimeout(function(){
        $(".error").remove();
        $(".error").hide();
    }, 2000 );
    $(".select2").select2({
    dropdownAutoWidth: true,
    width: '100%'
});
});
</script>
<script>
	$(document).ready(function(){
		var date_input=$('.datepicker'); //our date input has the name "date"
		var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
		date_input.datepicker({
		    format: 'dd-mm-yyyy',
			todayHighlight: true,
			autoclose: true,
			changeMonth: true, // this will help you to change month as you like
            changeYear: true, // this will help you to change years as you like
            yearRange: "2000:2021"
		});
/**/
$('#pickup').datepicker({
        autoClose : true,
        format : 'dd mmm yyyy',
        yearRange : 1,
        minDate : new Date(),
        onSelect: function(el) {
        	const ell = new Date(el);
        $("#drop_off").datepicker({ minDate : ell})
        }
      });

      $("#drop_off").datepicker({
        autoClose : true,
        format : 'dd mmm yyyy'
      })
/**/
	});
</script>

<script>
    $(document).ready(function(){
        var wrapper = $('.field_wrapper'); //Input field wrapper
            var html  ='<div><form action="" method="POST" id="pet-form">';
                html += '<div class="input-field col m12 l12 s12">';
                html += '<select class="select2 browser-default" name="type_id" id="type-id" form="pet-form">';
                html += '<option value="1">DOG</option>';
                html += '<option value="2">CAT</option>';
                html +='</select>';
                html +='</div>';
                html +='<div class="input-field col m10 l10 s12">';
                html +='<label>Pet Name</label>';
                html +='<input type="text" name="pet_name" id="petname" form="pet-form" required>';
                html +='</div>';
                html +='<div class="input-field col m2 l2 s12">';
                html +='<label>Pet Weight</label>';
                html +='<input type="text" name="pet_weight" id="petweight" form="pet-form" required>';
                html +='</div>';
                html +='<div class="input-field col m12 s12">';
                html +='<p><label>GENDER </label></p>';
                html +='<p><label><input name="gender" class="gender" type="radio" value="Male-Not Neutered" checked="" form="pet-form"><span>Male-Not Neutered</span></label>';
                html +='<label><input name="gender" class="gender" type="radio" value="Female-Not Spayed" checked="" form="pet-form"><span>Female-Not Spayed</span></label>';
                html +='<label><input name="gender" class="gender" type="radio" value="Male Neutered" checked="" form="pet-form"><span>Male Neutered</span></label>';
                html +='<label><input name="gender" class="gender" type="radio" value="Female Spayed" checked="" form="pet-form"><span>Female Spayed</span></label></p>';
                html +='</div>';
                html +='<div class="input-field col m12 l12 s12">';
                html +='<select class="select2 browser-default" name="breed_id" id="breed_id">';
                html +='<?= $breeds?>';
                html +='</select>';
                html +='</div>'; 
                //html +='<input type="submit" name="addpet" id="pet-save" class="btn cyan waves-effect waves-light" value="Save">';
                html +='<button type="submit" id="pet-save" class="btn cyan waves-effect waves-light" name="Addpet" form="pet-form">save</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                html +='<input type="reset" name="cancel" class="btn red waves-effect waves-light  remove_button" value="Cancel" form="pet-form">';
                html +='</form></div>';
                
            // ADDD PET   
        $(".add_button").on('click',function(){
        $("#add_pet_btn_show").hide();
            $(wrapper).html(html);  
            $(".select2").select2({
                dropdownAutoWidth: true,
                width: '100%'
            });
        });
        
        $(wrapper).on('click', '.remove_button', function(e){
            e.preventDefault();
            $(this).parent('div').remove(); //Remove field html
           $("#add_pet_btn_show").show();
        });
        
        
        // save pet info
        $(document).on('click',"#pet-save",function(e){
           $("#formId").submit();
             var url = '<?php echo base_url()."AjaxAddPet";?>';
             var form = $("#pet-form");
             var pet_name = $("#petname").val();
             var pet_weight = $("#petweight").val();
             var gender = $(".gender").val();
             var breed_id = $("#breed_id").val();
             
             if(pet_name =="" || pet_name ==null || breed_id =="" || breed_id ==null || pet_weight =="" || pet_weight ==null || gender =="" || gender ==null) 
             {
                 alert("All fields are required");
             }else{
                      $.ajax({
                       url : url,
                       type : "POST",
                       cache:false,
                       dataType: 'json',
                       data : {"pet_name" : pet_name, 'pet_weight' : pet_weight , 'gender' : gender , 'breed_id' : breed_id},
                       success:function(res){
                            $('.field_wrapper').parent('div').remove(); //Remove field html
                            $("#add_pet_btn_show").show();
                            var returnedData = JSON.parse(JSON.stringify(res));
                           if(returnedData.status == 1)
                           {
                               var reshtml = '';
                               $.each(returnedData.data, function (i, v) {
                                   
                                   reshtml += '<div class="col s12 m12 l12">';
                                   reshtml +='<div class="col s12 m2 l2">';
                                   reshtml +='<label><input type="checkbox" name="pet_id[]" value="'+ v.pet_id +'" checked><span></span></label>';
                                   reshtml += '<img src="'+v.petlogo+'" height="50" width="50" class="pet-profile">';
                                   reshtml += '</div>';
                                   reshtml += '<div class="col s12 l2 m2">';
                                   reshtml += '<div class="petname"><b>'+ v.name +'</b></div>';  
                                   reshtml += '<div class="breed">'+ v.breedName +'</div>';
                                   reshtml += '</div>';
                                   reshtml += '<div class="col s12 l3 m3">';
                                   reshtml += '<div class="pet_edit_btn"><a href="javascript:void(0);" class="button" data-pet_id="'+v.pet_id+'" id="edit_pet_button">EDIT</a></div>';
                                   reshtml += '</div>';
                                   reshtml +='</div>';
                               })
                               $("#update-pets").html(reshtml);
                              
                               return false;
                               e.preventDefault();
                          
                           }
                           return false;
                           e.preventDefault();
                       }
                });
             }
             
    });
    
        $("#company_id").on('change',function(){
            var cid = $(this).val();
            if(cid !='' && cid !=null && cid !=NaN)
            {
                var url = '<?= base_url()."AjaxService"?>';        
                $.ajax({
                   url : url,
                   method : "GET",
                   data  : { 'cid' : cid},
                   cache : false,
                   success : function(response)
                            {
                                $("#service_id").html(response);
                            }
                });
            }
        });
        
       
       
       //EDIT Pet Button 
        $(document).on('click','#edit_pet_button',function(){
           //
           var pet_id = $(this).attr('data-pet_id');
           /*Edit fetch Json Ajax Data for edit to show the data */
           $.ajax({
              url : '<?= base_url()."AjaxEditPetInfo"?>',
              cache:false,
              method : "GET",
              data : { "pet_id" : pet_id},
              dataType: 'json',
              success : function(res){
                  var json = JSON.parse(JSON.stringify(res));
                  var edit_wrapper = $('.edit_wrapper');
                  var classname = "#pet_id"+pet_id;
                  $(classname).hide();
                  if(json.status == 1)
                  {
                      select1 =(json.data.gender == "Male-Not Neutered") ? "checked": "";
                      select2 =(json.data.gender == "Female-Not Spayed") ? "checked": "";
                      select3 =(json.data.gender == "Male Neutered") ? "checked": "";
                      select4 =(json.data.gender == "Female Spayed") ? "checked": "";
                      var div_id = "div_"+json.data.pet_id;
                       var editform = '';
                           editform ='<div id="'+div_id+'">';
                           editform +='<h4>Edit Pet</h4><hr>';
                           editform +='<form action="javscript:void(0);" method="post" name="editpetform" id="edit-pet-form">';
                           editform += '<input type="hidden" name="edit_pet_id" id="edit_pet_id" value="'+json.data.pet_id+'">';
                           editform +='<div class="input-field col m8 l8 s12">';
                           editform +='<input type="text" name="edit_pet_name" id="edit_pet_name" form="edit-pet-form" value="'+ json.data.name +'" required>';
                           editform +='<label>pet name</label>';
                           editform +='</div>';
                           editform +='<div class="input-field col m4 l4 s12">';
                           editform +='<input type="text" name="pet_weight" id="edit_pet_weight" form="edit-pet-form" value="'+ json.data.weight +'" required>';
                           editform +='<label>pet weight</label>';
                           editform +='</div>';
                           editform += '<div class="input-field col m4 l4 s12">';
                           editform +='<select class="select2 browser-default" name="breed_id" id="edit_breed_id" form="edit-pet-form">';
                           editform += json.breeds;
                           editform +='</select>';
                           editform += '</div>';
                           editform += '<div class="input-field col m4 l4 s12">';
                           editform +='<select class="select2 browser-default" name="color_mark_id" id="edit_color_mark_id" form="edit-pet-form">';
                           editform += json.color_mark;
                           editform +='</select>';
                           editform += '</div>';
                           editform += '<div class="input-field col m4 l4 s12">';
                           editform +='<select class="select2 browser-default" name="veterinary_id" id="edit_veterinary_id" form="edit-pet-form">';
                           editform += json.veterinary;
                           editform +='</select>';
                           editform += '</div>';
                           editform +='<div class="input-field col m12 s12">';
                           editform +='<p><label>GENDER </label></p>';
                           editform +='<p><label><input name="gender" class="edit_gender" type="radio" form="edit-pet-form" value="Male-Not Neutered" '+select1+'><span>Male-Not Neutered</span></label>';
                           editform +='<label><input name="gender" class="edit_gender" type="radio" form="edit-pet-form" value="Female-Not Spayed" '+select2+' ><span>Female-Not Spayed</span></label>';
                           editform +='<label><input name="gender" class="edit_gender" type="radio" form="edit-pet-form" value="Male Neutered" '+select3+' ><span>Male Neutered</span></label>';
                           editform +='<label><input name="gender" class="edit_gender" type="radio" form="edit-pet-form" value="Female Spayed" '+select4+'><span>Female Spayed</span></label></p>';
                           editform +='</div>';
                           editform += '<div class="input-field col m12 s12">';
                           editform += '<input type="text" name="edit_pet_bdate" class="datepicker" form="edit-pet-form" value="'+json.data.bdate+'" id="edit_pet_bdate">';
                           editform +='<label>Birthdate</label>';
                           editform += '</div>';
                           editform +='<button type="submit" id="pet-update" data-id="'+json.data.pet_id+'" class="btn cyan waves-effect waves-light" name="Updatepet" form="edit-pet-form">save</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                           editform +='<input type="reset" name="cancel" class="btn red waves-effect waves-light  remove_edit_button" data-id="'+json.data.pet_id+'" value="Cancel">';
                 
                           editform +='</form>';
                           editform +='</div>';
                           $(edit_wrapper).append(editform);  
                           
                           /**/
                           var date_input=$('.datepicker'); //our date input has the name "date"
                    		var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
                    		date_input.datepicker({
                    		    minDate: new Date(),
                    			format: 'dd-mm-yyyy',
                    			todayHighlight: true,
                    			autoclose: true,
                    			changeMonth: true, // this will help you to change month as you like
                                changeYear: true, // this will help you to change years as you like
                                yearRange: "2000:2021"
                    		});
                           /**/
                           $(".select2").select2({
                                dropdownAutoWidth: true,
                                width: '100%'
                            });
                  }else{
                      
                  }
              }
           });
           
              
        });
        var edit_wrapper = $('.edit_wrapper');
        $(edit_wrapper).on('click', '.remove_edit_button', function(e){
             var pet_id = $(this).attr('data-id');
             var classname = "#pet_id"+pet_id;
             //$(this).parent('div').remove(); //Remove field html
             var divid = "#div_"+pet_id;
             
             
             $(divid).remove();
             
           $(classname).show();
            e.preventDefault();
            
        });
        
        
        $(document).on('click','#pet-update',function(e){
            
            var petid = $(this).attr('data-id');
            
            var edit_name = $("#edit_pet_name").val();
            var edit_weight = $("#edit_pet_weight").val();
            var edit_gender = $(".edit_gender").val();
            var breed_id = $("#edit_breed_id").val();
            var veterinary_id = $("#edit_veterinary_id").val();
            var color_mark_id = $("#edit_color_mark_id").val();
            var bdate = $("#edit_pet_bdate").val();
            
            if((edit_name =="" || edit_name == null) || (edit_weight =="" || edit_weight == null) || (edit_gender =="" || edit_gender == null) || 
               (breed_id =="" || breed_id == null) || (veterinary_id =="" || veterinary_id == null) || (color_mark_id =="" || color_mark_id == null))
            {
                alert("all fields are required ");
            }else{
                var passdata = { "pet_id" : petid , "name" : edit_name , "weight" : edit_weight , "bdate" : bdate ,"edit_gender" : edit_gender , "breed_id" : breed_id , "veterinary_id" : veterinary_id ,"color_mark_id" :color_mark_id};
                    $.ajax({
                        url : '<?= base_url()."AjaxUpdatePet"?>',
                        method : "POST",
                        dataType : "json",
                        cache : false,
                        data : passdata,
                        success : function(res){
                            var json = JSON.parse(JSON.stringify(res));
                            if(json.status == 1)
                            {
                                //alert("Pet updated successfully");
                                var pethtml = '';
                               $.each(json.pets, function (i, v) {
                                   
                                   pethtml += '<div class="col s12 m12 l12">';
                                   pethtml +='<div class="col s12 m2 l2">';
                                   pethtml +='<label><input type="checkbox" name="pet_id[]" value="'+ v.pet_id +'" checked><span></span></label>';
                                   pethtml += '<img src="'+v.petlogo+'" height="50" width="50" class="pet-profile">';
                                   pethtml += '</div>';
                                   pethtml += '<div class="col s12 l2 m2">';
                                   pethtml += '<div class="petname"><b>'+ v.name +'</b></div>';  
                                   pethtml += '<div class="breed">'+ v.breedName +'</div>';
                                   pethtml += '</div>';
                                   pethtml += '<div class="col s12 l3 m3">';
                                   pethtml += '<div class="pet_edit_btn"><a href="javascript:void(0);" class="button" data-pet_id="'+v.pet_id+'" id="edit_pet_button">EDIT</a></div>';
                                   pethtml += '</div>';
                                   pethtml +='</div>';
                                   
                               })
                               $("#update-pets").html(pethtml);
                               $(".edit_wrapper").parent('div').remove(); //Remove field html
                               window.location.reload();
                            }else{
                                alert("failed");
                            }
                            
                            return false;
                            e.preventDefault();
                        }
                    })
            }
        
        });
    });
</script>