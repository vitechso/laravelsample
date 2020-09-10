<?php
error_reporting(0);
//error_reporting(E_ALL); 
//ini_set('display_errors', 1); 
    /* 
        This is an example class script proceeding secured API
        To use this class you should keep same as query string and function name
        Ex: If the query string value rquest=delete_user Access modifiers doesn't matter but function should be
             function delete_user(){
                 You code goes here
             }
        Class will execute the function dynamically;
        
        usage :
        
            $object->response(output_data, status_code);
            $object->_request   - to get santinized input   
            
            output_data : JSON (I am using)
            status_code : Send status message for headers
            
        Add This extension for localhost checking :
            Chrome Extension : Advanced REST client Application
            URL : https://chrome.google.com/webstore/detail/hgmloofddffdnphfgcellkdfbfbjeloo
    */
    
    require_once("Rest.inc.php");
    define('SITE_URL', 'http://companies.worktoday.in/');
    //define('DEAL_IMAGE_URL', 'resources/images/deals/');
    //define('USER_IMAGE_URL', 'resources/images/users/');

    class API extends REST {
    
        public $data = "";
        public $per_page = 10;
        const DB_SERVER = "workcompanies.db.11995266.f42.hostedresource.net";
        const DB_USER = "workcompanies";
        const DB_PASSWORD = "Year2019#@";
        const DB = "workcompanies";
        
        private $db = NULL;
        
        public function __construct(){
            parent::__construct();              // Init parent contructor
            $this->dbConnect(); 
            date_default_timezone_set("Asia/Calcutta");                 // Initiate Database connection
        }
        
        /*
         *  Database connection 
        */
        private function dbConnect(){
            $this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
            if($this->db)
                mysql_select_db(self::DB,$this->db);
                mysql_query('SET CHARACTER SET utf8');
        }
        
        /*
         * Public method for access api.
         * This method dynmically call the method based on the query string
         *
         */
        public function processApi(){
            $func = strtolower(trim(str_replace("/","",$_REQUEST['request'])));
            if((int)method_exists($this,$func) > 0)
                $this->$func();
            else
                $this->response('',404);                // If the method not exist with in this class, response would be "Page not found".
        }
 /*user login api */

 //image url companies.worktoday.in/resources/uploads/
private function login()
{
    
    $email=$this->_request['email'];        
    $password=$this->_request['password']; 
    $token=$this->_request['token'];      
    $device_type=$this->_request['device_type']; 
        if(empty($email) || empty($password))
            {
                $result['status']='0';
                $result['message']="Email and password cannot be blank";
                $this->response($this->json($result), 200);
            }
            $sql_hash = mysql_query("SELECT U.*,tc.CompanyFullname FROM users U,tbl_companies tc WHERE U.UserCompanyID=tc.CompanyID and  (email = '$email' or username='$email' ) LIMIT 1 ", $this->db);
            if(mysql_num_rows($sql_hash) > 0)
            {
                $result_hash = mysql_fetch_array($sql_hash, MYSQL_ASSOC);
               $company_id= $result_hash['UserCompanyID'];
               $id= $result_hash['id'];
               //print_r($company_id);die;
                if($result_hash['activated'] != '1')
                {
                    $result['status'] = '0';
                    $result['message']="Account not activated!";
                    $this->response($this->json($result), 200);
                }

                /* default admin entry*/

              
                $hash_data = $result_hash['password']; 
                $check = $this->CheckPassword($password, $hash_data);
                $updatetoken=mysql_query("update users set token='".$token."',device_type='".$device_type."' where email='".$email."'",$this->db);
                if($check)
                {
                    $result['UserID'] = $result_hash['id'];
                    $result['Username'] = $result_hash['username'];
                    $result['Firstname'] = $result_hash['first_name'];
                    $result['Lastname'] = $result_hash['last_name'];
                    $result['City'] = $result_hash['city'];
                    $result['State'] = $result_hash['state'];
                    $result['country'] = $result_hash['country'];
                    $result['Email'] = $result_hash['email'];
                    $result['Mobile'] = $result_hash['mobile'];
                    $result['UserCompanyID'] = $result_hash['UserCompanyID'];
                    $result['CompanyFullname'] = $result_hash['CompanyFullname'];
                    $result['UserType'] = $result_hash['UserType'];
                    $result['is_manager'] = $result_hash['is_manager'];
                    $result['token']=$result_hash['token'];
                    $result['device_type'] = $result_hash['device_type'];
                    $result['UserDOB']=$result_hash['UserDOB'];
                    $result['activated'] = $result_hash['activated'];
                    $result['banned']=$result_hash['banned'];
                    $result['ban_reason'] = $result_hash['ban_reason'];
                    $result['new_password_key']=$result_hash['new_password_key'];
                    $result['new_password_requested'] = $result_hash['new_password_requested'];
                    $result['supervisor']= $result_hash['supervisor'];
                    $result['new_email']=$result_hash['new_email'];
                    $result['new_email_key'] = $result_hash['new_email_key'];
                    $result['last_ip']=$result_hash['last_ip'];
                    $result['last_login'] = $result_hash['last_login'];
                    $result['UserAbout']=$result_hash['UserAbout'];
                    $result['UserGetEmail'] = $result_hash['UserGetEmail'];
                    $result['created']=$result_hash['created'];
                    $result['modified'] = $result_hash['modified'];
                    $result['Profile_Pic'] =$result_hash['profile_pic'];

                    $result['roles']=array();
                    $query=mysql_query("select  * from  tbl_user_role where RoleuserID='".$result_hash['UserType']."'");
                    $r= mysql_fetch_array($query, MYSQL_ASSOC);
                    $result['roles']=$r;
                    
                    $departments = mysql_query("SELECT dm .* , D.department_name, D.id
                                            FROM tbl_departmentemploy dm, tbl_department D
                                            WHERE  dm.DepartmentID = D.id AND dm.EmployID =".$result_hash['id']);
            if(mysql_num_rows($departments) == 0){
               $result['status'] = '0';
               $result['message']="No record found!";
              $this->response($this->json($result), 200);
            }
            else
            {
               while ($result_dept = mysql_fetch_assoc($departments)) {

                       $result['department'][] = $result_dept;
                   
                       }
                   /*  if($result_hash['Define_Role'] == '0' && $result_hash['UserType'] == '1')
                {
                    $depart1=mysql_query("INSERT INTO tbl_department (department_name,company_id)VALUES('HR Department','$company_id')",$this->db);
                    $depart=mysql_insert_id();
                    $user=mysql_query("INSERT INTO tbl_departmentemploy(DepartmentID,EmployID)Values('$depart','$id')",$this->db);
                    $depart2=mysql_query("INSERT INTO tbl_department (department_name,company_id)VALUES('Accounts Department','$company_id')",$this->db);
                    $head1=mysql_query("INSERT INTO tbl_heads (HeadCompanyID,HeadName)VALUES('$company_id','Office Work')",$this->db);
                         $hd1=mysql_insert_id();
                    $activity1=mysql_query("INSERT INTO tbl_activities ( ActivityHeadID, ActivityName)VALUES('$hd1','Client Meeting')",$this->db);
                    $activity2=mysql_query("INSERT INTO tbl_activities ( ActivityHeadID, ActivityName)VALUES('$hd1','Internal Meeting')",$this->db);
                    $head2=mysql_query("INSERT INTO tbl_heads (HeadCompanyID,HeadName)VALUES('$company_id','Client A')",$this->db);
                        $hd2=mysql_insert_id();
                    $activity3=mysql_query("INSERT INTO tbl_activities ( ActivityHeadID, ActivityName)VALUES('$hd2',' Documentation')",$this->db);
                    $activity4=mysql_query("INSERT INTO tbl_activities ( ActivityHeadID, ActivityName)VALUES('$hd2','Phone Cell')",$this->db);
                    $users_tbl=mysql_query("UPDATE users SET Define_Role='1' where UserCompanyID='$company_id'",$this->db);
                    $users_tbl=mysql_query("UPDATE tbl_companies SET Start_day='Monday', End_day='Saturday' , Start_time='10:00:00 AM', End_time ='19:00:00 PM' where CompanyID='$company_id'",$this->db);
                }*/
                
                    $this->response($this->json(array('status'=>'1',  "message" => "You are Logged In",'data'=>$result)), 200);
                }
            }
                else
                {
                    $error = array('status'=>'0', "message" => "Password is not correct");
                    $this->response($this->json($error), 200);
                }
            }
        else
        {
            $error = array('status'=>'0' ,"message" => "Email is not correct");
            $this->response($this->json($error), 200);
        }
}


       /* company register api*/
           private function admin_register()
        {  
            $result = array();
            $username=$this->_request['username'];
            $password=$this->_request['password'];
            $company_name =$this->_request['company_name'];
            $email=$this->_request['email'];
            $user_type=1;
            $mobile=$this->_request['mobile'];
            $new_email_key = md5(rand().microtime());           
            
          
            if(empty($username) || empty($email) || empty($company_name))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            $sql=mysql_query("select * from users where email='".$email."' ",$this->db)or die(mysql_error());
            if(mysql_num_rows($sql) > 0){
                $result['status'] = '0';
                $result['message']="email already exist !";
                $this->response($this->json($result), 200);
            }
            else
            {


                $UserPassword=rand(100000, 999999);
               
                //$result['UserID']=$Userinfo->UserID;
                
                $subject = "Welcome to Work Today!";
                $htmlContent = '
                <html>
                <head>
                    <title>Welcome to Work Today!.</title>
                    <p>Thanks for joining Work Today. We listed your sign in details below, make sure you keep them safe.
To verify your email address, please user OTP:</p>
                </head>
                <body>
                    
                    <table >
                        <tr>
                            <th>Please user verification OTP </th><td>'.$UserPassword.'</td>
                        </tr>
                    </table>
                </body>
                </html>';

                // Set content-type header for sending HTML email
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                // Additional headers
                $headers .= 'From: WorkToday Prime<support@worktoday.in>' . "\r\n";
                //$headers .= 'Cc: welcome@example.com' . "\r\n";
                //$headers .= 'Bcc: welcome2@example.com' . "\r\n";

                mail($email,$subject,$htmlContent,$headers);

               // $UserPassword =$this->HashPassword($UserPassword);

                $sql = mysql_query("INSERT INTO tbl_companies (CompanyFullname, CompanyEmail,CompanyPhone,Start_day,End_day,Start_time,End_time) VALUES ('".$company_name."','".$email."','".$mobile."','Monday','Saturday','10:00:00 AM','19:00:00 PM')", $this->db);
                $last_id=mysql_insert_id();

                $sql = mysql_query("INSERT INTO `users`(`username`, `password`, `email`,`mobile`,`activated`,`UserType`,`UserCompanyID`,`created`) VALUES ('".$username."','".$UserPassword."','".$email."','".$mobile."','1','".$user_type."','".$last_id."','".date('Y-m-d H:i:s')."')", $this->db);
                  $id=mysql_insert_id();


                /* drop plates qury*/

                 $depart1=mysql_query("INSERT INTO tbl_department (department_name,company_id)VALUES('HR Department','$last_id')",$this->db);
                    $depart=mysql_insert_id();
                    $user=mysql_query("INSERT INTO tbl_departmentemploy(DepartmentID,EmployID)Values('$depart','$id')",$this->db);
                    $depart2=mysql_query("INSERT INTO tbl_department (department_name,company_id)VALUES('Accounts Department','$last_id')",$this->db);
                    $head1=mysql_query("INSERT INTO tbl_heads (HeadCompanyID,HeadName)VALUES('$last_id','Office Work')",$this->db);
                         $hd1=mysql_insert_id();
                    $activity1=mysql_query("INSERT INTO tbl_activities ( ActivityHeadID, ActivityName)VALUES('$hd1','Client Meeting')",$this->db);
                    $activity2=mysql_query("INSERT INTO tbl_activities ( ActivityHeadID, ActivityName)VALUES('$hd1','Internal Meeting')",$this->db);
                    $head2=mysql_query("INSERT INTO tbl_heads (HeadCompanyID,HeadName)VALUES('$last_id','Client A')",$this->db);
                        $hd2=mysql_insert_id();
                    $activity3=mysql_query("INSERT INTO tbl_activities ( ActivityHeadID, ActivityName)VALUES('$hd2',' Documentation')",$this->db);
                    $activity4=mysql_query("INSERT INTO tbl_activities ( ActivityHeadID, ActivityName)VALUES('$hd2','Phone Cell')",$this->db);
                    $users_tbl=mysql_query("UPDATE users SET Define_Role='1' where UserCompanyID='$last_id'",$this->db);
                   /* $rolecompany=mysql_query("INSERT INTO tbl_roles (UserID,RoleName,RoleManageActivitiesAdd,RoleManageActivitiesEdit,RoleManageActivitiesDelete,RoleManageHeadAdd,RoleManageHeadEdit,RoleManageHeadDelete,RoleManageUserAdd,RoleManageUserEdit,RoleManageUserDelete,RoleManageLeavesAdd,RoleManageLeavesEdit,RoleManageLeavesDelete,RoleManageDepartmentAdd,RoleManageDepartmenEdit,RoleManageDepartmentRemove,RoleManageCoffeeAdd,RoleManageCoffeeEdit,RoleManageCoffeeRemove,RoleManageBulletinAdd,RoleManageBulletinEdit,RoleManageBulletinDelete,RoleManageTaskAdd,RoleManageTaskEdit,RoleManageTaskDelete,created_at) VALUES('".$id."','Admin','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','".date('Y-m-d H:i:s')."')",$this->db);*/

                     $role=mysql_query("INSERT INTO tbl_user_role (UserID,RoleName,RoleManageActivitiesAdd,RoleManageActivitiesEdit,RoleManageActivitiesDelete,RoleManageHeadAdd,RoleManageHeadEdit,RoleManageHeadDelete,RoleManageUserAdd,RoleManageUserEdit,RoleManageUserDelete,RoleManageLeavesAdd,RoleManageLeavesEdit,RoleManageLeavesDelete,RoleManageDepartmentAdd,RoleManageDepartmenEdit,RoleManageDepartmentRemove,RoleManageCoffeeAdd,RoleManageCoffeeEdit,RoleManageCoffeeRemove,RoleManageBulletinAdd,RoleManageBulletinEdit,RoleManageBulletinDelete,RoleManageTaskAdd,RoleManageTaskEdit,RoleManageTaskDelete,created_at) VALUES('".$id."','Admin','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','".date('Y-m-d H:i:s')."')",$this->db);
               $usertyperole1=mysql_insert_id();
               $updateusertype=mysql_query("update users SET UserType='".$usertyperole1."' where id='".$id."'",$this->db);
                   

                $result['user_id']= $id;
                $result['status'] = "1";
                $result['message']="Your registered";
                $this->response($this->json($result), 200);
           }
        }

      /* otp verified api */

      private function OTP_verification(){
            $result = array();
            $user_id=$this->_request['user_id'];
            $otp=$this->_request['otp'];
            if(empty($user_id) || empty($otp)){
                $result['status'] = '0';
                $result['message']="OTP required";
                $this->response($this->json($result), 200);
            }
             $sql=mysql_query("select * from users where id='".$user_id."' ",$this->db)or die(mysql_error());
             $row = mysql_fetch_assoc($sql);
             $password=$row["password"];
             if($password==$otp){
                $result['status'] = '1';
                $result['message']="verified OTP";
                $this->response($this->json($result), 200);
             }
             else{
                $result['status'] = '0';
                $result['message']=" OTP not match";
                $this->response($this->json($result), 200);
             }


      }

      /* email change api*/

        private function change_email_address(){
            $result = array();
            $user_id=$this->_request['user_id'];
            $email=$this->_request['email'];
            if(empty($user_id) || empty($email)){
                $result['status'] = '0';
                $result['message']="Email required";
                $this->response($this->json($result), 200);
            }
             $sql=mysql_query("select * from users where email='".$email."' ",$this->db)or die(mysql_error());
            if(mysql_num_rows($sql) > 0){
                $result['status'] = '0';
                $result['message']="email already exist !";
                $this->response($this->json($result), 200);
            }else{
             $sql=mysql_query("update users set email='".$email."' where id='".$user_id."'",$this->db);
              $sql=mysql_query("select * from users where id='".$user_id."' ",$this->db)or die(mysql_error());
             $row = mysql_fetch_assoc($sql);
             $company_id=$row["UserCompanyID"];
             $query=mysql_query("update tbl_companies set CompanyEmail='".$email."' where   CompanyID='".$company_id."'",$this->db);
                $result['status'] = '1';
                $result['message']="Email changed";
                $this->response($this->json($result), 200);
             }
}

      

     // add_users api   
        private function add_user()
        {  
            $result = array();
            $username=$this->_request['username'];
            $company_id=$this->_request['company_id'];
            $password=$this->_request['password'];
            $Role_type=$this->_request['Role_type'];
            $department_id=$this->_request['department_id'];
            $department_head=$this->_request['department_head'];
            $supervisor_id=$this->_request['supervisor_id'];
            $email=$this->_request['email'];
            $user_type=$this->_request['user_type'];
            $mobile=$this->_request['mobile'];
            $new_email_key = md5(rand().microtime());           
            $passwords = $this->HashPassword($password);
          
            if(empty($username) || empty($password) || empty($email) || empty($Role_type)|| empty($department_id)|| empty($department_head))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            
            $sql=mysql_query("select * from users where username='".$username."' ",$this->db)or die(mysql_error());
            
            if(mysql_num_rows($sql) > 0){
                $result['status'] = '0';
                $result['message']="Username already exist !";
                $this->response($this->json($result), 200);
            }
              $sql1=mysql_query("select * from users where email='".$email."' ",$this->db)or die(mysql_error());
              if(mysql_num_rows($sql1) > 0){
                $result['status'] = '0';
                $result['message']="Email already exist !";
                $this->response($this->json($result), 200);
            }

            else
            {
                /*email send user details */
                   $subject = "Welcome to Work Today!";
                $htmlContent = '<html>
                <head>
<meta http-equiv="Content-Language" content="en">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Employee</title>

</head>

<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">

<div align="center">
    <table border="0" width="100" cellspacing="0" cellpadding="0">
        <tr>
            <td>
            <table border="0" width="100%" cellpadding="0" style="border-collapse: collapse">
                <tr>
                    <td align="center">
                    <img border="0" src="../uploads/timesheet.png" width="200" height="189"></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>
                        <p align="center"><b>
                        <font face="Arial" size="5" color="#666666">Thank you for registering with WorkToday</font></b>
                    </td>
                </tr>
                
                
                <tr>
                    <td>
                    <p align="center">&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>
                    <p align="center"><font face="Arial" color="#666666"><b>
                    Please verify your email within 48 hours,</b> <br>
                    otherwise, your registration will become invalid and <br>
                    you will have to register again.</font></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>
                    <p align="center"><font face="Arial" color="#666666">We have 
                    listed your sign in details below, make sure you keep them 
                    safe.</font></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>
                    <p align="center"><font face="Arial" color="#666666"><b>Your 
                    username:</b>'.$username.'<br>
                    <b>Your email address:</b>'.$email.'<br>
                    <b>Your password is:</b>
                   '.$password.'
                    <br><br>
                    <b align="center"><a href="companies.worktoday.in/auth/login"><button type="button" style="background: #FF8800; color: white;" >Login Here</button></a></b>
                </font></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>
                    <p align="center"><font face="Arial" color="#666666"><b>See you on the other side.<br>
                    ~WorkToday ~</b></font></td>
                </tr>
               
                <tr>
                    <td bgcolor="#333333">&nbsp;</td>
                </tr>
                <tr>
                    <td bgcolor="#333333">&nbsp;</td>
                </tr>
                <tr>
                    <td bgcolor="#333333">
                    <div align="center">
                        <table border="0" width="36%" cellpadding="0" style="border-collapse: collapse">
                            <tr>
                                <td align="center">
                                <a href="">
                                <img border="0" src="../uploads/fb_icon.png" width="40" height="40"></a></td>
                                <td align="center">
                                <a href="">
                                <img border="0" src="../uploads/instagram_icon.png" width="40" height="40"></a></td>
                                <td align="center">
                                <a href="">
                                <img border="0" src="../uploads/twitter_icon.png" width="40" height="40"></a></td>
                                <td align="center">
                                <a href="">
                                <img border="0" src="../uploads/yt_icon.png" width="40" height="40"></a></td>
                            </tr>
                        </table>
                    </div>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#333333">
                    <p align="center">
                    <img border="0" src="../uploads/divider.png" width="639" height="39"></td>
                </tr>
                <tr>
                    <td bgcolor="#333333">
                    <p align="center">
                    <font face="Arial" size="2" color="#FFFFFF">Copyright&nbsp; 
                    Novastreams Technology Pvt Ltd. All Rights Reserved.<br>
                    <br>
                    Our mailing address is: support@worktoday.in<br>
                    <a href="mailto:support@worktoday.in"><font color="#FFFFFF">
                    </font></a><br>&nbsp;</font></td>
                </tr>
                <tr>
                    <td bgcolor="#333333">&nbsp;</td>
                </tr>
            </table>
            </td>
        </tr>
    </table>
</div>

</body>

</html>
';

                // Set content-type header for sending HTML email
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                // Additional headers
                $headers .= 'From: WorkToday Prime<support@worktoday.in>' . "\r\n";
                //$headers .= 'Cc: welcome@example.com' . "\r\n";
                //$headers .= 'Bcc: welcome2@example.com' . "\r\n";

                mail($email,$subject,$htmlContent,$headers);
               /*end*/
                $sql = mysql_query("INSERT INTO `users`(`username`, `password`, `email`,`mobile`,`activated`,`Define_Role`,`is_manager`,`UserCompanyID`,`created`) VALUES ('".$username."','".$passwords."','".$email."','".$mobile."','1','2','".$department_head."','".$company_id."','".date('Y-m-d H:i:s')."')", $this->db);
                $last_id=mysql_insert_id();



                 /*add role user type*/
                 /* role type 0 == new assign role 1== already assign role*/
               if($Role_type==1){
                $role_name=$this->_request['role_name'];
	            $activity_add=$this->_request['activity_add'];
	            $activity_edit=$this->_request['activity_edit'];
	            $activity_delete=$this->_request['activity_delete'];
	            $head_add=$this->_request['head_add'];
	            $head_edit=$this->_request['head_edit'];
	            $head_delete=$this->_request['head_delete'];
	            $user_add=$this->_request['user_add'];
	            $user_edit=$this->_request['user_edit'];
	            $user_delete=$this->_request['user_delete'];
	            $leaves_add=$this->_request['leaves_add'];
	            $leaves_edit=$this->_request['leaves_edit'];
	            $leaves_delete=$this->_request['leaves_delete'];
	            $department_add=$this->_request['department_add'];
	            $department_edit=$this->_request['department_edit'];
	            $department_delete=$this->_request['department_delete'];
	            $coffeecorner_add=$this->_request['coffeecorner_add'];
	            $coffeecorner_edit=$this->_request['coffeecorner_edit'];
	            $coffeecorner_delete=$this->_request['coffeecorner_delete'];
	            $bulletinboard_add=$this->_request['bulletinboard_add'];
	            $bulletinboard_edit=$this->_request['bulletinboard_edit'];
	            $bulletinboard_delete=$this->_request['bulletinboard_delete'];
	            $task_add=$this->_request['task_add'];
	            $task_edit=$this->_request['task_edit'];
	            $task_delete=$this->_request['task_delete'];
	            $date=date('Y-m-d G:i:s');

                  $role=mysql_query("INSERT INTO tbl_user_role (UserID,RoleName,RoleManageActivitiesAdd,RoleManageActivitiesEdit,RoleManageActivitiesDelete,RoleManageHeadAdd,RoleManageHeadEdit,RoleManageHeadDelete,RoleManageUserAdd,RoleManageUserEdit,RoleManageUserDelete,RoleManageLeavesAdd,RoleManageLeavesEdit,RoleManageLeavesDelete,RoleManageDepartmentAdd,RoleManageDepartmenEdit,RoleManageDepartmentRemove,RoleManageCoffeeAdd,RoleManageCoffeeEdit,RoleManageCoffeeRemove,RoleManageBulletinAdd,RoleManageBulletinEdit,RoleManageBulletinDelete,RoleManageTaskAdd,RoleManageTaskEdit,RoleManageTaskDelete,created_at) VALUES('".$last_id."','".$role_name."','".$activity_add."','".$activity_edit."','".$activity_delete."','".$head_add."','".$head_edit."','".$head_delete."','".$user_add."','".$user_edit."','".$user_delete."','".$leaves_add."','".$leaves_edit."','".$leaves_delete."','".$department_add."','".$department_edit."','".$department_delete."','".$coffeecorner_add."','".$coffeecorner_edit."','".$coffeecorner_delete."','".$bulletinboard_add."','".$bulletinboard_edit."','".$bulletinboard_delete."','".$task_add."','".$task_edit."','".$task_delete."','".$date."')",$this->db);
               $usertyperole1=mysql_insert_id();
               $updateusertype=mysql_query("update users SET UserType='".$usertyperole1."' where id='".$last_id."'",$this->db);

               }
               else{ 
               $role=mysql_query("select * from tbl_roles where RoleID='".$user_type."'");
               $qry = mysql_fetch_array($role, MYSQL_ASSOC);
               $rolename= $qry['RoleName'];
               /*$rolestatus= $qry['RoleStatus'];*/
               $RoleManageActivitiesAdd= $qry['RoleManageActivitiesAdd'];
               $RoleManageActivitiesEdit= $qry['RoleManageActivitiesEdit'];
               $RoleManageActivitiesDelete= $qry['RoleManageActivitiesDelete'];
               $RoleManageHeadAdd= $qry['RoleManageHeadAdd'];
               $RoleManageHeadEdit= $qry['RoleManageHeadEdit'];
               $RoleManageHeadDelete= $qry['RoleManageHeadDelete'];
               $RoleManageUserAdd=$qry['RoleManageUserAdd'];
               $RoleManageUserEdit=$qry['RoleManageUserEdit'];
               $RoleManageUserDelete=$qry['RoleManageUserDelete'];
               $RoleManageLeavesAdd=$qry['RoleManageLeavesAdd'];
               $RoleManageLeavesEdit=$qry['RoleManageLeavesEdit'];
               $RoleManageLeavesDelete=$qry['RoleManageLeavesDelete'];
               $RoleManageDepartmentAdd=$qry['RoleManageDepartmentAdd'];
               $RoleManageDepartmenEdit=$qry['RoleManageDepartmenEdit'];
               $RoleManageDepartmentRemove=$qry['RoleManageDepartmentRemove'];
               $RoleManageCoffeeAdd=$qry['RoleManageCoffeeAdd'];
               $RoleManageCoffeeEdit=$qry['RoleManageCoffeeEdit'];
               $RoleManageCoffeeRemove=$qry['RoleManageCoffeeRemove'];
               $RoleManageBulletinAdd=$qry['RoleManageBulletinAdd'];
               $RoleManageBulletinEdit=$qry['RoleManageBulletinEdit'];
               $RoleManageBulletinDelete=$qry['RoleManageBulletinDelete'];
               $RoleManageTaskAdd=$qry['RoleManageTaskAdd'];
               $RoleManageTaskEdit=$qry['RoleManageTaskEdit'];
               $RoleManageTaskDelete=$qry['RoleManageTaskDelete'];
               $date=date('Y-m-d G:i:s');


               $role=mysql_query("INSERT INTO tbl_user_role (UserID,RoleName,RoleManageActivitiesAdd,RoleManageActivitiesEdit,RoleManageActivitiesDelete,RoleManageHeadAdd,RoleManageHeadEdit,RoleManageHeadDelete,RoleManageUserAdd,RoleManageUserEdit,RoleManageUserDelete,RoleManageLeavesAdd,RoleManageLeavesEdit,RoleManageLeavesDelete,RoleManageDepartmentAdd,RoleManageDepartmenEdit,RoleManageDepartmentRemove,RoleManageCoffeeAdd,RoleManageCoffeeEdit,RoleManageCoffeeRemove,RoleManageBulletinAdd,RoleManageBulletinEdit,RoleManageBulletinDelete,RoleManageTaskAdd,RoleManageTaskEdit,RoleManageTaskDelete,created_at) VALUES('".$last_id."','".$rolename."','".$RoleManageActivitiesAdd."','".$RoleManageActivitiesEdit."','".$RoleManageActivitiesDelete."','".$RoleManageHeadAdd."','".$RoleManageHeadEdit."','".$RoleManageHeadDelete."','".$RoleManageUserAdd."','".$RoleManageUserEdit."','".$RoleManageUserDelete."','".$RoleManageLeavesAdd."','".$RoleManageLeavesEdit."','".$RoleManageLeavesDelete."','".$RoleManageDepartmentAdd."','".$RoleManageDepartmenEdit."','".$RoleManageDepartmentRemove."','".$RoleManageCoffeeAdd."','".$RoleManageCoffeeEdit."','".$RoleManageCoffeeRemove."','".$RoleManageBulletinAdd."','".$RoleManageBulletinEdit."','".$RoleManageBulletinDelete."','".$RoleManageTaskAdd."','".$RoleManageTaskEdit."','".$RoleManageTaskDelete."','".$date."')",$this->db);
               $usertyperole=mysql_insert_id();
               $updateusertype=mysql_query("update users SET UserType='".$usertyperole."' where id='".$last_id."'",$this->db);
        
                }

               /*end*/
                $depart = explode(',', $department_id);
                for($i=0;$i<count($depart);$i++) {
                	$sql = mysql_query("INSERT INTO `tbl_departmentemploy`(`DepartmentID`,`EmployID`) values('".$depart[$i]."','".$last_id."')", $this->db);
                }
                if($department_head==3 && $supervisor_id!=''){
                	 $super = mysql_query("INSERT INTO `tbl_supervisor`(`user_id`,`supervisor_id`) values('".$last_id."','".$supervisor_id."')", $this->db);
                     $update_role=mysql_query("update users SET supervisor='1' where id='".$supervisor_id."'",$this->db);
                }
                
                $result['id']= $last_id;
                $result['status'] = '1';
                $result['message']="New Team Member Registred";
                $this->response($this->json($result), 200);
           }
        }

  // update users  
        private function update_user()
        {  
            $result = array();
            $user_id=$this->_request['user_id'];
            $username=addslashes($this->_request['username']);
            $mobile=$this->_request['mobile'];
                      
            $supervisor_id = $this->_request['supervisor_id'];
            /*role update*/
                $role_name=$this->_request['role_name'];
                $activity_add=$this->_request['activity_add'];
                $activity_edit=$this->_request['activity_edit'];
                $activity_delete=$this->_request['activity_delete'];
                $head_add=$this->_request['head_add'];
                $head_edit=$this->_request['head_edit'];
                $head_delete=$this->_request['head_delete'];
                $user_add=$this->_request['user_add'];
                $user_edit=$this->_request['user_edit'];
                $user_delete=$this->_request['user_delete'];
                $leaves_add=$this->_request['leaves_add'];
                $leaves_edit=$this->_request['leaves_edit'];
                $leaves_delete=$this->_request['leaves_delete'];
                $department_add=$this->_request['department_add'];
                $department_edit=$this->_request['department_edit'];
                $department_delete=$this->_request['department_delete'];
                $coffeecorner_add=$this->_request['coffeecorner_add'];
                $coffeecorner_edit=$this->_request['coffeecorner_edit'];
                $coffeecorner_delete=$this->_request['coffeecorner_delete'];
                $bulletinboard_add=$this->_request['bulletinboard_add'];
                $bulletinboard_edit=$this->_request['bulletinboard_edit'];
                $bulletinboard_delete=$this->_request['bulletinboard_delete'];
                $task_add=$this->_request['task_add'];
                $task_edit=$this->_request['task_edit'];
                $task_delete=$this->_request['task_delete'];
                $date=date('Y-m-d G:i:s');
          
            if( empty($user_id) )
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
          

                $sql = mysql_query("UPDATE  users SET username='".$username."',mobile='".$mobile."' where id='".$user_id."'", $this->db);

                
                $role=mysql_query("update  tbl_user_role SET RoleName='".$role_name."',RoleManageActivitiesAdd='".$activity_add."',RoleManageActivitiesEdit='".$activity_edit."',RoleManageActivitiesDelete='".$activity_delete."',RoleManageHeadAdd='".$head_add."',RoleManageHeadEdit='".$head_edit."',RoleManageHeadDelete='".$head_delete."',RoleManageUserAdd='".$user_add."',RoleManageUserEdit='".$user_edit."',RoleManageUserDelete='".$user_delete."',RoleManageLeavesAdd='".$leaves_add."',RoleManageLeavesEdit='".$leaves_edit."',RoleManageLeavesDelete='".$leaves_delete."',RoleManageDepartmentAdd='".$department_add."',RoleManageDepartmenEdit='".$department_edit."',RoleManageDepartmentRemove='".$department_delete."',RoleManageCoffeeAdd='".$coffeecorner_add."',RoleManageCoffeeEdit='".$coffeecorner_edit."',RoleManageCoffeeRemove='".$coffeecorner_delete."',RoleManageBulletinAdd='".$bulletinboard_add."',RoleManageBulletinEdit='".$bulletinboard_edit."',RoleManageBulletinDelete='".$bulletinboard_delete."',RoleManageTaskAdd='".$task_add."',RoleManageTaskEdit='".$task_edit."',RoleManageTaskDelete='".$task_delete."',created_at='".$date."' where UserID='".$user_id."'",$this->db);
                if($supervisor_id ==""){

                 $sql = mysql_query("UPDATE  tbl_supervisor SET Deleted='0' where user_id='".$user_id."'", $this->db);
                }else{
                   $sele_sup=mysql_query("select * from tbl_supervisor where user_id='".$user_id."'",$this->db);
                   if(mysql_num_rows($sele_sup) > 0){
                       $sql = mysql_query("UPDATE  tbl_supervisor SET supervisor_id='".$supervisor_id."' where user_id='".$user_id."'", $this->db);
                    }
                    else{
                      
                         $addsupervisor = mysql_query("INSERT INTO `tbl_supervisor`(`user_id`,`supervisor_id`) values('".$user_id."','".$supervisor_id."')", $this->db);
                         
                      }

                    
                }

                
                
               
                $result['status'] = '1';
                $result['message']="User updated";
                $this->response($this->json($result), 200);
          /* }*/
        }


     // password change api 

        private function change_password()
        {
          $user_id=$this->_request['user_id'];
          // $old_password=$this->_request['old_password'];
          // $old=$this->HashPassword($old_password);
          $new_password=$this->_request['new_password'];
          
          $confirm_password=$this->_request['confirm_password'];

            if(empty($new_password) || empty($confirm_password) || empty($user_id)){
                $result['status']='0';
                $result['message']='Invalid data provided';
            }
       else{
            // $pass= mysql_query("select password from users where id='".$user_id."'");
            // $row = mysql_fetch_assoc($pass);
            // $old_passwordd = $row['password'];


           // echo "$old_passwordd";exit();
           // if ($old_password!=$old_passwordd)
           // {
          
           //       $result['status']='0';
           //        $result['message']='old Password not match';
           //   }

            if($new_password != $confirm_password){
                  $result['status']='0';
                  $result['message']='Password not match';
            }
            else{
                if($new_password){
                $new_pass=$this->HashPassword($new_password);
               $sql =mysql_query("update users set password='".$new_pass."' where id='".$user_id."'");
                $result['status']=1;
                $result['message']=" Your password changed";
            }
            else{
                $result['status'] = '0';
                $result['message']="Not Updated";
            }
            }
        }
    
            $this->response($this->json($result), 200);
        }
    
 //   forgot password api send user email address password  
        private function forgotpassword()
        {
            $UserEmail=$this->_request['UserEmail'];
            if(empty($UserEmail))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            $sql=mysql_query("select email from users where email='".$UserEmail."'", $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql)>0)
            {
                //$Userinfo=mysql_fetch_object($sql);
                
                $UserPassword=rand(100000, 999999);
               
                //$result['UserID']=$Userinfo->UserID;
                
                $subject = "Your password changed";
                $htmlContent = '
                <html>
                <head>
                    <title>Your password is changed.</title>
                </head>
                <body>
                    
                    <table >
                        <tr>
                            <th>Please use your new password</th><td>'.$UserPassword.'</td>
                        </tr>
                    </table>
                </body>
                </html>';

                // Set content-type header for sending HTML email
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                // Additional headers
                $headers .= 'From: WorkToday Prime<support@worktoday.in>' . "\r\n";
                //$headers .= 'Cc: welcome@example.com' . "\r\n";
                //$headers .= 'Bcc: welcome2@example.com' . "\r\n";

                mail($UserEmail,$subject,$htmlContent,$headers);

                $UserPassword =$this->HashPassword($UserPassword);
                $sql=mysql_query("update users set password='".$UserPassword."' where email='".$UserEmail."'", $this->db)or die(mysql_error());
                $result['status']='1';
                  $result['message']=" Your new password is sent to registered email ID";
            }
            else
            {
               
                 $result['status'] = 0;
                $result['message']="Invalid email.";
            }
            // If success everythig is good send header as "OK" and return list of users in JSON format
            $this->response($this->json($result), 200);
        }

// add new role in company
    private function add_roles(){  
            $result = array();
            $company_id=$this->_request['company_id'];
            $role_name=$this->_request['role_name'];
            $manager=$this->_request['manager'];
            $activity_add=$this->_request['activity_add'];
            $activity_edit=$this->_request['activity_edit'];
            $activity_delete=$this->_request['activity_delete'];
            $head_add=$this->_request['head_add'];
            $head_edit=$this->_request['head_edit'];
            $head_delete=$this->_request['head_delete'];
            $user_add=$this->_request['user_add'];
            $user_edit=$this->_request['user_edit'];
            $user_delete=$this->_request['user_delete'];
            $leaves_add=$this->_request['leaves_add'];
            $leaves_edit=$this->_request['leaves_edit'];
            $leaves_delete=$this->_request['leaves_delete'];
            $department_add=$this->_request['department_add'];
            $department_edit=$this->_request['department_edit'];
            $department_delete=$this->_request['department_delete'];
            $coffeecorner_add=$this->_request['coffeecorner_add'];
            $coffeecorner_edit=$this->_request['coffeecorner_edit'];
            $coffeecorner_delete=$this->_request['coffeecorner_delete'];
            $bulletinboard_add=$this->_request['bulletinboard_add'];
            $bulletinboard_edit=$this->_request['bulletinboard_edit'];
            $bulletinboard_delete=$this->_request['bulletinboard_delete'];
            $task_add=$this->_request['task_add'];
            $task_edit=$this->_request['task_edit'];
            $task_delete=$this->_request['task_delete'];
            
   
            if(empty($company_id) || empty($role_name))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            $sql=mysql_query("select * from tbl_roles where RoleCompanyID='".$company_id."' AND  RoleName='".$role_name."'",$this->db)or die(mysql_error());
            if(mysql_num_rows($sql) > 0){
                $result['status'] = '0';
                $result['message']="This role name already exist !";
                $this->response($this->json($result), 200);
            }
            else
            {
              $sql = mysql_query("INSERT INTO `tbl_roles`( `RoleCompanyID`, `RoleName`, `RoleStatus`, `RoleCreatedAt`, `Manager`,`RoleManageActivitiesAdd`,`RoleManageActivitiesEdit`, `RoleManageActivitiesDelete`,`RoleManageHeadAdd`,`RoleManageHeadEdit`,`RoleManageHeadDelete`,`RoleManageUserAdd`,`RoleManageUserEdit`,`RoleManageUserDelete`,`RoleManageLeavesAdd`,`RoleManageLeavesEdit`,`RoleManageLeavesDelete`,`RoleManageDepartmentAdd`,`RoleManageDepartmenEdit`,`RoleManageDepartmentRemove`,`RoleManageCoffeeAdd`,`RoleManageCoffeeEdit`,`RoleManageCoffeeRemove`,`RoleManageBulletinAdd`,`RoleManageBulletinEdit`,`RoleManageBulletinDelete`,`RoleManageTaskAdd`,`RoleManageTaskEdit`,`RoleManageTaskDelete`) VALUES ('".$company_id."','".$role_name."','1','".date('Y-m-d H:i:s')."','".$manager."','".$activity_add."','".$activity_edit."','".$activity_delete."','".$head_add."','".$head_edit."','".$head_delete."','".$user_add."','".$user_edit."','".$user_delete."','".$leaves_add."','".$leaves_edit."','".$leaves_delete."','".$department_add."','".$department_edit."','".$department_delete."','".$coffeecorner_add."','".$coffeecorner_edit."','".$coffeecorner_delete."','".$bulletinboard_add."','".$bulletinboard_edit."','".$bulletinboard_delete."','".$task_add."','".$task_edit."','".$task_delete."')", $this->db);
                
                $result['id']= mysql_insert_id();
                $result['status'] = '1';
                $result['message']="role assigned";
                $this->response($this->json($result), 200);
           }
        }


    // add new department in company 
        private function add_department(){
            $result = array();
            $company_id=$this->_request['company_id'];
            $department_name=$this->_request['department_name'];
            $date=date('Y-m-d G:i:s');
            
             if(empty($company_id) || empty($department_name))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }

             $sql=mysql_query("select * from tbl_department where company_id='".$company_id."' AND  department_name='".$department_name."'",$this->db)or die(mysql_error());
            if(mysql_num_rows($sql) > 0){
                $result['status'] = '0';
                $result['message']="Department name already exist !";
                $this->response($this->json($result), 200);
            }
            else
            {
              $sql = mysql_query("INSERT INTO `tbl_department`(`department_name`, `company_id`,`created_at`) VALUES ('".$department_name."','".$company_id."','".$date."')", $this->db);
                
                $result['id']= mysql_insert_id();
                $result['status'] = '1';
                $result['message']="Department Added";
                $this->response($this->json($result), 200);
           }
        }


      // add new Head in company
    private function add_head(){
            $result = array();
            $company_id=$this->_request['company_id'];
            $head_name=$this->_request['head_name'];
            $description=$this->_request['description'];
            
             if(empty($company_id) || empty($head_name)|| empty($description))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }

             $sql=mysql_query("select * from tbl_heads where HeadCompanyID='".$company_id."' AND  HeadName='".$head_name."'",$this->db)or die(mysql_error());
            if(mysql_num_rows($sql) > 0){
                $result['status'] = '0';
                $result['message']="Head name already exist !";
                $this->response($this->json($result), 200);
            }
            else
            {
              $sql = mysql_query("INSERT INTO `tbl_heads`(`HeadCompanyID`, `HeadName`, `HeadDescription`,`HeadStatus`,`HeadCreatedAt`) VALUES ('".$company_id."','".$head_name."','".$description."','1','".date('Y-m-d H:i:s')."')", $this->db);
                
               /* $result['id']= mysql_insert_id();*/
                $result['status'] = '1';
                $result['message']="Head Added";
                $this->response($this->json($result), 200);
           }
        }

      // add new Activity in Head




         private function add_activity(){
            $result = array();
            $head_id=$this->_request['head_id'];
            $activity_name=$this->_request['activity_name'];
            $description=$this->_request['description'];
            
             if(empty($head_id) || empty($activity_name)|| empty($description))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }

             $sql=mysqli_query($this->db,"SELECT * from  tbl_activities where ActivityHeadID='".$head_id."' AND  ActivityName='".$activity_name."'")or die(mysqli_error());
            if(mysqli_num_rows($sql) > 0){
                $result['status'] = '0';
                $result['message']="Head name already exist !";
                $this->response($this->json($result), 200);
            }
            else
            {
                    
              $sql = mysqli_query($this->db,"INSERT INTO `tbl_activities`(`ActivityHeadID`, `ActivityName`, `ActivityDescription`,`ActivityStatus`,`ActivityCreatedAt`) VALUES ('".$head_id."','".$activity_name."','".$description."','1','".date('Y-m-d H:i:s')."')");
                
                /*$result['id']= mysql_insert_id();*/
                $result['status'] = '1';
                $result['message']="Activity added";
                $this->response($this->json($result), 200);
           }

        }

     
      // update department by department id
         private function update_department(){
            $result = array();
            $department_id=$this->_request['department_id'];
            $department_name=$this->_request['department_name'];
            
             if(empty($department_id) || empty($department_name))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }

            //  $sql=mysql_query("select * from tbl_department where company_id='".$company_id."' AND  department_name='".$department_name."'",$this->db)or die(mysql_error());
            // if(mysql_num_rows($sql) > 0){
            //     $result['status'] = 0;
            //     $result['message']="Department name already exist !";
            //     $this->response($this->json($result), 200);
            // }
            else
            {
              $sql = mysql_query("UPDATE  `tbl_department` SET department_name='".$department_name."' where id='".$department_id."' ", $this->db);
                
               
                $result['status'] = '1';
                $result['message']="Department updated";
                $this->response($this->json($result), 200);
           }
        }

         private function update_head(){
            $result = array();
            $head_id=$this->_request['head_id'];
            $head_name=$this->_request['head_name'];
            $head_description=$this->_request['head_description'];
            
             if(empty($head_id) || empty($head_name))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }

           
            else
            {
              $sql = mysql_query("UPDATE  tbl_heads SET   HeadName='".$head_name."',HeadDescription='".$head_description."' where HeadID='".$head_id."' ", $this->db);
                
               
                $result['status'] = '1';
                $result['message']="Head updated";
                $this->response($this->json($result), 200);
           }
        }
        
         private function update_activity(){
            $result = array();
            $activity_id=$this->_request['activity_id'];
            $head_id=$this->_request['head_id'];
            $activity_name=$this->_request['activity_name'];
            $activity_description=$this->_request['activity_description'];
            
             if(empty($activity_id)|| empty($head_id) || empty($activity_name))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }

           
            else
            {
              $sql = mysql_query("UPDATE   tbl_activities SET ActivityHeadID='".$head_id."' , ActivityName='".$activity_name."',  ActivityDescription='".$activity_description."' where   ActivityID='".$activity_id."' ", $this->db);
                
               
                $result['status'] = '1';
                $result['message']="Activity updated";
                $this->response($this->json($result), 200);
           }
        }
        
        

        // update role by role id
        private function update_roles()
        {  
            $result = array();
            $role_id=$this->_request['role_id'];
            $role_name=$this->_request['role_name'];
            $manager=$this->_request['manager'];
            $activity_add=$this->_request['activity_add'];
            $activity_edit=$this->_request['activity_edit'];
            $activity_delete=$this->_request['activity_delete'];
            $head_add=$this->_request['head_add'];
            $head_edit=$this->_request['head_edit'];
            $head_delete=$this->_request['head_delete'];
            $user_add=$this->_request['user_add'];
            $user_edit=$this->_request['user_edit'];
            $user_delete=$this->_request['user_delete'];
            $leaves_add=$this->_request['leaves_add'];
            $leaves_edit=$this->_request['leaves_edit'];
            $leaves_delete=$this->_request['leaves_delete'];
          /*  $expenses_add=$this->_request['expenses_add'];
            $expenses_edit=$this->_request['expenses_edit'];
            $expenses_delete=$this->_request['expenses_delete'];*/
            $department_add=$this->_request['department_add'];
            $department_edit=$this->_request['department_edit'];
            $department_delete=$this->_request['department_delete'];
            $coffeecorner_add=$this->_request['coffeecorner_add'];
            $coffeecorner_edit=$this->_request['coffeecorner_edit'];
            $coffeecorner_delete=$this->_request['coffeecorner_delete'];
            $bulletinboard_add=$this->_request['bulletinboard_add'];
            $bulletinboard_edit=$this->_request['bulletinboard_edit'];
            $bulletinboard_delete=$this->_request['bulletinboard_delete'];
            $task_add=$this->_request['task_add'];
            $task_edit=$this->_request['task_edit'];
            $task_delete=$this->_request['task_delete'];
            $date=date('Y-m-d G:i:s');
            
   
          
            if(empty($role_id) || empty($role_name))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            // $sql=mysql_query("select * from tbl_roles where RoleCompanyID='".$company_id."' AND  RoleName='".$role_name."'",$this->db)or die(mysql_error());
            // if(mysql_num_rows($sql) > 0){
            //     $result['status'] = 0;
            //     $result['message']="This role name already exist !";
            //     $this->response($this->json($result), 200);
            // }
            else
            {
              $sql = mysql_query("UPDATE  tbl_roles SET RoleName='".$role_name."',RoleCreatedAt='".$date."',RoleManageActivitiesAdd='".$activity_add."',RoleManageActivitiesEdit='".$activity_edit."',RoleManageActivitiesDelete='".$activity_delete."',RoleManageHeadAdd='".$head_add."',RoleManageHeadEdit='".$head_edit."',RoleManageHeadDelete='".$head_delete."',RoleManageUserAdd='".$user_add."',RoleManageUserEdit='".$user_edit."',RoleManageUserDelete='".$user_delete."',RoleManageLeavesAdd='".$leaves_add."',RoleManageLeavesEdit='".$leaves_edit."',RoleManageLeavesDelete='".$leaves_delete."',RoleManageDepartmentAdd='".$department_add."',RoleManageDepartmenEdit='".$department_edit."',RoleManageDepartmentRemove='".$department_delete."',RoleManageCoffeeAdd='".$coffeecorner_add."',RoleManageCoffeeEdit='".$coffeecorner_edit."',RoleManageCoffeeRemove='".$coffeecorner_delete."',RoleManageBulletinAdd='".$bulletinboard_add."',RoleManageBulletinEdit='".$bulletinboard_edit."',RoleManageBulletinDelete='".$bulletinboard_delete."',RoleManageTaskAdd='".$task_add."',RoleManageTaskEdit='".$task_edit."',RoleManageTaskDelete='".$task_delete."' where  RoleID='".$role_id."'", $this->db);
                
                
                $result['status'] = '1';
                $result['message']="Role updated";
                $this->response($this->json($result), 200);
           }
        }
   //   get department all company wise

         private function get_all_department_by_company(){
            $result = array();
            $company_id=$this->_request['company_id'];
             if(empty($company_id))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
                   $sql = mysql_query("select * from  tbl_department where company_id='".$company_id."'");
                   $result = array();
                        $i=0;
                        if(mysql_num_rows($sql) == 0){
                         
                          $result['status'] = '0';
                          $result['message']="Not found";
                          $this->response($this->json($result), 200);
                        }
                         else
                          {
                         while ($row= mysql_fetch_assoc($sql))
                         {

                             $result[$i]['id'] = $row['id'];
                             $result[$i]['department_name'] = $row['department_name'];   
                  
                         $i++;
                       }
                  
                        $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
                    }
         }

         //list of employees for supervisors

         public function get_emp_list_for_supervisor(){

            $result = array();
            $supervisor_id = $this->_request['supervisor_id'];

            //check if the id exist
            if(empty($supervisor_id)){

                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);

            }else{

                //get and validate the data for employee based on supervisor id
                $sql = mysql_query("select u.username,d.department_name,u.id ,d.id as Department_id ,S.* from  users u,tbl_department d , tbl_departmentemploy E,tbl_supervisor S where u.id =S.user_id AND E.DepartmentID=d.id and E.EmployID=S.user_id and S.supervisor_id=".$supervisor_id);
                   $result = array();
                        $i=0;
                        if(mysql_num_rows($sql) == 0){
                         
                          $result['status'] = '0';
                          $result['message']="Not found";
                          $this->response($this->json($result), 200);
                        }
                         else
                          {
                         while ($row= mysql_fetch_assoc($sql))
                         {

                             $result[$i]['user_id']= $row['id'];
                             $result[$i]['username']= $row['username']; 
                             $result[$i]['department_id']= $row['Department_id'];
                             $result[$i]['department_name']= $row['department_name']; 
                            
                             $i++;
                       }
                  
                        $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
                    }

            }
         }

         //list of employees for supervisors

       // get all users in a company
       private function get_all_users_by_company(){
            $result = array();
            $company_id=$this->_request['company_id'];
             if(empty($company_id))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
                   $sql = mysql_query("select u.*, R.RoleName , R.RoleuserID from  users u , tbl_user_role R where   u.id=R.UserID and  u.UserCompanyID='".$company_id."' order by u.username");
                   $result = array();
                        $i=0;
                        if(mysql_num_rows($sql) == 0){
                         
                          $result['status'] = '0';
                          $result['message']="Not found";
                          $this->response($this->json($result), 200);
                         }
                         else
                          {
                         while ($row= mysql_fetch_assoc($sql))
                         {

                             $result[$i]['user_id']= $row['id'];
                             $result[$i]['username']= $row['username'];
                             $result[$i]['mobile']= $row['mobile'];
                             $result[$i]['email']= $row['email'];
                             $result[$i]['first_name']= $row['first_name'];
                             $result[$i]['last_name']= $row['last_name'];
                             $result[$i]['is_manager']= $row['is_manager'];
                             $result[$i]['supervisor']= $row['supervisor'];
                             $result[$i]['company_id']= $row['UserCompanyID'];
                             $result[$i]['activated']= $row['activated'];
                             $result[$i]['device_type']= $row['device_type'];
                             $result[$i]['city']= $row['city'];
                             $result[$i]['state']= $row['state'];
                             $result[$i]['about_user']= $row['UserAbout'];
                             $result[$i]['RoleuserID']= $row['UserType'];
                             $result[$i]['role_name']= $row['RoleName'];
                             $result[$i]['Deleted']= $row['Deleted'];
                             $result[$i]['profile_pic']=$row['profile_pic'];
                             /*role start*/
                              $result[$i]['roles']=array();
                    $user_role=mysql_query("select  * from  tbl_user_role where RoleuserID='".$row['UserType']."'");
                    $r= mysql_fetch_array($user_role, MYSQL_ASSOC);
                    $result[$i]['roles']=$r;
                             
                             if($result[$i]['is_manager']==3 && $result[$i]['supervisor']==1){
                                $sql1 =mysql_query("SELECT s.*, u.username as super FROM users u, tbl_supervisor s  WHERE u.id=s.supervisor_id and s.user_id= '".$row['id']."'");
                                  
                                  $res= mysql_fetch_assoc($sql1);
                                  $result[$i]['supervisor_name']=$res['super'];
                                  $result[$i]['supervisor_id']=$res['supervisor_id'];

                             }
                             $departments = mysql_query("select DE.*, D.department_name FROM 
                                tbl_departmentemploy DE, tbl_department D where DE.DepartmentID=D.id and DE.EmployID = '".$row['id']."'");
                                    while ($result_dept = mysql_fetch_assoc($departments)) {

                                                   $result[$i]['department'][] = $result_dept;
                                        }

                                      
                            $i++;
                       }
                  
                        $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
                    }
         
     }


         private function get_all_roles_by_company(){
            $result = array();
            $company_id=$this->_request['company_id'];
             if(empty($company_id))
            {
                $result['status'] = 0;
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
                   $sql = mysql_query("select * from  tbl_roles where RoleID IN (1,2) OR RoleCompanyID='".$company_id."'");
                   $result = array();
                        $i=0;
                        if(mysql_num_rows($sql) == 0){
                         
                          $result['status'] = '0';
                          $result['message']="Not found";
                          $this->response($this->json($result), 200);
                        }
                         else
                          {
                         while ($row= mysql_fetch_assoc($sql))
                         {

                             $result[$i]['RoleID'] = $row['RoleID'];
                              $result[$i]['RoleName'] = $row['RoleName'];
                              $i++;
                       }
                  
                        $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
                    }
         }
          private function get_supervisor_list(){
            $result = array();
            $company_id=$this->_request['company_id'];
             if(empty($company_id))
            {
                $result['status'] = 0;
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
                  $sql=mysql_query("SELECT * from users where UserCompanyID='".$company_id."' and is_manager=3", $this->db);
                   $result = array();
                        $i=0;
                        if(mysql_num_rows($sql) == 0){
                         
                          $result['status'] = '0';
                          $result['message']="Not found";
                          $this->response($this->json($result), 200);
                        }
                         else
                          {
                         while ($row= mysql_fetch_assoc($sql))
                         {

                             $result[$i]['user_id'] = $row['id'];
                              $result[$i]['username'] = $row['username'];
                              $i++;
                       }
                  
                        $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
                    }
         }
         /* get role company id wise  all details api*/
          private function get_roles(){
            $result = array();
            $company_id=$this->_request['company_id'];
             if(empty($company_id))
            {
                $result['status'] = 0;
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
                   $sql = mysql_query("select * from  tbl_roles where RoleID IN (1,2) OR RoleCompanyID='".$company_id."'");
                   $result = array();
                        $i=0;
                        if(mysql_num_rows($sql) == 0){
                         
                          $result['status'] = '0';
                          $result['message']="Not found";
                          $this->response($this->json($result), 200);
                        }
                         else
                          {
                         while ($row= mysql_fetch_assoc($sql))
                         {

                              $result[$i] = $row;
                             
                              $i++;
                       }
                  
                        $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
                    }
         }
        private function gettasks()
        {
            $UserID = $this->_request['UserID'];
            $Type = $this->_request['UserType'];
            $PageNo   = $this->_request['PageNo'];
            
            $Limit = 10;
            $nextlimit = $Limit*$PageNo;
            
            if(empty($UserID) || $PageNo=='' || empty($Type))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            if($Type == '3')
            {
                $sql=mysql_query("select T.*, U.username from tbl_tasks T, users U where U.id=T.TaskCreatedByUserID AND T.TaskAssignToUserID='".$UserID."' order by T.TaskCreatedAt ASC LIMIT ".$nextlimit.",".$Limit, $this->db)or die(mysql_error());
                $result = array();
                $i=0;
                if(mysql_num_rows($sql) == 0){
                  $result['status'] = '0';
                  $result['message']="No record found!";
                  $this->response($this->json($result), 200);
                }
                else
                {
                    while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                    {
                        $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 0 for fix and 1 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 0 for routine and 1 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];  // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                        $i++;
                    }
                    $this->response($this->json(array('success'=>'success', 'data'=>$result)), 200);
                }
            }
            else
            {
                $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name from tbl_tasks T, users U, users UA, tbl_department D where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$UserID.'") order by T.TaskStartDate DESC LIMIT '.$nextlimit.','.$Limit, $this->db)or die(mysql_error());
                $result = array();
                $i=0;
                if(mysql_num_rows($sql) == 0){
                  $result['status'] = '0';
                  $result['message']="No record found!";
                  $this->response($this->json($result), 200);
                }
                else
                {
                    while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                    {
                        $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 0 for fix and 1 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 0 for routine and 1 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];  // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                        $i++;
                    }
                    $this->response($this->json(array('message'=>'data found', 'data'=>$result)), 200);
                }
            }
        }

        private function gettask()
        {
            $TaskID = $this->_request['TaskID'];
            
            if(empty($TaskID))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            
            $sql=mysql_query('select * from tbl_tasks where TaskID="'.$TaskID.'"', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              $result['status'] = '0';
              $result['message']="Invalid task id provided!";
              $this->response($this->json($result), 200);
            }
            else
            {
                $rlt = mysql_fetch_array($sql, MYSQL_ASSOC);
                
                $result['TaskID'] = $rlt['TaskID'];
                $result['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                $result['TaskHeadID'] = $rlt['TaskHeadID'];
                $result['TaskActivityID'] = $rlt['TaskActivityID'];
                $result['TaskName'] = stripslashes($rlt['TaskName']);
                $result['TaskType'] = $rlt['TaskType'];  // 0 for fix and 1 for flexy
                $result['TaskCategory'] = $rlt['TaskCategory'];  // 0 for routine and 1 for nonroutine
                $result['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                $result['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                $result['TaskStartDate'] = $rlt['TaskStartDate'];
                $result['TaskEndDate'] = $rlt['TaskEndDate'];
                $result['TaskStartTime'] = $rlt['TaskStartTime'];
                $result['TaskEndTime'] = $rlt['TaskEndTime'];
                $result['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                $result['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                $result['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                $result['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                $result['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                $result['TaskStatus'] = $rlt['TaskStatus'];  // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                
                $this->response($this->json(array('success'=>'success', 'data'=>$result)), 200);
            }
        }
        
        private function getmanagerdepartments()
        {
            $UserID = $this->_request['UserID'];
            
            if(empty($UserID))
            {
                $result['Success'] = "error";
                $result['msg']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            
            $sql=mysql_query("select d.id, d.department_name from tbl_department d, tbl_departmentemploy DE where d.id=DE.DepartmentID and DE.EmployID='".$UserID."' order by d.department_name ASC", $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              $result['success'] = "error";
              $result['msg']="No record found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i]['DepartmentID'] = $rlt['id'];
                    $result[$i]['DepartmentName'] = $rlt['department_name'];
                    $i++;
                }
                $this->response($this->json(array('success'=>'success', 'data'=>$result)), 200);
            }
        }

        private function getheads()
        {
            $CompanyID = $this->_request['CompanyID'];
            
            if(empty($CompanyID))
            {
                $result['Success'] = "error";
                $result['msg']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            
            $sql=mysql_query("select H.HeadID, H.HeadName from tbl_heads H where H.HeadStatus=1 and H.HeadCompanyID='".$CompanyID."' order by H.HeadName ASC", $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              $result['success'] = "error";
              $result['msg']="No record found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i]['HeadID'] = $rlt['HeadID'];
                    $result[$i]['HeadName'] = $rlt['HeadName'];
                    $i++;
                }
                $this->response($this->json(array('success'=>'success', 'data'=>$result)), 200);
            }
        }
        
        private function getactivities()
        {
            $HeadID = $this->_request['HeadID'];
            
            if(empty($HeadID))
            {
                $result['Success'] = "error";
                $result['msg']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            
            $sql=mysql_query("select A.ActivityID, A.ActivityName from tbl_activities A where A.ActivityStatus=1 and A.ActivityHeadID='".$HeadID."' order by A.ActivityName ASC", $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              $result['success'] = "error";
              $result['msg']="No record found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i]['ActivityID'] = $rlt['ActivityID'];
                    $result[$i]['ActivityName'] = $rlt['ActivityName'];
                    $i++;
                }
                $this->response($this->json(array('success'=>'success', 'data'=>$result)), 200);
            }
        }
        
        private function getdepartmentemployee()
        {
            $DepartmentID = $this->_request['DepartmentID'];
            
            if(empty($DepartmentID))
            {
                $result['Success'] = "error";
                $result['msg']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            
            $sql=mysql_query("select U.id, U.username, U.email, U.mobile, U.UserType from users U, tbl_departmentemploy DE where U.id=DE.EmployID and U.UserType=3 and DE.DepartmentID='".$DepartmentID."' order by U.username ASC", $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              $result['success'] = "error";
              $result['msg']="No record found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i]['UserID'] = $rlt['id'];
                    $result[$i]['UserName'] = $rlt['username'];
                    $result[$i]['UserEmail'] = $rlt['email'];
                    $result[$i]['UserMobile'] = $rlt['mobile'];
                    $result[$i]['UserType'] = $rlt['UserType'];
                    $i++;
                }
                $this->response($this->json(array('success'=>'success','data'=>$result)), 200);
            }
        }
        
        private function addtask()
        {
            $TaskDepartmentID = $this->_request['TaskDepartmentID'];
            $TaskAssignToUserID = $this->_request['TaskAssignToUserID'];
            $TaskHeadID = $this->_request['TaskHeadID'];
            $TaskActivityID = $this->_request['TaskActivityID'];
            $TaskName = addslashes($this->_request['TaskName']);
            $TaskType = $this->_request['TaskType'];
            $TaskCategory = $this->_request['TaskCategory'];
            $TaskStartDate = date('Y-m-d',strtotime($this->_request['TaskStartDate']));
            $TaskEndDate = date('Y-m-d',strtotime($this->_request['TaskEndDate']));
            $TaskStartTime = $this->_request['TaskStartTime'];
            $TaskEndTime = $this->_request['TaskEndTime'];
            $TaskDescription = addslashes($this->_request['TaskDescription']);
            $CreatedByUserID = $this->_request['CreatedByUserID'];
            $TaskTimeAlloted = $this->_request['TaskTimeAlloted'];
           $Status='1';
            
            if(empty($TaskDepartmentID) || empty($TaskAssignToUserID) || empty($TaskHeadID) || empty($TaskActivityID) || empty($TaskName) || empty($TaskStartDate) || empty($TaskEndDate) || empty($TaskStartTime) || empty($TaskEndTime) || empty($TaskDescription) || empty($CreatedByUserID) || empty($TaskTimeAlloted) || $TaskType == '' || $TaskCategory == '')
            {
                $result['Success'] = "error";
                $result['msg']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            
             $sql=mysql_query("insert into tbl_tasks set TaskDepartmentID='".$TaskDepartmentID."', TaskHeadID='".$TaskHeadID."', TaskActivityID='".$TaskActivityID."', TaskName='".$TaskName."', TaskType='".$TaskType."', TaskCategory='".$TaskCategory."', TaskCreatedByUserID='".$TaskCreatedByUserID."', TaskAssignToUserID='".$TaskAssignToUserID."', TaskStartDate='".$TaskStartDate."', TaskEndDate='".$TaskEndDate."', TaskStartTime='".$TaskStartTime."', TaskEndTime='".$TaskEndTime."', TaskDescription='".$TaskDescription."', TaskStatus='".$Status."', TaskTimeAlloted='".$TaskTimeAlloted."'", $this->db) or die(mysql_error());
          
            $result['TaskID'] = mysql_insert_id();
            
            $this->response($this->json(array('data'=>$result)), 200);
        }
        
        private function edittaskmanager()
        {
            $TaskID = $this->_request['TaskID'];
            $DepartmentID = $this->_request['DepartmentID'];
            $AssignToUserID = $this->_request['AssignToUserID'];
            $HeadID = $this->_request['HeadID'];
            $ActivityID = $this->_request['ActivityID'];
            $Name = addslashes($this->_request['Name']);
            $Type = $this->_request['Type'];
            $Category = $this->_request['Category'];
            $StartDate = date('Y-m-d',strtotime($this->_request['StartDate']));
            $EndDate = date('Y-m-d',strtotime($this->_request['EndDate']));
            $StartTime = $this->_request['StartTime'];
            $EndTime = $this->_request['EndTime'];
            $Description = addslashes($this->_request['Description']);
            $ManagerRemark = addslashes($this->_request['ManagerRemark']);
            $TimeAlloted = $this->_request['TimeAlloted'];
            
            if(empty($TaskID) || empty($DepartmentID) || empty($AssignToUserID) || empty($HeadID) || empty($ActivityID) || empty($Name) || empty($StartDate) || empty($EndDate) || empty($StartTime) || empty($EndTime) || empty($Description) || empty($TimeAlloted) || $Type == '' || $Category == '')
            {
                $result['status'] = 0;
                $result['msg']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            
            $sql=mysql_query("update tbl_tasks set TaskDepartmentID='".$DepartmentID."', TaskHeadID='".$HeadID."', TaskActivityID='".$ActivityID."', TaskName='".$Name."', TaskType='".$Type."', TaskCategory='".$Category."', TaskAssignToUserID='".$AssignToUserID."', TaskStartDate='".$StartDate."', TaskEndDate='".$EndDate."', TaskStartTime='".$StartTime."', TaskEndTime='".$EndTime."', TaskDescription='".$Description."', TaskManagerRemark='".$ManagerRemark."', TaskTimeAlloted='".$TimeAlloted."' where TaskID='".$TaskID."'", $this->db) or die(mysql_error());
            
            $this->response($this->json(array('success'=>'success')), 200);
        }
        
        private function approvetask()
        {
            $TaskID = $this->_request['TaskID'];

            if(empty($TaskID))
            {
                $result['status'] = '0';
                $result['msg']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            $sql=mysql_query("update tbl_tasks set TaskStatus=TaskStatus+1 where TaskID='".$TaskID."'", $this->db) or die(mysql_error());
            
            $this->response($this->json(array('success'=>'success')), 200);
        }



        private function get_department_list(){
               $user_id = $this->_request['user_id'];
            
            if(empty($user_id))
            {
                
                $result['status']='0';
                $result['message']="User id required";
                $this->response($this->json($result), 200);
            }


 $sql=mysql_query("select d.id, d.department_name from tbl_department d, tbl_departmentemploy DE where DE.DepartmentID=d.id and DE.EmployID='".$user_id."' GROUP BY DE.DepartmentID", $this->db)or die(mysql_error());
// $sql1=mysql_query("select count(EmployID) as total from tbl_departmentemploy where DepartmentID=2 ",$this->db);

  
    
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              
              $result['status']='0';
              $result['message']="No record found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i]['id'] = $rlt['id'];

                     $result[$i]['department_name'] = $rlt['department_name'];
                 
                   
                  
                    $i++;
                }
                $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$result)), 200);
            }

        }



/* with counter in department*/

 private function get_department_list_counter(){
               $user_id = $this->_request['user_id'];
            
            if(empty($user_id))
            {
                
                $result['status']='0';
                $result['message']="User id required";
                $this->response($this->json($result), 200);
            }
            $user= mysql_query("select * from users where id='".$user_id."'");
             $row = mysql_fetch_assoc($user);
             $manager = $row['is_manager'];
             $company_id = $row['UserCompanyID'];
             $supervisor = $row['supervisor'];


             if($company_id && $manager==1){
             
                $sql=mysql_query("SELECT D.id as department_id, D.department_name, (select Count(*) from tbl_departmentemploy where DepartmentID=D.id ) as team_member from tbl_department D where D.company_id='".$company_id."'", $this->db)or die(mysql_error());

             }else{
                  $sql=mysql_query("select d.id as department_id, d.department_name,count(DE.DepEmpID) as team_member from tbl_department d, tbl_departmentemploy DE where DE.DepartmentID=d.id and DE.DepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID='".$user_id."') GROUP BY DE.DepartmentID", $this->db)or die(mysql_error());
           }
            

    
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              
              $result['status']='0';
              $result['message']="No record found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i] = $rlt;
					
                   
                  /* $result[$i]['count']=array();
                  $qry=mysql_query("SELECT COUNT(EmployID) as Employee FROM tbl_departmentemploy where DepartmentID='".$rlt['id']."' ",$this->db);*/

              
                    $i++;
                }
                $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$result)), 200);
            }

        }

        private function get_employeelist_By_department(){
            $department_id = $this->_request['department_id'];
            
            if(empty($department_id))
            {
                $result['status'] ='0';
                $result['message']="Department id required";
                $this->response($this->json($result), 200);
            }
  $sql=mysql_query("select U.id, U.username, U.email, U.mobile,U.profile_pic from users U, tbl_departmentemploy DE where U.id=DE.EmployID and   DE.DepartmentID='".$department_id."' GROUP BY DE.EmployID order by U.username ASC", $this->db)or die(mysql_error());
 
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              
              $result['status']='0';
              $result['message']="No record found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i]['UserID'] = $rlt['id'];
                    $result[$i]['UserName'] = $rlt['username'];
                    // $result[$i]['UserEmail'] = $rlt['email'];
                    $result[$i]['profile_pic'] = $rlt['profile_pic'];
                  
                    $i++;
                }
                $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$result)), 200);
            }
        }

