<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Products;
use App\Models\Auth\User\User;
use App\Category;
use Validator;


class ArtistController extends Controller
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
        $counts = [
            'products' => \DB::table('products')->count(),
            'category' => \DB::table('categories')->count(),
        ];

        

        return view('artist.dashboard', ['counts' => $counts]);
        // return view('admin.dashboard');
    }

    function myprofile(User $user){
        // print_r($user);
        return view('artist.my_profile', ['user' => $user]);
    }

    function products(){
        $Products = Products::all();
        
        //echo '<pre>';print_r($Products); exit;
        return view('artist.products', ['products' => $Products]);
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
        return view('artist.add_product',['category_list'=>$category_list]);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $file = $request->file('profile');
        //print_r($file);exit;
        //Move Uploaded File
        $destinationPath = 'assets/images';
        
        // echo $request->get('phone_number');//exit;
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
        ]);
        // print_r($validator->errors()); exit;
        //echo $user->email; exit;
        $validator->sometimes('email', 'unique:users', function ($input) use ($user) {
            return strtolower($input->email) != strtolower($user->email);
        });

        if ($validator->fails()) return redirect()->back()->withFlashWarning($validator->errors());
// echo $request->get('national_id'); exit();
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->email = $request->get('email');
        $user->phone_number = $request->get('phone_number');
        $user->description = $request->get('description');
        if(isset($file) && $file->move($destinationPath,time().'.'.$file->getClientOriginalExtension())){
        $user->profile_img = time().'.'.$file->getClientOriginalExtension();
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->get('password'));
        }

        $user->active = $request->get('active', 1);
        $user->confirmed = $request->get('confirmed', 1);

        // print_r($user);exit;
        $user->save();
        //roles
        if ($request->has('roles')) {
            $user->roles()->detach();

            if ($request->get('roles')) {
                $user->roles()->attach($request->get('roles'));
            }
        }

        return redirect()->intended(route('artist.profile',['user' =>$user->id]))->withFlashSuccess('User Restored Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $products = Products::find($id);
        $products->delete();
        return redirect()->route('artist.products')->withFlashSuccess('Successfully Deleted');
    }

    public function artistaddress(Request $request)
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
}
