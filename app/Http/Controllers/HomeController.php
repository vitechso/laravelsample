<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Assign_user;
use App\Signees_clauses;
use App\Pact_temp_section;
use Redirect;
use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
// use Illuminate\Http\Request;
require_once 'vendor/autoload.php';
use Twilio\Rest\Client;
use Auth;


class HomeController extends Controller



{


    public function __construct()
    {
        // $this->middleware('auth', ['except' => ['index']]);
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        if (!Auth::check())

        {

            return Redirect::to('/login');

        }

        else

        {

            if(auth()->user()->hasRole('administrator')) {

                return Redirect::to('/admin');

           }
           if(auth()->user()->hasRole('admin')) {

                return Redirect::to('/companyadmin/signees');

           }
           else
            // elseif(auth()->user()->hasRole(config('auth.users.default_role')))

                {
       // echo auth()->user()->hasRole('company');
       // exit;

                return Redirect::to('/company/users');

                }
        }
        // return Redirect::to('/admin');

        // return Redirect::to('/artist/dashboard');

        // return view('ishop/index');
    }



    function pact_signees_form($id=''){
        // Send an SMS using Twilio's REST API and PHP
        $signees_clauses = Signees_clauses::where(['assign_tbid'=>base64_decode($id)])->first();
        if(isset($signees_clauses) && $signees_clauses->sig_name!=''){
            return redirect()->route('thankyou',$signees_clauses->assign_tbid);
        }
/*
        $sid = "AC448e1de7c35c623674d09229fcc093ad"; 
        // Your Account SID from www.twilio.com/console
        $token = "7b60ab091ef0e12d006498931ab07250"; 
        // Your Auth Token from www.twilio.com/console

        $client = new Client($sid, $token);
        $message = $client->messages->create(
          '8185054044', // Text this number
          array(
            'from' => '14692084512', // From a valid Twilio number
            'body' => 'Hello from Twilio!'
          )
        );

        print $message->sid;
exit;
*/
        if($id!=''){
            $id = base64_decode($id);
            //$id=1;
        // echo base64_decode($id);
        $assign_users = Assign_user::select(['assign_users.*','u1.name as signee_name','u2.name'])->join('users as u1','assign_users.user_id','=','u1.id')->join('users as u2','assign_users.admin_id','=','u2.id')->where(['assign_users.id'=>$id])->first();
        // $users = User::with('roles')->orderBy('id', 'desc')->first();
        // echo $assign_users->pact_id;
        $pact_temp_detail = Pact_temp_section::select(['pact_temp_section.*','pacttemplates.title'])->join('pacttemplates','pacttemplates.id','=','pact_temp_section.pacttemp_id')->where(['pacttemp_id'=>$assign_users->pact_id])->groupBy('pacttemp_id')->first();
        // echo '<pre>';print_r($pact_temp_detail); exit;
        return view('mobile-pages.index',['users' => $assign_users,'pact_temp_detail'=>$pact_temp_detail]);
        }else{
            return Redirect::to('/login');
        }
    }

    function signees_pact_sections($id){
        $signees_clauses = Signees_clauses::where(['assign_tbid'=>base64_decode($id)])->first();
        if(isset($signees_clauses) && $signees_clauses->sig_name!=''){
            return redirect()->route('thankyou',$signees_clauses->assign_tbid);
        }
        $id = base64_decode($id);
        // if($request->get('pact_id')){
        //     \Session::flash('pact_id',$request->get('pact_id'));
        //     $pactid = session('pact_id');
        // }else{
            // $id = $id;
        $assign_users = Assign_user::select(['assign_users.*','u1.name as signee_name','u2.name'])->join('users as u1','assign_users.user_id','=','u1.id')->join('users as u2','assign_users.admin_id','=','u2.id')->where(['assign_users.id'=>$id])->first();
        $pactid = $assign_users->pact_id;
        $pact_temp_detail = Pact_temp_section::where(['pacttemp_id'=>$pactid])->groupBy('pacttemp_id')->first();
        $clause_heading = json_decode($pact_temp_detail->clause_heading);
        $clause_body = json_decode($pact_temp_detail->clause_body);
        $hct = 0;
        if(is_array($clause_heading)){
        // print_r($pact_temp_detail); exit;
        $array = array_map('array_filter', $clause_heading);
        $clause_heading_count = count(array_filter($array));
        }else{
            $clause_heading_count = 0;
        }
        // print_r($array);
        
        // exit();
        // echo '<pre>';
        // echo count(json_decode($pact_temp_detail->clause_heading));exit;
        // echo '<pre>';print_r($clause_heading[0][0]); exit;
        // $signee_id = $request->old('signee_id');
        // $pact_id = $request->old('pact_id');
        // $admin_id = $request->old('admin_id');
        return view('mobile-pages.signee_section_pact',['pact_temp_detail'=>$pact_temp_detail,'totalsection'=>$clause_heading_count,'clause_heading'=>$clause_heading,'clause_body'=>$clause_body,'assign_section_detail'=>$assign_users]);
        // exit;
    }