/* department id wise user list all recore megha was here*/
private function get_department_user_list(){
            $department_id = $this->_request['department_id'];
            
            if(empty($department_id))
            {
                $result['status'] ='0';
                $result['message']="Department id required";
                $this->response($this->json($result), 200);
            }
  $sql=mysql_query("select U.* from users U, tbl_departmentemploy DE where U.id=DE.EmployID and   DE.DepartmentID='".$department_id."' GROUP BY DE.EmployID order by U.username ASC", $this->db)or die(mysql_error());
 
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              
              $result['status']='0';
              $result['message']="No record found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i] = $rlt;
                     $i++;
                }
                $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$result)), 200);
            }
        }


/* with counter employee*/
  private function get_employeelist_By_department_counter(){
            $department_id = $this->_request['department_id'];
            
            if(empty($department_id))
            {
                $result['status'] ='0';
                $result['message']="Department id required";
                $this->response($this->json($result), 200);
            }
  $sql=mysql_query("select U.id, U.username, U.email, U.mobile,U.profile_pic from users U, tbl_departmentemploy DE where U.id=DE.EmployID and   DE.DepartmentID='".$department_id."' GROUP BY DE.EmployID order by U.username ASC", $this->db)or die(mysql_error());
 
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              
              $result['status']='0';
              $result['message']="No record found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i]['UserID'] = $rlt['id'];
                    $result[$i]['UserName'] = $rlt['username'];
                    // $result[$i]['UserEmail'] = $rlt['email'];
                    $result[$i]['profile_pic'] = $rlt['profile_pic'];
                    $result[$i]['Task']=array();
                  $qry=mysql_query("SELECT
  (SELECT COUNT(TaskStatus) FROM tbl_tasks where TaskStatus='2' and TaskAssignToUserID='".$rlt['id']."') as Pendding, 
  (SELECT COUNT(TaskStatus) FROM tbl_tasks where TaskStatus='5' and TaskAssignToUserID='".$rlt['id']."') as completed",$this->db);

                   for($r=0; $r=mysql_fetch_assoc($qry,MYSQL_ASSOC);$r++)
           {
            
                 $result[$i]['Task']=$r;
                  // $i++;
             }
                    $i++;
                }
                $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$result)), 200);
            }
        }


         private function get_head_list(){
             $company_id = $this->_request['company_id'];
            
            if(empty($company_id))
            {
               
                $result['status'] ='0';
                $result['message']="head id required";
                $this->response($this->json($result), 200);
            }

            
             $sql=mysql_query("SELECT * FROM tbl_heads where HeadCompanyID='$company_id' ", $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              
              $result['status'] ='0';
              $result['message']="No record found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i]['HeadID'] = $rlt['HeadID'];
                    $result[$i]['HeadCompanyID'] = $rlt['HeadCompanyID'];
                    $result[$i]['HeadName'] = $rlt['HeadName'];
                    $result[$i]['HeadDescription']=$rlt['HeadDescription'];
                    $i++;
                }
                $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$result)), 200);
            }
        }

        private function get_activity_list(){
             $company_id = $this->_request['company_id'];
            
            if(empty($company_id))
            {
               
                $result['status'] ='0';
                $result['message']="company Id required";
                $this->response($this->json($result), 200);
            }

            
             $sql=mysql_query("SELECT h.*,a.* FROM tbl_heads h, tbl_activities a where h.HeadID=a.ActivityHeadID and  HeadCompanyID='".$company_id."' ", $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              
              $result['status'] ='0';
              $result['message']="No record found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i] = $rlt;
                    
                    $i++;
                }
                $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$result)), 200);
            }
        }

