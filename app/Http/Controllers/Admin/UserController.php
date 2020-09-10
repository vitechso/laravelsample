<?php







namespace App\Http\Controllers\Admin;







use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Access\User\EloquentUserRepository;
use Ramsey\Uuid\Uuid;
use Validator;







class UserController extends Controller
{   
    /**



     * Repository



     *



     * @var object



     */



    protected $repository;







    /**



     * Construct



     * 



     */



    public function __construct()



    {



        $this->repository = new EloquentUserRepository;



    }







    /**



     * Display a listing of the resource.



     *



     * @return \Illuminate\Http\Response



     */



    public function index(Request $request)



    {

        // $r = User::with('roles')->sortable(['email' => 'asc'])->paginate();
        $rt = User::with('roles')->orderBy('id', 'desc')->toSql();
        // print_r($rt);exit;
        return view('admin.company', ['users' => User::with('roles')->orderBy('id', 'desc')->get()]);



    }







    /**



     * Restore Users



     *



     * @return \Illuminate\Http\Response



     */



    public function restore(Request $request)



    {



        return view('admin.users.restore', ['users' => User::onlyTrashed()->with('roles')->sortable(['email' => 'asc'])->paginate()]);



    }







    /**



     * Restore Users



     *



     * @param int $id



     * @return \Illuminate\Http\Response



     */



    public function restoreUser($id)



    {



        $status = $this->repository->restore($id);







        if($status)



        {



            return redirect()->route('admin.users')->withFlashSuccess('User Restored Successfully!');



        }







        return redirect()->route('admin.users')->withFlashDanger('Unable to Restore User!');



    }







    /**



     * Show the form for creating a new resource.



     *



     * @return \Illuminate\Http\Response



     */



    public function create()



    {
        return view('admin.users.create');
    }

    public function create_admin()



    {
        return view('admin.users.create');
    }







    /**



     * Store a newly created resource in storage.



     *



     * @param  \Illuminate\Http\Request $request



     * @return \Illuminate\Http\Response



     */



    public function store(Request $request)
    {
        // echo 'users.store';exit;
        $this->validate($request, [

            'name'    =>  'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone_number'    =>  'required|unique:users',
            'password' => 'min:6|required_with:confirmed|same:confirmed',
            'confirmed' => 'min:6'

        ]);
        
        $name = $request->old('name');
        $email = $request->old('email');
        $phone_number = $request->old('phone_number');

        $user = User::create([

            'name'    =>  $request->get('name'),
            'email'    =>  $request->get('email'),
            'phone_number'    =>  $request->get('phone_number'),
            'active'    =>  $request->get('status'),
            'password' => bcrypt($request->get('password')),
            'confirmation_code' => Uuid::uuid4(),
            'confirmed' => true

        ]);
        // print_r($user); exit;
        // $category->save();
        $user->roles()->attach($request->get('role'));
        // return redirect()->route('admin.users')->with('flash_success', 'Data Added');
        return redirect()->route('admin.users')->withFlashSuccess('User Added Successfully!');
    }







    /**



     * Display the specified resource.



     *



     * @param User $user



     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View



     */



    public function show(User $user)



    {



        //print_r($user); exit;



        return view('admin.users.show', ['user' => $user]);



    }



     public function reports(Request $request)



    {


          //echo "reports";die;
        //print_r($user); exit;



         $r = User::with('roles')->sortable(['email' => 'asc'])->paginate();
        //print_r($r);exit;
        return view('admin.reports', ['users' => User::with('roles')->orderBy('id', 'desc')->paginate()]);



    }

    





    /**



     * Show the form for editing the specified resource.



     *



     * @param User $user



     * @return \Illuminate\Http\Response



     */



    public function edit(User $user)



    {


        // print_r($user);exit;
        return view('admin.users.edit', ['user' => $user, 'roles' => Role::get()]);



    }







    /**



     * Update the specified resource in storage.



     *



     * @param  \Illuminate\Http\Request $request



     * @param User $user



     * @return mixed



     */



    public function update(Request $request)
    {


        $user = User::find($request->get('user_editid'));
        $validator = Validator::make($request->all(), [

            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone_number'    =>  'required'
        ]);







        $validator->sometimes('email', 'unique:users', function ($input) use ($user) {



            return strtolower($input->email) != strtolower($user->email);



        });
        $validator->sometimes('phone_number', 'unique:users', function ($input) use ($user) {



            return strtolower($input->phone_number) != strtolower($user->phone_number);



        });







        // $validator->sometimes('password', 'min:6|confirmed', function ($input) {



        //     return $input->password;



        // });







        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());







        $user->name = $request->get('name');



        $user->email = $request->get('email');
        $user->phone_number = $request->get('phone_number');







        // if ($request->has('password')) {



        //     $user->password = bcrypt($request->get('password'));



        // }







        $user->active = $request->get('status');



        // $user->confirmed = $request->get('confirmed', 1);







        $user->save();







        //roles



        if ($request->has('roles')) {



            $user->roles()->detach();







            if ($request->get('roles')) {



                $user->roles()->attach($request->get('roles'));



            }



        }







        // return redirect()->intended(route('admin.users'));
        return redirect()->route('admin.users')->withFlashSuccess('User Updated Successfully!');


    }







    /**



     * Remove the specified resource from storage.



     *



     * @param  int $id



     * @return \Illuminate\Http\Response



     */



    public function destroy($id)



    {



        $status = $this->repository->destroy($id);







        if($status)



        {



            return redirect()->route('admin.users')->withFlashSuccess('User Deleted Successfully!');



        }







        return redirect()->route('admin.users')->withFlashDanger('Unable to Delete User!');



    }

    /**************** company ****************/
    public function company(Request $request)
    {
        
        return view('admin.company', ['users' => User::with('roles')->sortable(['email' => 'asc'])->paginate()]);
    }

    public function store_company(Request $request)
    {
        // echo $request->get('name');
        // echo 'users.store';exit;
        $this->validate($request, [

            'name'    =>  'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone_number'    =>  'required|unique:users',
            'password' => 'min:6|required_with:confirmed|same:confirmed',
            'confirmed' => 'min:6'

        ]);

        $user = User::create([

            'name'    =>  $request->get('name'),
            'email'    =>  $request->get('email'),
            'phone_number'    =>  $request->get('phone_number'),
            'password' => bcrypt($request->get('password')),
            'confirmation_code' => Uuid::uuid4(),
            'confirmed' => true

        ]);
        // print_r($user);exit;
        // $category->save();
        $user->roles()->attach($request->get('role'));
        // return redirect()->route('admin.users')->with('flash_success', 'Data Added');
        return redirect()->route('admin.company')->withFlashSuccess('User Added Successfully!');
    }

}



