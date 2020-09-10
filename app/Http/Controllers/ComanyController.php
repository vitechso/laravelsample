<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

// use Request;

use App\Products;

use App\Pacttemplate;
use App\Assign_user;

use App\Pact_temp_section;

use App\Models\Auth\User\User;

use App\Category;


use Ramsey\Uuid\Uuid;

use Validator;

use Auth;
// require_once __DIR__.'/../../../vendor/autoload.php';
// // use Twilio\Rest\Client;
include('vendor/autoload.php');
use Twilio\Rest\Client;


class ComanyController extends Controller

{

    public function __construct()
    {

        $this->middleware('auth');

    }

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()
    {
        
        // echo '/public_html/phonepact/vendor/autoload.php';
        // echo 'asdasdasd'; 
        // $sid    = "AC448e1de7c35c623674d09229fcc093ad";
        // $token  = "7b60ab091ef0e12d006498931ab07250";
        // $twilio = new Client($sid, $token); 

        // $message = $twilio->messages
        //                   ->create("+919685646285", // to
        //                             array(
        //                                "body" => "This is the ship that made the Kessel Run in fourteen parsecs?",
        //                                "from" => "+14692084512",
        //                                "mediaUrl" => array("https://c1.staticflickr.com/3/2899/14341091933_1e92e62d12_b.jpg")
        //                             )
        //                     );

        // print($message->sid);
        // exit;
        // echo auth()->user()->hasRole('admin');exit;
        $users = User::where(['created_by'=>auth()->user()->id])->with('roles')->orderBy('id', 'desc')->get();
        //SELECT pacttemplates.*,pact_temp_section.section_title,pact_temp_section.clause_body,pact_temp_section.clause_heading FROM `pacttemplates` JOIN pact_temp_section ON pacttemplates.id=pact_temp_section.pacttemp_id where pact_temp_section.clause_heading is not null and pact_temp_section.clause_heading != 'null' ORDER BY `pacttemplates`.`id`  DESC
        $pacttemplate = Pacttemplate::where(['created_by'=>auth()->user()->id])->orderBy('id', 'desc')->get();
        return view('admin.users', ['users' => $users,'pacttemplate'=>$pacttemplate,'created_by'=>auth()->user()->id]);

        // return view('admin.dashboard');

    }

    function checkphonenumber(Request $request){
        $userphone = User::select(['phone_number'])->where(['phone_number'=>$request->get('phone_number'),'created_by'=>auth()->user()->id])->first();
        if(isset($userphone) && $userphone->phone_number!=''){
            return $userphone->phone_number;
        }else{
            return 1;
        }
        //return auth()->user()->id;
    }


    public function pactshowlist(Request $request)
    {

        $assign_users = Assign_user::select(['assign_users.*','u1.name as signee_name','u2.name','pacttemplates.title','signees_clauses.sig_name'])->join('users as u1','assign_users.user_id','=','u1.id')->join('users as u2','assign_users.admin_id','=','u2.id')->join('signees_clauses','assign_users.id','=','signees_clauses.assign_tbid','left')->join('pacttemplates','assign_users.pact_id','=','pacttemplates.id')->where(['user_id'=>$request->get('user_id'),'admin_id'=>$request->get('admin_id')])->orderBy('assign_users.id', 'desc')->get();
        $assign_users_arr = array();
        foreach($assign_users as $k => $val){
            // $assign_users_arr[$k] = $val;
            $assign_users_arr[$k]['id'] = $val->id;
            $assign_users_arr[$k]['user_id'] = $val->user_id;
            $assign_users_arr[$k]['pact_id'] = $val->pact_id;
            $assign_users_arr[$k]['user_id'] = $val->user_id;
            $assign_users_arr[$k]['admin_id'] = $val->admin_id;
            $assign_users_arr[$k]['schedule_time'] = $val->schedule_time;
            $assign_users_arr[$k]['signee_name'] = $val->signee_name;
            $assign_users_arr[$k]['name'] = $val->name;
            $assign_users_arr[$k]['title'] = $val->title;
            $assign_users_arr[$k]['sig_name'] = $val->sig_name;
            $assign_users_arr[$k]['created_at'] = date('m-d-Y H:i:s',strtotime($val->created_at));
        }
        // print_r($assign_users_arr);
        // exit;
        // $pact_temp_detail = Pact_temp_section::select(['pact_temp_section.*','pacttemplates.title'])->join('pacttemplates','pacttemplates.id','=','pact_temp_section.pacttemp_id')->where(['pacttemp_id'=>$assign_users->pact_id])->groupBy('pacttemp_id')->first();
        return $assign_users_arr;
        //     'signees_id'    =>  $request->get('signees_id'),
        //     'assign_tbid'    =>  $request->get('assign_tbid'),
        //     'sig_name'    =>  $savefilename
        // ]);
    }