/* time sheet task list*/
  private function pending_and_inprogres_tasklist(){
             $user_id = $this->_request['user_id'];
            
            if(empty($user_id))
            {
               
                $result['status'] ='0';
                $result['message']="User id required";
                $this->response($this->json($result), 200);
            }

            
             $sql=mysql_query("SELECT T.TaskID,T.TaskHeadID,T.TaskActivityID,T.TaskName,H.HeadName,A.ActivityName FROM tbl_tasks T, tbl_heads H,tbl_activities A where T.TaskHeadID=H.HeadID and T.TaskActivityID=A.ActivityID and T.TaskStatus = 3  and T.TaskAssignToUserID='".$user_id."'  and Deleted=1", $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              
              $result['status'] ='0';
              $result['message']="No record found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i] = $rlt;
                   
                    //$result[$i]['Employ Name']=$rlt['username'];
                    $i++;
                }
                $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$result)), 200);
            }
        }

         private function get_activity_list_by_head(){
             $head_id = $this->_request['head_id'];
            
            if(empty($head_id))
            {
                $result['status'] = "0";
                $result['message']="Activity head id required";
                $this->response($this->json($result), 200);
            }

            
             $sql=mysql_query("SELECT * FROM tbl_activities where ActivityHeadID='$head_id' ", $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              
              $result['status'] = '0';
              $result['message']="No record found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i]['ActivityID'] = $rlt['ActivityID'];
                    $result[$i]['ActivityName'] = $rlt['ActivityName'];
                    $result[$i]['ActivityStatus'] = $rlt['ActivityStatus'];
                    //$result[$i]['Employ Name']=$rlt['username'];
                    $i++;
                }
                $this->response($this->json(array('message'=>'Activites found','status'=>'1','data'=>$result)), 200);
            }
        }

     /* add task api */

         private function Add_Task_By_Manager()
        {  
            $result = array();
            $department_id=$this->_request['department_id'];
            $head_id=$this->_request['head_id'];
            $activity_id=$this->_request['activity_id'];
            $title=$this->_request['title'];
            $task_type=$this->_request['task_type'];
            $task_category=$this->_request['task_category'];
            $TaskCreatedByUserID=$this->_request['TaskCreatedByUserID'];
            $TaskAssignToUserID=$this->_request['TaskAssignToUserID'];
            $start_date=$this->_request['start_date'];
            $end_date=$this->_request['end_date'];
            $start_time=$this->_request['start_time'];
            $end_time=$this->_request['end_time'];
            $task_description=$this->_request['task_description'];
            $task_status='2';
            $date=time();
            $create_at=date('Y-m-d G:i:s');
            $TaskTimeAlloted=$this->_request['TaskTimeAlloted'];
            $priority=$this->_request['priority'];
            $path = "../uploads/";
            $valid_formats = array("jpg","png","gif","bmp","jpeg","doc","docx","pdf");

              $name = $_FILES['attachment_file']['name'];
              $size = $_FILES['attachment_file']['size'];
              if(strlen($name)) {
                list($txt, $ext) = explode(".", $name);
                if(in_array($ext,$valid_formats)) {
                  if($size<(1024*1024)) {
                    $image_name =$txt."_".$date.".".$ext;
                    $tmp = $_FILES['attachment_file']['tmp_name'];
                    move_uploaded_file($tmp, $path.$image_name);
                      
              }
             }
            }
          
            if(empty($department_id) || empty($TaskCreatedByUserID) || empty($TaskAssignToUserID) ||empty($head_id) || empty($activity_id) || empty($task_description) || empty($start_time) || empty($end_time) || empty($priority) || empty($start_date) || empty($end_date) || empty($title) || empty($task_type) || empty($task_category))
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           

// Use strtotime function 
$strtdate = strtotime($start_date); 
$enddate = strtotime($end_date); 

 
           if($task_category==1){
                             $a=1; 
							for ($i = $strtdate; $i <= $enddate;  
							                                $i += (86400)) { 
							                                      
							$Store = date('Y-m-d', $i); 
							$array[$a] = $Store;
							/*$sql=mysql_query("INSERT into tbl_test (start,end_date) values ('".$array[$a]."','".$array[$a]."')");*/
							$sql = mysql_query("INSERT INTO `tbl_tasks`(`TaskDepartmentID`, `TaskHeadID`, `TaskActivityID`, `TaskName`, `TaskType`, `TaskCategory`, `TaskCreatedByUserID`,`TaskAssignToUserID`,`TaskStartDate`,`TaskEndDate`,`TaskStartTime`,`TaskEndTime`,`TaskDescription`,`TaskStatus`,`priority`,`TaskCreatedAt`,`TaskTimeAlloted`,`TaskAttachment`) VALUES ('".$department_id."','".$head_id."','".$activity_id."','".$title."','".$task_type."','".$task_category."','".$TaskCreatedByUserID."','".$TaskAssignToUserID."','".$array[$a]."','".$array[$a]."','".$start_time."','".$end_time."','".$task_description."','".$task_status."','".$priority."','".$create_at."','".$TaskTimeAlloted."','".$image_name."')", $this->db);
							 $a++;
							 
							} 
           }
            else
            {
                 $sql = mysql_query("INSERT INTO `tbl_tasks`(`TaskDepartmentID`, `TaskHeadID`, `TaskActivityID`, `TaskName`, `TaskType`, `TaskCategory`, `TaskCreatedByUserID`,`TaskAssignToUserID`,`TaskStartDate`,`TaskEndDate`,`TaskStartTime`,`TaskEndTime`,`TaskDescription`,`TaskStatus`,`priority`,`TaskCreatedAt`,`TaskTimeAlloted`,`TaskAttachment`) VALUES ('".$department_id."','".$head_id."','".$activity_id."','".$title."','".$task_type."','".$task_category."','".$TaskCreatedByUserID."','".$TaskAssignToUserID."','".$start_date."','".$end_date."','".$start_time."','".$end_time."','".$task_description."','".$task_status."','".$priority."','".$create_at."','".$TaskTimeAlloted."','".$image_name."')", $this->db);
                 // print_r($sql);
                  
              }
                  /* Notification add task*/

                 
            if ($sql > 0) {

                $assign=mysql_query("select * from users where id='".$TaskAssignToUserID."'",$this->db);
                         $sql = mysql_fetch_array($assign, MYSQL_ASSOC);
                         $assign_name= $sql['username'];
                         $assign_token= $sql['token'];
                         $assign_device= $sql['device_type'];
                $created=mysql_query("select * from users where id='".$TaskCreatedByUserID."'",$this->db);
                        $qry = mysql_fetch_array($created, MYSQL_ASSOC);
                        $create_name= $qry['username'];
                  
                        if($assign_device == "a"){
                             $notification_title = "New task by ".$create_name;
                             $message = $title;

                             $this->send_gcm($assign_token,$assign_name,$notification_title,$message);

                        }elseif($assign_device== 'i'){
                            $notification_title = "New task by ".$create_name;
                            $message = $title;
                            $this->send_fcm($assign_token,$assign_name,$notification_title,$message);
                        }

                $result['TaskID']= mysql_insert_id();
                $result['message'] = "Task Added";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
           }
         
        }
        


         private function delete_Task_By_Manager()
        {  
            $result = array();
            $task_id=$this->_request['task_id'];
           
            // $TaskName = addslashes($this->_request['TaskName']);
           
           
          
            if(empty($task_id) )
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           
            else
            {
                 $sql = mysql_query("UPDATE  tbl_tasks SET Deleted='0' where TaskID='".$task_id."' ", $this->db);
                 // print_r($sql);

               
                $result['message'] = "Task deleted";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
           }
        }
        private function delete_department()
        {  
            $result = array();
            $department_id=$this->_request['department_id'];
           
          
            if(empty($department_id) )
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           $sql=mysql_query("select count(*) as userscout from tbl_departmentemploy where DepartmentID='".$department_id."' ",$this->db)or die(mysql_error());
             $qry = mysql_fetch_array($sql, MYSQL_ASSOC);
            $users= $qry['userscout'];
            if($users!='0'){
                $result['status'] = '0';
                $result['message']="You can not Delete this department";
                $this->response($this->json($result), 200);
            }

            else
            {

                 $sql = mysql_query("Delete  from tbl_department where  id='".$department_id."' ", $this->db);
                 // print_r($sql);

               
                $result['message'] = "Department deleted";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
           }
        }

        /* delete head*/
            private function delete_head()
        {  
            $result = array();
            $head_id=$this->_request['head_id'];
           
           
          
            if(empty($head_id) )
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           $sql=mysql_query("select count(*) as Headcount from tbl_activities where ActivityHeadID='".$head_id."' ",$this->db)or die(mysql_error());
             $qry = mysql_fetch_array($sql, MYSQL_ASSOC);
            $users= $qry['Headcount'];
            if($users!='0'){
                $result['status'] = '0';
                $result['message']="You can not Delete this Head/Client/Project";
                $this->response($this->json($result), 200);
            }

            else
            {

                 $sql = mysql_query("Delete  from tbl_heads where  HeadID='".$head_id."' ", $this->db);
                 // print_r($sql);

               
                $result['message'] = "Head/Client/Project deleted";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
           }
        }

        /*delete role*/
          private function delete_role()
        {  
            $result = array();
            $role_id=$this->_request['role_id'];
           
           
          
            if(empty($role_id) )
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
                 $sql = mysql_query("Delete  from tbl_roles where  RoleID='".$role_id."' ", $this->db);
                

               
                $result['message'] = "Role  deleted";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
           
        }
        /*delete activite*/
           private function delete_activity()
        {  
            $result = array();
            $activity_id=$this->_request['activity_id'];
           
            // $TaskName = addslashes($this->_request['TaskName']);
           
           
          
            if(empty($activity_id) )
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           $sql=mysql_query("select count(*) as userscout from tbl_tasks where TaskActivityID='".$activity_id."' ",$this->db)or die(mysql_error());
             $qry = mysql_fetch_array($sql, MYSQL_ASSOC);
            $users= $qry['userscout'];
            if($users!='0'){
                $result['status'] = '0';
                $result['message']="You can not Delete this Activity";
                $this->response($this->json($result), 200);
            }

            else
            {

                 $sql = mysql_query("Delete  from tbl_activities where  ActivityID='".$activity_id."' ", $this->db);
                 // print_r($sql);

               
                $result['message'] = "Activity deleted";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
           }
        }
        private function edit_task_by_manager()
        {  
            $result = array();
            $task_id = $this->_request['task_id'];
            $department_id=$this->_request['department_id'];
            $head_id=$this->_request['head_id'];
            $activity_id=$this->_request['activity_id'];
            $title=$this->_request['title'];
            $task_type=$this->_request['task_type'];
            $task_category=$this->_request['task_category'];
            $TaskCreatedByUserID=$this->_request['TaskCreatedByUserID'];
            $TaskAssignToUserID=$this->_request['TaskAssignToUserID'];
            $start_date=$this->_request['start_date'];
            $end_date=$this->_request['end_date'];
            $start_time=$this->_request['start_time'];
            $end_time=$this->_request['end_time'];
            $task_description=$this->_request['task_description'];
            $task_status='2';
            $create_at=date('Y-m-d G:i:s');
            $date=time();
            $priority=$this->_request['priority'];
            $TaskTimeAlloted=$this->_request['TaskTimeAlloted'];
            $TaskManagerRemark=$this->_request['TaskManagerRemark'];
            $path = "../uploads/";
            $valid_formats = array("jpg","png","gif","bmp","jpeg","doc","docx","pdf");

              $name = $_FILES['attachment_file']['name'];
              $size = $_FILES['attachment_file']['size'];
              if(strlen($name)) {
                list($txt, $ext) = explode(".", $name);
                if(in_array($ext,$valid_formats)) {
                  if($size<(1024*1024)) {
                    $image_name =$txt."_".$date.".".$ext;
                    $tmp = $_FILES['attachment_file']['tmp_name'];
                    move_uploaded_file($tmp, $path.$image_name);
                      
              }
             }
            }
            if(empty($task_id) || empty($department_id) || empty($TaskAssignToUserID) || empty($head_id) || empty($activity_id) || empty($title) || empty($start_date) || empty($end_date) || empty($start_time) || empty($end_time) || empty($task_description) || empty($task_type) || empty($task_category) || empty($priority))
            {
               
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           
          
                $sql=mysql_query("update tbl_tasks set TaskDepartmentID='".$department_id."', TaskHeadID='".$head_id."', TaskActivityID='".$activity_id."', TaskName='".$title."', TaskType='".$task_type."', TaskCategory='".$task_category."', TaskAssignToUserID='".$TaskAssignToUserID."', TaskStartDate='".$start_date."', TaskEndDate='".$end_date."', TaskStartTime='".$start_time."', TaskEndTime='".$end_time."', TaskDescription='".$task_description."',TaskStatus='".$task_status."',priority='".$priority."', TaskManagerRemark='".$TaskManagerRemark."',TaskCreatedAt='".$create_at."', TaskTimeAlloted='".$TaskTimeAlloted."', TaskAttachment='".$image_name."' where TaskID='".$task_id."'", $this->db) or die(mysql_error());
                if ($sql > 0) {

                $assign=mysql_query("select * from users where id='".$TaskAssignToUserID."'",$this->db);
                         $sql = mysql_fetch_array($assign, MYSQL_ASSOC);
                         $assign_name= $sql['username'];
                         $assign_token= $sql['token'];
                         $assign_device= $sql['device_type'];
                $created=mysql_query("select T.TaskCreatedByUserID, u.username from tbl_tasks T,users u where T.TaskCreatedByUserID=u.id and T.TaskID='".$task_id."'",$this->db);

                        $qry = mysql_fetch_array($created, MYSQL_ASSOC);
                        $create_name= $qry['username'];
                  
                        if($assign_device == "a"){
                             $notification_title = "Task updtaed by".$create_name;
                             $message = $title;
                             $this->send_gcm($assign_token,$assign_name,$notification_title,$message);

                        }elseif($assign_device== 'i'){
                            $notification_title = "Task updtaed by".$create_name;
                            $message = $title;
                            $this->send_fcm($assign_token,$assign_name,$notification_title,$message);
                        }
               
                 $result['message'] = "Task Updated";
                 $result['status'] = '1';
                $this->response($this->json($result), 200);
          }
        }

      public function pending_task_list(){
         $TaskCreatedByUserID = $this->_request['TaskCreatedByUserID'];
          $TaskAssignToUserID = $this->_request['TaskAssignToUserID'];

            $user= mysql_query("select * from users where id='".$TaskCreatedByUserID."'");
	         $row = mysql_fetch_assoc($user);
	         $manager = $row['is_manager'];
	         $company_id = $row['UserCompanyID'];
	         $supervisor = $row['supervisor'];

            if($TaskCreatedByUserID && $supervisor==0 &&  $manager==2){
            $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name, H.HeadName  ,A.ActivityName,  TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D,tbl_heads H, tbl_activities A where T.TaskCreatedByUserID=U.id  and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskStatus=2 and T.Deleted=1 and T.TaskActivityID=A.ActivityID and T.TaskHeadID=H.HeadID and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$TaskCreatedByUserID.'") order by T.TaskID DESC  ', $this->db)or die(mysql_error());
           }

        elseif($TaskAssignToUserID ){
         $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName , TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.TaskStatus=2 and T.Deleted=1 and TaskAssignToUserID="'.$TaskAssignToUserID.'"  order by T.TaskID DESC', $this->db)or die(mysql_error());
     }elseif($TaskCreatedByUserID && $supervisor==1 && $manager==3){
     	$sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName , TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.TaskStatus=2 and T.Deleted=1 and TaskCreatedByUserID="'.$TaskCreatedByUserID.'"  order by T.TaskID DESC', $this->db)or die(mysql_error());
     }
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              
              $result['status'] = '0';
              $result['message']="No recode found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                     $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                        $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                        $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 0 for fix and 1 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 0 for routine and 1 for nonroutine
                       
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus']; 
                        $result[$i]['Priority'] = $rlt['priority']; 
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment'];
                        if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] = ($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                         $i++;
                }
                
                $this->response($this->json(array('status'=>'1','message'=>'Data found' ,'data'=>$result)), 200);
            }
        
    }
        
/*manager and employee task waiting approval status 4  list */
 private function waiting_approval_task_list(){
         $manager_id = $this->_request['manager_id'];
         $employee_id = $this->_request['employee_id'];

         $user= mysql_query("select * from users where id='".$manager_id."'");
         $row = mysql_fetch_assoc($user);
         $manager = $row['is_manager'];
         $company_id = $row['UserCompanyID'];
         $supervisor = $row['supervisor'];
            if($manager_id){
            $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name, H.HeadName  ,A.ActivityName,  TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D,tbl_heads H, tbl_activities A where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskStatus=4 and T.Deleted=1 and T.TaskActivityID=A.ActivityID and T.TaskHeadID=H.HeadID and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$manager_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
        }
        elseif($supervisor==1 && $manager_id==3){
          
               $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name, H.HeadName  ,A.ActivityName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D,tbl_heads H, tbl_activities A where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskStatus=4 and T.Deleted=1 and T.TaskActivityID=A.ActivityID and T.TaskHeadID=H.HeadID and T.TaskCreatedByUserID="'.$manager_id.'"  order by T.TaskID DESC', $this->db)or die(mysql_error());
        }
        	else{
            $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name, H.HeadName  ,A.ActivityName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D,tbl_heads H, tbl_activities A where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskStatus=4 and T.Deleted=1 and T.TaskActivityID=A.ActivityID and T.TaskHeadID=H.HeadID and T.TaskAssignToUserID="'.$employee_id.'"  order by T.TaskID DESC', $this->db)or die(mysql_error());
        }
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
              
              $result['status'] = '0';
              $result['message']="No recode found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                        $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                        $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType']; 
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory']; 
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus']; 
                        $result[$i]['Priority'] = $rlt['priority']; 
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment']; 
                         if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] = ($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                         $i++;
                }
                
                $this->response($this->json(array('status'=>'1','message'=>'Data found' ,'data'=>$result)), 200);
            }
        }

