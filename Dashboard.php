<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends CI_Controller {
	function __construct() 
	{
    	parent::__construct();
    	
    	if($this->session->userdata("authenticated")==FALSE)  
    	{
    		redirect("login");
    	}
    	if($this->session->userdata("usertpe")!=1){
    		redirect();
    	}
	}
	public function index()
	{
		//print_r($this->session->userdata());exit;
		$this->load->view('admin/home');
	}
	public function user_list(){
		if(isset($_POST['submit'])){
			
			$id = $this->input->post('hid');
			$name = $this->input->post('name');
			$email = $this->input->post('email');
			$mobile = $this->input->post('mobile');
			$msg = $this->input->post('message');
			$password = md5($this->input->post('password'));
			//$city = $this->input->post('city');
			//print_r($city); die;
			//$city_detail = implode(',', $city);
			//print_r($city_detail); die;
			$data = array('name'=>$name,'email'=>$email,'mobile'=>$mobile, 'message'=>$msg, 'password'=>$password);
			$where = array('id'=>$id);
			$resp = $this->admin_model->update_all($where, 'user', $data);
            $this->mail_password($email,$this->input->post('password'));
			$this->session->set_flashdata('msg', 'User Update Successfully');
			//echo $this->db->last_query(); die;
		}
		$this->db->order_by('id','DESC');
		$data['user'] = $this->admin_model->get_result('user',array('type!='=>1,'status'=>1));
		// echo count($data['user']);
	// echo '<pre>';print_r($data['user']);exit;
		//$data['citylist'] = $this->admin_model->get_result('city',array());
		$this->load->view('admin/user_list',$data);
	}
	public function user_view(){
		 $this->load->view('admin/user_add');
	}
	public function add_user()
	{
			$name = $this->input->post('name');
			$email = $this->input->post('email');
			$checkEmailresult = $this->admin_model->get_row('user',array('email'=>$email));
			if(isset($checkEmailresult) && $checkEmailresult['email']!=''){
				$this->session->set_flashdata('error', 'Email id already in use.');
				redirect('add-user');
			}else{

			$mobile = $this->input->post('mobile');
			$password = md5($this->input->post('password'));
			$data = array('name'=>$name,'email'=>$email,'mobile'=>$mobile,'password'=>$password);
			$resp = $this->admin_model->add('user',$data);
			$message = array('password'=>$this->input->post('password'),"url"=>"http://webilogic.com.md-in-16.webhostbox.net/new_realestate/",'email'=>$email);
			$this->sendmail_common($email,'New Registration by Real Estate',$message);
			$this->session->set_flashdata('msg', 'User Successfully Add');
			redirect('allusers');
			}
		
       
	}
	function delete_user(){
		$id = $this->uri->segment(3);
		$this->db->where('id',$id);
		$this->db->delete('user');
		$this->session->set_flashdata('msg','User delete successfully');
		redirect('allusers');
	}
	public function logout()
	{
		$this->session->sess_destroy();
		redirect();
	}
	public function property($id=''){
			//$id = array('id'=>$this->input->post('hid'));
		if($id!=''){
			$data['update_data'] = $this->db->query("SELECT * FROM `property_detail` where id = $id")->row_array();
			//echo '<pre>'; print_r($data['update_data']); die;
			$data['formurl']='property_edit';
		}
		if(!empty($_POST)){
			//echo '<pre>'; print_r($_FILES); die;
			$config['upload_path']          = './upload/images';
                $config['allowed_types']    = 'gif|jpg|jpeg|png';
            $this->load->library('upload', $config);
            $this->upload->do_upload('single_image');
            $upload_data = $this->upload->data();
////////////////////////////////////////////////////////////////////////////////////
         	$filesCount = count($_FILES['multiple_image']['name']);
				for($i = 0; $i < $filesCount; $i++){
					$_FILES['file']['name']     = $_FILES['multiple_image']['name'][$i];
					$_FILES['file']['type']     = $_FILES['multiple_image']['type'][$i];
					$_FILES['file']['tmp_name'] = $_FILES['multiple_image']['tmp_name'][$i];
					$_FILES['file']['error']     = $_FILES['multiple_image']['error'][$i];
					$_FILES['file']['size']     = $_FILES['multiple_image']['size'][$i];
				                // File upload configuration
				                $uploadPath = 'upload/multimg/';
				                $config['upload_path'] = $uploadPath;
				                $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf';
				                
				                // Load and initialize upload library
				                $this->load->library('upload', $config);
				                $this->upload->initialize($config);
				                
				                // Upload file to server
				                $_FILES['file']['name']=rand().'-'.$_FILES['file']['name'];
				                
				                if($this->upload->do_upload('file')){
				                    // Uploaded file data
				                    $fileData = $this->upload->data();
				                    $uploadData[$i]['file_name'] = $fileData['file_name'];
				                    $uploadData[$i]['uploaded_on'] = date("Y-m-d H:i:s");
				                }
				            }
				          if(!empty($uploadData)){
                           foreach ($uploadData as $key => $value) {
                           	 
                           	 $multiple_image[]=$value["file_name"];
                            
                           }
                           
                           $updatedata["multiple_image"]=implode(',',$multiple_image);
		                 }
			$input = $this->input->post(); 
			//print_r($input);  die;
		//$citybyid = $this->db->query("SELECT city_name  FROM `city` WHERE `id` = '".$input['city_name']."'")->result_array();
			//echo '<pre>'; print_r($citybyid); 
			//echo $this->db->last_query();
			//$uniqname =  substr($citybyid[0]['city_name'] ,0,4);
			$maxid = $this->db->query("SELECT max(id)+1 FROM `property_detail`")->row_array();
			//print_r($maxid); die;
			//$genrateid = $uniqname .$maxid['max(id)+1'].'ILB';
			//die;
			$data = array('title'=>$input['title'], 
				'title1'=>$input['title2'],
				'description'=>$input['property_description'],
				'single_img'=>$upload_data['file_name'],
				'multi_img'=>$updatedata['multiple_image'],
				'ptype_id'=>$input['property_type'],
				//'pcity_id'=>$input['city_name'],
				//'number'=>$input['area'],
				'min_price'=>$input['minprice'],
				'max_price'=>$input['maxprice'],
				'min_surface'=>$input['minsurface'],
				'max_surface'=>$input['maxsurface'],
				'type_t1'=>$input['t1'],
				'type_t2'=>$input['t2'],
				'country'=>$input['country'],
				'region'=>$input['region'],
				'departement'=>$input['departement'],
				'tax_system'=>$input['tax_system'],
				'property_date'=>$input['property_date']
				
				//'uniqid'=> $genrateid
					);
		// echo '<pre>'; print_r($data); die;
			$resp = $this->admin_model->add('property_detail',$data);
			//echo '<pre>'; print_r($resp); die;
			$this->session->set_flashdata('msg','Property Add Successfully');
			redirect('Dashboard/property_list');
		}
		$data['type_pro'] = $this->db->get('property_type')->result_array();
// 		$data['city'] = $this->db->get('city')->result_array();
        $data['statelist'] = $this->db->get('all_states')->result_array();
        $data['region_tb'] = $this->db->get('region_tb')->result_array();
		$this->load->view('admin/add_property',$data);
	}

	function property_edit()
	{
		if(isset($_POST['submit'])){
				$config['upload_path']          = './upload/images';
                $config['allowed_types']        = 'gif|jpg|jpeg|png';
               
                $this->load->library('upload', $config);
                $this->upload->do_upload('single_image');
                $upload_data = $this->upload->data();
                //echo '<pre>'; print_r($upload_data); 
////////////////////////////////////////////////////////////////////////////////////
                 $filesCount = count($_FILES['multiple_image']['name']);
				            for($i = 0; $i < $filesCount; $i++){
				                $_FILES['file']['name']     = $_FILES['multiple_image']['name'][$i];
				                $_FILES['file']['type']     = $_FILES['multiple_image']['type'][$i];
				                $_FILES['file']['tmp_name'] = $_FILES['multiple_image']['tmp_name'][$i];
				                $_FILES['file']['error']     = $_FILES['multiple_image']['error'][$i];
				                $_FILES['file']['size']     = $_FILES['multiple_image']['size'][$i];
				                
				                // File upload configuration
				                $uploadPath = 'upload/multimg/';
				                $config['upload_path'] = $uploadPath;
				                $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf';
				                
				                // Load and initialize upload library
				                $this->load->library('upload', $config);
				                $this->upload->initialize($config);
				                
				                // Upload file to server
				                $_FILES['file']['name']=rand().'-'.$_FILES['file']['name'];
				                if($this->upload->do_upload('file')){
				                    // Uploaded file data
				                    $fileData = $this->upload->data();
				                    $uploadData[$i]['file_name'] = $fileData['file_name'];
				                    $uploadData[$i]['uploaded_on'] = date("Y-m-d H:i:s");
				                }
				            }
				          if(!empty($uploadData)){
                           foreach ($uploadData as $key => $value) {
                           	 
                           	 $multiple_image[]=$value["file_name"];
                            
                           }
                           
                           $updatedata["multiple_image"]=implode(',',$multiple_image);
		                 }
		                 		//echo  $updatedata["multiple_image"]; die;
                 
			$input = $this->input->post(); 
			
			$data = array('title'=>$input['title'], 
				'title1'=>$input['title2'],
				'description'=>$input['property_description'],
				//'single_img'=>$upload_data['file_name'],
				//'multi_img'=>$updatedata['multiple_image'],
				'ptype_id'=>$input['property_type'],
				//'pcity_id'=>$input['city_name'],
				//'number'=>$input['area'],
				'min_price'=>$input['minprice'],
				'max_price'=>$input['maxprice'],
				'min_surface'=>$input['minsurface'],
				'max_surface'=>$input['maxsurface'],
				'type_t1'=>$input['t1'],
				'type_t2'=>$input['t2'],
				'property_date'=>$input['property_date'],
				'country'=>$input['country'],
				'region'=>$input['region'],
				'departement'=>$input['departement'],
				'tax_system'=>$input['tax_system'],
					);
			if($upload_data['file_name']!=''){
				$data['single_img'] = $upload_data['file_name'];
			}
			if(isset($updatedata["multiple_image"])){
				$data['multi_img'] = $updatedata["multiple_image"];
			}
			//print_r($data); die;
			$where = array('id'=>$input['eid']);
			$resp = $this->admin_model->update_all($where, 'property_detail', $data);
			redirect('all-property');
		}
	}
	
	
	function getcity(){
		$statecode = $_GET['id'];
		$data['city'] = $this->db->where(array('state_code'=>$statecode))->get('all_cities')->result_array();
		$html = '';
		foreach($data['city'] as $r){
			$html .= '<option value="'.$r['id'].'">'.$r['city_name'].'</option>';
		}
		echo $html;
		
	}

	public function property_list(){
		if(isset($_POST['submit'])){
				$config['upload_path']          = './upload/images';
                $config['allowed_types']        = 'gif|jpg|jpeg|png';
               
                $this->load->library('upload', $config);
                $this->upload->do_upload('single_image');
                $upload_data = $this->upload->data();
                //echo '<pre>'; print_r($upload_data); 
////////////////////////////////////////////////////////////////////////////////////
                 $filesCount = count($_FILES['multiple_image']['name']);
				            for($i = 0; $i < $filesCount; $i++){
				                $_FILES['file']['name']     = $_FILES['multiple_image']['name'][$i];
				                $_FILES['file']['type']     = $_FILES['multiple_image']['type'][$i];
				                $_FILES['file']['tmp_name'] = $_FILES['multiple_image']['tmp_name'][$i];
				                $_FILES['file']['error']     = $_FILES['multiple_image']['error'][$i];
				                $_FILES['file']['size']     = $_FILES['multiple_image']['size'][$i];
				                
				                // File upload configuration
				                $uploadPath = 'upload/multimg/';
				                $config['upload_path'] = $uploadPath;
				                $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf';
				                
				                // Load and initialize upload library
				                $this->load->library('upload', $config);
				                $this->upload->initialize($config);
				                
				                // Upload file to server
				                $_FILES['file']['name']=rand().'-'.$_FILES['file']['name'];
				                if($this->upload->do_upload('file')){
				                    // Uploaded file data
				                    $fileData = $this->upload->data();
				                    $uploadData[$i]['file_name'] = $fileData['file_name'];
				                    $uploadData[$i]['uploaded_on'] = date("Y-m-d H:i:s");
				                }
				            }
				          if(!empty($uploadData)){
                           foreach ($uploadData as $key => $value) {
                           	 
                           	 $multiple_image[]=$value["file_name"];
                            
                           }
                           
                           $updatedata["multiple_image"]=implode(',',$multiple_image);
		                 }
		                 		//echo  $updatedata["multiple_image"]; die;
                 
			$input = $this->input->post(); 
			
			$data = array('title'=>$input['title'], 
				'title1'=>$input['title2'],
				'description'=>$input['property_description'],
				//'single_img'=>$upload_data['file_name'],
				//'multi_img'=>$updatedata['multiple_image'],
				'ptype_id'=>$input['property_type'],
				//'pcity_id'=>$input['city_name'],
				//'number'=>$input['area'],
				'min_price'=>$input['minprice'],
				'max_price'=>$input['maxprice'],
				'min_surface'=>$input['minsurface'],
				'max_surface'=>$input['maxsurface'],
				'type_t1'=>$input['t1'],
				'type_t2'=>$input['t2'],
				'property_date'=>$input['property_date'],
				'country'=>$input['country'],
				'region'=>$input['region'],
				'departement'=>$input['departement'],
				'tax_system'=>$input['tax_system'],
					);
			if($upload_data['file_name']!=''){
				$data['single_img'] = $upload_data['file_name'];
			}
			if(isset($updatedata["multiple_image"])){
				$data['multi_img'] = $updatedata["multiple_image"];
			}
			//print_r($data); die;
			$where = array('id'=>$input['hid']);
			$resp = $this->admin_model->update_all($where, 'property_detail', $data);
		}
		$data['res'] = $this->admin_model->get_result('property_detail',NULL,'*');
		$data['type'] = $this->db->get('property_type')->result_array();
		$data['city'] = $this->db->get('city')->result_array();
		$this->load->view('admin/allproperty_list',$data);
	}

	public function delete_property(){
		$id = $this->uri->segment(3);
		$this->admin_model->deleteData('property_detail',array('id'=>$id));
		$this->session->set_flashdata('msg','Data delete successfully');
		redirect('Dashboard/property_list'); 
	}
	public function user_assign(){
		
		$data['property'] = $this->admin_model->get_result('property_detail',array());
		
		if(isset($_POST['submit'])){
			$input = $this->input->post();
			
			$property = $input['property'];
			$b = implode(',', $property);
			$data = array('user_id'=>$input['user_id'],
						'property_id'=>$b,
						'from_data'=>($input['from_date']!='') ?$input['from_date'] :date('Y-m-d'),
						'to_date'=>($input['to_date']!='') ? $input['to_date'] : date('Y-m-d',strtotime('+3 days'))
					);
			
			$idd = $this->db->query("SELECT id,user_id FROM assign_property WHERE user_id=".$data['user_id'])->row_array();
			
			
			if($data['user_id']==$idd['user_id']){
				$resp = $this->admin_model->update_all(array('id'=>$idd['id']), 'assign_property', $data);
			}else{
			
			$resp = $this->admin_model->add('assign_property',$data);
			}
			$this->session->set_flashdata('msg','Property Assign Successfully');
			redirect('Dashboard/user_assign_list');
		}
			
						$this->db->where('type=3');
		$data['user'] = $this->db->get('user')->result_array();
		
		$this->load->view('admin/assign_user',$data);
	}
	public function assign_agent(){
		if(isset($_POST['submit'])){
			$input = $this->input->post();
			$data = array('user_id'=>$input['user_id'],
						  'property_type_id'=>$input['property_id'],
						'from_data'=>($input['from_date']!='') ?$input['from_date'] :date('Y-m-d'),
						'to_date'=>($input['to_date']!='') ? $input['to_date'] : date('Y-m-d',strtotime('+3 days'))
					);
			$idd = $this->db->query("SELECT id,user_id FROM assign_property WHERE user_id=".$data['user_id'])->row_array();
			
			
			if($data['user_id']==$idd['user_id']){
				$resp = $this->admin_model->update_all(array('id'=>$idd['id']), 'assign_property', $data);
			}else{
			
			$resp = $this->admin_model->add('assign_property',$data);
			}
			$this->session->set_flashdata('msg','Property Assign Successfully');
			redirect('Dashboard/agent_assign_list_data');
		}
						$this->db->where('type=3');
		$data['user'] = $this->db->get('user')->result_array();
		//$data['propertytype']=$this->db->get('property_type')->result_array();
		$data['propertytype']=$this->db->query('SELECT property_type.type,property_detail.ptype_id FROM `property_detail` join property_type ON property_detail.ptype_id=property_type.id group by property_detail.ptype_id')->result_array();
		$this->load->view('admin/assign_agent',$data);
	}
	public function agent_assign_list_data(){
		if(!empty($_POST)){
				$input = $this->input->post();
			$data = array('user_id'=>$input['user_name'],
						'property_type_id'=>$input['type_name'],
						'from_data'=>$input['from_date'],
						'to_date'=>$input['to_date'],
					);
			//echo '<pre>'; print_r($data); 
			$where = array('id'=>$input['hid']);
			$resp = $this->admin_model->update_all($where, 'assign_property', $data);
			//echo $this->db->last_query(); die;
			$this->session->set_flashdata('msg','Update Agent Assign Successfully');
		}
		$data['assign']= $this->db->get('assign_property')->result_array();
		$data['user'] = $this->db->where('type=3')->get('user')->result_array();
		$data['property'] = $this->db->get('property_detail')->result_array();
		$data['ptype'] = $this->db->get('property_type')->result_array();
		$this->load->view('admin/assign_agentlist',$data);
	}
	public function user_assign_list(){
		if(!empty($_POST)){
				$input = $this->input->post();
			//$property = $input['property'];
				//echo '<pre>'; print_r($_POST); die;
			if(isset($input['property']) && $input['property']!=''){
			$b = implode(',', $input['property']);
					} 
			else{
			$b='';
			}
			//echo $b; die;
			$data = array('user_id'=>$input['user_name'],
						'property_id'=>$b,
						'from_data'=>$input['from_date'],
						'to_date'=>$input['to_date'],
					);
			//echo '<pre>'; print_r($data); 
			$where = array('id'=>$input['hid']);
			$resp = $this->admin_model->update_all($where, 'assign_property', $data);
			//echo $this->db->last_query(); die;
			$this->session->set_flashdata('msg','Update User Assign Successfully');
				
		}
		
		$data['assign']= $this->db->get('assign_property')->result_array();
						//$this->db;	
		$data['user'] = $this->db->where('type=3')->get('user')->result_array();
		//echo $this->db->last_query();
		$data['property'] = $this->db->get('property_detail')->result_array();
		$data['ptype'] = $this->db->get('property_type')->result_array();
		
		//print_r($data['assign']); die;
		$this->load->view('admin/assign_list',$data);
	}
	public function delete_user_assign(){
		$id = $this->uri->segment(3);
		$this->admin_model->deleteData('assign_property',array('id'=>$id));
		$this->session->set_flashdata('msg','Data delete successfully');
		redirect('Dashboard/agent_assign_list_data'); 
	}
	public function property_type(){
		if(!empty($_POST)){
			$input = $this->input->post();
			//print_r($input);
			$data = array('type'=>$input['ptype']);
			$resp = $this->admin_model->add('property_type',$data);
			$this->session->set_flashdata('msg','Property Type Add Successfully');
			redirect('Dashboard/property_type');
		}
		$data['type'] = $this->db->get('property_type')->result_array();
		$this->load->view('admin/property_type_add',$data);
		
	}
	public function delete_property_type(){
		$id = $this->uri->segment(3);
		$this->admin_model->deleteData('property_type',array('id'=>$id));
		$this->session->set_flashdata('msg','Property Type delete successfully');
		redirect('Dashboard/property_type'); 
	}
	public function update_ptype(){
		$input = $this->input->post();
			//echo '<pre>'; print_r($input); die;
		
			$data = array('type'=>$input['type'],
					);
			$where = array('id'=>$input['hid']);
			$resp = $this->admin_model->update_all($where, 'property_type', $data);
			//echo $this->db->last_query(); die;
			$this->session->set_flashdata('msg','Update Property Type Successfully');
			redirect('Dashboard/property_type');
	}
	public function message_add(){
		if(!empty($_POST)){
			$this->db->where(array('admin_view'=>1));
			$this->db->update('chatting',array('admin_view'=>0));
			$to = $this->input->post('to');
			$from = $this->input->post('from');
			$msg = $this->input->post('message');
			$cdate = $this->input->post('createdate');
		
			$data = array('to'=>$from,'from'=>$to,'message'=>$msg);
			
			//print_r($data);exit;
			$resp = $this->admin_model->add('chatting',$data);
			//echo $this->db->last_query(); die;
			$this->session->set_flashdata('msg','Message Send Successfully');
			redirect('Dashboard/message_add');
		}
						 $this->db->order_by('id','ASC');
		$data['detail']= $this->db->get('chatting')->result_array();
		$data['chat']= $this->db->query("SELECT * FROM `chatting` group by `from` ORDER BY `created_at` DESC")->result_array();
		//echo '<pre>'; print_r($data['detail']); die;
		
		$this->load->view('admin/add_message',$data);
	}
	
	
	function mail_test(){
		
	    $message = "Your email body text goes here";
	    $config['mailtype'] = "html";
	    $ci = & get_instance();
	    $ci->load->library('email', $config);
	    $ci->email->set_newline("\r\n");
	    $ci->email->from("deepakrathore.nuance@gmail.com");
	    $ci->email->to("deepakrathore.nuance@gmail.com");
	    $ci->email->subject("Resume from JobsBuddy 465");
	    $ci->email->message($message);
	    
	    echo $ci->email->send();  
	    echo $this->email->print_debugger();
	}
	
	
	
	function mail_password($email,$password){
		
	    $message = "Your Account is active.Please login with your register email id and password:- ".$password;
	    $config['mailtype'] = "html";
	    $ci = & get_instance();
	    $ci->load->library('email', $config);
	    $ci->email->set_newline("\r\n");
	    $ci->email->from("deepakrathore.nuance@gmail.com");
	    $ci->email->to($email);
	    $ci->email->subject("Permission For Login");
	    $ci->email->message($message);
	    
	    echo $ci->email->send();  
	    echo $this->email->print_debugger();
	}
	
	public function add_featured(){
	    
	    if(isset($_POST['submit'])){
	        
	        $config['upload_path']          = './upload/featuredpro';
                $config['allowed_types']        = 'gif|jpg|jpeg|png';
                $config['max_size']             = 1000;
                $config['max_width']            = 2024;
                $config['max_height']           = 1768;
                $this->load->library('upload', $config);
                $this->upload->do_upload('fimage');
                $upload_data = $this->upload->data();
                
              //  print_r($this->upload->display_errors());
               // exit;
               
              // echo'<pre>'; print_r($data['result']);die;
                
                  $data = array('title'=>$this->input->post('title'),
              
                            'image'=>$upload_data['file_name'],
                );
                 // print_r($data);die;
                
                 $this->admin_model->add('fetured',$data);
                 redirect('Dashboard/add_featured');
	    }
	    
	    $this->load->view('admin/featured_add');
	}
	
	
	public function featured_list(){
	    
	    $data['featured']=$this->db->get(' fetured')->result_array();
	    
	    //echo '<pre>'; print_r($data['featured']); die;
	    $this->load->view('admin/featured_list');
	}
	public function agent_add(){
		if(isset($_POST['submit'])){
			$input=$this->input->post();
			$data=array('name'=>$input['name'],
						'email'=>$input['email'],
						'mobile'=>$input['mobile'],
				);
			$resp = $this->admin_model->add('agent',$data);
			$this->session->set_flashdata('msg', 'Agent Successfully Add');
			redirect('dashboard/agent_list');
		}
		$this->load->view('admin/agent_add');
	}
	public function agent_list(){
		if(isset($_POST['submit'])){
			$input=$this->input->post();
			//print_r($_POST); die;
			$hid=$this->input->post('hid');
			$id=array('id'=>$hid);
			$data=array('name'=>$input['name'],
						'email'=>$input['email'],
						'mobile'=>$input['mobile']
				);
			$resp = $this->admin_model->update_all($id, 'agent', $data);
			//echo $this->db->last_query(); die;
		}
		$data['agentdata']=$this->db->get('agent')->result_array();
		//print_r($data); die;
		$this->load->view('admin/agents_list',$data);
	}
	public function delete_agent(){
		$id = $this->uri->segment(3);
		$this->admin_model->deleteData('agent',array('id'=>$id));
		$this->session->set_flashdata('msg','Data delete successfully');
		redirect('Dashboard/agent_list'); 
	}

	public function proposal_list(){

		$data['pro_list']=$this->db->get('proposal')->result_array();
		//echo '<pre>'; print_r($data['pro_list']); die;
		$this->load->view('admin/proposal_page_list',$data);
	}

	public function delete_proposal(){

		$id = $this->uri->segment(3);
		//$where = array('id'=>$id);
		$where = ['id'=>$id];

		$this->admin_model->deleteData('proposal',$where);
		redirect('dashboard/proposal_list');
	}

	public function financement_formdetail(){

		$data['funding_list']=$this->db->order_by('id','DESC')->get('funding')->result_array();
		//echo '<pre>'; print_r($data['pro_list']); die;
		$this->load->view('admin/financement_formdetail',$data);
	}

	public function formulairelist(){

		$data['formulaire_list']=$this->db->order_by('id','DESC')->get('formulaire')->result_array();
		//echo '<pre>'; print_r($data['pro_list']); die;
		$this->load->view('admin/formulairelist',$data);
	}

	public function declarer_formlist(){

		$data['declarer_list']=$this->db->order_by('id','DESC')->get('declarer_form')->result_array();
		//echo '<pre>'; print_r($data['pro_list']); die;
		$this->load->view('admin/declarer_formlist',$data);
	}

	public function theme_setting(){
		$data = array();
		if(isset($_POST['submit'])){
			if(isset($_FILES['logo']['name']) && $_FILES['logo']['name']!=''){
				$config['upload_path']          = './upload/logo';
                $config['allowed_types']        = 'jpg|jpeg|png';
                $config['file_name'] 			= time().str_replace(' ','-',$_FILES['logo']['name']);
                $this->load->library('upload', $config);
                if(!$this->upload->do_upload('logo')){
					$wrong=$this->upload->display_errors();
				 	$this->session->set_flashdata('error', $wrong);
				 	redirect('dashboard/theme_setting');
				}else{
                	$upload_data = $this->upload->data();
                	$updatedata['logo'] = $upload_data['file_name'];
            	}
			}
			$updatedata['color1'] = $this->input->post('color1');
			$updatedata['color2'] = $this->input->post('color2');
			$this->admin_model->update_all(array('id'=>$this->input->post('agent_id')),'user',$updatedata);
			// echo $this->db->last_query();exit;
		}
		$data['agentlist'] = $this->admin_model->get_result('user',array('status'=>1,'type'=>3));
		$this->load->view('admin/theme_setting',$data);
	}

	function bulkuploadproducts(){
		if(isset($_POST['submit'])){
			$file = fopen($_FILES["bulkupload"]["tmp_name"],"r");
			$result=array();
			$insertdata=array();
			$i=0;
			while ($row = fgetcsv($file)) { 
				$result[]=$row;
				$i++;
			} 
			fclose($file);
			$header=$result[0];
			//echo '<pre>'; print_r($header);
			$result = array_slice($result,1);
			foreach ($result as $key => $value) {
			   foreach ($value as $subkey => $subvalue) {
			      $insertdata[$key][$header[$subkey]]=$subvalue;
			   }
			}
			//echo "<pre>";print_r($insertdata);die;
			foreach ($insertdata as $insertkey => $insertvalue) {
				//echo '<pre>'; print_r($insertvalue);
			}
			exit;
		}
		// redirect('Dashboard/bulkuploadproducts');
		$this->load->view('admin/bulkupload');
	}

	public function sendmail_common($to,$subject,$msg)
	{
		$this->load->library('email');
		$this->email->from('bhushannuance@gmail.com', 'Real Estate');
		$this->email->to($to);
		$this->email->set_mailtype('html');
		$this->email->subject($subject);
		$data['msg'] = $msg;
		$mesg = $this->load->view('frontend/email_template',$data,true);
		$this->email->message($mesg);
		if($this->email->send()){
			return true;
		}else{
			return $this->email->print_debugger();
		}
	}
}
