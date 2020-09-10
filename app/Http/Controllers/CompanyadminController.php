<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

// use Request;

use App\Products;
use App\Pacttemplate;
use App\Assign_user;

use App\Models\Auth\User\User;
use App\Pact_temp_section;

use App\Category;

use App\Bank_details;

use App\User_address;

use App\Orders;
use Ramsey\Uuid\Uuid;

use Validator;

include('vendor/autoload.php');
use Twilio\Rest\Client;
use Auth;



class CompanyadminController extends Controller

{

    public function __construct()

    {

        $this->middleware('auth');

        // print_r(auth()->user()->id); exit;
    }

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()
    {
        $usercreated = User::find(auth()->user()->id);
        // print_r(auth()->user());exit;
        $users = User::where(['created_by'=>$usercreated->created_by])->with('roles')->orderBy('id', 'desc')->paginate();
        // print_r($users); exit;
        // echo auth()->user()->id;exit;
        $pacttemplate = Pacttemplate::where(['created_by'=>$usercreated->created_by])->orderBy('id', 'desc')->get();
        return view('admin.company_adminuser', ['users' => $users,'pacttemplate'=>$pacttemplate]);

        // return view('admin.dashboard');

    }

    public function assign_userpact(Request $request)
    {
        $usercreated = User::find(auth()->user()->id);
        $user_company_detail = User::where(['id'=>$usercreated->created_by])->first();
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
            $msg = ucwords($userdetail->name).', '. $user_company_detail->name.' has sent you a PhonePact.  Please take a moment to agree to the terms and sign.<br>'.route('pact-signees-form',[base64_encode($assign_user->id)]);
            
            $sid    = "AC448e1de7c35c623674d09229fcc093ad";
            $token  = "7b60ab091ef0e12d006498931ab07250";
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
                                           "body" => ucwords($userdetail->name)." has sent you a PhonePact.  Please take a moment to agree to the terms and sign. ".route('pact-signees-form',[base64_encode($assign_user->id)]),
                                           "from" => "14692084512",
                                           "mediaUrl" => array(asset($sendimg))
                                       )
                              );
            
        }
        
        return redirect()->route('companyadmin.signees')->withFlashSuccess('Pact Assign To User Successfully!');
    }
    

    public function addstore(Request $request)
    {
        
        // echo 'users.store';exit;
        //\Session::flash('message',$request->get('role'));
        $this->validate($request, [

            'name'    =>  'required|max:255',
            'email' => 'required|email|max:255',
            'phone_number'    =>  'required'
        ]);
        $usercreated = User::find(auth()->user()->id);
        $userphone = User::select(['phone_number'])->where(['phone_number'=>$request->get('phone_number'),'created_by'=>$usercreated->created_by])->first();


        if(isset($userphone) && $userphone->phone_number!=''){
            
            return redirect()->back()->withFlashDanger('Phone number already use');

        }

        $name = $request->old('name');
        $email = $request->old('email');
        $phone_number = $request->old('phone_number');
        $pin = $request->old('pin');

        
        // if()
        // return redirect()->route('company.add')->withFlashSuccess($request->get('role'));
        $user = User::create([

            'name'    =>  $request->get('name'),
            'email'    =>  $request->get('email'),
            'phone_number'    =>  $request->get('phone_number'),
            'created_by' => $usercreated->created_by,
            'pin' => $request->get('pin'),
            'confirmed' => true

        ]);
        // $category->save();
        $user->roles()->attach($request->get('role'));
        // print_r($user); exit;
        // return redirect()->route('admin.users')->with('flash_success', 'Data Added');
        return redirect()->route('companyadmin.signees')->withFlashSuccess('User Added Successfully!');
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

            $usercreated = User::find(auth()->user()->id);
            $userphone = User::select(['phone_number'])->where('id','!=',$request->get('user_editid'))->where(['phone_number'=>$request->get('phone_number'),'created_by'=>$usercreated->created_by])->first();

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
            return redirect()->intended(route('companyadmin.signees'));

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

        return redirect()->route('companyadmin.signees')->withFlashSuccess('Successfully Deleted');

    }

}