/* pending leave admin, manager, supervisor*/
        private function pending_leaves_list(){
         $user_id = $this->_request['user_id'];
         /*$company_id = $this->_request['company_id'];*/

         $user= mysql_query("select * from users where id='".$user_id."'");
         $row = mysql_fetch_assoc($user);
         $manager = $row['is_manager'];
         $company_id = $row['UserCompanyID'];
         $supervisor = $row['supervisor'];
         if($company_id && $manager==1){
         	$sql=mysql_query('select L.*, U.username, D.department_name from tbl_leaves L, users U, tbl_department D  where  L.LeaveUserID=U.id  and L.LeaveDepartmentID=D.id and L.Deleted=1 and L.LeaveStatus=0  and  D.company_id="'.$company_id.'" order by L.LeaveID DESC', $this->db)or die(mysql_error());
         }elseif($manager==2 && $supervisor==0){
         	 $sql=mysql_query('select L.*, U.username, D.department_name from tbl_leaves L, users U, tbl_department D where L.LeaveUserID!="'.$user_id.'" and L.LeaveUserID=U.id  and L.LeaveDepartmentID=D.id and L.Deleted=1 and L.LeaveStatus=0  and L.LeaveDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by L.LeaveID DESC', $this->db)or die(mysql_error());
         }
         elseif ($manager==3 && $supervisor==1) {
         	 $sql=mysql_query('select L.*, U.username, D.department_name from tbl_leaves L, tbl_supervisor s,users U, tbl_department D where  L.LeaveUserID=U.id  and L.LeaveDepartmentID=D.id and L.Deleted=1 and L.LeaveStatus=0  and L.LeaveUserID=s.user_id and s.supervisor_id="'.$user_id.'"  order by L.LeaveID DESC', $this->db)or die(mysql_error());
         }
           
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="No record found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                        $result[$i]['LeaveID'] = $rlt['LeaveID'];
                        $result[$i]['LeaveUserID'] = $rlt['LeaveUserID'];
                        $result[$i]['LeaveUserName'] = $rlt['username'];
                        $result[$i]['LeaveDepartmentID'] = $rlt['LeaveDepartmentID'];
                        $result[$i]['LeaveDepartmentName'] = $rlt['department_name'];
                        $result[$i]['LeaveSubject'] = $rlt['LeaveSubject'];
                        $result[$i]['LeaveStartDate'] = $rlt['LeaveStartDate'];
                        $result[$i]['LeaveEndDate'] = $rlt['LeaveEndDate'];
                        $result[$i]['LeaveEndTime'] = $rlt['leave_end_time'];
                        $result[$i]['LeaveStartTime'] = $rlt['leave_start_time'];
                        $result[$i]['LeaveDescription'] = $rlt['LeaveDescription'];
                        $result[$i]['LeaveStatus'] = $rlt['LeaveStatus'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['LeaveManagerComment'] = stripslashes($rlt['LeaveManagerComment']);
                        $result[$i]['LeaveCreatedAt'] = $rlt['LeaveCreatedAt']; 
                         $i++;
                }
                
                $this->response($this->json(array('status'=>'1','message'=>'Data found' ,'data'=>$result)), 200);
            }
        }

   private function all_leaves_list(){
         $user_id = $this->_request['user_id'];
         $department_id=$this->_request['department_id'];
         $leave_priority = $this->_request['leave_priority'];
         $leave_status = $this->_request['leave_status'];
         $from_date=$this->_request['from_date'];
         $to_date=$this->_request['to_date'];
         $employee_id=$this->_request['employee_id'];

          if(empty($user_id))
            {
                
                $result['status'] = '0';
                $result['message']="Manager id required";
                $this->response($this->json($result), 200);
            }

             $addquery='';
        if($department_id != 0 && $department_id != '') 
            { 
             $addquery.=' and L.LeaveDepartmentID='.$department_id; 
            }
        if($employee_id != 0 && $employee_id!= '') 
            {
             $addquery.=' and L.LeaveUserID='.$employee_id; 
            }
        if($leave_priority != 0 && $leave_priority!= '') 
            {
             $addquery.=' and L.priority='.$leave_priority; 
            }
        if($from_date != '') 
          { 
            $addquery.=' and L.LeaveStartDate>="'.date('Y-m-d', strtotime($from_date)).'"'; 
          }
        if($to_date != '') 
          { 
            $addquery.=' and  L.LeaveStartDate<="'.date('Y-m-d', strtotime($to_date)).'"'; 
          }
        if(in_array($leave_status, array(0,1,2,3,4,5)) && $leave_status != '%20' && $leave_status != '')
            {
             $addquery.=' and L.LeaveStatus='.$leave_status; 
            }
            
         $user= mysql_query("select * from users where id='".$user_id."'");
         $row = mysql_fetch_assoc($user);
         $manager = $row['is_manager'];
         $company_id = $row['UserCompanyID'];
         $supervisor = $row['supervisor'];
            
             /*   $sql=mysql_query('select L.*, U.username, D.department_name from tbl_leaves L, users U, tbl_department D where  L.LeaveUserID=U.id and   L.LeaveDepartmentID=D.id  and L.LeaveDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")'.$addquery.' order by L.LeaveID DESC', $this->db)or die(mysql_error());*/
            
            if($manager==1){
                 $query ='select L.*, U.username, D.department_name from tbl_leaves L, users U, tbl_department D  where  L.LeaveUserID=U.id  and L.LeaveDepartmentID=D.id and L.Deleted=1  and  D.company_id="'.$company_id.'"'.$addquery.' order by L.LeaveID DESC' ;
                 $sql=mysql_query($query);
         }elseif($manager==2 && $supervisor==0){
            $sql=mysql_query('select L.*, U.username, D.department_name from tbl_leaves L, users U, tbl_department D where  L.LeaveUserID=U.id and   L.LeaveDepartmentID=D.id  and L.LeaveDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")'.$addquery.' order by L.LeaveID DESC', $this->db)or die(mysql_error());
         }
         elseif ($manager==3 && $supervisor==1) {
             $sql=mysql_query('select L.*, U.username, D.department_name from tbl_leaves L, tbl_supervisor s,users U, tbl_department D where  L.LeaveUserID=U.id  and L.LeaveDepartmentID=D.id and L.Deleted=1 and L.LeaveStatus=0  and L.LeaveUserID=s.user_id and s.supervisor_id="'.$user_id.'" '.$addquery.'  order by L.LeaveID DESC', $this->db)or die(mysql_error());
         }
            
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0)
            {
             
              $result['status'] = '0';
              $result['message']="No record found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                        $result[$i]['LeaveID'] = $rlt['LeaveID'];
                        $result[$i]['LeaveUserID'] = $rlt['LeaveUserID'];
                        $result[$i]['LeaveUserName'] = $rlt['username'];
                        $result[$i]['LeaveDepartmentID'] = $rlt['LeaveDepartmentID'];
                        $result[$i]['LeaveDepartmentName'] = $rlt['department_name'];
                        $result[$i]['LeaveSubject'] = $rlt['LeaveSubject'];
                        $result[$i]['LeaveStartDate'] = $rlt['LeaveStartDate'];
                        $result[$i]['LeaveEndDate'] = $rlt['LeaveEndDate'];
                        $result[$i]['LeaveStartTime'] = $rlt['leave_start_time'];
                        $result[$i]['LeaveEndTime'] = $rlt['leave_end_time'];
                        $result[$i]['LeaveDescription'] = $rlt['LeaveDescription'];
                        $result[$i]['LeaveStatus'] = $rlt['LeaveStatus'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['LeaveManagerComment'] = stripslashes($rlt['LeaveManagerComment']);
                        $result[$i]['LeaveCreatedAt'] = $rlt['LeaveCreatedAt']; 
                         $i++;
                }
                
                $this->response($this->json(array('status'=>'1','message'=>'Data found' ,'data'=>$result)), 200);
            }
        }



 private function non_routine_task_list(){
         $TaskCreatedByUserID = $this->_request['TaskCreatedByUserID'];
         $TaskAssignToUserID = $this->_request['TaskAssignToUserID'];
            
            // if(empty($TaskCreatedByUserID))
            // {
                
            //     $result['status'] = '0';
            //     $result['message']="Invalid data provided!";
            //     $this->response($this->json($result), 200);
            // }
            if($TaskCreatedByUserID){
            $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal, T.TaskAttachment from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.TaskCategory=2 and T.Deleted=1 and TaskCreatedByUserID="'.$TaskCreatedByUserID.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$TaskCreatedByUserID.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="No record found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                         $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                         $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 0 for fix and 1 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 0 for routine and 1 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment'];
                        $result[$i]['Priority'] = $rlt['priority'];
                          if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] = ($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                        $i++;  // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                }
                $this->response($this->json(array('message'=>'Non-routine data found','status'=>'1','data'=>$result)), 200);
            }
        }

        if($TaskAssignToUserID){
                   $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName , TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal, T.TaskAttachment from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.TaskCategory=2 and T.Deleted=1 and TaskAssignToUserID="'.$TaskAssignToUserID.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$TaskAssignToUserID.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="No record found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                        $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                        $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 0 for fix and 1 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 0 for routine and 1 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment'];
                          if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] = ($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                        $i++; 
                         // 1- pending approval, 2- Pending , 3 - In Progress ,4 - Waiting for Completetion, 5 - Complete
                }
                $this->response($this->json(array('message'=>'Non-routine data found','status'=>'1','data'=>$result)), 200);
            }
        }
        }


          private function routine_task_list(){
         $TaskCreatedByUserID = $this->_request['TaskCreatedByUserID'];
         $TaskAssignToUserID = $this->_request['TaskAssignToUserID'];
            
            // if(empty($TaskCreatedByUserID))
            // {
               
            //     $result['status'] = '0';
            //     $result['message']="Invalid Manager id provided!";
            //     $this->response($this->json($result), 200);
            // }
         if($TaskCreatedByUserID){
            
            $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName,  TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta , tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.TaskDepartmentID=D.id and T.TaskCategory=1 and T.Deleted=1 and TaskCreatedByUserID="'.$TaskCreatedByUserID.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$TaskCreatedByUserID.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="No record found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                        $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                        $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 0 for fix and 1 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 0 for routine and 1 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];
                         $result[$i]['TaskAttachment'] = $rlt['TaskAttachment'];
                        $result[$i]['Priority'] = $rlt['priority'];

                        if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] = ($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                        $i++;  // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                }
                $this->response($this->json(array('message'=>'Routine data found','status'=>'1','data'=>$result)), 200);
            }
        }

        if($TaskAssignToUserID){

            $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal, T.TaskAttachment from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta , tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.TaskDepartmentID=D.id and T.TaskCategory=1 and T.Deleted=1 and TaskAssignToUserID="'.$TaskAssignToUserID.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$TaskAssignToUserID.'" )  order by T.TaskID DESC', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="No record found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                        $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                        $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 0 for fix and 1 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 0 for routine and 1 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];
                        $result[$i]['Priority'] = $rlt['priority'];
                         if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] = ($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                        $i++;  // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                }
                $this->response($this->json(array('message'=>'Routine data found','status'=>'1','data'=>$result)), 200);
            }
        }
        }
           // baiges counters in dashboard
          private function weakly_badges_counter(){

            $user_id= $this->_request['user_id'];
             if(empty($user_id)){
                $result['status'] ='0';
                $result['message']="user id required";
                $this->response($this->json($result), 200);
            }
            $count=mysql_query("SELECT
                      (SELECT COUNT(*) FROM tbl_tasks where Deleted=1 and  TaskCreatedByUserID='".$user_id."' and TaskStartDate > (DATE(NOW()) - INTERVAL 7 DAY) ) as employee, 
                      (SELECT COUNT(*) FROM tbl_tasks where Deleted=1 and  TaskAssignToUserID='".$user_id."' and TaskStartDate > (DATE(NOW()) - INTERVAL 7 DAY) ) as manger
                      ",$this->db);
             $result = array();
              $i=0;
            if(mysql_num_rows($count) == 0){

             $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
             }
             else{
            $rlt = mysql_fetch_array($count, MYSQL_ASSOC);
            // $rlt['Time']=$total_time;
            // $rlt['Deviation']=$Deviation;
            $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$rlt)), 200);
        }
          }


          /* admin dashboard counters*/

          private function admin_counters()
        {
            $company_id = $this->_request['company_id'];
          
            if(empty($company_id)){
                $result['status'] ='0';
                $result['message']="Company id required";
                $this->response($this->json($result), 200);
            }
         
            $count=mysql_query("SELECT
                      (SELECT COUNT(*) FROM users where UserCompanyID='".$company_id."') as users, 
                      (SELECT COUNT(*) FROM tbl_department where  company_id='".$company_id."') as departments,
                      (SELECT COUNT(*) FROM tbl_heads where  HeadCompanyID='".$company_id."') as heads,
                      (SELECT COUNT(*) FROM tbl_roles where  RoleID IN (1,2) OR RoleCompanyID='".$company_id."') as roles,
                      (select count(*) from tbl_heads h, tbl_activities a where h.HeadID=a.ActivityHeadID and h.HeadCompanyID='".$company_id."') as activites,
                      (select count(*) from tbl_tasks t, tbl_department d where t.Deleted=1 and d.company_id='".$company_id."' and t.TaskDepartmentID=d.id) as tasks,
                      (select count(*) from tbl_timesheet t, users u where u.UserCompanyID='".$company_id."' and t.submit_user_id=u.id) as timesheets,
                      (select count(*) from tbl_coffecorner t, users u where u.UserCompanyID='".$company_id."' and t.CoffeCornerUserID=u.id) as coffeecorner,
                      (select count(*) from tbl_bulletinboard t, users u where u.UserCompanyID='".$company_id."' and t.BulletinUserID=u.id) as bulletinboard",$this->db);

            $taskcounter=mysql_query("SELECT
                       (select count(*) from tbl_tasks t, tbl_department d where d.company_id='".$company_id."' and t.TaskStatus=1 and t.TaskDepartmentID=d.id and t.Deleted=1) as sendback,
                      (select count(*) from tbl_tasks t, tbl_department d where d.company_id='".$company_id."' and t.TaskStatus=2 and t.TaskDepartmentID=d.id and t.Deleted=1) as pending,
                      (select count(*) from tbl_tasks t, tbl_department d where d.company_id='".$company_id."' and t.TaskStatus=3 and t.TaskDepartmentID=d.id and t.Deleted=1) as inprogress,
                      (select count(*) from tbl_tasks t, tbl_department d where d.company_id='".$company_id."' and t.TaskStatus=4 and t.TaskDepartmentID=d.id and t.Deleted=1) as waiting_approval,
                      (select count(*) from tbl_tasks t, tbl_department d where d.company_id='".$company_id."' and t.TaskStatus=5 and t.TaskDepartmentID=d.id and t.Deleted=1) as completed",$this->db);

             $result = array();
              $i=0;
            if(mysql_num_rows($count) == 0){

             $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
             }
             else{
            $rlt = mysql_fetch_array($count, MYSQL_ASSOC);
            $task = mysql_fetch_array($taskcounter, MYSQL_ASSOC);
            // $rlt['Time']=$total_time;
            // $rlt['Deviation']=$Deviation;
            $this->response($this->json(array('status'=>'1','message'=>'Data found','counter'=>$rlt,'taskcounter'=>$task)), 200);
        }
    
         }  
          // counter manager 
         private function dashboard_counter_team()
        {
            $user_id = $this->_request['user_id'];
          
            if(empty($user_id)){
                $result['status'] ='0';
                $result['message']="user id required";
                $this->response($this->json($result), 200);
            }

            $count=mysql_query("SELECT
                      (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=2 and Deleted=1 and TaskCreatedByUserID='".$user_id."' and TaskStartDate > (DATE(NOW()) - INTERVAL 7 DAY) ) as pending, 
                      (SELECT COUNT(*) FROM tbl_tasks where Deleted=1 and  TaskCreatedByUserID='".$user_id."' and TaskStartDate > (DATE(NOW()) - INTERVAL 7 DAY) ) as total_task,
                      (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=3 and Deleted=1 and TaskCreatedByUserID='".$user_id."' and TaskStartDate > (DATE(NOW()) - INTERVAL 7 DAY) ) as In_progress,
                      (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=5 and Deleted=1 and TaskCreatedByUserID='".$user_id."' and TaskStartDate > (DATE(NOW()) - INTERVAL 7 DAY)) as completed,
                      (select COALESCE(ROUND( SUM( TIME_TO_SEC(TIMEDIFF(TaskEndTime, TaskStartTime))/3600) ),0)  from tbl_tasks where Deleted=1 and TaskCreatedByUserID='".$user_id."')as Time,
                      (SELECT COALESCE(ROUND( SUM( TIME_TO_SEC( TIMEDIFF( TaskSubmitEndTime, TaskSubmitStartTime ) ) /3600 )),0) FROM tbl_tasksubmits S LEFT JOIN tbl_tasks T ON T.TaskID = S.TaskSubmitTaskID WHERE S.Deleted=1 and T.TaskCreatedByUserID ='".$user_id."')AS TimeSubmit",$this->db);

             $result = array();
              $i=0;
            if(mysql_num_rows($count) == 0){

             $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
             }
             else{
            $rlt = mysql_fetch_array($count, MYSQL_ASSOC);
            // $rlt['Time']=$total_time;
            // $rlt['Deviation']=$Deviation;
            $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$rlt)), 200);
        }
    
         }  
          // new api deshboard counter
           private function team_counter()
        {
            $user_id = $this->_request['user_id'];
            $company_id = $this->_request['company_id'];
            $date=date('Y-m-d');

             $user= mysql_query("select * from users where id='".$user_id."'");
	         $row = mysql_fetch_assoc($user);
	         $manager = $row['is_manager'];
	        /* $company_id = $row['UserCompanyID'];*/
	         $supervisor = $row['supervisor'];
            if($company_id){
                 $count=mysql_query("SELECT
                      (select count(*) from tbl_tasks t, tbl_department d where t.Deleted=1 and t.TaskStatus=2 and d.company_id='".$company_id."' and t.TaskDepartmentID=d.id) as pending_task, 
                      (SELECT COUNT(*) FROM tbl_tasks t, tbl_department d where t.TaskStatus=4 and t.Deleted=1  and d.company_id='".$company_id."' and t.TaskDepartmentID=d.id) as waiting_approval,
                      (SELECT COUNT(*) FROM tbl_timesheet t ,tbl_department d  where   d.company_id='".$company_id."' and t.department_id=d.id) as Timesheet,
                      (select count(*) from  users  where  UserCompanyID='".$company_id."') as Team,
                      (SELECT COUNT(*) FROM tbl_attendance t, tbl_department d where  d.company_id='".$company_id."' and t.department_id=d.id and DATE_FORMAT(signin_time,'%Y-%m-%d')='".$date."') as attendance,
                      (select COALESCE(ROUND( SUM( TIME_TO_SEC(TIMEDIFF(TaskEndTime, TaskStartTime))/3600) ),0)   from tbl_tasks t, tbl_department d where t.Deleted=1 and  d.company_id='".$company_id."' and t.TaskDepartmentID=d.id)as Time,
                      (SELECT COALESCE(ROUND( SUM( TIME_TO_SEC( TIMEDIFF( TaskSubmitEndTime, TaskSubmitStartTime ) ) /3600 )),0)  FROM tbl_tasksubmits S LEFT JOIN tbl_tasks T  ON T.TaskID = S.TaskSubmitTaskID JOIN tbl_department D WHERE S.Deleted=1 and  D.company_id='".$company_id."' and T.TaskDepartmentID=D.id)AS TimeSubmit",$this->db);
            }
            elseif($supervisor==1 && $manager==3){
              $count=mysql_query("SELECT
                      (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=2 and Deleted=1 and TaskAssignToUserID='".$user_id."') as pending_task, 
                      (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=4 and Deleted=1  and TaskAssignToUserID='".$user_id."') as waiting_approval,
                      (SELECT COUNT(*) FROM tbl_timesheet t, tbl_supervisor s  where s.user_id=t.	submit_user_id and s.user_id='".$user_id."') as Timesheet,
                      (SELECT COUNT(*) FROM tbl_supervisor where  supervisor_id='".$user_id."') as Team,
                      (SELECT COUNT(*) FROM tbl_attendance a, tbl_supervisor s where a.user_id=s.user_id and s.supervisor_id='".$user_id."' and DATE_FORMAT(signin_time,'%Y-%m-%d')='".$date."') as attendance,
                      (SELECT COALESCE(ROUND( SUM( TIME_TO_SEC(TIMEDIFF(TaskEndTime, TaskStartTime))/3600) ),0)   from tbl_tasks t, tbl_supervisor s where t.Deleted=1 and TaskCreatedByUserID='".$user_id."') as Time,
                      (SELECT COALESCE(ROUND( SUM( TIME_TO_SEC( TIMEDIFF(TaskSubmitEndTime, TaskSubmitStartTime ) ) /3600 )),0)  from tbl_tasksubmits t , tbl_supervisor s where  t.TaskSubmitUserID=s.user_id and t.Deleted=1 and s.supervisor_id='".$user_id."') AS TimeSubmit",$this->db);
            }
            else
            {
             $count=mysql_query("SELECT
                      (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=2 and Deleted=1 and TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID='".$user_id."')) as pending_task, 
                      (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=4 and Deleted=1  and TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID='".$user_id."')) as waiting_approval,
                      (SELECT COUNT(*) FROM tbl_timesheet where  department_id IN (select DepartmentID from tbl_departmentemploy where EmployID='".$user_id."')) as Timesheet,
                      (SELECT COUNT(*) FROM tbl_departmentemploy where EmployID!='".$user_id."' and DepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID='".$user_id."')) as Team,
                      (SELECT COUNT(*) FROM tbl_attendance where department_id IN (select DepartmentID from tbl_departmentemploy where EmployID='".$user_id."') and DATE_FORMAT(signin_time,'%Y-%m-%d')='".$date."' ) as attendance,
                      (select COALESCE(ROUND( SUM( TIME_TO_SEC(TIMEDIFF(TaskEndTime, TaskStartTime))/3600) ),0)   from tbl_tasks where Deleted=1 and TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID='".$user_id."'))as Time,
                      (SELECT COALESCE(ROUND( SUM( TIME_TO_SEC( TIMEDIFF( TaskSubmitEndTime, TaskSubmitStartTime ) ) /3600 )),0)  FROM tbl_tasksubmits S LEFT JOIN tbl_tasks T ON T.TaskID = S.TaskSubmitTaskID WHERE S.Deleted=1 and TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID='".$user_id."'))AS TimeSubmit",$this->db);
            }
            

             $result = array();
              $i=0;
            if(mysql_num_rows($count) == 0){

             $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
             }
             else{
            $rlt = mysql_fetch_array($count, MYSQL_ASSOC);
            // $rlt['Time']=$total_time;
            // $rlt['Deviation']=$Deviation;
            $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$rlt)), 200);
        }
    
    
    
         }  
        
        // counters employee  
        private function dashboard_counters_user(){
           $user_id = $this->_request['user_id'];

            if(empty($user_id))
            {
                $result['status'] ='0';
                $result['message']="user id required";
                $this->response($this->json($result), 200);
            }

         $counts=mysql_query("SELECT
                  (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=2 and TaskAssignToUserID='".$user_id."' and TaskStartDate > (DATE(NOW()) - INTERVAL 7 DAY) ) as pending,
                  (SELECT COUNT(*) FROM tbl_tasks where  TaskAssignToUserID='".$user_id."' and TaskStartDate > (DATE(NOW()) - INTERVAL 7 DAY) ) as total_task, 
                  (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=3 and TaskAssignToUserID='".$user_id."' and TaskStartDate > (DATE(NOW()) - INTERVAL 7 DAY) ) as In_progress,
                  (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=5 and TaskAssignToUserID='".$user_id."' and TaskStartDate > (DATE(NOW()) - INTERVAL 7 DAY)) as completed,
                  (select COALESCE(SUM(TaskTimeAlloted),0)  from tbl_tasks where TaskAssignToUserID='".$user_id."')as TaskTimeAlloted,
                  (select COALESCE(ROUND( SUM( TIME_TO_SEC(TIMEDIFF(TaskEndTime, TaskStartTime))/3600) ),0) from tbl_tasks where TaskAssignToUserID='".$user_id."')as TimeAssigned,
                  (select COALESCE(ROUND( SUM(TIME_TO_SEC(TIMEDIFF(TaskSubmitEndTime, TaskSubmitStartTime))/3600) ),0)  from tbl_tasksubmits where  TaskSubmitUserID='".$user_id."'and Deleted=1) as TimeSubmit ",$this->db);

    

             $result = array();
              $i=0;
            if(mysql_num_rows($counts) == 0){

             $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
             }
             else{
            $rlt = mysql_fetch_array($counts, MYSQL_ASSOC);
            
            $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$rlt)), 200);
        }
        }

 // employee single counter in dash board with new changes 
       private function employee_counter(){
           $employee_id = $this->_request['employee_id'];
           $date=date('Y-m-d');
            if(empty($employee_id))
            {
                $result['status'] ='0';
                $result['message']="user id required";
                $this->response($this->json($result), 200);
            }
         
         $num= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$employee_id."'");
           $row = mysql_fetch_assoc($num);
           $department_id = $row['DepartmentID'];
         $counts=mysql_query("SELECT
                  (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=2 and Deleted=1 and TaskAssignToUserID='".$employee_id."') as pending_task, 
                  (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=4 and Deleted=1 and TaskAssignToUserID='".$employee_id."') as waiting_approval,
                  (SELECT COUNT(*) FROM tbl_timesheet  where    submit_user_id='".$employee_id."') as Timesheet,
                      (SELECT COUNT(*) FROM tbl_departmentemploy where  DepartmentID='".$department_id."') as Team,
                      (SELECT COUNT(*) FROM tbl_attendance where user_id='".$employee_id."' and DATE_FORMAT(signin_time,'%Y-%m-%d')='".$date."' ) as attendance,
                  (select COALESCE(SUM(TaskTimeAlloted),0)  from tbl_tasks where TaskAssignToUserID='".$employee_id."')as TaskTimeAlloted,
                  (select COALESCE(ROUND( SUM( TIME_TO_SEC(TIMEDIFF(TaskEndTime, TaskStartTime))/3600) ),0)  from tbl_tasks where  Deleted=1 and TaskAssignToUserID='".$employee_id."')as TimeAssigned,
                  (select COALESCE(ROUND( SUM( TIME_TO_SEC(TIMEDIFF(TaskSubmitEndTime, TaskSubmitStartTime))/3600) ),0)  from tbl_tasksubmits where  TaskSubmitUserID='".$employee_id."' and Deleted=1 ) as TimeSubmit  ",$this->db);

    

             $result = array();
              $i=0;
            if(mysql_num_rows($counts) == 0){

             $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
             }
             else{
            $rlt = mysql_fetch_array($counts, MYSQL_ASSOC);
            
            $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$rlt)), 200);
        }
        }
        // dashboard wheel counter task status

 private function dashboard_wheel_counter(){

           $user_id = $this->_request['user_id'];
           $manager_id = $this->_request['manager_id'];
           $company_id = $this->_request['company_id'];
           $supervisor_id = $this->_request['supervisor_id'];


          $user= mysql_query("select * from users where id='".$user_id."'");
          $row = mysql_fetch_assoc($user);
          $manager = $row['is_manager'];
          /* $company_id = $row['UserCompanyID'];*/
          $supervisor = $row['supervisor'];
          
        if($user_id){
           $count=mysql_query("SELECT
          (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=2 and Deleted=1 and TaskAssignToUserID='".$user_id."') as Pending, 
          (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=4 and Deleted=1 and TaskAssignToUserID='".$user_id."') as Waiting_for_approval,
          (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=3 and Deleted=1 and TaskAssignToUserID='".$user_id."') as In_progress,
          (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=1 and Deleted=1 and  TaskAssignToUserID='".$user_id."') as sendback,
          (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=5 and Deleted=1 and TaskAssignToUserID='".$user_id."') as completed",$this->db); 
        } elseif($supervisor_id){
             $count=mysql_query("SELECT
                       (select count(*) from tbl_tasks t, tbl_supervisor s where s.supervisor_id='".$supervisor_id."' and t.TaskStatus=1 and t.Deleted=1 and t.TaskCreatedByUserID=s.supervisor_id ) as sendback,
                      (select count(*) from tbl_tasks t, tbl_supervisor s where s.supervisor_id='".$supervisor_id."' and t.Deleted=1 and  t.TaskStatus=2 and t.TaskCreatedByUserID=s.supervisor_id) as Pending,
                      (select count(*) from  tbl_tasks t, tbl_supervisor s where s.supervisor_id='".$supervisor_id."' and  t.Deleted=1 and   t.TaskStatus=3 and t.TaskCreatedByUserID=s.supervisor_id) as In_progress,
                      (select count(*) from  tbl_tasks t, tbl_supervisor s where s.supervisor_id='".$supervisor_id."' and t.Deleted=1 and   t.TaskStatus=4 and t.TaskCreatedByUserID=s.supervisor_id) as Waiting_for_approval,
                      (select count(*) from  tbl_tasks t, tbl_supervisor s where s.supervisor_id='".$supervisor_id."' and t.TaskStatus=5 and t.Deleted=1 and t.TaskCreatedByUserID=s.supervisor_id) as completed",$this->db);
        }

        elseif($company_id){
             $count=mysql_query("SELECT
                       (select count(*) from tbl_tasks t, tbl_department d where d.company_id='".$company_id."' and t.TaskStatus=1 and t.Deleted=1 and t.TaskDepartmentID=d.id) as sendback,
                      (select count(*) from tbl_tasks t, tbl_department d where d.company_id='".$company_id."' and t.TaskStatus=2 and t.Deleted=1 and t.TaskDepartmentID=d.id) as Pending,
                      (select count(*) from tbl_tasks t, tbl_department d where d.company_id='".$company_id."' and t.TaskStatus=3 and  t.Deleted=1  and t.TaskDepartmentID=d.id) as In_progress,
                      (select count(*) from tbl_tasks t, tbl_department d where d.company_id='".$company_id."' and t.TaskStatus=4 and t.Deleted=1 and t.TaskDepartmentID=d.id) as Waiting_for_approval,
                      (select count(*) from tbl_tasks t, tbl_department d where d.company_id='".$company_id."' and t.TaskStatus=5 and  t.Deleted=1 and t.TaskDepartmentID=d.id) as completed",$this->db);
        }else{
             $count=mysql_query("SELECT
                  (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=2 and Deleted=1 and  TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID='".$manager_id."')) as Pending,
                  (SELECT COUNT(*) FROM tbl_tasks where  TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID='".$manager_id."')) as total_task, 
                  (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=3 and Deleted=1 and TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID='".$manager_id."')) as In_progress,
                  (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=5 and Deleted=1 and TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID='".$manager_id."')) as completed,
                  (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=4 and Deleted=1 and TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID='".$manager_id."')) as Waiting_for_approval,
                  (SELECT COUNT(*) FROM tbl_tasks where TaskStatus=1 and  Deleted=1 and TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID='".$manager_id."')) as sendback",$this->db);
         }

             $result = array();
              $i=0;
            if(mysql_num_rows($count) == 0){

             $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
             }
             else{
            $rlt = mysql_fetch_array($count, MYSQL_ASSOC);
           
            $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$rlt)), 200);
        }
    
    
        }

private function dashboard_percentage_graph(){
           $user_id = $this->_request['user_id'];
          
            if(empty($user_id)){
                $result['status'] ='0';
                $result['message']="user id required";
                $this->response($this->json($result), 200);
            }
  
        
            $count=mysql_query("SELECT
          (SELECT   COALESCE(concat(round(( TaskStatus/TaskCreatedByUserID * 100 ),2)),0) 
        FROM tbl_tasks where TaskCreatedByUserID='".$user_id."' and Deleted=1 and TaskStatus=2
        GROUP BY TaskCreatedByUserID)AS pendingtask, 
          (SELECT  COALESCE(concat(round(( TaskStatus/TaskCreatedByUserID * 100 ),2)),0)
        FROM tbl_tasks where TaskCreatedByUserID='".$user_id."' and Deleted=1 and TaskStatus=5
        GROUP BY TaskCreatedByUserID) AS completetask,
        (SELECT  COALESCE(concat(round(( TaskStatus/TaskCreatedByUserID * 100 ),2)),0)
        FROM tbl_tasks where TaskCreatedByUserID='".$user_id."' and Deleted=1 and TaskStatus=3
        GROUP BY TaskCreatedByUserID) AS inprogresstask,
           (SELECT  COALESCE(concat(round(( TaskStatus/TaskCreatedByUserID * 100 ),2)),0)
        FROM tbl_tasks where TaskCreatedByUserID='".$user_id."' and Deleted=1 and TaskStatus=4
        GROUP BY TaskCreatedByUserID) AS approvaltask",$this->db);

             $result = array();
              $i=0;
            if(mysql_num_rows($count) == 0){

             $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
             }
             else{
            $rlt = mysql_fetch_array($count, MYSQL_ASSOC);
           
            $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$rlt)), 200);
        }
    
    
        }

       private function emp_dashboard_percentage_graph(){
           $user_id = $this->_request['user_id'];
          
            if(empty($user_id)){
                $result['status'] ='0';
                $result['message']="user id required";
                $this->response($this->json($result), 200);
            }
  
        
            $count=mysql_query("SELECT
          (SELECT   COALESCE(concat(round(( TaskStatus/TaskAssignToUserID * 100 ),2)),0) 
        FROM tbl_tasks where TaskAssignToUserID='".$user_id."' and TaskStatus=2
        GROUP BY TaskAssignToUserID)AS pendingtask, 
          (SELECT  COALESCE(concat(round(( TaskStatus/TaskAssignToUserID * 100 ),2)),0)
        FROM tbl_tasks where TaskAssignToUserID='".$user_id."' and TaskStatus=5
        GROUP BY TaskAssignToUserID) AS completetask,
        (SELECT  COALESCE(concat(round(( TaskStatus/TaskAssignToUserID * 100 ),2)),0)
        FROM tbl_tasks where TaskAssignToUserID='".$user_id."' and TaskStatus=3
        GROUP BY TaskAssignToUserID) AS inprogresstask,
           (SELECT COALESCE(concat(round(( TaskStatus/TaskAssignToUserID * 100 ),2)),0)
        FROM tbl_tasks where TaskAssignToUserID='".$user_id."' and TaskStatus=4
        GROUP BY TaskAssignToUserID) AS approvaltask",$this->db);

             $result = array();
              $i=0;
            if(mysql_num_rows($count) == 0){

             $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
             }
             else{
            $rlt = mysql_fetch_array($count, MYSQL_ASSOC);
           
            $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$rlt)), 200);
        }
    
    
        }




        private function get_bulletinboard(){
             $company_id = $this->_request['company_id'];
            
            if(empty($company_id))
            {
                $result['status'] ='0';
                $result['message']="company id required";
                $this->response($this->json($result), 200);
            }

            
             $sql=mysql_query("SELECT b.* , u.id FROM tbl_bulletinboard b, users u where u.id=b.BulletinUserID and UserCompanyID='".$company_id."' order by b.BulletinUserID DESC", $this->db)or die(mysql_error());
             
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i] = $rlt;
                    
                    $i++;
                }
                $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$result)), 200);
            }
        }


          private function get_Coffee_Corner(){
             $UserCompanyID = $this->_request['UserCompanyID'];
             $user_id = $this->_request['user_id'];
            
            if(empty($user_id))
            {
                $result['status'] ='0';
                $result['message']="company id required";
                $this->response($this->json($result), 200);
            }

           $user= mysql_query("select * from users where id='".$user_id."'");
             $row = mysql_fetch_assoc($user);
             $manager = $row['is_manager'];
             $company_id = $row['UserCompanyID'];
             $supervisor = $row['supervisor'];
             $sql=mysql_query("SELECT tc.*,u.UserCompanyID,(select  COALESCE(like_dislike,'0')  from tbl_coffeecorner_like where coffeecorner_id=tc.CoffeeCornerID and user_id='".$user_id."') as like_dislike, (select Count(*) from tbl_coffeecorner_like where coffeecorner_id=tc.CoffeeCornerID and like_dislike=1 ) as likes  , (select Count(*) from tbl_coffeecorner_comments where coffeecorner_id=tc.CoffeeCornerID ) as comments FROM tbl_coffecorner tc, users u, tbl_coffeecorner_like l where  tc.CoffeCornerUserID=u.id    and tc.Deleted=1 and u.UserCompanyID='".$company_id."' group by CoffeeCornerID", $this->db)or die(mysql_error());
            
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i] = $rlt;
                     
			           
                        $i++;
                }
                $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$result)), 200);
            }
        }

        //noddy was here

        private function get_Coffee_Corner_limit(){
             $UserCompanyID = $this->_request['UserCompanyID'];
            
            if(empty($UserCompanyID))
            {
                $result['status'] ='0';
                $result['message']="company id required";
                $this->response($this->json($result), 200);
            }

            
            
             $sql=mysql_query("SELECT tc.*,u.UserCompanyID FROM tbl_coffecorner tc, users u where  tc.CoffeCornerUserID=u.id and  u.UserCompanyID='".$UserCompanyID."' and tc.Deleted=1 LIMIT 4", $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i] = $rlt;
                    
                    $i++;
                }
                $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$result)), 200);
            }
        }

        //noddy was here

          private function update_leave_by_manager()
        {  
            $result = array();
            $LeaveManagerComment =addslashes($this->_request['LeaveManagerComment']);
            $leave_status =$this->_request['leave_status'];
            $leave_id=$this->_request['leave_id'];
          
          
            if(empty($leave_id)|| empty($LeaveManagerComment)|| empty($leave_status))
            {
                 $result['status'] = "0";
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           switch ($leave_status) {
               case '2':
                 $sql=mysql_query("update  tbl_leaves set LeaveManagerComment='".$LeaveManagerComment."', LeaveStatus='".$leave_status."' where LeaveID='".$leave_id."'", $this->db) or die(mysql_error());
               
                 $result['message'] = "Leave disaproved";
                 $result['status'] = '1';
                $this->response($this->json($result), 200);
                break;
                case '3':
                 $sql=mysql_query("update  tbl_leaves set LeaveManagerComment='".$LeaveManagerComment."', LeaveStatus='".$leave_status."' where LeaveID='".$leave_id."'", $this->db) or die(mysql_error());
               
                 $result['message'] = "Leave send back";
                 $result['status'] = '1';
                $this->response($this->json($result), 200);
                break;
                case '1':
                  $sql=mysql_query("update  tbl_leaves set LeaveManagerComment='".$LeaveManagerComment."', LeaveStatus='".$leave_status."' where LeaveID='".$leave_id."'", $this->db) or die(mysql_error());
               
                 $result['message'] = "Leave approved";
                 $result['status'] = '1';
                $this->response($this->json($result), 200);
                break;
               default:
                   # code...
                   break;
           }
         
        
        }


 private function Approve_OR_Disapproved_Task()
        {  
            $result = array();
            $emp_remark = $this->_request['emp_remark'];
            $manger_remark = addslashes($this->_request['manger_remark']);
            $task_status =$this->_request['task_status'];
            $task_id=$this->_request['task_id'];
           
            if(empty($task_id)|| empty($task_status))
            {
                 $result['status'] = "0";
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           
           if($task_status == '3')
            {
                $sql=mysql_query("update  tbl_tasks set TaskEmployeeRemark='".$emp_remark."', TaskManagerRemark='".$manger_remark."', TaskStatus='".$task_status."' where TaskID='".$task_id."'", $this->db) or die(mysql_error());
               
                 $result['message'] = "In progress task";
                 $result['status'] = '1';
                $this->response($this->json($result), 200);
          }
          if($task_status == '4')
            {
                $sql=mysql_query("update  tbl_tasks set TaskEmployeeRemark='".$emp_remark."', TaskManagerRemark='".$manger_remark."', TaskStatus='".$task_status."' where TaskID='".$task_id."'", $this->db) or die(mysql_error());
               
                 $result['message'] = "Task waiting for approval";
                 $result['status'] = '1';
                $this->response($this->json($result), 200);
          }
          if($task_status == '5')
            {

            $sql=mysql_query("select * from  tbl_tasksubmits where TaskSubmitTaskID='".$task_id."' and Submission_status=2 and Deleted=1 and Submission_status IN (2 ,4)",$this->db)or die(mysql_error());
            if(mysql_num_rows($sql) > 0){
                $result['status'] = 0;
                $result['message']="Approve all Timesheet Line items to proceed!";
                $this->response($this->json($result), 200);
            }
            else{
                $sql=mysql_query("update  tbl_tasks set TaskEmployeeRemark='".$emp_remark."', TaskManagerRemark='".$manger_remark."', TaskStatus='".$task_status."' where TaskID='".$task_id."'", $this->db) or die(mysql_error());
               
                 $result['message'] = "Task Completed";
                 $result['status'] = '1';
                $this->response($this->json($result), 200);
               }
         }
          
        }


     // get attendance list
      private function emp_attendance_list(){
          
         $user_id = $this->_request['user_id'];
         $date = date('Y-m-d');  
            if(empty($user_id))
            {
               
                $result['status'] ='0';
                $result['message']="company id required";
                $this->response($this->json($result), 200);
            }

             $pass= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$user_id."'");
             $row = mysql_fetch_assoc($pass);
             $department_id=$row["DepartmentID"];
             $sql=mysql_query("select D.* ,A.*,u.username from tbl_department D, tbl_attendance A, users u where A.user_id=u.id and A.user_id!='".$user_id."' and D.id=A.department_id and DATE_FORMAT(A.signin_time,'%Y-%m-%d')='".$date."' and A.department_id='".$department_id."' order by u.username ASC", $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i]['attendance_id'] = $rlt['attendance_id'];
                    $result[$i]['user_id'] = $rlt['user_id'];
                    $result[$i]['emp_name'] = $rlt['username'];
                    $result[$i]['manager_id'] = $rlt['manager_id'];
                    $result[$i]['department_id'] = $rlt['department_id'];
                    $result[$i]['department_name'] = $rlt['department_name'];
                    $result[$i]['signin_time']=$rlt['signin_time'];
                    $result[$i]['signin_location'] = $rlt['signin_location'];
                    $result[$i]['add_lat'] = $rlt['add_lat'];
                    $result[$i]['add_long']=$rlt['add_long'];
                    $result[$i]['emp_atten_status'] = $rlt['emp_atten_status'];
                    $result[$i]['emp_atten_color']=$rlt['emp_atten_color'];
                    $result[$i]['boss_atten_status'] = $rlt['boss_atten_status'];
                    $result[$i]['boss_atten_color   ']=$rlt['boss_atten_color   '];
                    $result[$i]['moth_year'] = $rlt['moth_year'];
                    $result[$i]['boss_comment']=$rlt['boss_comment'];
                    $result[$i]['Status'] = $rlt['Status'];
                    $result[$i]['signout_time']=$rlt['signout_time'];
                    $result[$i]['view_status'] = $rlt['view_status'];
                    $result[$i]['signout_location'] = $rlt['signout_location'];
                    $result[$i]['login_status']=$rlt['login_status'];

                    $i++;
                }
                $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$result)), 200);
            }
    }

// update profile user
      private function update_profile_by_user()
        {  
            $result = array();
            $mobile = $this->_request['mobile'];
            $first_name = addslashes($this->_request['first_name']);
            $last_name= addslashes($this->_request['last_name']);
            $country=$this->_request['country'];
            $city=$this->_request['city'];
            $user_dob=$this->_request['user_dob'];
            $state=$this->_request['state'];
            $user_about=$this->_request['user_about'];
            $profile_pic = $_FILES["profile_pic"];
            $profile_pic = $_FILES["profile_pic"]["name"];
            $tmp_name = $_FILES["profile_pic"]["tmp_name"];
            move_uploaded_file($tmp_name,"../uploads/".$profile_pic);
            $id=$this->_request['id'];
            
          
            if(empty($id) )
            {
               
                 $result['status'] = "0";
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           
          if(empty($profile_pic)){
            $sql=mysql_query("update users set mobile='".$mobile."', first_name='".$first_name."', last_name='".$last_name."', country='".$country."', city='".$city."', state='".$state."',UserDOB='".$user_dob."', UserAbout='".$user_about."' where id='".$id."'", $this->db) or die(mysql_error());
          }

            else{
                $sql=mysql_query("update users set mobile='".$mobile."',first_name='".$first_name."', last_name='".$last_name."', country='".$country."', city='".$city."', state='".$state."',UserDOB='".$user_dob."', UserAbout='".$user_about."', profile_pic='".$profile_pic."' where id='".$id."'", $this->db) or die(mysql_error());
              } 

             
         if($sql >0 ){
            $update=mysql_query("SELECT * from users where id='".$id."'",$this->db);
               $rlt = mysql_fetch_array($update, MYSQL_ASSOC);

                 $result['message'] = "Profile updated";
                 $result['status'] = '1';
                 $result['update'] =$rlt;
                $this->response($this->json($result), 200);
           }
          
        }

        private function daily_report_list(){
          
               $user_id = $this->_request['user_id'];
               $self_id = $this->_request['self_id'];
               $date =date('Y-m-d');
                 /* if(empty($user_id))
                  {
               
                        $result['status'] ='0';
                        $result['message']="manager id required";
                        $this->response($this->json($result), 200);
                  }*/

           $user= mysql_query("select * from users where id='".$user_id."'");
           $row = mysql_fetch_assoc($user);
           $manager = $row['is_manager'];
           $company_id = $row['UserCompanyID'];
           $supervisor = $row['supervisor'];
            
            if($manager==1){
            	/*select R.* , u.username, D.department_name from daily_reports R, users u , tbl_department D where u.id=R.user_id and D.id=R.department_id and  D.company_id='".$company_id."'*/
            	$sql=mysql_query("select R.* , u.username, D.department_name from daily_reports R, users u , tbl_department D where D.company_id='".$company_id."' and  DATE_FORMAT(R.created_at,'%Y-%m-%d')='".$date."' and  u.id=R.user_id  and D.id=R.department_id group by  R.report_id", $this->db)or die(mysql_error());
            }elseif($manager==2){

              $sql=mysql_query("SELECT d.*,u.username ,dt.department_name FROM daily_reports d, tbl_department dt,users u WHERE d.user_id=u.id and  d.department_id=dt.id and DATE_FORMAT(d.created_at,'%Y-%m-%d')='".$date."' and d.department_id IN (select DepartmentID from tbl_departmentemploy where EmployID='".$user_id."')", $this->db)or die(mysql_error());
            }elseif($supervisor==1){
                $sql=mysql_query("select R.* , D.department_name, U.username from users U, daily_reports R , tbl_department D , tbl_supervisor S where R.user_id=U.id and D.id=R.department_id and DATE_FORMAT(R.created_at,'%Y-%m-%d')='".$date."' and  S.user_id=R.user_id and S.supervisor_id='".$user_id."'", $this->db)or die(mysql_error());
            }else{
            	$sql=mysql_query("select R.* , D.department_name , U.username from users U, daily_reports R , tbl_department D where R.user_id=U.id and D.id=R.department_id and DATE_FORMAT(R.created_at,'%Y-%m-%d')='".$date."' and R.user_id='".$self_id."' ");
            }
            
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    
                    $result[$i]['report_id'] = $rlt['report_id'];
                    $result[$i]['text_report']=$rlt['text_report'];
                    $result[$i]['department_id'] = $rlt['department_id'];
                    $result[$i]['emp_name'] = $rlt['username'];
                    $result[$i]['created_at']=$rlt['created_at'];
                    $result[$i]['attachment_file']=$rlt['attachment_file'];
                    $result[$i]['comment']=$rlt['comment'];
                    $result[$i]['department_name']=$rlt['department_name'];

                    $i++;
                }
                $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$result)), 200);
            }
    }