    public function admin_signees()

    {
        $users = User::where(['created_by'=>auth()->user()->id])->with('roles')->sortable(['email' => 'desc'])->paginate();
        // print_r($users); exit;
        // echo auth()->user()->id;exit;
        return view('admin.company_adminuser', ['users' => $users]);

        // return view('admin.dashboard');
    }

    public function addstore(Request $request)
    {
        // echo 'users.store';exit;
        \Session::flash('message',$request->get('role'));
        
        $this->validate($request, [

            'name'    =>  'required|max:255',
            'email' => 'required|email|max:255',
            'phone_number'    =>  'required'
        ]);

        $userphone = User::select(['phone_number'])->where(['phone_number'=>$request->get('phone_number'),'created_by'=>auth()->user()->id])->first();


        if(isset($userphone) && $userphone->phone_number!=''){
            
            return redirect()->back()->withFlashDanger('Phone number already use');

        }
        $name = $request->old('name');
        $email = $request->old('email');
        $phone_number = $request->old('phone_number');
        $pin = $request->old('pin');

        // $role = $request->old('role');
        // if()
        // return redirect()->route('company.add')->withFlashSuccess($request->get('role'));
        $user = User::create([

            'name'    =>  $request->get('name'),
            'email'    =>  $request->get('email'),
            'phone_number'    =>  $request->get('phone_number'),
            'pin' => $request->get('pin'),
            'created_by' => auth()->user()->id,
            // 'password' => (isset($request->get('password'))) ? bcrypt($request->get('password')) : 123123,
            'password' => ($request->get('password')!=null) ? bcrypt($request->get('password')) : 123123,
            'confirmation_code' => Uuid::uuid4(),
            'confirmed' => true

        ]);
        // $category->save();
        $user->roles()->attach($request->get('role'));
        // print_r($user); exit;
        // return redirect()->route('admin.users')->with('flash_success', 'Data Added');
        return redirect()->route('company.users')->withFlashSuccess('User Added Successfully!');
    }


    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request)
    {
            $user = User::find($request->get('user_editid'));
            // echo $request->get('status');exit;
            // print_r($request->get('user_editid')); exit;
            $validator = Validator::make($request->all(), [

                'name' => 'required|max:255',
                'email' => 'required|email|max:255',
                'phone_number'    =>  'required'
            ]);

            $userphone = User::select(['phone_number'])->where(['phone_number'=>$request->get('phone_number'),'created_by'=>auth()->user()->id])->first();

            if(isset($userphone) && $userphone->phone_number!=''){
                
                return redirect()->back()->withFlashDanger('Phone number already use');

            }
            // $validator->sometimes('email', 'unique:users', function ($input) use ($user) {
            //     return strtolower($input->email) != strtolower($user->email);
            // });
            // $validator->sometimes('phone_number', 'unique:users', function ($input) use ($user) {
            //     return strtolower($input->phone_number) != strtolower($user->phone_number);
            // });
            // $validator->sometimes('password', 'min:6|confirmed', function ($input) {
            //     return $input->password;
            // });
            // if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());


            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->pin = $request->get('pin');
            $user->phone_number = $request->get('phone_number');
            // $user->active = $request->get('status');
            // if ($request->has('password')) {
            //     $user->password = bcrypt($request->get('password'));
            // }

            $user->active = $request->get('status', 1);

            // $user->confirmed = $request->get('confirmed', 1);
            $user->save();
            //roles
            if ($request->has('roles')) {
                $user->roles()->detach();
                if ($request->get('roles')) {
                    $user->roles()->attach($request->get('roles'));
                }
            }
            return redirect()->intended(route('company.users'));

    }



    function myprofile(User $user){

        // print_r($user);

        return view('artist.my_profile', ['user' => $user]);

    }

    public function pactall()
    {
        //$pacttemplate = Pacttemplate::where(['created_by'=>auth()->user()->id])->orderBy('id', 'desc')->get();

        $pacttemplate = Pacttemplate::join("pact_temp_section",'pact_temp_section.pacttemp_id','=','pacttemplates.id')->select(["pact_temp_section.clause_body","pact_temp_section.section_title","pacttemplates.*"])->where(['pacttemplates.created_by'=>auth()->user()->id])->orderBy('pacttemplates.id', 'desc')->get();
       // echo "<pre>";print_r($pacttemplate->toArray());die;
        // $pact_temp_section = Pact_temp_section::where(['pacttemp_id'=>$pacttemplate->id])->first();
        return view('admin.pacts.company_pactslist', ['pacttemplate' => $pacttemplate]);
    }

    public function pact()
    {

        return view('admin.pacts.company_pact');
    }

    public function pactedit(Pacttemplate $pacttemplate)
    {
        $pact_temp_section = Pact_temp_section::where(['pacttemp_id'=>$pacttemplate->id])->first();
        return view('admin.pacts.company_pact', ['pacttemplate' => $pacttemplate,'pact_temp_section' =>$pact_temp_section]);
    }

    
    public function pact_sectionadd(Request $request)
    {
        // echo $request->get('clause_heading');
        // exit;
        $filename = $request->file('mms_image');
        $destinationPath = 'assets/images';
        // $file = $request->file('profile');
        // echo $request->get('sections');
        // echo 'users.store';exit;
        $this->validate($request, [

            'delivery_msg'    =>  'required',
            'complete_confirm_msg' => 'required',
            'title' => 'required',
            'type' => 'required',
            'time_complete' => 'required'

        ]);
        // if($request->get('clause_heading')==''){
        //     return redirect()->route('company.pactedit',[$request->get('pacttemp_id')])->withFlashDanger('clause heading and body require');
        // }
        $days = $request->old('days');
        $total_nudges = $request->old('total_nudges');
        $nudge_msg = $request->old('nudge_msg');
        $delivery_time = $request->old('delivery_time');
        $delivery_msg = $request->old('delivery_msg');
        $complete_confirm_msg = $request->old('complete_confirm_msg');
        $time_complete = $request->old('time_complete');
        $title = $request->old('title');

        $pact_temp_sectionid = $pact_template_edit ='';
        if($request->get('pacttemp_id')!=null){
            $pact_temp_sectionid = Pact_temp_section::where(['pacttemp_id'=>$request->get('pacttemp_id')])->first();
            $pact_template_edit = Pacttemplate::find($request->get('pacttemp_id'));
        }
        if(isset($filename) && $filename->move($destinationPath,'mms_img'.time().'.'.$filename->getClientOriginalExtension())){
            $mms_img = 'mms_img'.time().'.'.$filename->getClientOriginalExtension();
        }else{
            $mms_img = $request->get('old_file');
        }
        if($pact_template_edit!=''){
            $pact_template_edit->title    =  $request->get('title');
            $pact_template_edit->type     =  $request->get('type');
            //$pact_template_edit->mms_img     =  (isset($mms_img) && $mms_img!='') ? $mms_img : '';
            

            $pact_template_edit->save();
        }else{
            $pacttemplate = Pacttemplate::create([
            'title'    =>  $request->get('title'),
            'type'     =>  $request->get('type'),
            'temp_section' => count($request->get('section_title')),
            'created_by' => auth()->user()->id
            ]);
            //print_r($pacttemplate);exit;
        }
        // print_r($pact_temp_sectionid);exit;
        if($pact_temp_sectionid!=''){
            $pact_temp_section = Pact_temp_section::find($pact_temp_sectionid->id);

            $pact_temp_section->pacttemp_id = $request->get('pacttemp_id');
            $pact_temp_section->delivery_msg    =  $request->get('delivery_msg');
            $pact_temp_section->section_title     =  implode(',',$request->get('section_title'));
            $pact_temp_section->complete_confirm_msg =  $request->get('complete_confirm_msg');
            $pact_temp_section->has_campaign   =  $request->get('has_campaign');
            $pact_temp_section->time_complete   =  $request->get('time_complete');
            $pact_temp_section->days     = $request->get('days');
            $pact_temp_section->total_nudges = $request->get('total_nudges');
            $pact_temp_section->nudge_msg = $request->get('nudge_msg');
            $pact_temp_section->delivery_time = $request->get('delivery_time');
            $pact_temp_section->frequency = $request->get('frequency');
            $pact_temp_section->mms_img     =  (isset($mms_img) && $mms_img!='') ? $mms_img : '';
            $pact_temp_section->save();
            
        }else{
            

            $pact_temp_section = Pact_temp_section::create([
                'pacttemp_id' => $pacttemplate->id,
                'delivery_msg'    =>  $request->get('delivery_msg'),
                'section_title'     =>  implode(',',$request->get('section_title')),
                'complete_confirm_msg' =>  $request->get('complete_confirm_msg'),
                'has_campaign'   =>  $request->get('has_campaign'),
                'time_complete'   =>  $request->get('time_complete'),
                'days'     => $request->get('days'),
                'total_nudges' => $request->get('total_nudges'),
                'nudge_msg' => $request->get('nudge_msg'),
                'delivery_time' => $request->get('delivery_time'),
                'frequency' => $request->get('frequency'),
                'mms_img'     =>  (isset($mms_img) && $mms_img!='') ? $mms_img : ''

            ]);
        }
        //print_r($pact_temp_section);exit;
        return redirect()->route('company.pactedit',[$pact_temp_section->pacttemp_id])->withFlashSuccess('Pact Template Added Successfully!');

    }


    public function pact_clauses(Request $request)
    {
        
        
        $arr = array(
            'clause_heading' => json_encode($request->get('clause_heading')),
            'clause_body'    =>  json_encode($request->get('clause_body'))
        );
        
        $pact_temp_sectionid = Pact_temp_section::where(['pacttemp_id'=>$request->get('pacttemp_id')])->first();
        // print_r($pact_temp_sectionid);exit;
        if($pact_temp_sectionid){
            $pact_temp_section = Pact_temp_section::find($pact_temp_sectionid->id);

            $pact_temp_section->clause_heading = json_encode($request->get('clause_heading'));
            $pact_temp_section->clause_body    =  json_encode($request->get('clause_body'));
            $pact_temp_section->save();
            
        
        }
        
        return redirect()->route('company.pactedit',[$request->get('pacttemp_id')])->withFlashSuccess('Pact Template Added Successfully!');

    }

    public function pact_destroy($id)
    {
        $pacttemplate = Pacttemplate::find($id);
        $pacttemplate_clauses = Pact_temp_section::where(['pacttemp_id'=>$id])->get();
        
        if($pacttemplate->delete()){
            foreach($pacttemplate_clauses as $pactval){
                $pact_temp_section = Pact_temp_section::find($pactval->id);
                
                $pact_temp_section->delete();
            }
            return redirect()->route('company.pactall')->withFlashSuccess('Template Deleted Successfully!');
        }else{
        
            return redirect()->route('company.pactall')->withFlashDanger('Unable to Delete Template!');
        }
    }


    function pact_template(){
        $useradmin = User::where(['role_id'=>1])->join('users_roles','users.id','=','users_roles.user_id')->first();
// print_r();
        // exit;
        //$pacttemplate = Pacttemplate::where(['created_by'=>$useradmin->id])->orderBy('id', 'desc')->get();
        $pacttemplate = Pacttemplate::join("pact_temp_section",'pact_temp_section.pacttemp_id','=','pacttemplates.id')->select(["pact_temp_section.clause_body","pact_temp_section.section_title","pacttemplates.*"])->where(['pacttemplates.created_by'=>$useradmin->id])->orderBy('pacttemplates.id', 'desc')->get();
        // $pact_temp_section = Pact_temp_section::where(['pacttemp_id'=>$pacttemplate->id])->first();
        return view('admin.pacts.pact_export', ['pacttemplate' => $pacttemplate]);
    }

    function pact_templateview(Pacttemplate $pacttemplate){
        $pact_temp_section = Pact_temp_section::where(['pacttemp_id'=>$pacttemplate->id])->first();

        return view('admin.pacts.company_pact', ['pacttemplate' => $pacttemplate,'pact_temp_section' =>$pact_temp_section,'url'=>'company.pact_export_add','view'=>'true']);
    }

    function pactexport(Pacttemplate $pacttemplate){
        $pact_temp_section = Pact_temp_section::where(['pacttemp_id'=>$pacttemplate->id])->first();

        return view('admin.pacts.company_pact', ['pacttemplate' => $pacttemplate,'pact_temp_section' =>$pact_temp_section,'url'=>'company.pact_export_add']);
    }

    public function pact_export_add(Request $request)
    {
        // echo $request->get('sections');
        // echo 'users.store';exit;
        $this->validate($request, [

            'delivery_msg'    =>  'required',
            'complete_confirm_msg' => 'required',
            'title' => 'required',
            'type' => 'required',
            'time_complete' => 'required'

        ]);

        // echo $request->get('pacttemp_id');

            
        //     print_r($pact_temp_sectionid);
        // exit;
        $days = $request->old('days');
        $total_nudges = $request->old('total_nudges');
        $nudge_msg = $request->old('nudge_msg');
        $delivery_time = $request->old('delivery_time');
        $delivery_msg = $request->old('delivery_msg');
        $complete_confirm_msg = $request->old('complete_confirm_msg');
        $time_complete = $request->old('time_complete');
        $title = $request->old('title');
        $frequency = $request->old('frequency');

        //$pact_temp_sectionid = $pact_template_edit ='';
        // if($request->get('pacttemp_id')!=null){
        //     $pact_temp_sectionid = Pact_temp_section::where(['pacttemp_id'=>$request->get('pacttemp_id')])->first();
        //     $pact_template_edit = Pacttemplate::find($request->get('pacttemp_id'));
        // }
        // if($pact_template_edit!=''){
        //     $pact_template_edit->title    =  $request->get('title');
        //     $pact_template_edit->type     =  $request->get('type');
            

        //     $pact_template_edit->save();
        // }else{
            $pacttemplate = Pacttemplate::create([
            'title'    =>  $request->get('title'),
            'type'     =>  $request->get('type'),
            'temp_section' => count($request->get('section_title')),
            'created_by' => auth()->user()->id
            ]);
            //print_r($pacttemplate);exit;
        // } 
        // print_r($pact_temp_sectionid);exit;
        // if($pact_temp_sectionid!=''){
        //     $pact_temp_section = Pact_temp_section::find($pact_temp_sectionid->id);

        //     $pact_temp_section->pacttemp_id = $request->get('pacttemp_id');
        //     $pact_temp_section->delivery_msg    =  $request->get('delivery_msg');
        //     $pact_temp_section->section_title     =  implode(',',$request->get('section_title'));
        //     $pact_temp_section->complete_confirm_msg =  $request->get('complete_confirm_msg');
        //     $pact_temp_section->has_campaign   =  $request->get('has_campaign');
        //     $pact_temp_section->time_complete   =  $request->get('time_complete');
        //     $pact_temp_section->days     = $request->get('days');
        //     $pact_temp_section->total_nudges = $request->get('total_nudges');
        //     $pact_temp_section->nudge_msg = $request->get('nudge_msg');
        //     $pact_temp_section->delivery_time = $request->get('delivery_time');
        //     $pact_temp_section->frequency = $request->get('frequency');
        //     $pact_temp_section->save();
            
        // }else{
           
            $pact_temp_sectionid = Pact_temp_section::where(['pacttemp_id'=>$request->get('pacttemp_id')])->first();


            $pact_temp_section = Pact_temp_section::create([
                'pacttemp_id' => $pacttemplate->id,
                'delivery_msg'    =>  $request->get('delivery_msg'),
                'section_title'     =>  implode(',',$request->get('section_title')),
                'complete_confirm_msg' =>  $request->get('complete_confirm_msg'),
                'has_campaign'   =>  $request->get('has_campaign'),
                'time_complete'   =>  $request->get('time_complete'),
                'days'     => $request->get('days'),
                'total_nudges' => $request->get('total_nudges'),
                'nudge_msg' => $request->get('nudge_msg'),
                'delivery_time' => $request->get('delivery_time'),
                'frequency' => $request->get('frequency'),
                'clause_body' => $pact_temp_sectionid->clause_body,
                'clause_heading' => $pact_temp_sectionid->clause_heading


            ]);
            // print_r($pact_temp_section); exit;
        // }
        return redirect()->route('company.pactall')->withFlashSuccess('Pact Template Added Successfully!');
    }

    function sendsms_bytwillo(){
       
        $sid    = "AC448e1de7c35c623674d09229fcc093ad";
        $token  = "7b60ab091ef0e12d006498931ab07250";
        $twilio = new Client($sid, $token);

        $message = $twilio->messages
                          ->create("8185054044", // to
                                   array(
                                       "body" => "This is the ship that made the Kessel Run in fourteen parsecs?",
                                       "from" => "14692084512",
                                       "mediaUrl" => array("https://c1.staticflickr.com/3/2899/14341091933_1e92e62d12_b.jpg")
                                   )
                          );
        print($message->sid);
        exit;
    }


    public function assign_userpact(Request $request)
    {
        
        
        
        // $this->sendsms_bytwillo();
        // echo 'users.store';exit;
        // \Session::flash('message',$request->get('role'));
        // $this->validate($request, [

        //     'name'    =>  'required|max:255',
        //     'email' => 'required|email|max:255|unique:users',
        //     'phone_number'    =>  'required|unique:users'
        // ]);
        // $name = $request->old('name');
        // $email = $request->old('email');
        // $phone_number = $request->old('phone_number');
        // $pin = $request->old('pin');

        // $role = $request->old('role');
        // if()
        // return redirect()->route('company.add')->withFlashSuccess($request->get('role'));
        $user_company_detail = User::where(['id'=>auth()->user()->id])->first();
        $assign_userid = explode(',', $request->get('user_id'));
        for ($i=0; $i <count($assign_userid) ; $i++) { 
            # code...
            $assign_user = Assign_user::create([

                'user_id'    =>  $assign_userid[$i],
                'pact_id'    =>  $request->get('pact_id'),
                'admin_id'    =>  auth()->user()->id,
                'schedule_time' => $request->get('schedule_time')
            ]);
            $pact_temp_section = Pact_temp_section::select(['mms_img'])->where(['pacttemp_id'=>$request->get('pact_id')])->first();
            $userdetail = User::where(['id'=>$assign_user->user_id])->first();
            $userdetail->phone_number;
            $msg = ucwords($userdetail->name).' has sent you a PhonePact.  Please take a moment to agree to the terms and sign.<br>'.route('pact-signees-form',[base64_encode($assign_user->id)]);
            
            $sid    = "AC448e1de7c35c623674d09229fcc093ad";
            $token  = "7b60ab091ef0e12d006498931ab07250";
           // $arr = array(ucwords($userdetail->name).", ". $user_company_detail->name." has sent you a PhonePact.  Please take a moment to agree to the terms and sign.".route('pact-signees-form',base64_encode($assign_user->id)));
                //print_r($arr);
                //die(); exit;
            $twilio = new Client($sid, $token);
            if($pact_temp_section->mms_img!=''){
                $sendimg = 'assets/images/'.$pact_temp_section->mms_img;
            }else{
                $sendimg = 'assets/user-front/img/pact_signee_phoneimage.png';
            }

            //print_r(expression)
            $message = $twilio->messages
                              ->create($userdetail->phone_number, // to
                                       array(
                                           "body" => ucwords($userdetail->name).", ". $user_company_detail->name." has sent you a PhonePact.  Please take a moment to agree to the terms and sign.".route('pact-signees-form',[base64_encode($assign_user->id)]),
                                           "from" => "14692084512",
                                           "mediaUrl" => array(asset($sendimg))
                                       )
                              );
            // print($message->sid);
            // print_r($userdetail); 
            // echo $pacturl;
            // exit;
            //
        }
        // $category->save();
        // $user->roles()->attach($request->get('role'));
        // print_r($assign_user); exit;
        // return redirect()->route('admin.users')->with('flash_success', 'Data Added');
        return redirect()->route('company.users')->withFlashSuccess('Pact Assign To User Successfully!');
    }

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $category_list = Category::select(['id','cat_name'])->get();

        //print_r($category_list);

        return view('admin.users.create');

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        $this->validate($request, [

            'title'    =>  'required|max:255'

        ]);

        $pro_image = '';

        //Move Uploaded File

        $pro_image = array();

        $destinationPath = 'assets/images';

        $pro_image_file = $request->file('pro_image');



        if(isset($pro_image_file) && count($pro_image_file)>0){

        for ($i=0; $i <count($pro_image_file) ; $i++) { 

           

            if(isset($pro_image_file) && $pro_image_file[$i]->move($destinationPath,'product'.$i.time().'.'.$pro_image_file[$i]->getClientOriginalExtension())){

            $pro_image[] = 'product'.$i.time().'.'.$pro_image_file[$i]->getClientOriginalExtension();

            }

        }

        }

        $category = (count($request->get('category'))>0) ? implode(',',$request->get('category')) : ''; //exit;

        $products = new Products([

            'title'    =>  $request->get('title'),

            'description'    =>  $request->get('description'),

            'price'    =>  $request->get('price'),

            'created_by' => auth()->user()->id

        ]);

        $products['category_id'] = $category;

        $products['pro_image'] = (!empty($pro_image) && count($pro_image) >0) ? implode(',', $pro_image) : '';

        $products['make_price'] = $request->get('make_price');

        // print_r(Products::all()); exit;

        $products->save();

        return redirect()->route('artist.products')->withFlashSuccess('Product Add Successfully');

    }





    public function update_product(Request $request,$id)

    {

        $this->validate($request, [

            'title'    =>  'required|max:255'

        ]);

        $pro_image = array();

        //Move Uploaded File

        $destinationPath = 'assets/images';

        $pro_image_file = $request->file('pro_image');

        if(isset($pro_image_file) && count($pro_image_file)>0){

        for ($i=0; $i <count($pro_image_file) ; $i++) { 

           

            if(isset($pro_image_file) && $pro_image_file[$i]->move($destinationPath,'product'.$i.time().'.'.$pro_image_file[$i]->getClientOriginalExtension())){

            $pro_image[] = 'product'.$i.time().'.'.$pro_image_file[$i]->getClientOriginalExtension();

            }

        }

        }

        //print_r($pro_image); exit;

        $category = (count($request->get('category'))>0) ? implode(',',$request->get('category')) : ''; //exit;

        $products = Products::find($id);

        // $products =[

        //     'title'    =>  $request->get('title'),

        //     'description'    =>  $request->get('description'),

        //     'price'    =>  $request->get('price'),

        //     'created_by' => auth()->user()->id

        // ];

        if(!empty($pro_image) && count($pro_image) >0){

            $products->pro_image = implode(',', $pro_image);

        }

        $products->title = $request->get('title');

        $products->category_id = $category;

        $products->make_price = $request->get('make_price');

        //print_r($products); exit;

        $products->save();

        return redirect()->route('artist.products')->withFlashSuccess('Product Add Successfully');

    }



    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        //

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        $products = Products::find($id);

        $category_list = Category::select(['id','cat_name'])->get();

        //print_r($products); exit;

        return view('artist.edit_products', ['products' => $products,'category_list'=>$category_list]);

    }



    



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        $users = User::find($id);

        $users->delete();

        return redirect()->route('company.users')->withFlashSuccess('Successfully Deleted');

    }



    /*****************************/

    function artist_address_list()

    {

        $where=array("user_id"=>auth()->user()->id);

        $artist_address_list=User_address::where($where)->get();

        return view('artist.artist_address_list',["artist_address_list"=>$artist_address_list]);

    }



    function add_artist_address(){

        return view('artist.add_artist_address');

    }



    function edit_artist_address($id){

        // echo $id;

        $artist_address = User_address::find($id);

        return view('artist.edit_artist_address',['artist_address'=>$artist_address]);

    }

    /*****************************/



    /******** my orders **********/

    function artist_order_list()

    {

        $where=array("user_id"=>auth()->user()->id);

        $artist_order_list=Orders::select('order_tb.*','products.title','products.description')->where($where)->join('products', 'order_tb.product_id', '=', 'products.id')->get();

        return view('artist.artist_order_list',["artist_order_list"=>$artist_order_list]);

    }



    

    /*****************************/







    function store_artist_address(){

        // if(!empty($_POST))

        // {

            //echo auth()->user()->id;die;

            //echo "<pre>";

            //print_r(Request::post());die;

            $insert_data=$_POST;



            //print_r($insert_data); exit;

            $insert_data["user_id"]=auth()->user()->id;

            User_address::create($insert_data);

            return redirect()->route('artist.my-address-list')->withFlashSuccess('Successfully saved');

        //}

        // return redirect()->route('artist.products')->withFlashSuccess('Successfully Deleted');

    }

    function update_artist_address(Request $request,$id){

        // if(!empty($_POST))

        // {

            //echo auth()->user()->id;die;

            //echo "<pre>";

            //print_r(Request::post());die;

            $insert_data = User_address::find($id);

            $insert_data['first_name']=$_POST['first_name'];

            $insert_data['last_name']=$_POST['last_name'];

            $insert_data['building_no']=$_POST['building_no'];

            $insert_data['street']=$_POST['street'];

            $insert_data['district']=$_POST['district'];

            $insert_data['city']=$_POST['city'];

            $insert_data['region']=$_POST['region'];

            $insert_data['additional_instructions']=$_POST['additional_instructions'];

            // $insert_data['set_defult']=$_POST['set_defult'];

            // print_r($insert_data); exit;

            //$insert_data["user_id"]=auth()->user()->id;

            $insert_data->update();

            return redirect()->route('artist.my-address-list')->withFlashSuccess('Successfully updated');

        //}

        // return redirect()->route('artist.products')->withFlashSuccess('Successfully Deleted');

     }

}