    function signees_pact_signature($id,Request $request){
        $id = base64_decode($id);
       // echo $request->get('pact_id');
        // $id = base64_encode($id);
        $signees_clauses = Signees_clauses::where(['signees_id'=>$request->get('signees_id'),'assign_tbid'=>$request->get('assign_tbid')])->first();

        if(isset($signees_clauses) && $signees_clauses->id){
            $signees_updateclauses = Signees_clauses::find($signees_clauses->id);
            $signees_updateclauses->signees_id = $request->get('signees_id');
            $signees_updateclauses->assign_tbid = $request->get('assign_tbid');
            $signees_updateclauses->save();
        }else{

            $signees_insertclauses = Signees_clauses::create([
                'signees_id'    =>  $request->get('signees_id'),
                'assign_tbid'    =>  $request->get('assign_tbid')
            ]);
        }
        //print_r($signees_clauses); exit();
        // print_r($signees_clauses); exit;
        return redirect()->route('signature-template',base64_encode($id));
    }
    function signature_template($id){
        $id = base64_decode($id);
        $assign_users = Assign_user::select(['assign_users.*','u1.name as signee_name','u2.name'])->join('users as u1','assign_users.user_id','=','u1.id')->join('users as u2','assign_users.admin_id','=','u2.id')->where(['assign_users.id'=>$id])->first();
        return view('mobile-pages.sigtest',['assign_users'=>$assign_users]);
        // return view('mobile-pages.signature');
    }

    function signature_save($id){
        // $id = base64_decode($id);
        $assign_users = Assign_user::select(['assign_users.*','u1.name as signee_name','u2.name'])->join('users as u1','assign_users.user_id','=','u1.id')->join('users as u2','assign_users.admin_id','=','u2.id')->where(['assign_users.id'=>$id])->first();
        return view('mobile-pages.thankyou',['assign_users'=>$assign_users]);
    }

    function saveimage(Request $request){
        $path = storage_path('signature_image');
        // $upload_dir = somehow_get_upload_dir();
        $img = $request->get('imgBase64');
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $savefilename = time()."signatureimage_name.png";
        $file = $path."/".$savefilename;
        $success = file_put_contents($file, $data);
        $signees_clauses = Signees_clauses::where(['signees_id'=>$request->get('signees_id'),'assign_tbid'=>$request->get('assign_tbid')])->first();
        // $signees_clauses = Signees_clauses::create([
        //     'signees_id'    =>  $request->get('signees_id'),
        //     'assign_tbid'    =>  $request->get('assign_tbid'),
        $signees_updateclauses = Signees_clauses::find($signees_clauses->id);
        $signees_updateclauses->sig_name = $savefilename;
        $signees_updateclauses->save();
        // ]);
        // echo route('thankyou',base64_encode($request->get('assign_tbid')));
        return $signees_updateclauses;
        // return view('mobile-pages.thankyou');
    }
    /* original function 
    function signees_pact_sections(Request $request){
        $pactid = $request->get('pact_id');
        $pact_temp_detail = Pact_temp_section::where(['pacttemp_id'=>$pactid])->groupBy('pacttemp_id')->first();
        $clause_heading = json_decode($pact_temp_detail->clause_heading);
        $clause_body = json_decode($pact_temp_detail->clause_body);
        // echo '<pre>';print_r($clause_heading[0][0]); exit;
        $signee_id = $request->old('signee_id');
        $pact_id = $request->old('pact_id');
        $admin_id = $request->old('admin_id');
        return view('mobile-pages.agree',['pact_temp_detail'=>$pact_temp_detail,'totalsection'=>count($clause_heading),'clause_heading'=>$clause_heading,'clause_body'=>$clause_body]);
        // exit;
    }*/






    public function index123()



    {



         $counts = [



            'users' => 0,



            'users_unconfirmed' => 0,



            'users_inactive' => 0,



            'protected_pages' => 0,



        ];



        return view('admin.dashboard',['counts' => $counts]);



    }



}