// current month daily reposrt
    private function self_employee_daily_report_curnt_month(){
          
               $user_id = $this->_request['user_id'];
               $date =date('Y-m-d');
                  if(empty($user_id))
                  {
               
                        $result['status'] ='0';
                        $result['message']="manager id required";
                        $this->response($this->json($result), 200);
                  }
                   
            
            $sql=mysql_query('select D.*,COUNT(DATE_FORMAT(D.created_at,"%Y-%m-%d")) as count_report, U.username ,TD.department_name from daily_reports D, users U, tbl_department TD where D.created_at >=(CURDATE()-INTERVAL 1 MONTH) and D.department_id=TD.id and D.user_id=U.id and user_id="'.$user_id.'" GROUP BY  DATE_FORMAT(D.created_at,"%Y-%m-%d") order by D.created_at DESC', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    
                    $result[$i]['report_id'] = $rlt['report_id'];
                    $result[$i]['text_report']=$rlt['text_report'];
                     $result[$i]['username']=$rlt['username'];
                    $result[$i]['department_name']=$rlt['department_name'];
                    $result[$i]['department_id'] = $rlt['department_id'];
                    $result[$i]['counter'] = $rlt['count_report'];
                    $result[$i]['created_at']=$rlt['created_at'];
                    $result[$i]['comment']=$rlt['comment'];

                    $i++;
                }
                $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$result)), 200);
            }
    }


// all team daily reposrt date wise data 
    private function current_date_wise_daily_report(){
          
               $user_id = $this->_request['user_id'];
                $date=date('Y-m-d');
                  if(empty($user_id))
                  {
               
                        $result['status'] ='0';
                        $result['message']="manager id required";
                        $this->response($this->json($result), 200);
                  }
                   // $pass= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$user_id."'");
                   // $row = mysql_fetch_assoc($pass);
                   // $department_id=$row["DepartmentID"];
          //  select D.*,E.EmployID,U.username from tbl_department D, tbl_departmentemploy E, users U where E.EmployID=U.id and D.id=E.DepartmentID and and  company_id=1
            $sql=mysql_query("SELECT d.*,u.username ,dt.department_name FROM daily_reports d, tbl_department dt,users u WHERE d.user_id=u.id and `date`='".date('Y-m-d')."' and department_id=dt.id and d.user_id='".$user_id."'", $this->db)or die(mysql_error());
            $result = array();
        
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
            }
            else
            {
                $result = mysql_fetch_array($sql, MYSQL_ASSOC);
               $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }
    }

// verified attendance by manager
  private function verified_attendance_list_by_manager(){
          
       
        // $user_id = $this->_request['user_id'];
         $attendance_id = $this->_request['attendance_id'];
         $view_status = '1';
            
            if(empty($attendance_id))
            {
               
                $result['status'] ='0';
                $result['message']="Invalid data provided";
                $this->response($this->json($result), 200);
            }

            
            $sql=mysql_query("update tbl_attendance set view_status='".$view_status."' where attendance_id='".$attendance_id."'", $this->db)or die(mysql_error());
            $result = array();
          
             $result['message'] = "Attendance verified";
             $result['status'] = '1';
            $this->response($this->json($result), 200);
            
    }


   private function submit_attendance_By_emp()
        {  
            $result = array();
            $user_id=$this->_request['user_id'];
           
            $date=date('Y-m-d');
            $signin_time=date('Y-m-d G:i:s');
            $signin_location= addslashes($this->_request['signin_location']);
            $add_lat=$this->_request['add_lat'];
            $add_long=$this->_request['add_long'];
            $emp_atten_status=$this->_request['emp_atten_status'];
            $emp_atten_color=$this->_request['emp_atten_color'];
            $boss_atten_status=$this->_request['boss_atten_status'];
            $boss_atten_color=$this->_request['boss_atten_color'];
            $moth_year=$this->_request['moth_year'];
            $boss_comment=$this->_request['boss_comment'];

            $Status='1';
        
          
            if(empty($user_id) ||  empty($signin_location)|| empty($add_lat) || empty($add_long))
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            $sql=mysql_query("select * from tbl_attendance where cur_date = '".$date."' and  user_id='".$user_id."' ",$this->db)or die(mysql_error());
            if(mysql_num_rows($sql) > 0){
                $result['status'] = 0;
                $result['message']="You have already signed-in";
                $this->response($this->json($result), 200);
            }
            else
            {


                 $pass= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$user_id."'");
                   $row = mysql_fetch_assoc($pass);
                   $department_id = $row['DepartmentID'];
                  
                  
                 $sql = mysql_query("INSERT INTO `tbl_attendance`(`user_id`,`department_id`,`signin_time`,`cur_date`, `signin_location`, `add_lat`, `add_long`,`emp_atten_status`,`emp_atten_color`,`moth_year`,`boss_comment`,`Status`) VALUES ('".$user_id."','".$department_id."','".$signin_time."','".$date."','".$signin_location."','".$add_lat."','".$add_long."','".$emp_atten_status."','".$emp_atten_color."','".$moth_year."','".$boss_comment."','".$Status."')", $this->db);
                 // print_r($sql);

                $result['attendance_id']= mysql_insert_id();
               
                $result['message'] = "You have signed-in";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
           }
        }

        public function signout_emp(){
                $result = array();
                $attendance_id=$this->_request['attendance_id']; 
                $signout_time=date('Y-m-d G:i:s');
                $signout_location = $_request['signout_location'];
               

               if(empty($attendance_id) ||  empty($signout_location))
                    {
                      
                         $result['status'] = '0';
                        $result['message']="Invalid data provided!";
                        $this->response($this->json($result), 200);
                    }
         
            $sql=mysql_query("update tbl_attendance set signout_time='".$signout_time."', signout_location='".$signout_location."' WHERE attendance_id='".$attendance_id."' ",$this->db);
        
           if ($sql > 0) {


               $update=mysql_query("SELECT * from tbl_attendance where attendance_id='".$attendance_id."'",$this->db);
               $rlt = mysql_fetch_array($update, MYSQL_ASSOC);
                $result['message'] = "You have signed-out";
                $result['status'] = '1';
                $result['data'] = $rlt;
               }
                else {
                    $result['status'] = "0";
                    $result['message']   = "Could not update, please try again";
                }
                $this->response($this->json($result), 200);


    }
        
  private function add_experience_By_Manager()
        {  
            $result = array();
            $user_id = $this->_request['user_id'];
            $company_name =  addslashes($this->_request['company_name']);
            $designation=$this->_request['designation'];
            $address= addslashes($this->_request['address']);
            $start_date=$this->_request['start_date'];
            $end_date=$this->_request['end_date'];
            $description= addslashes($this->_request['description']);
            $job_status=$this->_request['job_status'];
            $company_logo = $_FILES["company_logo"];
            $company_logo = $_FILES["company_logo"]["name"];
            $tmp_name = $_FILES["company_logo"]["tmp_name"];
              move_uploaded_file($tmp_name,"../uploads/".$company_logo);
          
          
            if( empty($user_id))
            {
               
                 $result['status'] = "0";
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           
          $sql=mysql_query("INSERT INTO `experience_users` (`user_id`,`company_name`,`designation`,`address`,`start_date`,`end_date`,`description`,`company_logo`,`job_status`) VALUES('".$user_id."','".$company_name."','".$designation."','".$address."','".$start_date."','".$end_date."','".$description."','".$company_logo."','".$job_status."')",$this->db);
             
                 $result['message'] = " Added profile";
                 $result['status'] = '1';
                $this->response($this->json($result), 200);
          
        }

  /* education details submit */
        private function add_education_By_user()
        {  
            $result = array();
            $user_id = $this->_request['user_id'];
            $university_name =  addslashes($this->_request['university_name']);
            $college_name=$this->_request['college_name'];
            $branch_name= addslashes($this->_request['branch_name']);
            $school=$this->_request['school'];
            $degree_name= addslashes($this->_request['degree_name']);
            $eduction_descrition= addslashes($this->_request['eduction_descrition']);
            $clg_start_year=$this->_request['clg_start_year'];
            $clg_end_year =$this->_request["clg_end_year"];
            $university_logo = $_FILES["university_logo"];
            $university_logo = $_FILES["university_logo"]["name"];
            $tmp_name = $_FILES["university_logo"]["tmp_name"];
            move_uploaded_file($tmp_name,"../uploads/".$university_logo);

            if( empty($user_id))
            {
               
                $result['status'] = "0";
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           
          $sql=mysql_query("INSERT INTO `users_education_profile` (`user_id`,`university_name`,`college_name`,`school`,`branch_name`,`degree_name`,`eduction_descrition`,`clg_start_year`,`clg_end_year`,`university_logo`) VALUES('".$user_id."','".$university_name."','".$college_name."','".$school."','".$branch_name."','".$degree_name."','".$eduction_descrition."','".$clg_start_year."','".$clg_end_year."','".$university_logo."')",$this->db);
             
                 $result['message'] = "Added education profile";
                 $result['status'] = '1';
                $this->response($this->json($result), 200);
          
        }

   private function get_user_experience(){
        $user_id = $this->_request['user_id'];
          if(empty($user_id)){
            $result['message']="user id required";
            $result['status']="0";
            $this->response($this->json($result),200);
          }

          $sql=mysql_query("select * from experience_users where user_id='".$user_id."' ",$this->db);
         $result = array();
            $i=0;
        if(mysql_num_rows($sql) == 0){
              $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
            }

             else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                     $result[$i]['id'] = $rlt['id'];
                      $result[$i]['user_id'] = $rlt['user_id'];
                    $result[$i]['company_name'] = $rlt['company_name'];
                    $result[$i]['designation']=$rlt['designation'];
                    $result[$i]['address'] = $rlt['address'];
                     $result[$i]['start_date'] = $rlt['start_date'];
                    $result[$i]['end_date']=$rlt['end_date'];
                    $result[$i]['description']=$rlt['description'];
                     $result[$i]['company_logo']=$rlt['company_logo'];
                    $result[$i]['job_status']=$rlt['job_status'];
                    
                    $i++;

                }
                $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$result)), 200);
            }

   }

   // list of education 
 private function get_user_education(){
        $user_id = $this->_request['user_id'];
          if(empty($user_id)){
            $result['message']="Manager id required";
            $result['status']="0";
            $this->response($this->json($result),200);
          }

          $sql=mysql_query("select * from users_education_profile where user_id='".$user_id."' ",$this->db);
         $result = array();
            $i=0;
        if(mysql_num_rows($sql) == 0){
              $result['status'] ='0';

              $result['message']="Not found!";
              $this->response($this->json($result), 200);
            }

             
                   $rlt = mysql_fetch_array($sql, MYSQL_ASSOC);
                    $result['status'] ='1';
                      $result['message']='Data found';
                 $result['data']=$rlt;
                  
                    
             $this->response($this->json($result), 200);
               // $this->response($this->json(array('status'=>'1','message'=>'Data found successfully','data'=>$result)), 200);
            

   }
    
   // update user education api  

   private function update_education_profile(){
         $id=$this->_request['id'];
         // $user_id=$this->_request['user_id'];
         $university_name= addslashes($this->_request['university_name']);
         $college_name=$this->_request['college_name'];
         $branch_name= addslashes($this->_request['branch_name']);
         $school= addslashes($this->_request['school']);
         $degree_name= addslashes($this->_request['degree_name']);
         $clg_start_year=$this->_request['clg_start_year'];
         $clg_end_year=$this->_request['clg_end_year'];
         $eduction_descrition= addslashes($this->_request['eduction_descrition']);
         $university_logo = $_FILES["university_logo"];
         $university_logo = $_FILES["university_logo"]["name"];
         $tmp_name = $_FILES["university_logo"]["tmp_name"];
         move_uploaded_file($tmp_name,"../uploads/".$university_logo);


         if(empty($id)){
            $result['status']='0';
            $result['message']="Invalid data Provided";
            $this->response($this->json($result),200);
         }
       // $sql=mysql_query("update  users_education_profile set university_name='".$university_name."' ,college_name='".$college_name."', branch_name='".$branch_name."', school='".$school."',degree_name='".$degree_name."',clg_start_year='".$clg_start_year."',clg_end_year='".$clg_end_year."',eduction_descrition='".$eduction_descrition."' where id='".$id."'",$this->db);
          if(empty($university_logo)){
            $sql=mysql_query("update  users_education_profile set university_name='".$university_name."' ,college_name='".$college_name."', branch_name='".$branch_name."', school='".$school."',degree_name='".$degree_name."',clg_start_year='".$clg_start_year."',clg_end_year='".$clg_end_year."',eduction_descrition='".$eduction_descrition."' where id='".$id."'", $this->db) or die(mysql_error());
          }

            else{
                $sql=mysql_query("update  users_education_profile set university_name='".$university_name."' ,college_name='".$college_name."', branch_name='".$branch_name."', school='".$school."',degree_name='".$degree_name."',clg_start_year='".$clg_start_year."',clg_end_year='".$clg_end_year."',eduction_descrition='".$eduction_descrition."',university_logo='".$university_logo."' where id='".$id."'", $this->db) or die(mysql_error());
              } 


        if ($sql > 0) {


               $update=mysql_query("SELECT * from users_education_profile where id='".$id."'",$this->db);
               $rlt = mysql_fetch_array($update, MYSQL_ASSOC);
                $result['message'] = "Eduction profile updated";
                $result['status'] = '1';
                $result['update'] = $rlt;
               }
                else {
                    $result['status'] = "0";
                    $result['message']   = "Could not update, please try again";
                }
                $this->response($this->json($result), 200);

   }


//  user exp update api and get
    private function update_experience_profile()
    {
        $id=$this->_request['id'];
        $company_name = addslashes($this->_request['company_name']);
        $designation=$this->_request['designation'];
        $address=$this->_request['address'];
        $start_date=$this->_request['start_date'];
        $end_date=$this->_request['end_date'];
        $description=$this->_request['description'];
        $job_status=$this->_request['job_status'];
        $company_logo = $_FILES["company_logo"];
        $company_logo = $_FILES["company_logo"]["name"];
        $tmp_name = $_FILES["company_logo"]["tmp_name"];
        move_uploaded_file($tmp_name,"../uploads/".$company_logo);

         if(empty($id)){
            $result['status']='0';
            $result['message']="Invalid data provided";
         }
        // $sql=mysql_query("update  experience_users set company_name='".$company_name."' ,designation='".$designation."', address='".$address."', start_date='".$start_date."',end_date='".$end_date."',description='".$description."',job_status='".$job_status."',company_logo='".$company_logo."' where id='".$id."'",$this->db);

            if(empty($company_logo)){
            $sql=mysql_query("update  experience_users set company_name='".$company_name."' ,designation='".$designation."', address='".$address."', start_date='".$start_date."',end_date='".$end_date."',description='".$description."',job_status='".$job_status."' where id='".$id."'", $this->db) or die(mysql_error());
          }

            else{
                $sql=mysql_query("update  experience_users set company_name='".$company_name."' ,designation='".$designation."', address='".$address."', start_date='".$start_date."',end_date='".$end_date."',description='".$description."',job_status='".$job_status."',company_logo='".$company_logo."' where id='".$id."'", $this->db) or die(mysql_error());
              } 

        if ($sql > 0) {


               $update=mysql_query("SELECT * from experience_users where id='".$id."'",$this->db);
               $rlt = mysql_fetch_array($update, MYSQL_ASSOC);
                $result['message'] = "Experience profile updated";
                $result['status'] = '1';
                $result['update'] = $rlt;
               }
                else {
                    $result['status'] = "0";
                    $result['message']   = "Could not update, please try again";
                }
                $this->response($this->json($result), 200);

   }

      // get emp task list only
     private function get_emp_all_task_list(){
               $user_id = $this->_request['user_id'];
               $date = $this->_request['date'];

    
            
            if(empty($user_id))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            
            $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.Deleted=1 and  TaskAssignToUserID="'.$user_id.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                        $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                        $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 1 for fix and 2 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 1 for routine and 2 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        
                        if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] =($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                        $i++;  // 1- pending approval, 2- Pending , 3 - In Progress ,4 - Waiting for Completetion, 5 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }
        // task id by get total use time line items 

        private function get_timetaken_by_TaskID(){
            $task_id= $this->_request['task_id'];
            if(empty($task_id))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           $sql=mysql_query('select SEC_TO_TIME(SUM(UNIX_TIMESTAMP(TaskSubmitEndTime) - UNIX_TIMESTAMP(`TaskSubmitStartTime`))) as takentime from tbl_tasksubmits where `TaskSubmitTaskID`= "'.$task_id.'" and Deleted=1', $this->db)or die(mysql_error());
            $row= mysql_fetch_assoc($sql);
            $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$row)), 200);

        }

      
     // get task all emp by manager
       private function get_manager_all_task_list(){
               $user_id = $this->_request['user_id'];
               $task_status = $this->_request['task_status'];
               $from_date = $this->_request['from_date'];
               $to_date = $this->_request['to_date'];
               $task_type = $this->_request['task_type'];
               $task_category = $this->_request['task_category'];
               $task_priority = $this->_request['task_priority'];
            if(empty($user_id))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
             $user= mysql_query("select * from users where id='".$user_id."'");
             $row = mysql_fetch_assoc($user);
             $manager = $row['is_manager'];
             $company_id = $row['UserCompanyID'];
             $supervisor = $row['supervisor'];
             if($supervisor==1 && $manager==3){
                  $sql=mysql_query('select T.*, U.username as CreatedByUsername, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%Y-%m-%d %H:%i:%s A"), DATE_FORMAT(T.TaskStartTime,"%Y-%m-%d %H:%i:%s A")) as Avialable_time  from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th, tbl_supervisor s where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and  T.Deleted=1  and T.TaskCreatedByUserID=s.supervisor_id and s.supervisor_id="'.$user_id.'" order by T.TaskID DESC', $this->db) or die(mysql_error());
             }
             else{
            if($task_status){
            $sql=mysql_query('select T.*, U.username as CreatedByUsername, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%Y-%m-%d %H:%i:%s A"), DATE_FORMAT(T.TaskStartTime,"%Y-%m-%d %H:%i:%s A")) as Avialable_time  from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and  T.Deleted=1 and T.TaskStatus="'.$task_status.'" and TaskCreatedByUserID="'.$user_id.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
            }
            elseif($task_type){
            	 $sql=mysql_query('select T.*, U.username as CreatedByUsername, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%Y-%m-%d %H:%i:%s A"), DATE_FORMAT(T.TaskStartTime,"%Y-%m-%d %H:%i:%s A")) as Avialable_time  from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and  T.Deleted=1 and T.TaskType="'.$task_type.'" and TaskCreatedByUserID="'.$user_id.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
            }
            elseif($task_category){
                $sql=mysql_query('select T.*, U.username as CreatedByUsername, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%Y-%m-%d %H:%i:%s A"), DATE_FORMAT(T.TaskStartTime,"%Y-%m-%d %H:%i:%s A")) as Avialable_time  from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and  T.Deleted=1 and T.TaskCategory="'.$task_category.'" and TaskCreatedByUserID="'.$user_id.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
            }
             elseif($task_priority){
                $sql=mysql_query('select T.*, U.username as CreatedByUsername, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%Y-%m-%d %H:%i:%s A"), DATE_FORMAT(T.TaskStartTime,"%Y-%m-%d %H:%i:%s A")) as Avialable_time  from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and  T.Deleted=1 and T.priority="'.$task_priority.'" and TaskCreatedByUserID="'.$user_id.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
            }
            elseif($from_date||$to_date){
                $sql=mysql_query('select T.*, U.username as CreatedByUsername, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%Y-%m-%d %H:%i:%s A"), DATE_FORMAT(T.TaskStartTime,"%Y-%m-%d %H:%i:%s A")) as Avialable_time  from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and  T.Deleted=1 and TaskStartDate >= "'.$from_date.'" and TaskEndDate <= "'.$to_date.'"  and TaskCreatedByUserID="'.$user_id.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
            }
            elseif($user_id){
            $sql=mysql_query('select T.*, U.username as CreatedByUsername, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName,  TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and  T.Deleted=1 and TaskCreatedByUserID="'.$user_id.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
             }
         }
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['CreatedByUsername'] = $rlt['CreatedByUsername'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                         $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                         $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 0 for fix and 1 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 0 for routine and 1 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment']; 
                        if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] =($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                        $i++;  // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }
// in progress task list by  department manager 


private function get_inprogrestask_list(){
               $user_id = $this->_request['user_id'];
            
  
            if(empty($user_id))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            $user= mysql_query("select * from users where id='".$user_id."'");
	         $row = mysql_fetch_assoc($user);
	         $manager = $row['is_manager'];
	         $company_id = $row['UserCompanyID'];
	         $supervisor = $row['supervisor'];
	         if($supervisor==0 && $manager==2 ){
                    $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.TaskStatus=3 and  T.Deleted=1 and TaskCreatedByUserID="'.$user_id.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
	         }
            $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.TaskStatus=3 and  T.Deleted=1 and TaskCreatedByUserID="'.$user_id.'"  order by T.TaskID DESC', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                         $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                         $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 1 for fix and 2 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 1 for routine and 2 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment'];
                        if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] =($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                        $i++;  // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }

        // emp inprogress task list 
        private function user_inprogrestask_list(){
               $user_id = $this->_request['user_id'];
            
  
            if(empty($user_id))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            
            $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName ,  TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.TaskStatus=3 and T.Deleted=1 and  TaskAssignToUserID="'.$user_id.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                         $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                         $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 0 for fix and 1 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 0 for routine and 1 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment'];
                        if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] =($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                        $i++;  // 1 - pending approval, 2- Pending , 3 - In Progress ,4 - Waiting for Completetion, 5 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }

        // compalete task list department manager 

         private function get_completetask_list(){
               $user_id = $this->_request['user_id'];
            
  
            if(empty($user_id))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
              
             $user= mysql_query("select * from users where id='".$user_id."'");
             $row = mysql_fetch_assoc($user);
             $manager = $row['is_manager'];
             $company_id = $row['UserCompanyID'];
             $supervisor = $row['supervisor'];
             if($supervisor==1 && $manager==3){
                 $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName , TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th , tbl_supervisor s where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.TaskStatus=5 and T.Deleted=1 and T.TaskCreatedByUserID=s.supervisor_id and s.supervisor_id="'.$user_id.'"  order by T.TaskID DESC', $this->db)or die(mysql_error()); 
             }
            else{
                $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName , TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.TaskStatus=5 and T.Deleted=1  and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error()); 
            }
           
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                         $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                         $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 0 for fix and 1 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 0 for routine and 1 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment'];
                        if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] =($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                        $i++;  // 1 - pending approval, 2- Pending , 3 - In Progress ,4 - Waiting for Completetion, 5 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }

   // emp completed task list
        private function user_completetask_list(){
               $user_id = $this->_request['user_id'];
            
      
            if(empty($user_id))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            
            $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.TaskStatus=5 and T.Deleted=1 and TaskAssignToUserID="'.$user_id.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                         $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                         $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 0 for fix and 1 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 0 for routine and 1 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment'];
                        if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] =($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                        $i++;  
                        // 1 - pending approval, 2- Pending , 3 - In Progress ,4 - Waiting for Completetion, 5 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }

  // get laeve categories api 
         private function get_leave_categories(){
              
            
            $sql=mysql_query('select * from leaves_categories', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['categories_id'] = $rlt['categories_id'];
                        $result[$i]['categories_name'] = $rlt['categories_name'];
                      
                        $result[$i]['status'] = $rlt['status'];
                       
                       
                        $i++;  
                       
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }


      // add leave emp and manager 
         private function add_leave_by_employee()
        {  
            $result = array();
            $user_id=$this->_request['user_id'];
            // $leaves_categories=$this->_request['leaves_categories'];
            $leave_subject=$this->_request['leave_subject'];
            $leave_start_date=$this->_request['leave_start_date'];
            $leave_start_time=$this->_request['leave_start_time'];
            $leave_end_date=$this->_request['leave_end_date'];
            $leave_end_time=$this->_request['leave_end_time'];
            $description=$this->_request['description'];
            $leave_status='0';
            $priority=$this->_request['priority'];
           
          
            if(empty($user_id)  || empty($leave_start_date) || empty($leave_start_time)|| empty($leave_end_date)|| empty($leave_end_time) || empty($description) ||  empty($priority))
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           
            else
            {
                 $pass= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$user_id."'");
                   $row = mysql_fetch_assoc($pass);
                   $department_id = $row['DepartmentID'];
                 $sql=mysql_query("INSERT INTO `tbl_leaves` (`LeaveUserID`,`LeaveDepartmentID`,`LeaveSubject`,`LeaveStartDate`,`leave_start_time`,`leave_end_time`,`LeaveEndDate`,`LeaveDescription`,`LeaveStatus`,`priority`) VALUES  ('".$user_id."','".$department_id."','".$leave_subject."','".$leave_start_date."','".$leave_start_time."','".$leave_end_time."','".$leave_end_date."','".$description."','".$leave_status."','".$priority."')",$this->db);
                 // print_r($sql);

                $result['leave_id']= mysql_insert_id();
               
                $result['message'] = "Submitted leave";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
           }
        }

        /* edit leave in employee*/

        private function update_leave()
        {  
            $result = array();
            $leave_id=$this->_request['leave_id'];
            $leave_subject=$this->_request['leave_subject'];
            $leave_start_date=$this->_request['leave_start_date'];
            $leave_start_time=$this->_request['leave_start_time'];
            $leave_end_date=$this->_request['leave_end_date'];
            $leave_end_time=$this->_request['leave_end_time'];
            $description=$this->_request['description'];
            $leave_status='0';
            $priority=$this->_request['priority'];
           
          
            if(empty($leave_id) || empty($leave_subject) || empty($leave_start_date) || empty($leave_start_time)|| empty($leave_end_date)|| empty($leave_end_time) || empty($description) ||  empty($priority))
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           
            else
            {
                 
                 $sql=mysql_query("Update  tbl_leaves SET LeaveSubject='".$leave_subject."',LeaveStartDate='".$leave_start_date."',leave_start_time='".$leave_start_time."',leave_end_time='".$leave_end_time."',LeaveEndDate='".$leave_end_date."',LeaveDescription='".$description."',LeaveStatus='".$leave_status."',priority='".$priority."' where LeaveID='".$leave_id."'",$this->db);
                 
               
                $result['message'] = "update leave";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
           }
        }
        


        /* deleted leave but recode not delete only disable leave row*/


         private function delete_leave()
        {  
            $result = array();
            $leave_id=$this->_request['leave_id'];
            $Deleted='0';
            
           
          
            if(empty($leave_id))
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           
            else
            {
                 
                 $sql=mysql_query("Update  tbl_leaves SET Deleted='".$Deleted."' where LeaveID='".$leave_id."'",$this->db);
                 
               
                $result['message'] = "Deleted leave";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
           }
        }
        
        // emp leave list
        private function get_leave_list_by_employee(){
              
               $user_id = $this->_request['user_id'];
               $leave_status = $this->_request['leave_status'];
            if(empty($user_id))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }


            if($leave_status!=""){
            $sql=mysql_query('select l.* from tbl_leaves l  where LeaveStatus="'.$leave_status.'" and l.Deleted=1 and l.LeaveUserID="'.$user_id.'"  order by l.LeaveID DESC', $this->db)or die(mysql_error());
            }else{
               $sql=mysql_query('select l.* from tbl_leaves l  where  l.Deleted=1 and  l.LeaveUserID="'.$user_id.'"  order by l.LeaveID DESC', $this->db)or die(mysql_error()); 
            }
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['leave_id'] = $rlt['LeaveID'];
                        $result[$i]['user_id'] = $rlt['LeaveUserID'];
                        $result[$i]['leave_subject'] = $rlt['LeaveSubject'];
                        $result[$i]['leave_start_date'] = $rlt['LeaveStartDate'];
                        $result[$i]['leave_start_time'] = $rlt['leave_start_time'];
                        $result[$i]['leave_end_date'] = $rlt['LeaveEndDate'];
                        $result[$i]['leave_end_time'] = $rlt['leave_end_time'];
                        $result[$i]['description'] = stripslashes($rlt['LeaveDescription']);
                        $result[$i]['leave_status'] = $rlt['LeaveStatus']; // 0 for fix and 1 for flexy
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['applied_date'] = $rlt['LeaveCreatedAt'];
                        $result[$i]['remark']=$rlt['LeaveManagerComment'];
                        $i++;  // 1 - pending approval, 2- Pending , 3 - In Progress ,4 - Waiting for Completetion, 5 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }


  // get task list  status wise 1 to 5
private function get_all_task_by_status(){
               $user_id = $this->_request['user_id'];
               $task_status = $this->_request['task_status'];

                if(empty($user_id))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            switch ($task_status) {
                case '2':
                     $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName,  TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%Y-%m-%d %H:%i:%s A"), DATE_FORMAT(T.TaskStartTime,"%Y-%m-%d %H:%i:%s A")) as Avialable_time  from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.Deleted=1 and TaskAssignToUserID="'.$user_id.'" and T.TaskStatus="'.$task_status.'"  and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
                    break;
                    case '3':
                       $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%Y-%m-%d %H:%i:%s A"), DATE_FORMAT(T.TaskStartTime,"%Y-%m-%d %H:%i:%s A")) as Avialable_time  from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.Deleted=1 and TaskAssignToUserID="'.$user_id.'" and T.TaskStatus="'.$task_status.'"  and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
                        break;
                   case '5':
                      $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%Y-%m-%d %H:%i:%s A"), DATE_FORMAT(T.TaskStartTime,"%Y-%m-%d %H:%i:%s A")) as Avialable_time  from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.Deleted=1  and  TaskAssignToUserID="'.$user_id.'" and T.TaskStatus="'.$task_status.'"  and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
                       break;
                default:
                    # code...
                    break;
            }
          if($user_id){
            $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName, TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.Deleted=1 and TaskAssignToUserID="'.$user_id.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());

      
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                  while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                         $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                         $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 1 for fix and 2 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 1 for routine and 2 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment'];
                        if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] =($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                        $i++;  // 1- pending approval, 2- Pending , 3 - In Progress ,4- Waiting for Completetion, 5 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        
        }
    }

// employee this month task counter  
        private function employee_current_month_task_count(){
               $user_id = $this->_request['user_id'];
            
            if(empty($user_id))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            
            
            $sql=mysql_query('select COUNT(DATE_FORMAT(TaskCreatedAt,"%Y-%m-%d")) as Task_Count, DATE_FORMAT(TaskCreatedAt,"%Y-%m-%d") as date from tbl_tasks where TaskCreatedAt>=(CURDATE()-INTERVAL 1 MONTH)  and TaskAssignToUserID="'.$user_id.'" GROUP BY  DATE_FORMAT(TaskCreatedAt,"%Y-%m-%d") order by TaskCreatedAt DESC', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
               while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 
                        $result[$i]['task'] = $rlt['Task_Count'];
                        $result[$i]['date'] = $rlt['date'];
                       
                        $i++; 
                 // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                }
            
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }
       // manager task count current manth

         private function manager_current_month_task_count(){
               $user_id = $this->_request['user_id'];
            
            if(empty($user_id))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            
            
            $sql=mysql_query('select COUNT(DATE_FORMAT(TaskCreatedAt,"%Y-%m-%d")) as Task_Count, DATE_FORMAT(TaskCreatedAt,"%Y-%m-%d") as date from tbl_tasks where TaskCreatedAt>=(CURDATE()-INTERVAL 1 MONTH)  and TaskCreatedByUserID="'.$user_id.'" GROUP BY  DATE_FORMAT(TaskCreatedAt,"%Y-%m-%d") order by TaskCreatedAt DESC', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
               while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 
                        $result[$i]['task'] = $rlt['Task_Count'];
                        $result[$i]['date'] = $rlt['date'];
                       
                        $i++; 
                 // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                }
            
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }

           /* current month attendance recode and count in  employee */
       private function attendance_current_month_count(){
               $user_id = $this->_request['user_id'];
            
            if(empty($user_id))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            
            
            $sql=mysql_query('select *, COUNT(DATE_FORMAT(signin_time,"%Y-%m-%d")) as Task_Count, DATE_FORMAT(signin_time,"%Y-%m-%d") as date from tbl_attendance where signin_time>=(CURDATE()-INTERVAL 1 MONTH)  and user_id="'.$user_id.'" GROUP BY  DATE_FORMAT(signin_time,"%Y-%m-%d") order by signin_time DESC', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
               while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 
                        $result[$i]['count'] = $rlt['Task_Count'];
                        $result[$i]['date'] = $rlt['date'];
                        $result[$i]['user_id'] = $rlt['user_id'];
                        $result[$i]['signin_time'] = $rlt['signin_time'];
                        $result[$i]['signin_location'] = $rlt['signin_location'];
                        $result[$i]['signout_time'] = $rlt['signout_time'];
                        $result[$i]['signout_location'] = $rlt['signout_location'];
                        $i++; 
                 // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                }
            
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }

       //employee date wise task list 
         private function date_wise_task_manager_employee(){
            $employee_id = $this->_request['employee_id'];
            $manager_id = $this->_request['manager_id'];
            $date =$this->_request['date'];
            
            if(empty($date))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           if($employee_id){
            $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%Y-%m-%d %H:%i:%s A"), DATE_FORMAT(T.TaskStartTime,"%Y-%m-%d %H:%i:%s A")) as Avialable_time  from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID  and DATE_FORMAT(T.TaskCreatedAt,"%Y-%m-%d") = "'.$date.'" and  TaskAssignToUserID="'.$employee_id.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$employee_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
           }else{

            $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID  and DATE_FORMAT(T.TaskCreatedAt,"%Y-%m-%d") = "'.$date.'" and  TaskCreatedByUserID="'.$manager_id.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$manager_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
        }
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                         $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                         $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 0 for fix and 1 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 0 for routine and 1 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment'];
                        if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] =($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                        $i++; 
                 // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                }

                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }

 // date wise leave list

        private function date_wise_manager_leavelist(){
            $user_id = $this->_request['user_id'];
            $date =$this->_request['date'];
            
            if(empty($user_id) || empty($date))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            $user= mysql_query("select * from users where id='".$user_id."'");
         $row = mysql_fetch_assoc($user);
         $manager = $row['is_manager'];
         $company_id = $row['UserCompanyID'];
         $supervisor = $row['supervisor'];
            
             /*   $sql=mysql_query('select L.*, U.username, D.department_name from tbl_leaves L, users U, tbl_department D where  L.LeaveUserID=U.id and   L.LeaveDepartmentID=D.id  and L.LeaveDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")'.$addquery.' order by L.LeaveID DESC', $this->db)or die(mysql_error());*/
            
            if($manager==1){
                 $query ='select L.*, U.username, D.department_name, U.id from tbl_leaves L, users U, tbl_department D  where  L.LeaveUserID=U.id  and L.LeaveDepartmentID=D.id and L.Deleted=1 and DATE_FORMAT(L.LeaveCreatedAt,"%Y-%m-%d") = "'.$date.'"  and  D.company_id="'.$company_id.'" order by L.LeaveID DESC' ;
                 $sql=mysql_query($query);
         }elseif($manager==2 && $supervisor==0){
            $sql=mysql_query('select L.*, U.username, U.id , D.department_name from tbl_leaves L, users U, tbl_department D where  L.LeaveUserID=U.id and DATE_FORMAT(L.LeaveCreatedAt,"%Y-%m-%d") = "'.$date.'"  and   L.LeaveDepartmentID=D.id  and L.LeaveDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'") order by L.LeaveID DESC', $this->db)or die(mysql_error());
         }
         elseif ($manager==3 && $supervisor==1) {
             $sql=mysql_query('select L.*, U.username, U.id, D.department_name from tbl_leaves L, tbl_supervisor s,users U, tbl_department D where  L.LeaveUserID=U.id  and L.LeaveDepartmentID=D.id and L.Deleted=1 and L.LeaveStatus=0  and L.LeaveUserID=s.user_id and DATE_FORMAT(L.LeaveCreatedAt,"%Y-%m-%d") = "'.$date.'"  and s.supervisor_id="'.$user_id.'"  order by L.LeaveID DESC', $this->db)or die(mysql_error());
         }else{
         	 
            $sql=mysql_query('select L.*, U.username, U.id , D.department_name from tbl_leaves L, users U , tbl_department D where   DATE_FORMAT(L.LeaveCreatedAt,"%Y-%m-%d") = "'.$date.'" and   L.LeaveUserID=U.id and L.LeaveDepartmentID=D.id and L.LeaveUserID="'.$user_id.'"  order by L.LeaveID DESC', $this->db)or die(mysql_error());
         }
          
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                        $result[$i]['LeaveUserName'] = $rlt['username'];
                        $result[$i]['LeaveUserID'] = $rlt['id'];
                        $result[$i]['LeaveID'] = $rlt['LeaveID'];
                        $result[$i]['LeaveDepartmentID'] = $rlt['LeaveDepartmentID'];
                        $result[$i]['LeaveDepartmentName']=$rlt['department_name'];
                        $result[$i]['LeaveSubject'] = $rlt['LeaveSubject'];
                        $result[$i]['LeaveStartDate'] = $rlt['LeaveStartDate'];
                        $result[$i]['LeaveEndDate'] = $rlt['LeaveEndDate'];
                        $result[$i]['LeaveStartTime'] = $rlt['leave_start_time'];
                        $result[$i]['LeaveEndTime'] = $rlt['leave_end_time'];
                        $result[$i]['LeaveDescription'] = stripslashes($rlt['LeaveDescription']);
                       $result[$i]['LeaveCreatedAt'] = $rlt['LeaveCreatedAt'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['LeaveManagerComment'] = $rlt['LeaveManagerComment'];
                        $result[$i]['LeaveStatus'] = $rlt['LeaveStatus'];
                       // $result[$i]['Durring'] = $rlt['Avialable_time'];
                        $i++; 
                 // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                }

                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }
 // date wise user employee wise leave list

         private function date_wise_employee_leavelist(){
            $user_id = $this->_request['user_id'];
            $date =$this->_request['date'];
            
            if(empty($user_id) || empty($date))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           // $num= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$user_id."'");
           // $row = mysql_fetch_assoc($num);
           // $department_id = $row['DepartmentID'];
            $sql=mysql_query('select L.*, U.username, U.id , D.department_name from tbl_leaves L, users U , tbl_department D where LeaveUserID="'.$user_id.'" and DATE_FORMAT(L.LeaveCreatedAt,"%Y-%m-%d") = "'.$date.'" and   L.LeaveUserID=U.id and L.LeaveDepartmentID=D.id', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['username'] = $rlt['username'];
                        $result[$i]['user_id'] = $rlt['id'];
                        $result[$i]['LeaveID'] = $rlt['LeaveID'];
                        $result[$i]['LeaveDepartmentID'] = $rlt['LeaveDepartmentID'];
                        $result[$i]['LeaveDepartmentName']=$rlt['department_name'];
                        $result[$i]['LeaveSubject'] = $rlt['LeaveSubject'];
                        $result[$i]['LeaveStartDate'] = $rlt['LeaveStartDate'];
                        $result[$i]['LeaveEndDate'] = $rlt['LeaveEndDate'];
                        $result[$i]['LeaveStartTime'] = $rlt['leave_start_time'];
                        $result[$i]['LeaveEndTime'] = $rlt['leave_end_time'];
                        $result[$i]['LeaveDescription'] = stripslashes($rlt['LeaveDescription']);
                        $result[$i]['LeaveCreatedAt'] = $rlt['LeaveCreatedAt'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['LeaveManagerComment'] = $rlt['LeaveManagerComment'];
                        $result[$i]['LeaveStatus'] = $rlt['LeaveStatus'];
                       // $result[$i]['Durring'] = $rlt['Avialable_time'];
                        $i++; 
                 // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                }

                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }

        }
    // date wise attendance list manger
         private function date_wise_manager_attendancelist(){
            $user_id = $this->_request['user_id'];
            $date =$this->_request['date'];
            
            if(empty($user_id) || empty($date))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            $user= mysql_query("select * from users where id='".$user_id."'");
             $row = mysql_fetch_assoc($user);
             $manager = $row['is_manager'];
             $company_id = $row['UserCompanyID'];
             $supervisor = $row['supervisor'];

           if($manager==2){
            $sql=mysql_query('select A.*, U.username, U.id , D.department_name from tbl_attendance A, users U , tbl_department D where A.department_id IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'") and DATE_FORMAT(A.signin_time ,"%Y-%m-%d") ="'.$date.'" and   A.user_id=U.id and A.department_id=D.id', $this->db)or die(mysql_error());
           }elseif($manager==1){
            $sql=mysql_query('select A.*, U.username, U.id , D.department_name from tbl_attendance A, users U , tbl_department D where D.company_id="'.$company_id.'"  and DATE_FORMAT(A.signin_time ,"%Y-%m-%d") ="'.$date.'" and   A.user_id=U.id and A.department_id=D.id', $this->db)or die(mysql_error());
           }
           elseif ($supervisor==1 && $manager==3) {
           
              $sql=mysql_query('select A.*, U.username, U.id , D.department_name from tbl_attendance A, users U , tbl_department D , tbl_supervisor s where s.supervisor_id="'.$user_id.'"  and DATE_FORMAT(A.signin_time ,"%Y-%m-%d") ="'.$date.'" and s.user_id=A.user_id and  A.user_id=U.id and A.department_id=D.id', $this->db)or die(mysql_error());
           }
           
            
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                        $result[$i]['username'] = $rlt['username'];
                        $result[$i]['user_id'] = $rlt['id'];
                        $result[$i]['AttendanceID'] = $rlt['attendance_id'];
                        $result[$i]['DepartmentID'] = $rlt['department_id'];
                        $result[$i]['DepartmentName']=$rlt['department_name'];
                        $result[$i]['SignInTime'] = $rlt['signin_time'];
                        $result[$i]['SignOutTime'] = $rlt['signout_time'];
                        $result[$i]['SingInLocation'] = $rlt['signin_location'];
                        $result[$i]['SingOutLocation'] = $rlt['signout_location'];
                        $result[$i]['AddLat'] = $rlt['add_lat'];
                        // $result[$i]['LeaveDescription'] = stripslashes($rlt['LeaveDescription']);
                       
                        $result[$i]['AddLong'] = $rlt['add_long'];
                        $result[$i]['AttendanceStatus'] = $rlt['Status'];
                        $result[$i]['VerifiedStatus'] = $rlt['view_status'];
                       // $result[$i]['Durring'] = $rlt['Avialable_time'];
                        $i++; 
                 
                }

                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }

        // date wise attendance list single user employee
         private function date_wise_employee_attendancelist(){
            $user_id = $this->_request['user_id'];
            $date =$this->_request['date'];
            
            if(empty($user_id) || empty($date))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           // $num= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$user_id."'");
           // $row = mysql_fetch_assoc($num);
           // $department_id = $row['DepartmentID'];
            $sql=mysql_query('select A.*, U.username, U.id , D.department_name from tbl_attendance A, users U , tbl_department D where A.user_id="'.$user_id.'" and DATE_FORMAT(A.signin_time ,"%Y-%m-%d") ="'.$date.'" and   A.user_id=U.id and A.department_id=D.id', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                        $result[$i]['username'] = $rlt['username'];
                        $result[$i]['user_id'] = $rlt['id'];
                        $result[$i]['AttendanceID'] = $rlt['attendance_id'];
                        $result[$i]['DepartmentID'] = $rlt['department_id'];
                        $result[$i]['DepartmentName']=$rlt['department_name'];
                        $result[$i]['SignInTime'] = $rlt['signin_time'];
                        $result[$i]['SignOutTime'] = $rlt['signout_time'];
                        $result[$i]['SingInLocation'] = $rlt['signin_location'];
                        $result[$i]['SingOutLocation'] = $rlt['signout_location'];
                        $result[$i]['AddLat'] = $rlt['add_lat'];
                        // $result[$i]['LeaveDescription'] = stripslashes($rlt['LeaveDescription']);
                       
                        $result[$i]['AddLong'] = $rlt['add_long'];
                        $result[$i]['AttendanceStatus'] = $rlt['Status'];
                        $result[$i]['VerifiedStatus'] = $rlt['view_status'];
                       // $result[$i]['Durring'] = $rlt['Avialable_time'];
                        $i++; 
                 
                }

                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }

    
        // date wise daily report list manger
         private function date_wise_manager_dailyreportlist(){
            $user_id = $this->_request['user_id'];
            $date =$this->_request['date'];
            
            if(empty($user_id) || empty($date))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           $num= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$user_id."'");
           $row = mysql_fetch_assoc($num);
           $department_id = $row['DepartmentID'];
            $sql=mysql_query('select R.*, U.username, U.id , D.department_name from daily_reports R, users U , tbl_department D where R.department_id="'.$department_id.'" and DATE_FORMAT(R.created_at ,"%Y-%m-%d") ="'.$date.'" and   R.user_id=U.id and R.department_id=D.id', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                        $result[$i]['username'] = $rlt['username'];
                        $result[$i]['UserID'] = $rlt['user_id'];
                        $result[$i]['ReportID'] = $rlt['report_id'];
                        $result[$i]['DepartmentID'] = $rlt['department_id'];
                        $result[$i]['DepartmentName']=$rlt['department_name'];
                        $result[$i]['Attachment'] = $rlt['attachment_file'];
                        $result[$i]['Report'] = stripslashes($rlt['text_report']);
                        $result[$i]['created_at'] = $rlt['created_at'];
                        $result[$i]['comment'] = $rlt['comment'];
                        $i++; 
                 
                }

                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }

    // date wise emloyee daily report single user
        private function date_wise_employee_dailyreport_list(){
            $user_id = $this->_request['user_id'];
            $date =$this->_request['date'];
            
            if(empty($user_id) || empty($date))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           // $num= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$user_id."'");
           // $row = mysql_fetch_assoc($num);
           // $department_id = $row['DepartmentID'];
            $sql=mysql_query('select R.*, U.username, U.id , D.department_name from daily_reports R, users U , tbl_department D where R.user_id="'.$user_id.'" and DATE_FORMAT(R.created_at ,"%Y-%m-%d") ="'.$date.'" and   R.user_id=U.id and R.department_id=D.id order by R.created_at DESC', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                        $result[$i]['username'] = $rlt['username'];
                        $result[$i]['UserID'] = $rlt['user_id'];
                        $result[$i]['ReportID'] = $rlt['report_id'];
                        $result[$i]['DepartmentID'] = $rlt['department_id'];
                        $result[$i]['DepartmentName']=$rlt['department_name'];
                        $result[$i]['Attachment'] = $rlt['attachment_file'];
                        $result[$i]['comment'] = $rlt['comment'];
                        $result[$i]['created_at'] = $rlt['created_at'];
                        $result[$i]['Report'] = stripslashes($rlt['text_report']);
                        $i++; 
                 
                }

                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }

    // add daily report
 private function submit_daily_report_by_emp()
        {  
            $result = array();
            $user_id=$this->_request['user_id'];
            $attachment_file = $_FILES["attachment_file"];
            $attachment_file = $_FILES["attachment_file"]["name"];
            $tmp_name = $_FILES["attachment_file"]["tmp_name"];
            move_uploaded_file($tmp_name,"../uploads/".$attachment_file);

            $text_report=$this->_request['text_report'];
            
            $date=date('Y-m-d');
            $created_at=date('Y-m-d G:i:s');
           
          
            if(empty($user_id) || empty($text_report))
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            $sql=mysql_query("SELECT * FROM `daily_reports` WHERE  `date`='".$date."' and `user_id`='".$user_id."'",$this->db)or die(mysql_error());
            if(mysql_num_rows($sql) > 0){
                $result['status'] = 0;
                $result['message']="Daily Report already submitted!";
                $this->response($this->json($result), 200);
            }
            else
            {
                 $pass= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$user_id."'");
                   $row = mysql_fetch_assoc($pass);
                   $department_id = $row['DepartmentID'];
    $sql=mysql_query("INSERT INTO `daily_reports` (`user_id`,`department_id`,`attachment_file`,`text_report`,`date`,`created_at`) VALUES  ('".$user_id."','".$department_id."','".$attachment_file."','".$text_report."','".$date."','".$created_at."')",$this->db);
          $abc= mysql_insert_id();
    if($sql >0 ){

        $data=mysql_query("SELECT * from daily_reports where report_id='".$abc."'",$this->db);
               $rlt = mysql_fetch_array($data, MYSQL_ASSOC);

              
               
                $result['message'] = "Report submitted";
                $result['status'] ='1';
                $result['data']=$rlt;
                $this->response($this->json($result), 200);
           }
          
             else {
                    $result['status'] = "0";
                    $result['message']   = "Could not submit, please try again";
                
           }
        }
    }

     private function submit_comment_in_task()
        {  
            $result = array();
            $user_id=$this->_request['user_id'];
            $task_id=$this->_request['task_id'];
            $comment_text=$this->_request['comment_text'];
            $status='1';
            $Deleted='1';
            $created_at=date('Y-m-d G:i:s');
           
          
            if(empty($user_id) || empty($task_id) || empty($comment_text))
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           
            
    
                 
    $sql=mysql_query("INSERT INTO `tbl_task_comments` (`user_id`,`task_id`,`comment_text`,`created_at`,`status`,`Deleted`) VALUES  ('".$user_id."','".$task_id."','".$comment_text."','".$created_at."','".$status."','".$Deleted."')",$this->db);
         
              
               
                $result['message'] = "send";
                $result['status'] ='1';
                // $result['data']=$rlt;
                $this->response($this->json($result), 200);
          
        
    }
     // update comment 
          public function update_comment()
                    {  
                        $result = array();
                        $comment_id=$this->_request['comment_id'];
                        $comment_text=$this->_request['comment_text'];
                        $is_created=date('Y-m-d G:i:s');
                        
                        if(empty($comment_id)|| empty($comment_text))
                        {
                          
                             $result['status'] = '0';
                            $result['message']="Invalid data provided!";
                            $this->response($this->json($result), 200);
                        }
                       
                        else
                        {
                            
                            $sql=mysql_query("update  tbl_task_comments set comment_text='".$comment_text."' ,Created_at='".$is_created."' where comment_id='".$comment_id."'",$this->db);
                             // print_r($sql);

                            
                           
                            $result['message'] = "Comment updated";
                            $result['status'] ='1';
                            $this->response($this->json($result), 200);
                       }
        }
     // Delete comment

    public function Delete_comment()
                    {  
                        $result = array();
                        $comment_id=$this->_request['comment_id'];
                        $is_created=date('Y-m-d G:i:s');
                        
                        if(empty($comment_id))
                        {
                          
                             $result['status'] = '0';
                            $result['message']="Invalid data provided!";
                            $this->response($this->json($result), 200);
                        }
                       
                        else
                        {
                            
                            $sql=mysql_query("update  tbl_task_comments set Deleted=0 ,Created_at='".$is_created."' where comment_id='".$comment_id."'",$this->db);
                            $result['message'] = "Comment Deleted";
                            $result['status'] ='1';
                            $this->response($this->json($result), 200);
                       }
        }
    // comment list 
     public function get_comment_by_task(){
          $result=array();
          $task_id=$this->_request['task_id'];
          if(empty($task_id)){
            $result['status']='0';
            $result['message']='Invalid data';
            $this->response($this->json($result),200);
          }

          $sql=mysql_query('select T.*, u.username from tbl_task_comments T, users u where T.user_id=u.id and T.Deleted=1 and T.status=1 and T.task_id="'.$task_id.'" order by T.comment_id DESC',$this->db);

         $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
          else{
            while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)) 
            {
               $result[$i]['comment_id']=$rlt['comment_id'];
               $result[$i]['user_id']=$rlt['user_id'];
               $result[$i]['user_name']=$rlt['username'];
               $result[$i]['task_id']=$rlt['task_id'];
               $result[$i]['comment']=$rlt['comment_text'];
               $result[$i]['created_at']=$rlt['created_at'];
               $result[$i]['comment_status']=$rlt['status'];
               $result[$i]['Deleted']=$rlt['Deleted'];
               $i++;
            }

            $this->response($this->json(array('status'=>'1','message'=>'Data Found','data'=>$result)),200);
          }
    }

        
    public function edit_daily_report (){
         $result = array();
        $report_id=$this->_request['report_id']; 
        $text_report=addslashes($this->_request['text_report']);
        $attachment_file = $_FILES["attachment_file"];
        $attachment_file = $_FILES["attachment_file"]["name"];
        $tmp_name = $_FILES["attachment_file"]["tmp_name"];
        move_uploaded_file($tmp_name,"../uploads/".$attachment_file);

          if(empty($report_id) )
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           if(empty($attachment_file)){
            $sql=mysql_query("update daily_reports set text_report='".$text_report."' WHERE report_id='".$report_id."' ",$this->db);
          }else{
             $sql=mysql_query("update daily_reports set text_report='".$text_report."', attachment_file='".$attachment_file."' WHERE report_id='".$report_id."' ",$this->db);
          }
           if ($sql > 0) {


               $update=mysql_query("SELECT * from daily_reports where report_id='".$report_id."'",$this->db);
               $rlt = mysql_fetch_array($update, MYSQL_ASSOC);
                $result['message'] = "Report updated";
                $result['status'] = '1';
                $result['data'] = $rlt;
               }
                else {
                    $result['status'] = "0";
                    $result['message']   = "Could not update, please try again";
                }
                $this->response($this->json($result), 200);


    }

    // user feedback submit
         private function submit_feedback()
        {  
            $result = array();
            $user_id=$this->_request['user_id'];
            $usefull_app=$this->_request['usefull_app'];
            $easy_to_understand=$this->_request['easy_to_understand'];
            $easy_to_install=$this->_request['easy_to_install'];
            $smooth_to_operate=$this->_request['smooth_to_operate'];
            $team_can_operate_well=$this->_request['team_can_operate_well'];
            $bussiness_improvement=$this->_request['bussiness_improvement'];
            $salesman_explain_well=$this->_request['salesman_explain_well'];
            $will_recommend_to_other=$this->_request['will_recommend_to_other'];
            $rating=$this->_request['rating'];
            $comment=$this->_request['comment'];
            $Status='1';
            $Deleted='1';
            $is_createdd=date('Y-m-d G:i:s');
            $expensive=$this->_request['expensive'];
           
          
            if(empty($user_id))
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           
            else
            {
                
$sql=mysql_query("INSERT INTO `user_feedback` (`user_id`,`usefull_app`,`easy_to_understand`,`easy_to_install`,`smooth_to_operate`,`team_can_operate_well`,`bussiness_improvement`,`salesman_explain_well`,`will_recommend_to_other`,`rating`,`comment`,`Status`,`Deleted`,`is_createdd`,`Expensive`) VALUES  ('".$user_id."','".$usefull_app."','".$easy_to_understand."','".$easy_to_install."','".$smooth_to_operate."','".$team_can_operate_well."','".$bussiness_improvement."','".$salesman_explain_well."','".$will_recommend_to_other."','".$rating."','".$comment."','".$Status."','".$Deleted."','".$is_createdd."','".$expensive."')",$this->db);
                 // print_r($sql);

                $result['report_id']= mysql_insert_id();
               
                $result['message'] = "Submitted feedback";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
           }
        }
        


  public function terms_and_conditions(){
       $result = array();

       $sql = mysql_query("select content from terms_and_conditions");
       $row= mysql_fetch_assoc($sql);

    $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$row)), 200);
  }


// faq api

  public function faq(){
      $result = array();
           $faq_cat = mysql_query("select * FROM faq_category");
           $i=0;
                if(mysql_num_rows($faq_cat) == 0){
                 
                  $result['status'] = '0';
                  $result['message']="Not found";
                  $this->response($this->json($result), 200);
                }
                else
                {  
               while ($row = mysql_fetch_assoc($faq_cat)) {
                     // array_push($result['category_name'] = $row['category_name']);
                 //array_push($result= $row);
                $result[$i] = $row;

           $faq_cat1 = mysql_query("select * FROM faq where faq_id = '".$row['faq_id']."'");
               while ($result_faq = mysql_fetch_assoc($faq_cat1)) {

                       $result[$i]['faq_data'][] = $result_faq;
                      
           }
    
            $i++;

       } 
       }  
           
        $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
    }
  

  public function signout_by_employee()
  {

   $result=array();
   $attendance_id=$this->_request['attendance_id'];
   $signout_location=addslashes($this->_request['signout_location']);
   $add_long=$this->_request['add_long'];
   $add_lat=$this->_request['add_lat'];
   $signout_time=date('Y-m-d G:i:s');
   $status='2';

           if(empty($attendance_id) || empty($signout_location) || empty($add_lat) || empty($add_long)){
              $result['status']='0';
              $result['message']='Invalid data';
              $this->response($this->json($result),200);

           }

           $sql = mysql_query("UPDATE tbl_attendance set add_lat='".$add_lat."', add_long='".$add_long."',
            status='".$status."',signout_location='".$signout_location."', signout_time='".$signout_time."' where attendance_id='".$attendance_id."'",$this->db);
             
             $result['status']='1';
             $result['message']='You have signed-out';
           $this->response($this->json($result), 200);

        }

        public function check_status_attendance_currentdate()
        {
           $result=array();
           $user_id=$this->_request['user_id'];
           $date=date('Y-m-d');

           
           if(empty($user_id)){
            $result['status']='1';
             $result['message']='Invalid data';
             $this->response($this->json($result),200);
           }

               $sql=mysql_query("select * from tbl_attendance where user_id='".$user_id."' and cur_date='".$date."'",$this->db);
               if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else{
               $rlt=mysql_fetch_array($sql,MYSQL_ASSOC);
               $result['status']='1';
               $result['message']='Data found';
               $result['data']=$rlt;
           $this->response($this->json($result),200);
       }
        }


// submit to-do  record
// to-do type_of_repetition station 1=minute,2=hour,3=Day,4=week,5=month,6=year

        public function add_to_do()
        {  
            $result = array();
            $user_id=$this->_request['user_id'];
            $date=$this->_request['date'];
            $time=$this->_request['time'];
            $title=$this->_request['title'];
            $description=$this->_request['description'];
            $interval=$this->_request['interval'];
            $alaram_set_status=$this->_request['alaram_set_status'];
            $type_of_repetition=$this->_request['type_of_repetition'];
            $notification_request_id=$this->_request['notification_request_id']; 
            $status='1';
            $Deleted='1';
            $is_created=date('Y-m-d G:i:s');
         
           
          
            if(empty($user_id) || empty($date) || empty($time) || empty($description))
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           
            else
            {
                
                $sql=mysql_query("INSERT INTO `tbl_todo` (`user_id`,`Tododate`,`time`,`title`,`description`,`todo_interval`,`alaram_set_status`,`type_of_repetition`,`notification_request_id`,`is_created`,`status`,`Deleted`) VALUES  ('".$user_id."','".$date."','".$time."','".$title."','".$description."','".$interval."','".$alaram_set_status."','".$type_of_repetition."','".$notification_request_id."','".$is_created."','".$status."','".$Deleted."')",$this->db);
                

                $result['to-do_id']= mysql_insert_id();
               
                $result['message'] = " Added";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
           }
        }
        

        public function update_todo(){
         $result = array();
           $todo_id=$this->_request['todo_id'];
            $date=$this->_request['date'];
            $time=$this->_request['time'];
            $title=$this->_request['title'];
            $description=$this->_request['description'];
            $interval=$this->_request['interval'];
            $alaram_set_status=$this->_request['alaram_set_status'];
            $type_of_repetition=$this->_request['type_of_repetition'];
            $notification_request_id=$this->_request['notification_request_id']; 
            $is_created=date('Y-m-d G:i:s');
          if(empty($todo_id) )
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
         
             $sql=mysql_query("UPDATE tbl_todo set Tododate='".$date."',time='".$time."',title='".$title."',description='".$description."',todo_interval='".$interval."',alaram_set_status='".$alaram_set_status."', type_of_repetition='".$type_of_repetition."',notification_request_id='".$notification_request_id."',is_created='".$is_created."' WHERE id='".$todo_id."'",$this->db);
        
           if ($sql > 0) {


               $update=mysql_query("SELECT * from tbl_todo where id='".$todo_id."'",$this->db);
               $rlt = mysql_fetch_array($update, MYSQL_ASSOC);
                $result['message'] = "Updated";
                $result['status'] = '1';
                $result['data'] = $rlt;
               }
                else {
                    $result['status'] = "0";
                    $result['message']   = "Could not update, please try again";
                }
                $this->response($this->json($result), 200);

    }


     // delete To do by To do Id


         Public function delete_todo(){
            $result = array();
            $todo_id=$this->_request['todo_id'];

            if(empty($todo_id)){
               $result['status'] = '0';
               $result['message']="Invalid data provided!";
               $this->response($this->json($result), 200);
            }
            $sql=mysql_query("UPDATE  tbl_todo set Deleted='0' where id='".$todo_id."'",$this->db);
                   $result['message'] = "Deleted";
                    $result['status'] = '1';
                   $this->response($this->json($result), 200);
         }


     // To do list
       public function get_todo_list(){
        $result = array();
        $user_id=$this->_request['user_id'];

        if(empty($user_id)){
           $result['status'] = '0';
           $result['message']="Invalid data provided!";
           $this->response($this->json($result), 200);
        }
        $sql=mysql_query("select * from tbl_todo where user_id='".$user_id."'",$this->db);
        $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                  while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                    $result[$i]['id'] = $rlt['id'];
                    $result[$i]['date'] = $rlt['Tododate'];
                    $result[$i]['time'] = $rlt['time'];
                    $result[$i]['title'] = $rlt['title'];
                    $result[$i]['description'] = $rlt['description'];
                    $result[$i]['alaram_set_status'] = $rlt['alaram_set_status'];
                    $result[$i]['todo_interval'] = $rlt['todo_interval'];
                    $result[$i]['type_of_repetition'] = $rlt['type_of_repetition'];
                    $result[$i]['notification_request_id'] = $rlt['notification_request_id'];
                         $result[$i]['is_created'] = $rlt['is_created'];
                       
                        $i++;  // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }
     }


     // add time sheet api 

          public function add_timesheet()
        {  
                $result = array();
                $user_id=$this->_request['user_id'];
                $task_id=$this->_request['task_id'];
                $head_id=$this->_request['head_id'];
                $activity_id=$this->_request['activity_id'];
                $submit_date=$this->_request['submit_date'];
                $start_time=date('Y-m-d G:i:s',strtotime($this->_request['start_time']));
                $end_time=date('Y-m-d G:i:s',strtotime($this->_request['end_time']));
                $comment=addslashes($this->_request['comment']);
                $timesheet_status=$this->_request['timesheet_status'];
                $is_created=date('Y-m-d G:i:s');
                $date=date('Y-m-d');

                $image = $_FILES["image"];
                $image = $_FILES["image"]["name"];
                $tmp_name = $_FILES["image"]["tmp_name"];
                move_uploaded_file($tmp_name,"../uploads/".$image);
          
            if(empty($user_id) || empty($start_time) || empty($end_time) || empty($comment)|| empty($timesheet_status))
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           
            /* user department */
       
                $num= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$user_id."'");
                $row = mysql_fetch_assoc($num);
                $department_id = $row['DepartmentID'];

              
                $sql=mysql_query("INSERT INTO `tbl_tasksubmits` (`TaskSubmitTaskID`,`TaskSubmitUserID`,`department_id`,`head_id`,`activity_id`,`TaskSubmitDate`,`TaskSubmitStartTime`,`TaskSubmitEndTime`,`TaskSubmitUserComment`,`Attachment`,`TaskSubmitCreatedAt`,`Submission_status`) VALUES  ('".$task_id."','".$user_id."','".$department_id."','".$head_id."','".$activity_id."','".$submit_date."','".$start_time."','".$end_time."','".$comment."','".$image."','".$is_created."','".$timesheet_status."')",$this->db);
                
                 
                      mysql_query("Update tbl_timesheet set check_user_id=0,timesheet_status=0 where submit_date='".$submit_date."' and submit_user_id='".$user_id."'",$this->db);
                  $query=mysql_query("select * from tbl_timesheet where submit_user_id='".$user_id."' and submit_date='".$submit_date."' ",$this->db)or die(mysql_error());
                    if(mysql_num_rows($query) > 0){
                       $result['status'] = '1';
                       $result['message']="Line Item added";
                       $this->response($this->json($result), 200);
                    }
                    else{
                          $lineitems=mysql_query("INSERT INTO tbl_timesheet (department_id,submit_user_id,submit_date,timesheet_status,is_manager,created_at) VALUES  ('".$department_id."','".$user_id."','".$submit_date."','0','0','".$is_created."')",$this->db);
                         
                      }
              
                $result['message'] = "Line Item added";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
        }
        
    /* update timesheet api */

        public function update_line_items_by_employee()
          {  
            $result = array();
            $lineitem_id=$this->_request['lineitem_id'];
            $task_id=$this->_request['task_id'];
            $head_id=$this->_request['head_id'];
            $activity_id=$this->_request['activity_id'];
            $submit_date=$this->_request['submit_date'];
            $start_time=date('Y-m-d H:i:s',strtotime($this->_request['start_time']));
            $end_time=date('Y-m-d H:i:s',strtotime($this->_request['end_time']));
            $comment=addslashes($this->_request['comment']);
            $timesheet_status=2;
            $date=date('Y-m-d');
            $is_created=date('Y-m-d G:i:s');
                $image = $_FILES["image"];
                $image = $_FILES["image"]["name"];
                $tmp_name = $_FILES["image"]["tmp_name"];
                move_uploaded_file($tmp_name,"../uploads/".$image);
          
            if(empty($lineitem_id) || empty($start_time) || empty($end_time) || empty($comment)|| empty($timesheet_status))
            {
              
                 $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
           
            else
            {
                
                $sql=mysql_query("update  tbl_tasksubmits SET  TaskSubmitTaskID='".$task_id."',head_id='".$head_id."',activity_id='".$activity_id."',TaskSubmitDate='".$submit_date."',TaskSubmitStartTime='".$start_time."',TaskSubmitEndTime='".$end_time."',TaskSubmitUserComment='".$comment."',Attachment='".$image."',TaskSubmitCreatedAt='".$is_created."',Submission_status='".$timesheet_status."' where TaskSubmitID='".$lineitem_id."'",$this->db);
                $user=mysql_query("select * from tbl_tasksubmits where TaskSubmitID='".$lineitem_id."' ",$this->db)or die(mysql_error());
                $userdta = mysql_fetch_array($user);
                $user_id=$userdta['TaskSubmitUserID'];
                $num= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$user_id."'");
                $row = mysql_fetch_assoc($num);
                $department_id = $row['DepartmentID'];
                $query=mysql_query("select * from tbl_timesheet where submit_user_id='".$user_id."' and submit_date='".$date."' ",$this->db)or die(mysql_error());
                    if(mysql_num_rows($query) > 0){
                       $result['status'] = '1';
                       $result['message']="Work Done update";
                       $this->response($this->json($result), 200);
                    }
                    else{
                          $lineitems=mysql_query("INSERT INTO tbl_timesheet (department_id,submit_user_id,submit_date,timesheet_status,is_manager,created_at) VALUES  ('".$department_id."','".$user_id."','".$date."','0','0','".$is_created."')",$this->db);
                         
                      }
                 // print_r($sql);

               
                $result['message'] = "Work Done update";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
           }
        }

 

// line item team list  

 
           
    public function get_line_items_team_list(){
        $result = array();
        $user_id=$this->_request['user_id'];

          if(empty($user_id))
         {
           $result['status'] = '0';
           $result['message']="Invalid data provided!";
           $this->response($this->json($result), 200);
          }

           $num= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$user_id."'");
           $row = mysql_fetch_assoc($num);
           $department_id = $row['DepartmentID'];
           $sql=mysql_query("SELECT D.*,U.username, U.id FROM tbl_departmentemploy D , users U WHERE D.DepartmentID='".$department_id."' and D.EmployID=U.id",$this->db);
           $result = array();
            $i=0;
                if(mysql_num_rows($sql) == 0){
                 
                  $result['status'] = '0';
                  $result['message']="Not found";
                  $this->response($this->json($result), 200);
                }
                else
                {
                      while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                    
                     // $result[$i] = $rlt;
                      $result[$i]['id'] = $rlt['id'];
                      $result[$i]['username'] = $rlt['username'];
                      $result[$i]['line_item']=array();
                      $qry=mysql_query("select T.*,tk.TaskName,tk.TaskType,tk.TaskCategory, TIMEDIFF(DATE_FORMAT(T.TaskSubmitEndTime,'%Y-%m-%d %H:%i:%s A'), DATE_FORMAT(T.TaskSubmitStartTime,'%Y-%m-%d %H:%i:%s A')) as Durring   from tbl_tasksubmits T, tbl_tasks tk where T.TaskSubmitTaskID=tk.TaskID and T.Deleted=1 and T.TaskSubmitUserID='".$rlt['EmployID']."'",$this->db); 
                      while ($result_faq = mysql_fetch_assoc($qry)) {

                       $result[$i]['line_item'][]= $result_faq;
                      
                        }
                           
                         $i++; 
                         
                    }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }
     }

        //  time sheet list

     public function get_timesheet_employee_and_manager(){
        $result=array();
        $manager_id=$this->_request['manager_id'];
        $employee_id=$this->_request['employee_id'];
        $status=$this->_request['status'];


         $user= mysql_query("select * from users where id='".$manager_id."'");
             $row = mysql_fetch_assoc($user);
             $manager = $row['is_manager'];
             $company_id = $row['UserCompanyID'];
             $supervisor = $row['supervisor'];

        if($manager_id!="" && $manager==2 && $supervisor==0){


        	if($status!="" ){
 				$sql=mysql_query("select T.*, U.username, D.department_name from tbl_timesheet T, users U, tbl_department D where T.submit_user_id=U.id  and T.department_id=D.id and T.timesheet_status='".$status."'  and T.department_id IN (select DepartmentID from tbl_departmentemploy where EmployID='".$manager_id."')  order by T.timesheet_id DESC",$this->db);
        	}
            else{
 				$sql=mysql_query("select T.*, U.username, D.department_name from tbl_timesheet T, users U, tbl_department D where T.submit_user_id=U.id  and T.department_id=D.id  and T.department_id IN (select DepartmentID from tbl_departmentemploy where EmployID='".$manager_id."')  order by T.timesheet_id DESC",$this->db);
        	}
        
        }elseif($manager_id!="" && $manager==3 &&  $supervisor==1){
            if($status!=""){
               $sql=mysql_query("select T.*, U.username, D.department_name from tbl_timesheet T, users U, tbl_department D , tbl_supervisor s where T.submit_user_id=U.id  and T.department_id=D.id  and s.user_id=T.submit_user_id and  s.supervisor_id='".$manager_id."' and T.timesheet_status='".$status."' order by T.timesheet_id DESC",$this->db);
            }else{
                $sql=mysql_query("select T.*, U.username, D.department_name from tbl_timesheet T, users U, tbl_department D , tbl_supervisor s where T.submit_user_id=U.id  and T.department_id=D.id  and s.user_id=T.submit_user_id and  s.supervisor_id='".$manager_id."'  order by T.timesheet_id DESC",$this->db);
            }
                
            }else{
        	if($status!=""){
        		$sql=mysql_query("select T.*, U.username, D.department_name from tbl_timesheet T, users U, tbl_department D where T.submit_user_id=U.id  and T.department_id=D.id  and T.timesheet_status='".$status."' and  T.submit_user_id='".$employee_id."' order by T.submit_date DESC",$this->db);
        	}
            else{
               $sql=mysql_query("select T.*, U.username, D.department_name from tbl_timesheet T, users U, tbl_department D where T.submit_user_id=U.id  and T.department_id=D.id  and T.submit_user_id='".$employee_id."' order by T.submit_date DESC",$this->db);
            }
           
        }
        
        $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                  while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                    $result[$i] = $rlt;
                    $i++;  
                    // 1 - pending approval,2- Pending ,3 - In Progress ,4 - Waiting for Completetion, 5- Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }
     }

     /* get list of line items*/

    public function get_timesheet_lineitem_list()
    {
        $result = array();
       
        $task_id=$this->_request['task_id'];

        if( empty($task_id)){
           $result['status'] = '0';
           $result['message']="Invalid data provided!";
           $this->response($this->json($result), 200);
        }

        $sql=mysql_query("select * , TIMEDIFF(DATE_FORMAT(TaskSubmitEndTime,'%Y-%m-%d %H:%i:%s %p'), DATE_FORMAT(TaskSubmitStartTime,'%Y-%m-%d %H:%i:%s %p')) as Durring from tbl_tasksubmits  where  Deleted=1 and TaskSubmittaskID='".$task_id."'",$this->db);
        $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                  while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                    $result[$i] = $rlt;
                    $i++;  // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }
     }

/* line items by manager*/

    public function get_lineitems_list_by_employee()
    {
       
        $employee_id=$this->_request['employee_id'];
        $lastdate=date('Y-m-d',strtotime("-1 days"));
        $date=$this->_request['date'];
        if(empty($employee_id ) || empty($date))
        {
           $result['status'] = '0';
           $result['message']="Invalid data provided!";
           $this->response($this->json($result), 200);
        }
          if($date){
            $query=mysql_query("SELECT
                         (select timesheet_status from tbl_timesheet  where  submit_date='".$date."' and submit_user_id='".$employee_id."') as    timesheet_status,
                        (select COALESCE(timesheet_id ,0) from tbl_timesheet  where  submit_date='".$date."' and submit_user_id='".$employee_id."' ) as timesheet_id,
                        (select COALESCE(SEC_TO_TIME(SUM(UNIX_TIMESTAMP(TaskSubmitEndTime) - UNIX_TIMESTAMP(`TaskSubmitStartTime`))),0)  from tbl_tasksubmits where Deleted=1 and  TaskSubmitDate ='".$date."' and Submission_status!=1 and TaskSubmitUserID= '".$employee_id."') as submit_time,
                        (select COALESCE(SEC_TO_TIME(SUM(UNIX_TIMESTAMP(TaskSubmitEndTime) - UNIX_TIMESTAMP(`TaskSubmitStartTime`))),0)  from tbl_tasksubmits where Submission_status=3 and Deleted=1 and TaskSubmitDate ='".$date."' and TaskSubmitUserID= '".$employee_id."') as approve_time ",
                        $this->db);
        
        
            $result = array();
            $i=0;
             if(mysql_num_rows($query) == NULL)
                        {
             
                              $result['status'] = '0';
                              $result['message']="Not found";
                              $this->response($this->json($result), 200);
                        }
      
            $rlt = mysql_fetch_array($query, MYSQL_ASSOC);
            $result[$i]['timesheet_status'] = $rlt['timesheet_status'];
            
            $result[$i]['approve_time'] = $rlt['approve_time'];
            $result[$i]['submit_time'] = $rlt['submit_time'];
            $result[$i]['timesheet_id'] = $rlt['timesheet_id'];
            
                $result[$i]['line_item']=array();

                $sql=mysql_query("select T.TaskName,T.TaskType, T.TaskCategory,TL.*, H.HeadName , A.ActivityName ,TIMEDIFF(DATE_FORMAT(TL.TaskSubmitEndTime,'%Y-%m-%d %H:%i:%s %p'), DATE_FORMAT(TL.TaskSubmitStartTime,'%Y-%m-%d %H:%i:%s %p')) as Durring from tbl_tasksubmits TL, tbl_tasks T, tbl_heads H, tbl_activities A where Submission_status!=0 and TL.TaskSubmitUserID='".$employee_id."' and H.HeadID=TL.head_id and A.ActivityID=TL.activity_id and 	TL.TaskSubmitDate ='".$date."' and TL.Deleted=1 and TL.TaskSubmitTaskID=T.TaskID order by 	TaskSubmitID DESC",$this->db);
                        if(mysql_num_rows($sql) == 0)
                        {
             
                              $result['status'] = '0';
                              $result['message']="Not found";
                              $this->response($this->json($result), 200);
                        }
                    while ($line= mysql_fetch_assoc($sql)) 
                    {
                          $result[$i]['line_item'][] = $line;  
                    }
                    $i++;  
               // }
            $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            
          }
      
    }

/*filter api timesheet*/


    public function timesheet_filter_data()
    {
        $result = array();
        $user_id=$this->_request['user_id'];
            if(empty($user_id))
            {
               $result['status'] = '0';
               $result['message']="Invalid data provided!";
               $this->response($this->json($result), 200);
            }

            $sql=mysql_query("SELECT * FROM `tbl_tasksubmits` WHERE TaskSubmitDate >= '".$fromdate."' and TaskSubmitDate <= '".$todate."' ",$this->db);
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i] = $rlt;
                    $i++;  
                    // 1 - pending approval, 2- Pending , 3 - In Progress ,4 - Waiting for Completetion, 5 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }
    }



/*add directory api */

public function add_directory()
{  
    $result = array();
    $user_id=$this->_request['user_id'];
    $business_name=addslashes($this->_request['business_name']);
    $namecontact1=$this->_request['namecontact1'];
    $mobile1=$this->_request['mobile1'];
    $email1=$this->_request['email1'];
    $namecontact2=$this->_request['namecontact2'];
    $mobile2=$this->_request['mobile2'];
    $email2=$this->_request['email2'];
    $address=$this->_request['address'];
    $city=$this->_request['city'];
    $pincode=$this->_request['pincode'];
    $remark=addslashes($this->_request['remark']);
    $is_created=date('Y-m-d G:i:s');
    $Status=1;
    $Deleted=1;
        if(empty($user_id) || empty($business_name) || empty($namecontact1))
         {
            $result['status'] = '0';
            $result['message']="Invalid data provided!";
            $this->response($this->json($result), 200);
         }
                       
        else
         {
            $num= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$user_id."'");
            $row = mysql_fetch_assoc($num);
            $department_id = $row['DepartmentID'];
            $sql=mysql_query("INSERT INTO `tbl_directory` (`User_id`,`Department_id`,`Business_Name`,`Namecontact1`,`Mobile1`,`Email1`,`Namecontact2`,`Mobile2`,`Email2`,`Address`,`City`,`Pincode`,`Remarks`,`Created_at`,`Status`,`Deleted`) VALUES  ('".$user_id."','".$department_id."','".$business_name."','".$namecontact1."','".$mobile1."','".$email1."','".$namecontact2."','".$mobile2."','".$email2."','".$address."','".$city."','".$pincode."','".$remark."','".$is_created."','".$Status."','".$Deleted."')",$this->db);
                             // print_r($sql);

            $result['id']= mysql_insert_id();
            $result['message'] = "Directory added";
            $result['status'] ='1';
            $this->response($this->json($result), 200);
         }
}
        
/*edit directory api 24 jan 2019*/

    public function update_directory()
    {  
        $result = array();
        $directory_id=$this->_request['directory_id'];
        $business_name=addslashes($this->_request['business_name']);
        $namecontact1=$this->_request['namecontact1'];
        $mobile1=$this->_request['mobile1'];
        $email1=$this->_request['email1'];
        $namecontact2=$this->_request['namecontact2'];
        $mobile2=$this->_request['mobile2'];
        $email2=$this->_request['email2'];
        $address=addslashes($this->_request['address']);
        $city=$this->_request['city'];
        $pincode=$this->_request['pincode'];
        $remark=addslashes($this->_request['remark']);
        $is_created=date('Y-m-d G:i:s');
                        
            if(empty($directory_id) || empty($business_name) || empty($namecontact1))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            else
            {
                $sql=mysql_query("update  tbl_directory set Business_Name='".$business_name."',Namecontact1='".$namecontact1."',Mobile1='".$mobile1."',Email1='".$email1."',Namecontact2='".$namecontact2."',Mobile2='".$mobile2."',Email2='".$email2."',Address='".$address."',City='".$city."',Pincode='".$pincode."',Remarks='".$remark."',Created_at='".$is_created."' where DirectoryID='".$directory_id."'",$this->db);
                $result['message'] = "Directory updated";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
            }
    }

/*delete directory */
    public function Delete_directory()
    {  
        $result = array();
        $directory_id=$this->_request['directory_id'];
        $is_created=date('Y-m-d G:i:s');
            if(empty($directory_id))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
                       
            else
            {
                $sql=mysql_query("update  tbl_directory set Deleted=0 ,Created_at='".$is_created."' where DirectoryID='".$directory_id."'",$this->db);
                $result['message'] = "Directory Deleted";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
            }
    }

      // directory list


        public function get_user_directory_list(){
                $result = array();
                $user_id=$this->_request['user_id'];
                

                if(empty($user_id)){
                   $result['status'] = '0';
                   $result['message']="Invalid data provided!";
                   $this->response($this->json($result), 200);
                }
        $sql=mysql_query("select D.*,U.username from tbl_directory D, users U where D.User_id=U.id and D.status=1 and D.Deleted=1 and User_id='".$user_id."'",$this->db);
        $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                  while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                    $result[$i] = $rlt;
                    $i++;  // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }
     }

   // verified directory and favourite
   public function verified_and_favourite_status_directory()
                    {  
                        $result = array();
                        $directory_id=$this->_request['directory_id'];
                        $verified=$this->_request['verified'];
                        $favourite=$this->_request['favourite'];
                        if(empty($directory_id))
                        {
                          
                             $result['status'] = '0';
                            $result['message']="Invalid data provided!";
                            $this->response($this->json($result), 200);
                        }
                       
                        else
                        {
                            
                            $sql=mysql_query("update  tbl_directory set verified_status='".$verified."' , favourite_status='".$favourite."' where DirectoryID='".$directory_id."'",$this->db);
                             // print_r($sql);

                            
                           
                            $result['message'] = "update";
                            $result['status'] ='1';
                            $this->response($this->json($result), 200);
                       }
        }  

// directory list department wise
     public function get_directory_list(){
                $result = array();
                $user_id=$this->_request['user_id'];
               
                if(empty($user_id)){
                   $result['status'] = '0';
                   $result['message']="Invalid data provided!";
                   $this->response($this->json($result), 200);
                }
        $num= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$user_id."'");
           $row = mysql_fetch_assoc($num);
           $department_id = $row['DepartmentID'];

        $sql=mysql_query("select D.*,U.username from tbl_directory D, users U where D.User_id=U.id and D.Deleted=1 and D.status=1 and  Department_id='".$department_id."'",$this->db);
        $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                  while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                    $result[$i] = $rlt;
                    $i++;  // 0 - pending approval, 1- Pending , 2 - In Progress ,3 - Waiting for Completetion, 4 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }
     }


// add contact us 

     public function add_contact_us()
                    {  
                        $result = array();
                        $user_id=$this->_request['user_id'];
                        $name=addslashes($this->_request['name']);
                        $email=$this->_request['email'];
                        $mobile=$this->_request['mobile'];
                        $message=addslashes($this->_request['message']);
                        $is_created=date('Y-m-d G:i:s');
                        $status=1;
                        $Deleted=1;
                     
                      
                        if(empty($user_id))
                        {
                          
                             $result['status'] = '0';
                            $result['message']="Invalid data provided!";
                            $this->response($this->json($result), 200);
                        }
                       
                        else
                        {

                            $num= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$user_id."'");
                            $row = mysql_fetch_assoc($num);
                            $department_id = $row['DepartmentID'];
                            
                            $sql=mysql_query("INSERT INTO `tbl_contactus` (`user_id`,`Department_id`,`name`,`email`,`mobile`,`message`,`created_at`,`status`,`Deleted`) VALUES  ('".$user_id."','".$department_id."','".$name."','".$email."','".$mobile."','".$message."','".$is_created."','".$status."','".$Deleted."')",$this->db);
                             // print_r($sql);

                            $result['id']= mysql_insert_id();
                           
                            $result['message'] = "Contact added";
                            $result['status'] ='1';
                            $this->response($this->json($result), 200);
                       }
        }
        
/*  bulletin board filter date wise api*/
    private function bulletin_board_date_filter()
    {
        $company_id = $this->_request['company_id'];
        $date =$this->_request['date'];
            
            if(empty($company_id) || empty($date))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }

            $sql=mysql_query('select B.*, U.username, U.id  from tbl_bulletinboard B, users U  where B.BulletinUserID="'.$company_id.'" and DATE_FORMAT(B.BulletinCreatedDate ,"%Y-%m-%d") ="'.$date.'" and   B.BulletinUserID=U.id', $this->db)or die(mysql_error());
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                
                    $result[$i]['company_name'] = $rlt['username'];
                    $result[$i]['bulletin_board_id'] = $rlt['BulletinID'];
                    $result[$i]['title']=$rlt['BulletinTitle'];
                    $result[$i]['image'] = $rlt['image'];
                    $result[$i]['description'] = stripslashes($rlt['BulletinDescription']);
                    $result[$i]['created_at'] = $rlt['BulletinCreatedDate'];
                    $i++; 
                 
                }

                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }

    }

/*  get employee list by manger all department*/

    public function get_employee_list_by_manager()
    {
        $result=array();
        $user_id=$this->_request['user_id'];

            if(empty($user_id))
            {
                $result['status'] = '0';
                $result['message']="User id required";
                $this->response($this->json($result), 200);
            }
              $user= mysql_query("select * from users where id='".$user_id."'");
                     $row = mysql_fetch_assoc($user);
                     $manager = $row['is_manager'];
                     $company_id = $row['UserCompanyID'];
                     $supervisor = $row['supervisor'];
               if($manager==2){
                      $sql=mysql_query("select E.*,U.username,U.mobile,U.email,U.city,U.state,U.country,U.id, U.profile_pic, D.department_name from tbl_departmentemploy E, users U, tbl_department D where E.DepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID='".$user_id."') and E.DepartmentID=D.id and E.EmployID=U.id group by E.EmployID order by U.username ",$this->db) or die(mysql_error());   
                     }
               
               else{
                  $sql = mysql_query("select U.username,U.mobile,U.email,U.city,U.state,U.country,U.id, U.profile_pic,d.department_name,U.id ,S.* from  users U,tbl_department d , tbl_departmentemploy E,tbl_supervisor S where U.id =S.user_id AND E.DepartmentID=d.id and E.EmployID=S.user_id and S.supervisor_id='".$user_id."' group by E.EmployID");
               }

                $result = array();
                $i=0;
            if(mysql_num_rows($sql) == 0)
            {
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                {
                    $result[$i]['employee_id'] = $rlt['id'];
                    $result[$i]['employee_name'] = stripslashes($rlt['username']);
                    $result[$i]['department_name']=$rlt['department_name'];
                    $result[$i]['country']=$rlt['country'];
                    $result[$i]['state']=$rlt['state'];
                    $result[$i]['city']=$rlt['city'];
                    $result[$i]['email'] = $rlt['email'];
                    $result[$i]['mobile'] = $rlt['mobile'];
                    $result[$i]['profile_pic'] = $rlt['profile_pic'];
                    $i++; 
                 
                }

                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }
             
    }

/*     daily report comment */

    public function submit_comment_daily_report()
    {  
        $result = array();
        $report_id=$this->_request['report_id'];
        $comment=addslashes($this->_request['comment']);
                        
            if(empty($report_id) || empty($comment))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            else
            {
                $sql=mysql_query("update  daily_reports set comment='".$comment."'  where   report_id='".$report_id."'",$this->db);
                $result['message'] = "submitted comment";
                $result['status'] ='1';
                $this->response($this->json($result), 200);
            }
    }  


/*approve disapprov and send back line items by manager
    3 approved , 4 send back and 5 disapproved*/

    public function line_items_response_by_manager()
    {  
        $result = array();
        $remark = addslashes($this->_request['remark']);
        $line_item_id =$this->_request['line_item_id'];
        $submission_status=$this->_request['submission_status'];
          
          
            if(empty($line_item_id)|| empty($submission_status))
            {
                 $result['status'] = "0";
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            switch ($submission_status) {
                case '2':
                $sql=mysql_query("update  tbl_tasksubmits set Remark='".$remark."', Submission_status='".$submission_status."' where TaskSubmitID='".$line_item_id."'", $this->db) or die(mysql_error());
               
                $result['message'] = "Line Item Pending";
                $result['status'] = '1';
                $this->response($this->json($result), 200);
                break;
                case '3':
                $sql=mysql_query("update  tbl_tasksubmits set Remark='".$remark."', Submission_status='".$submission_status."' where TaskSubmitID='".$line_item_id."'", $this->db) or die(mysql_error());
               
                $result['message'] = "Line Item Approved";
                $result['status'] = '1';
                $this->response($this->json($result), 200);
                break;

                case '4':
                $sql=mysql_query("update  tbl_tasksubmits set Remark='".$remark."', Submission_status='".$submission_status."' where TaskSubmitID='".$line_item_id."'", $this->db) or die(mysql_error());
               
                $result['message'] = "Line Item Send Back";
                $result['status'] = '1';
                $this->response($this->json($result), 200);
                break;

                case '5':
                $sql=mysql_query("update  tbl_tasksubmits set Remark='".$remark."', Submission_status='".$submission_status."' where TaskSubmitID='".$line_item_id."'", $this->db) or die(mysql_error());
               
                $result['message'] = "Line Items Disapproved";
                $result['status'] = '1';
                $this->response($this->json($result), 200);
                   break;
                   
               default:
                   # code...
                   break;
            }
          
    }
          
   public function delete_line_items(){
    $result=array();
    $lineitem_id=$this->_request['lineitem_id'];
    if(empty($lineitem_id))
            {
                 $result['status'] = "0";
                $result['message']="Line Item Id required";
                $this->response($this->json($result), 200);
            }
      $sql=mysql_query("update  tbl_tasksubmits set  Deleted='0' where TaskSubmitID='".$lineitem_id."'", $this->db) or die(mysql_error());
               
                $result['message'] = "Deleted Lineitem";
                $result['status'] = '1';
                $this->response($this->json($result), 200);
   }

    public function user_tracking(){
         $result=array();
         $user_id=$this->_request['user_id'];
         $lat=$this->_request['lat'];
         $long=$this->_request['long'];
         $address=$this->_request['address'];
            if(empty($user_id))
                    {
                         $result['status'] = "0";
                        $result['message']="Line Item Id required";
                        $this->response($this->json($result), 200);
                    }
             $sql=mysql_query("update  users set  lat ='".$lat."', longitude ='".$long."', current_address='".$address."' where id='".$user_id."'", $this->db) or die(mysql_error());
               
                $result['message'] = "updated";
                $result['status'] = '1';
                $this->response($this->json($result), 200);
   }

   /* get lattitude and logetitude*/
     public function get_employee_tacking()
    {
        $result=array();
        $user_id=$this->_request['user_id'];

            if(empty($user_id))
            {
                $result['status'] = '0';
                $result['message']="User id required";
                $this->response($this->json($result), 200);
            }
                
                $sql=mysql_query("select  id, username,lat, longitude , current_address from users where id='".$user_id."' ",$this->db) or die(mysql_error());
                $result = array();
                $i=0;
            if(mysql_num_rows($sql) == 0)
            {
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                   $rlt = mysql_fetch_array($sql, MYSQL_ASSOC);
                    $result[$i] = $rlt;
                   
              

                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }
             
    }
   public function timesheet_approve_by_manager()
    {  
        $result = array();
        $check_user_id =$this->_request['check_user_id'];
        $timesheet_id =$this->_request['timesheet_id'];
        $approvalcreated_at = date('Y-m-d H:i:s');
        $timesheet_status=1;
          
          
            if(empty($timesheet_id)||empty($check_user_id))
            {
                 $result['status'] = "0";
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }

            switch ($timesheet_status) {
                case '1':
                $sql=mysql_query("update  tbl_timesheet set  timesheet_status='".$timesheet_status."',check_user_id='".$check_user_id."',approval_created_at='".$approvalcreated_at."' where timesheet_id='".$timesheet_id."'", $this->db) or die(mysql_error());
               
                $result['message'] = "Timesheet approved";
                $result['status'] = '1';
                $this->response($this->json($result), 200);
                break;
                
                   
               default:
                   # code...
                   break;
            }
          
    }



    /* submit buitton click employee site timesheet */

    public function submit_timesheet()
    {  
        $result = array();
        $submit_user_id =$this->_request['submit_user_id'];
        $date =date('Y-m-d');
        $created_at=date('Y-m-d H:i:s');
      
          
            if(empty($submit_user_id))
            {
                 $result['status'] = "0";
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }

             $timesheet=mysql_query("select * from tbl_timesheet where submit_user_id='".$submit_user_id."' and submit_date='".$date."' ",$this->db)or die(mysql_error());
                    if(mysql_num_rows($timesheet) > 0){
                       $result['status'] = '1';
                       $result['message']="already submited";
                       $this->response($this->json($result), 200);
                    }
                    else{
                $num= mysql_query("select * from  tbl_departmentemploy  where EmployID='".$submit_user_id."'");
                $row = mysql_fetch_assoc($num);
                $department_id = $row['DepartmentID'];        

             $query=mysql_query("SELECT  SEC_TO_TIME(SUM(UNIX_TIMESTAMP(TaskSubmitEndTime) - UNIX_TIMESTAMP(`TaskSubmitStartTime`))) as submit_time from tbl_tasksubmits where  DATE_FORMAT(TaskSubmitCreatedAt ,'%Y-%m-%d') ='".$date."' and Deleted=1 and  TaskSubmitUserID= '".$submit_user_id."'",$this->db);
               $row = mysql_fetch_assoc($query);
               $total_time = $row['submit_time'];

            if($submit_user_id){

            	$timesheet=mysql_query("Update tbl_timesheet set total_time_lineitems='".$total_time."',created_at='".$created_at."', is_manager='1' where submit_date='".$date."' and submit_user_id='".$submit_user_id."'");
               
              
                $result['message'] = "Timesheet submit";
                $result['status'] = '1';
                $this->response($this->json($result), 200);
               }
          }
    }
          
     /*  multiple approval line items api */   
  public function multiple_approve_lineitems()
    {  
        $result = array();
       
        $line_item_id =explode(',',$this->_request['line_item_id']);
        $submission_status=3;
          
          
            if(empty($line_item_id)|| empty($submission_status))
            {
                 $result['status'] = "0";
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
             
               $count=count($line_item_id);
               for ($i=0;$i<$count;$i++) {

                  $sql=mysql_query("UPDATE  tbl_tasksubmits set Submission_status='".$submission_status."' where TaskSubmitID='".$line_item_id[$i]."'", $this->db) or die(mysql_error());
                 
               }
                $result['message'] = "Line Item Approved";
                $result['status'] = '1';
                $this->response($this->json($result), 200);
               
            
          
    }

public function auto_update(){
       $result=array();

        $query= $this->db->query("UPDATE tbl_tasks SET TaskStatus ='3' WHERE TaskStartTime >= NOW() AND TaskStatus='2'");
        $result['message'] = "update";
        $result['status'] = '1';
        $this->response($this->json($result), 200);
    }
    /* task filter api date type category name from date to date  single date*/

     private function task_filter_by_manager(){
               $user_id = $this->_request['user_id'];
               $department_id = $this->_request['department_id'];
               $task_status = $this->_request['task_status'];
               $from_date = $this->_request['from_date'];
               $to_date = $this->_request['to_date'];
               $task_type = $this->_request['task_type'];
               $task_category = $this->_request['task_category'];
               $employee_id = $this->_request['employee_id'];
               $task_priority = $this->_request['task_priority'];

            if(empty($user_id))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }


             $addquery='';
        if($department_id != 0 && $department_id != '') 
            { 
             $addquery.=' and T.TaskDepartmentID='.$department_id; 
            }
        if($employee_id != 0 && $employee_id!= '') 
            {
             $addquery.=' and T.TaskAssignToUserID='.$employee_id; 
            }
        if($task_type != 0 && $task_type != '') 
            {
             $addquery.=' and T.TaskType='.$task_type; 
            }
        if($from_date != '') 
          { 
            $addquery.=' and T.TaskStartDate>="'.date('Y-m-d', strtotime($from_date)).'"'; 
          }
        if($to_date != '') 
          { 
            $addquery.=' and T.TaskStartDate<="'.date('Y-m-d', strtotime($to_date)).'"'; 
          }
        if(in_array($task_status, array(1,2,3,4,5)) && $task_status != '%20' && $task_status != '')
            {
             $addquery.=' and T.TaskStatus='.$task_status; 
            }
        if(in_array($task_category, array(1,2)) && $task_category != '%20' && $task_category != '')
            {
             $addquery.=' and T.TaskCategory='.$task_category; 
            }
        if(in_array($task_priority, array(1,2)) && $task_priority != '%20' && $task_priority != '')
            {
             $addquery.=' and T.priority='.$task_priority; 
            }
        $user= mysql_query("select * from users where id='".$user_id."'");
                     $row = mysql_fetch_assoc($user);
                     $manager = $row['is_manager'];
                     $company_id = $row['UserCompanyID'];
                     $supervisor = $row['supervisor'];
        if($manager==1){
            $sql=mysql_query('select T.*,  U.username as CreatedByUsername, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName , TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id  and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and  T.Deleted=1 and  U.UserCompanyID="'.$company_id.'" and U.UserCompanyID=D.company_id and D.id=T.TaskDepartmentID '.$addquery.' order by T.TaskID DESC', $this->db)or die(mysql_error());
         }
        elseif ($manager==3 && $supervisor==1) {
        	$sql=mysql_query('select T.*,  U.username as CreatedByUsername, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName , TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.TaskStatus=2 and T.Deleted=1 and TaskCreatedByUserID="'.$user_id.'" '.$addquery.' order by T.TaskID DESC', $this->db)or die(mysql_error());
        }
        else{

        $sql=mysql_query('select T.*, U.username as CreatedByUsername, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName,  TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and  T.Deleted=1   and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")'.$addquery.' order by T.TaskID DESC', $this->db)or die(mysql_error());
         }
         
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                        $result[$i]['CreatedByUsername'] = $rlt['CreatedByUsername'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                        $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                        $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 1 for fix and 2 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 1 for routine and 2 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment'];
                        if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] =($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                        $i++;  // 1- pending approval, 2- Pending , 3 - In Progress ,4- Waiting for Completetion, 5 - Complete
                        
                        
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }


        private function daily_report_filter(){
               $user_id = $this->_request['user_id'];
               $department_id = $this->_request['department_id'];
               $employee_id = $this->_request['employee_id'];
               $from_date = $this->_request['from_date'];
               $to_date = $this->_request['to_date'];
              

            if(empty($user_id))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }

                $addquery='';
        if($department_id != 0 && $department_id != '') 
            { 
             $addquery.=' and R.department_id='.$department_id; 
            }
        if($employee_id != 0 && $employee_id!= '') 
            {
             $addquery.=' and R.user_id='.$employee_id; 
            }
       
        if($from_date != '') 
          { 
            $addquery.=' and DATE_FORMAT(R.created_at,"%Y-%m-%d")>="'.date('Y-m-d', strtotime($from_date)).'"'; 
          }
          if($to_date != '') 
          { 
            $addquery.=' and  DATE_FORMAT(R.created_at,"%Y-%m-%d")<="'.date('Y-m-d', strtotime($to_date)).'"'; 
          }
         $user= mysql_query("select * from users where id='".$user_id."'");
           $row = mysql_fetch_assoc($user);
           $manager = $row['is_manager'];
           $company_id = $row['UserCompanyID'];
           $supervisor = $row['supervisor'];
            
            if($manager==1){
            	$sql=mysql_query("select R.* , u.username as emp_name , D.department_name  from daily_reports R, users u , tbl_department D where u.id=R.user_id and D.id=R.department_id and  D.company_id='".$company_id."'".$addquery." group by  R.report_id", $this->db)or die(mysql_error());
            }elseif($manager==2){

              $sql=mysql_query("SELECT d.*,u.username as emp_name  ,dt.department_name FROM daily_reports d, tbl_department dt,users u WHERE d.user_id=u.id and  d.department_id=dt.id  and d.department_id IN (select DepartmentID from tbl_departmentemploy where EmployID='".$user_id."')".$addquery." order by d.report_id DESC ", $this->db)or die(mysql_error());
            }elseif($supervisor==1){
                $sql=mysql_query("select R.* , D.department_name, U.username as emp_name from users U, daily_reports R , tbl_department D , tbl_supervisor S where R.user_id=U.id and D.id=R.department_id  and  S.user_id=R.user_id and S.supervisor_id='".$user_id."' ".$addquery." order by R.report_id DESC", $this->db)or die(mysql_error());
            }else{
            	$sql=mysql_query("select R.* , D.department_name , U.username as emp_name  from users U, daily_reports R , tbl_department D where R.user_id=U.id and D.id=R.department_id and R.user_id='".$self_id."' ".$addquery." order by R.report_id DESC");
            }
            
          
             
           
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                        $result[$i] = $rlt;
                       
                        $i++;  
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }

         private function attendance_filter(){
            $department_id = $this->_request['department_id'];
            $employee_id = $this->_request['employee_id'];
            $from_date = $this->_request['from_date'];
            $to_date = $this->_request['to_date'];
            $user_id = $this->_request['user_id'];
              
                  if(empty($user_id))
                    {
                         
                        $result['status'] = '0';
                        $result['message']="Invalid data provided!";
                        $this->response($this->json($result), 200);
                    }

                $addquery='';
        if($department_id != 0 && $department_id != '') 
            { 
             $addquery.=' and A.department_id='.$department_id; 
            }
        if($employee_id != 0 && $employee_id!= '') 
            {
             $addquery.=' and A.user_id='.$employee_id; 
            }
       
        if($from_date != '') 
          { 
            $addquery.=' and DATE_FORMAT(A.signin_time,"%Y-%m-%d")>="'.date('Y-m-d', strtotime($from_date)).'"'; 
          }
          if($to_date != '') 
          { 
            $addquery.=' and  DATE_FORMAT(A.signin_time,"%Y-%m-%d")<="'.date('Y-m-d', strtotime($to_date)).'"'; 
          }
           $user= mysql_query("select * from users where id='".$user_id."'");
           $row = mysql_fetch_assoc($user);
           $manager = $row['is_manager'];
           $company_id = $row['UserCompanyID'];
           $supervisor = $row['supervisor'];
             
             if($manager==1){
				$sql=mysql_query("SELECT A.*,D.department_name,U.username from tbl_attendance A, tbl_department D, users U where A.user_id=U.id and A.department_id=D.id and D.company_id='".$company_id."' ".$addquery." order by A.attendance_id DESC",$this->db)or die(mysql_error());
             }elseif ($manager==2) {
             	$sql=mysql_query("SELECT A.*,D.department_name,U.username from tbl_attendance A, tbl_department D, users U where A.user_id=U.id and A.department_id=D.id  and A.department_id IN (select DepartmentID from tbl_departmentemploy where EmployID= '".$user_id."')".$addquery." order by A.attendance_id DESC ",$this->db)or die(mysql_error());
             }elseif ($manager==3 && $supervisor==1) {
             	$sql=mysql_query("SELECT A.*,D.department_name,U.username from tbl_attendance A, tbl_department D, users U tbl_supervisor S where A.user_id=U.id and A.department_id=D.id and S.user_id=A.user_id and S.supervisor_id='".$user_id."' ".$addquery." ",$this->db)or die(mysql_error());
             }else{
                 $sql=mysql_query("SELECT A.*,D.department_name,U.username from tbl_attendance A, tbl_department D, users U where A.user_id=U.id and A.department_id=D.id and A.user_id='".$user_id."'".$addquery." ",$this->db)or die(mysql_error());
             }
             
            
             
           
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                        $result[$i] = $rlt;
                       
                        $i++;  
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }
           
          
        }

       /* analetics api for admin*/
    
         function admin_analytics_counter(){
         	$result=array();
         	$company_id=$this->_request['company_id'];

            $department_id = $this->_request['department_id'];
            $employee_id = $this->_request['employee_id'];
            $head_id = $this->_request['head_id'];
            $activity_id = $this->_request['activity_id'];
         	if(empty($company_id)){
                 $result['status'] = '0';
                 $result['message']="Invalid data provided!";
                 $this->response($this->json($result), 200);
         	}
         	   $addquery='';
        if($department_id != 0 && $department_id != '') 
            { 
             $addquery.=' and T.TaskDepartmentID='.$department_id; 
            }
        if($employee_id != 0 && $employee_id!= '') 
            {
             $addquery.=' and T.TaskAssignToUserID='.$employee_id; 
            }
        if($head_id != 0 && $head_id != '') 
            { 
             $addquery.=' and T.TaskHeadID='.$head_id; 
            }
        if($activity_id != 0 && $activity_id!= '') 
            {
             $addquery.=' and T.TaskActivityID='.$activity_id; 
            }
      
           
         	 $count=mysql_query("SELECT
          (select count(T.TaskID)  from tbl_tasks T , tbl_department D where D.id=T.TaskDepartmentID and  D.company_id='".$company_id."' and  T.TaskStatus=1 and T.Deleted=1  '".$addquery."')as sendbacktask,
          (select count(T.TaskID)  from tbl_tasks T , tbl_department D where D.id=T.TaskDepartmentID and  D.company_id='".$company_id."' and  T.TaskStatus=2 and T.Deleted=1) as pending ,
          (select count(T.TaskID)  from tbl_tasks T , tbl_department D where D.id=T.TaskDepartmentID and  D.company_id='".$company_id."' and  T.TaskStatus=3 and T.Deleted=1)as In_progress ,
          (select count(T.TaskID)  from tbl_tasks T , tbl_department D where D.id=T.TaskDepartmentID and  D.company_id='".$company_id."' and  T.TaskStatus=4 and T.Deleted=1) as waiting_for_approval ,
          (select count(T.TaskID)  from tbl_tasks T , tbl_department D where D.id=T.TaskDepartmentID and  D.company_id='".$company_id."' and  T.TaskStatus=5 and T.Deleted=1) as completed ,
          (select COALESCE(ROUND( SUM( TIME_TO_SEC(TIMEDIFF(TaskSubmitStartTime, TaskStartTime))/3600) ),0 ) from tbl_department D,tbl_tasksubmits S LEFT JOIN tbl_tasks T ON T.TaskID=S.TaskSubmitTaskID where D.company_id='".$company_id."' and D.id=T.TaskDepartmentID )as TaskTotalTime ,
          (select COALESCE(ROUND( SUM( TIME_TO_SEC(TIMEDIFF(TaskSubmitStartTime, TaskStartTime))/3600) ),0)  from tbl_department D ,tbl_tasksubmits S LEFT JOIN tbl_tasks T ON T.TaskID=S.TaskSubmitTaskID where D.company_id='".$company_id."' and D.id=T.TaskDepartmentID and T.TaskStartDate >= DATE(NOW()) - INTERVAL 6 DAY )as 6daysTaskTotalTime  ,
          (select COALESCE(ROUND( SUM( TIME_TO_SEC(TIMEDIFF(TaskSubmitStartTime, TaskSubmitEndTime))/3600)),0) from tbl_department D ,tbl_tasksubmits S LEFT JOIN tbl_tasks T ON T.TaskID=S.TaskSubmitTaskID where  D.id=T.TaskDepartmentID and  D.company_id='".$company_id."')as TotalTimeTimesheet ,
           (select COALESCE(ROUND( SUM(TIME_TO_SEC(TIMEDIFF(TaskSubmitStartTime, TaskSubmitEndTime))/3600) ),0) from tbl_department D ,tbl_tasksubmits S LEFT JOIN tbl_tasks T ON T.TaskID=S.TaskSubmitTaskID where  D.id=T.TaskDepartmentID and  D.company_id='".$company_id."' and  T.TaskStartDate >= DATE(NOW()) - INTERVAL 6 DAY)as 6daystimesheet 
          ",$this->db);

             $result = array();
              $i=0;
            if(mysql_num_rows($count) == 0){

             $result['status'] ='0';
              $result['message']="Not found!";
              $this->response($this->json($result), 200);
             }
             else{
            $rlt = mysql_fetch_array($count, MYSQL_ASSOC);
            $rlt['sendback']='Send Back Tasks';
            $rlt['pendingtask']='Pending Tasks';
            $rlt['Inprogress']='In Progress Tasks';
            $rlt['Waitingapproval']='Waiting For Approval Tasks';
            $rlt['completedtask']='Completed Tasks';
            $rlt['tasktime']='Task Total Time';
            $rlt['Weektasktime']='Last 6 Days Tasks';
            $rlt['timesheet_time']='Timesheet Total Time';
            $rlt['Weektimesheet']='Last 6 Days Timesheets';
            
           
            $this->response($this->json(array('status'=>'1','message'=>'Data found','data'=>$rlt)), 200);
        }
    

         }
/*add supoort api in user site*/
 private function add_user_support(){
            $result = array();
            $user_id=$this->_request['user_id'];
            $fullname=$this->_request['fullname'];
            $email=$this->_request['email'];
            $mobile=$this->_request['mobile'];
            $description=addslashes($this->_request['description']);
            
             if(empty($user_id) || empty($fullname)|| empty($email))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }

            $sql = mysql_query("INSERT INTO `tbl_support_users`(`user_id`, `full_name`,`email`,`mobile`,`description`,`created_at`) VALUES ('".$user_id."','".$fullname."','".$email."','".$mobile."','".$description."','".date('Y-m-d H:i:s')."')", $this->db);
            
                $result['status'] = '1';
                $result['message']="added";
                $this->response($this->json($result), 200);
           

        }

        /* add coffee corner code by megha*/
        private function add_coffeecorner(){
            $result = array();
            $user_id=$this->_request['user_id'];
            $title=addslashes($this->_request['title']);
            $datetime=date('Y-m-d G:i:s');
            $description=addslashes($this->_request['description']);
            $path = "../uploads/";
            $valid_formats = array("jpg","png","gif","bmp","jpeg","doc","docx","pdf");

              $name = $_FILES['coffeecornerimage']['name'];
              $size = $_FILES['coffeecornerimage']['size'];
              if(strlen($name)) {
                list($txt, $ext) = explode(".", $name);
                if(in_array($ext,$valid_formats)) {
                  if($size<(1024*1024)) {
                    $image_name = time().$id.".".$ext;
                    $tmp = $_FILES['coffeecornerimage']['tmp_name'];
                    move_uploaded_file($tmp, $path.$image_name);
                      
              }
             }
            }
          
            
             if(empty($user_id) || empty($title)|| empty($description))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }

            $sql = mysql_query("INSERT INTO `tbl_coffecorner`(`CoffeCornerUserID`,`CoffeCornerTitle`,`CoffeeCornerDescription`,`CoffeCornerStatus`,`CoffeCornerCreatedDate`,`image`) VALUES ('".$user_id."','".$title."','".$description."','1','".$datetime."','".$image_name."')", $this->db);
            
                $result['status'] = '1';
                $result['message']="added";
                $this->response($this->json($result), 200);
           

        }
 /* add bulletin Board code by megha*/
        private function add_bulletinboard(){
            $result = array();
            $user_id=$this->_request['user_id'];
            $title=addslashes($this->_request['title']);
            $datetime=date('Y-m-d G:i:s');
            $description=addslashes($this->_request['description']);
            $path = "../uploads/";
            $valid_formats = array("jpg","png","gif","bmp","jpeg","doc","docx","pdf");

              $name = $_FILES['bulletinboardimage']['name'];
              $size = $_FILES['bulletinboardimage']['size'];
              if(strlen($name)) {
                list($txt, $ext) = explode(".", $name);
                if(in_array($ext,$valid_formats)) {
                  if($size<(1024*1024)) {
                    $image_name = time().$id.".".$ext;
                    $tmp = $_FILES['bulletinboardimage']['tmp_name'];
                    move_uploaded_file($tmp, $path.$image_name);
                      
              }
             }
            }
          
            
             if(empty($user_id) || empty($title)|| empty($description))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }

            $sql = mysql_query("INSERT INTO `tbl_bulletinboard`(`BulletinUserID`,`BulletinTitle`,`BulletinDescription`,`BulletinStatus`,`image`,`BulletinCreatedDate`) VALUES ('".$user_id."','".$title."','".$description."','1','".$image_name."','".$datetime."')", $this->db);
            
                $result['status'] = '1';
                $result['message']="added";
                $this->response($this->json($result), 200);
           

        }

 /* Edit bulletin Board code by megha*/
        private function edit_bulletinboard(){
            $result = array();
            $bulletinboard_id=$this->_request['bulletinboard_id'];
            $title=addslashes($this->_request['title']);
            $datetime=date('Y-m-d G:i:s');
            $description=addslashes($this->_request['description']);
            $path = "../uploads/";
            $valid_formats = array("jpg","png","gif","bmp","jpeg","doc","docx","pdf");

              $name = $_FILES['bulletinboardimage']['name'];
              $size = $_FILES['bulletinboardimage']['size'];
              if(strlen($name)) {
                list($txt, $ext) = explode(".", $name);
                if(in_array($ext,$valid_formats)) {
                  if($size<(1024*1024)) {
                    $image_name = time().$id.".".$ext;
                    $tmp = $_FILES['bulletinboardimage']['tmp_name'];
                    move_uploaded_file($tmp, $path.$image_name);
                      
              }
             }
            }
          
            
             if(empty($bulletinboard_id) || empty($title)|| empty($description))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
             if($image_name!=""){
                $sql = mysql_query("update tbl_bulletinboard set BulletinTitle='".$title."',BulletinDescription='".$description."',image='".$image_name."',BulletinCreatedDate='".$datetime."' where BulletinID='".$bulletinboard_id."'", $this->db)or die(mysql_error());
             }
            $sql = mysql_query("update tbl_bulletinboard set BulletinTitle='".$title."',BulletinDescription='".$description."',BulletinCreatedDate='".$datetime."' where BulletinID='".$bulletinboard_id."'", $this->db)or die(mysql_error());
            
            
                $result['status'] = '1';
                $result['message']="Updated";
                $this->response($this->json($result), 200);
           

        }
/* Edit coffeecorner code by megha*/
        private function edit_coffeecorner(){
            $result = array();
            $coffeecorner_id=$this->_request['coffeecorner_id'];
            $title=addslashes($this->_request['title']);
            $datetime=date('Y-m-d G:i:s');
            $description=addslashes($this->_request['description']);
            $path = "../uploads/";
            $valid_formats = array("jpg","png","gif","bmp","jpeg","doc","docx","pdf");

              $name = $_FILES['coffeecornerimage']['name'];
              $size = $_FILES['coffeecornerimage']['size'];
              if(strlen($name)) {
                list($txt, $ext) = explode(".", $name);
                if(in_array($ext,$valid_formats)) {
                  if($size<(1024*1024)) {
                    $image_name = time().$id.".".$ext;
                    $tmp = $_FILES['coffeecornerimage']['tmp_name'];
                    move_uploaded_file($tmp, $path.$image_name);
                      
              }
             }
            }
          
            
             if(empty($coffeecorner_id) || empty($title)|| empty($description))
            {
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            $sql = mysql_query("update tbl_coffecorner set CoffeCornerTitle='".$title."',CoffeeCornerDescription='".$description."',CoffeCornerCreatedDate='".$datetime."', image='".$image_name."' where CoffeeCornerID='".$coffeecorner_id."'", $this->db)or die(mysql_error());
            
            
                $result['status'] = '1';
                $result['message']="Updated";
                $this->response($this->json($result), 200);
           

        }

        /* admin api */
           private function get_task_list_admin(){
               $company_id = $this->_request['company_id'];
               $status = $this->_request['status'];
  
            if(empty($company_id))
            {
                
                $result['status'] = '0';
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
            if($status!=""){
             $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName , TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskStatus="'.$status.'" and T.TaskAssignToUserID=UA.id  and  T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and  T.Deleted=1 and  U.UserCompanyID="'.$company_id.'" and U.UserCompanyID=D.company_id and D.id=T.TaskDepartmentID  order by T.TaskID DESC', $this->db)or die(mysql_error());
            }
            else{
            $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName , TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id  and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and  T.Deleted=1 and  U.UserCompanyID="'.$company_id.'" and U.UserCompanyID=D.company_id and D.id=T.TaskDepartmentID  order by T.TaskID DESC', $this->db)or die(mysql_error());
        }
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                         $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                         $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 0 for fix and 1 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 0 for routine and 1 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment'];
                        if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] =($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                        $i++;  // 1 - pending approval, 2- Pending , 3 - In Progress ,4 - Waiting for Completetion, 5 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }

        /*today task all section*/

           private function get_today_task_list(){
               $user_id = $this->_request['user_id'];
               $self_id = $this->_request['self_id'];
               $todaydate=date('Y-m-d');
                    $user= mysql_query("select * from users where id='".$user_id."'");
                     $row = mysql_fetch_assoc($user);
                     $manager = $row['is_manager'];
                     $company_id = $row['UserCompanyID'];
                     $supervisor = $row['supervisor'];
                  
            if($manager==1){
             $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName , TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id  and  T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and  T.Deleted=1 and  U.UserCompanyID="'.$company_id.'" and U.UserCompanyID=D.company_id and D.id=T.TaskDepartmentID and T.TaskStartDate="'.$todaydate.'" order by T.TaskID DESC', $this->db)or die(mysql_error());
            }elseif($manager==2){
                 $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName,  TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and  T.Deleted=1   and T.TaskStartDate="'.$todaydate.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")  order by T.TaskID DESC', $this->db)or die(mysql_error());
            }elseif($manager==3 && $supervisor==1){
              $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName , TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th  where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and T.Deleted=1 and T.TaskCreatedByUserID="'.$user_id.'" and   T.TaskStartDate="'.$todaydate.'" order by T.TaskID DESC', $this->db)or die(mysql_error()); 
            }
            else{
            $sql=mysql_query('select T.*, U.username, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName,  TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and  T.Deleted=1   and T.TaskStartDate="'.$todaydate.'"  and T.TaskAssignToUserID="'.$self_id.'"  order by T.TaskID DESC', $this->db)or die(mysql_error());
        }
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]['CreatedByUsername'] = $rlt['username'];
                        $result[$i]['AssignToUsername'] = $rlt['AssignUserName'];
                        $result[$i]['TaskID'] = $rlt['TaskID'];
                        $result[$i]['TaskDepartmentID'] = $rlt['TaskDepartmentID'];
                        $result[$i]['DepartmentName'] = $rlt['department_name'];
                        $result[$i]['TaskHeadID'] = $rlt['TaskHeadID'];
                         $result[$i]['TaskHeadName'] = $rlt['HeadName'];
                        $result[$i]['TaskActivityID'] = $rlt['TaskActivityID'];
                         $result[$i]['TaskActivityName'] = $rlt['ActivityName'];
                        $result[$i]['TaskName'] = stripslashes($rlt['TaskName']);
                        $result[$i]['TaskType'] = $rlt['TaskType'];  // 0 for fix and 1 for flexy
                        $result[$i]['TaskCategory'] = $rlt['TaskCategory'];  // 0 for routine and 1 for nonroutine
                        $result[$i]['TaskCreatedByUserID'] = $rlt['TaskCreatedByUserID'];
                        $result[$i]['TaskAssignToUserID'] = $rlt['TaskAssignToUserID'];
                        $result[$i]['TaskStartDate'] = $rlt['TaskStartDate'];
                        $result[$i]['TaskEndDate'] = $rlt['TaskEndDate'];
                        $result[$i]['TaskStartTime'] = $rlt['TaskStartTime'];
                        $result[$i]['TaskEndTime'] = $rlt['TaskEndTime'];
                        $result[$i]['TaskDescription'] = stripslashes($rlt['TaskDescription']);
                        $result[$i]['TaskEmployeeRemark'] = stripslashes($rlt['TaskEmployeeRemark']);
                        $result[$i]['TaskManagerRemark'] = stripslashes($rlt['TaskManagerRemark']);
                        $result[$i]['TaskTimeAlloted'] = $rlt['TaskTimeAlloted'];
                        $result[$i]['TaskCreatedAt'] = $rlt['TaskCreatedAt'];
                        $result[$i]['TaskStatus'] = $rlt['TaskStatus'];
                        $result[$i]['Priority'] = $rlt['priority'];
                        $result[$i]['TaskAttachment'] = $rlt['TaskAttachment'];
                        if($rlt['daystotal']==0){
                            $result[$i]['Durring'] = $rlt['timediffrence']+0;
                        }else{
                       $result[$i]['Durring'] =($rlt['starttime']+$rlt['endtime']+($rlt['daystotal']*8)-8);
                       }
                        $i++;  // 1 - pending approval, 2- Pending , 3 - In Progress ,4 - Waiting for Completetion, 5 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }

        /* today task user */
          private function get_todaytask_username_list(){
               $user_id = $this->_request['user_id'];
               $self_id = $this->_request['self_id'];
               $todaydate=date('Y-m-d');
                    $user= mysql_query("select * from users where id='".$user_id."'");
                     $row = mysql_fetch_assoc($user);
                     $manager = $row['is_manager'];
                     $company_id = $row['UserCompanyID'];
                     $supervisor = $row['supervisor'];
                  
            if($manager==1){
             $sql=mysql_query('select UA.id as user_id, UA.profile_pic, UA.username as AssignUserName,UA.supervisor ,D.department_name, (select Count(*) from tbl_tasks  where TaskAssignToUserID=T.TaskAssignToUserID and TaskStartDate="'.$todaydate.'") as task_counter from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id  and  T.TaskDepartmentID=D.id  and  T.Deleted=1 and  U.UserCompanyID="'.$company_id.'" and U.UserCompanyID=D.company_id and D.id=T.TaskDepartmentID and T.TaskStartDate="'.$todaydate.'" GROUP by T.TaskAssignToUserID order by T.TaskID DESC', $this->db)or die(mysql_error());
            }elseif($manager==2){
                 $sql=mysql_query('select UA.id as user_id,UA.profile_pic ,UA.username as AssignUserName,UA.supervisor , D.department_name, (select Count(*) from tbl_tasks  where TaskAssignToUserID=T.TaskAssignToUserID and TaskStartDate="'.$todaydate.'") as task_counter from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id  and  T.Deleted=1   and T.TaskStartDate="'.$todaydate.'" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID="'.$user_id.'")   GROUP by T.TaskAssignToUserID order by T.TaskID DESC', $this->db)or die(mysql_error());
            }elseif($manager==3 && $supervisor==1){
              $sql=mysql_query('select UA.id as user_id,UA.profile_pic, UA.username as AssignUserName, UA.supervisor , D.department_name, (select Count(*) from tbl_tasks  where TaskAssignToUserID=T.TaskAssignToUserID and TaskStartDate="'.$todaydate.'") as task_counter from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th , tbl_supervisor s where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.Deleted=1 and T.TaskCreatedByUserID=s.supervisor_id and s.supervisor_id="'.$user_id.'" and T.TaskStartDate="'.$todaydate.'" GROUP by T.TaskAssignToUserID order by T.TaskID DESC', $this->db)or die(mysql_error()); 
            }
            else{
            $sql=mysql_query('select UA.id as user_id, UA.profile_pic, UA.username as AssignUserName,D.department_name, UA.supervisor ,(select Count(*) from tbl_tasks  where TaskAssignToUserID=T.TaskAssignToUserID and TaskStartDate="'.$todaydate.'") as task_counter from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and  T.Deleted=1   and T.TaskStartDate="'.$todaydate.'"  and T.TaskAssignToUserID="'.$self_id.'"   GROUP by T.TaskAssignToUserID order by T.TaskID DESC', $this->db)or die(mysql_error());
        }
            $result = array();
            $i=0;
            if(mysql_num_rows($sql) == 0){
             
              $result['status'] = '0';
              $result['message']="Not found";
              $this->response($this->json($result), 200);
            }
            else
            {
                while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                
                 $result[$i]=$rlt;
                       
                        $i++;  // 1 - pending approval, 2- Pending , 3 - In Progress ,4 - Waiting for Completetion, 5 - Complete
                }
                $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
            }


        }
        /* add like coffe corner*/
        private function coffeecorner_like_dislike(){
            $result = array();
            $user_id=$this->_request['user_id'];
            $coffeecorner_id=$this->_request['coffeecorner_id'];
            $datetime=date('Y-m-d G:i:s');
            $dislilke_id=$this->_request['dislilke_id'];
            
           $sel=mysql_query("select * from tbl_coffeecorner_like where user_id='".$user_id."' and coffeecorner_id='".$coffeecorner_id."'",$this->db);
            if(mysql_num_rows($sel) > 0)
            {
		            
		             $sql = mysql_query("UPDATE tbl_coffeecorner_like SET Deleted=1 ,like_dislike='".$dislilke_id."' where coffeecorner_id='".$coffeecorner_id."' and  user_id='".$user_id."'", $this->db);
		               $result['status'] = '1';
		                $result['message']="submit";
		                $this->response($this->json($result), 200);
		      
            }
           else{


           	 $sql = mysql_query("INSERT INTO `tbl_coffeecorner_like`(`coffeecorner_id`,`user_id`,`created_at`,`like_dislike`) VALUES ('".$coffeecorner_id."','".$user_id."','".$datetime."','1')", $this->db);
           	   $result['status'] = '1';
                $result['message']="submit";
                $this->response($this->json($result), 200);
           }
            

        }

 public function get_timesheet_list_admin()
    {
       $result=array();
       $company_id=$this->_request['company_id'];
       $status=$this->_request['status'];

           if(empty($company_id)){
               $result['status'] = '0';
               $result['message']="Invalid data provided!";
               $this->response($this->json($result), 200);
           }
           if($status!=""){
                $sql=mysql_query("select t.*,u.username,u.id from tbl_timesheet t, users u where u.id=t.submit_user_id and t.timesheet_status='".$status."'  and u.UserCompanyID='".$company_id."' order by t.submit_date DESC")or die(mysql_error());
           }
           else{
           $sql=mysql_query("select t.*,u.username,u.id from tbl_timesheet t, users u where u.id=t.submit_user_id and u.UserCompanyID='".$company_id."' order by t.submit_date DESC")or die(mysql_error());
           }
          
                    $result = array();
                    $i=0;
                    if(mysql_num_rows($sql) == 0)
                    {
                      $result['status'] = '0';
                      $result['message']="Not found";
                      $this->response($this->json($result), 200);
                    }
                    else
                    {
                        while ($rlt = mysql_fetch_array($sql, MYSQL_ASSOC))
                      {
                          $result[$i]=$rlt;
                           $i++;
                     } 
                   $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
               }
    }     
       /* admin end */
       
       /*get user role*/
        public function get_user_role()
    {
           $result=array();
           $user_id=$this->_request['user_id'];

           if(empty($user_id)){
               $result['status'] = '0';
               $result['message']="Invalid data provided!";
               $this->response($this->json($result), 200);
           }
           
            $sql=mysql_query("select R.*, U.supervisor from tbl_user_role R, users U where R.RoleuserID=U.UserType and UserID='".$user_id."'")or die(mysql_error());
                $result = array();
                $i=0;
                    if(mysql_num_rows($sql) == 0)
                    {
                      $result['status'] = '0';
                      $result['message']="Not found";
                      $this->response($this->json($result), 200);
                    }
                    else
                    {
                       $rlt = mysql_fetch_array($sql, MYSQL_ASSOC);
                      
                          $result[$i]=$rlt;
                        
                   $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
               }
    }   
    /* mutiple activity update*/
     public function multiple_activity_update()
    {  
        $result = array();
       
        $activity_id =explode(',',$this->_request['activity_id']);
        $head_id=$this->_request['head_id'];
        $activity_name=$this->_request['activity_name'];
        $description=$this->_request['description'];
        $activity_list=json_decode($activity_name,true);
        $description_list=json_decode($description,true);
          
            if(empty($activity_id))
            {
                 $result['status'] = "0";
                $result['message']="Invalid data provided!";
                $this->response($this->json($result), 200);
            }
             
               $count=count($activity_id);
               for ($i=0;$i<$count;$i++) {
/*".$activity_list[$i]['Activity']."','".$description_list[$i]['Description']."'*/
                  $sql=mysql_query("UPDATE  tbl_activities set  ActivityHeadID='".$head_id."', ActivityName='".$activity_list[$i]['Activity']."', ActivityDescription='".$description_list[$i]['Description']."' where ActivityID='".$activity_id[$i]."'", $this->db) or die(mysql_error());
                 
               }
                $result['message'] = "Updated";
                $result['status'] = '1';
                $this->response($this->json($result), 200);
               
            
          
    }

public function filter()
    {
          
 
   $Date1 = '01-10-2010'; 
$Date2 = '05-10-2010'; 
  
// Declare an empty array 
$array = array(); 
  
// Use strtotime function 
$Variable1 = strtotime($Date1); 
$Variable2 = strtotime($Date2); 
  
// Use for loop to store dates into array    
/* for ($i=0;$i<count($emparr);$i++) {
                     $arr['emp_id'] = $emparr[$i];

                     $insert = $this->db->insert('employee_task',$arr);*/
// 86400 sec = 24 hrs = 60*60*24 = 1 day 
$a=1; 
for ($i = $Variable1; $i <= $Variable2;  
                                $i += (86400)) { 
                                      
$Store = date('Y-m-d', $i); 
$array[$a] = $Store;
$sql=mysql_query("INSERT into tbl_test (start,end_date) values ('".$array[$a]."','".$array[$a]."')");
 $a++;
 
} 
   
 print_r($array ); 

 }   


/* coffee corner submit comments*/
public function submit_comment_coffeecorner()
                    {  
                        $result = array();
                        $user_id=$this->_request['user_id'];
                        $comment_text=addslashes($this->_request['comment_text']);
                        $coffeecorner_id=$this->_request['coffeecorner_id'];
                        $is_created=date('Y-m-d G:i:s');
                        $Deleted=1;
                     
                      
                        if(empty($user_id)|| empty($coffeecorner_id))
                        {
                          
                             $result['status'] = '0';
                            $result['message']="Invalid data provided!";
                            $this->response($this->json($result), 200);
                        }
                       
                        else
                        {

                           $sql=mysql_query("INSERT INTO `tbl_coffeecorner_comments` (`coffeecorner_id`,`user_id`,`comment_text`,`created_at`,`Deleted`) VALUES  ('".$coffeecorner_id."','".$user_id."','".$comment_text."','".$is_created."','".$Deleted."')",$this->db);
                             // print_r($sql);

                            $result['id']= mysql_insert_id();
                           
                            $result['message'] = "Comment submited";
                            $result['status'] ='1';
                            $this->response($this->json($result), 200);
                       }
        }
        
  public function get_comments_coffeecorner()
    {
           $result=array();
           $coffeecorner_id=$this->_request['coffeecorner_id'];

           if(empty($coffeecorner_id)){
               $result['status'] = '0';
               $result['message']="Invalid data provided!";
               $this->response($this->json($result), 200);
           }
       
            $sql=mysql_query("select C.*, U.username, U.profile_pic from tbl_coffeecorner_comments C, users U where C.user_id=U.id and  C.coffeecorner_id='".$coffeecorner_id."'")or die(mysql_error());
                $result = array();
                $i=0;
                    if(mysql_num_rows($sql) == 0)
                    {
                      $result['status'] = '0';
                      $result['message']="Not found";
                      $this->response($this->json($result), 200);
                    }
                    else
                    {
                       while($rlt = mysql_fetch_array($sql, MYSQL_ASSOC)){
                      
                          $result[$i]=$rlt;
                          $i++;
                        }
                   $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
               }
    }   

public function get_company_profile()
    {
           $result=array();
           $company_id=$this->_request['company_id'];

           if(empty($company_id)){
               $result['status'] = '0';
               $result['message']="Invalid data provided!";
               $this->response($this->json($result), 200);
           }
       
            $sql=mysql_query("select *  from tbl_companies where CompanyID='".$company_id."'")or die(mysql_error());
                $result = array();
                $i=0;
                    if(mysql_num_rows($sql) == 0)
                    {
                      $result['status'] = '0';
                      $result['message']="Not found";
                      $this->response($this->json($result), 200);
                    }
                    else
                    {
                      $rlt = mysql_fetch_array($sql, MYSQL_ASSOC);
                      
                         $result[$i]= $rlt;
                   $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
               }
    }   

private function update_company_profile(){
            $result = array();
            $company_id=$this->_request['company_id'];
            $company_name=$this->_request['company_name'];
            $email=$this->_request['email'];
            $phone=$this->_request['phone'];
            $start_day=$this->_request['start_day'];
            $end_day=$this->_request['end_day'];
            $end_time=$this->_request['end_time'];
            $start_time=$this->_request['start_time'];
            $address=$this->_request['address'];
           
            if(empty($company_id)){
               $result['status'] = '0';
               $result['message']="Invalid data provided!";
               $this->response($this->json($result), 200);
           }
       
          
		      $sql = mysql_query("UPDATE tbl_companies SET CompanyFullname='".$company_name."' ,CompanyEmail='".$email."',CompanyPhone='".$phone."',CompanyAddress='".$address."',Start_day='".$start_day."',End_day='".$end_day."',Start_time='".$start_time."',End_time='".$end_time."' where CompanyID='".$company_id."'", $this->db);
		               $result['status'] = '1';
		               $result['message']="submit";
		               $this->response($this->json($result), 200);
		      
          
           	   $result['status'] = '1';
                $result['message']="Updated";
                $this->response($this->json($result), 200);
           }

 

            
            

           /* head and activity duble aaray get code by megha*/

     public function get_head_activity(){

      $result = array();
      $company_id=$this->_request['company_id'];
      if(empty($company_id))
            {
               
                $result['status'] ='0';
                $result['message']="head id required";
                $this->response($this->json($result), 200);
            }
             $faq_cat=mysql_query("SELECT * FROM tbl_heads where HeadCompanyID='$company_id' ", $this->db)or die(mysql_error());
            
            $i=0;
            if(mysql_num_rows($faq_cat) == 0){
              
              $result['status'] ='0';
              $result['message']="No record found!";
              $this->response($this->json($result), 200);
            }
          
                else
                {  
               while ($row = mysql_fetch_assoc($faq_cat)) {
                  
                $result[$i] = $row;

           $faq_cat1 = mysql_query("select * FROM tbl_activities where ActivityHeadID = '".$row['HeadID']."'");
               while ($result_faq = mysql_fetch_assoc($faq_cat1)) {

                       $result[$i]['activities'][] = $result_faq;
                      
           }
    
            $i++;

       } 
       }  
           
        $this->response($this->json(array('message'=>'data found','status'=>'1','data'=>$result)), 200);
    }
  

       

 public function send_gcm($reg_token,$name,$title,$message)
 {  

   $api_key = 'AAAAFPAIPAA:APA91bERwCgnGumDv1WGVIyfUsy3HJnZWF8A-rJWmCaE9tR07G5cIzO66YQunj-oGXlfifQ2loofxJgpT5xqHKVTKi4olqqG0sw0afEjHUbcAfsYOF4XKa_NNDJK0gy7q4oYBhZIkOsO'; 
   
    $api_key = $api_key; 
   $reg_token = array($reg_token);  
   $message = $message;
   $title = $title; 
   
   $msg = array
   (
    'message'  => $message,
    'title'  => $title,
    'vibrate' => "1",
    'sound'  => 1,
    'largeIcon' => 'large_icon',
    'smallIcon' => 'small_icon'
   );


   $fields = array
   (
    'registration_ids'  => $reg_token,
    'data'   => $msg
   );

  

   $headers = array
   (
    'Authorization: key=' . $api_key,
    'Content-Type: application/json'
   ); 

      $ch = curl_init();
   curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
   curl_setopt( $ch,CURLOPT_POST, true );
   curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
   curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
   curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
   curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode($fields) );
   $result = curl_exec($ch);
    curl_close($ch);

  

     $res = json_decode($result);

    $flag = $res->success;
    if($flag == 1){

    return true;
   
 }else{

    return false;

   }
 }




 public function send_fcm($reg_token,$name,$title,$message){

 
    $api_key = 'AAAAbxt3_qI:APA91bESLWeZUIGMnEhDfobac0CAsBhxg3C70YtFzmc8zaoOG6ufnl2CuDvY6Dw4HiJdXD_yOgZOWGaHzNgIiKVfc7Yslmd0M5FVxErozcWCf1umrKDrfNjCktmIXrjs0f5-oJ-FFFnX';

    $api_key = $api_key; 
    $reg_token = array($reg_token);   
    $message = $message;
    $title = $title; 

    
   
   $msg = array
   (
    'body'  => $message,
    'title'  => $title,
    'vibrate' => "1",
    'sound'  => 1,
    'largeIcon' => 'large_icon',
    'smallIcon' => 'small_icon'
   );
   ///print_r($msg);

   $fields = array
   (
    'registration_ids'  => $reg_token,
    'notification'   => $msg
   
   );

   $headers = array
   (
    'Authorization: key=' .$api_key,
    'Content-Type: application/json'
   ); 

   $ch = curl_init();
   curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
   curl_setopt( $ch,CURLOPT_POST, true );
   curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
   curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
   curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
   curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode($fields));
   $result = curl_exec($ch);
   curl_close($ch);

 

   $res = json_decode($result);
//print_r($res);
   $flag = $res->success;
   
    if($flag == 1){

     return true;
    }else{
      return false;
    }
 }

        /* 
         *  Encode array into JSON
        */
        private function json($data){
            if(is_array($data)){
                return json_encode($data, true);
            }
        }
    }
    // Initiiate Library
    
    $api = new API;
    $api->processApi();
?> 
<!-- select T.*, U.username as CreatedByUsername, UA.username as AssignUserName, D.department_name,ta.ActivityName,th.HeadName,  TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as timediffrence, TIMEDIFF("19:00:00",  DATE_FORMAT(T.TaskStartTime,"%H:%i:%s A")) as starttime ,TIMEDIFF(DATE_FORMAT(T.TaskEndTime,"%H:%i:%s A"),"10:00:00" ) as endtime , DATEDIFF(T.TaskEndDate, T.TaskStartDate) as daystotal from tbl_tasks T, users U, users UA, tbl_department D , tbl_activities ta, tbl_heads th where T.TaskCreatedByUserID=U.id and T.TaskAssignToUserID=UA.id and T.TaskDepartmentID=D.id and T.TaskActivityID=ta.ActivityID and T.TaskHeadID=th.HeadID and  T.Deleted=1 and TaskStartDate >= "2019-06-01" OR TaskStartDate <= "2019-07-01"  and TaskCreatedByUserID="102" and T.TaskDepartmentID IN (select DepartmentID from tbl_departmentemploy where EmployID=102)  order by T.TaskStartDate DESC -->