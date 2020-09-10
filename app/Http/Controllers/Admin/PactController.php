<?php



namespace App\Http\Controllers\Admin;



use App\Products;

use App\Pacttemplate;

use App\Pact_temp_section;

use App\Category;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;



class PactController extends Controller

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

        // $Products = Products::paginate();

        // print_r(Category::all()); exit;
        // echo auth()->user()->id;
        $pacttemplate = Pacttemplate::select(["pacttemplates.*",'pact_temp_section.section_title as pact_section_title','pact_temp_section.clause_heading'])->leftJoin("pact_temp_section",'pact_temp_section.pacttemp_id','=','pacttemplates.id')->where(['pacttemplates.created_by'=>auth()->user()->id])->orderBy('pacttemplates.id', 'desc')->paginate(20);
        // foreach ($pacttemplate as $key => $value) {
        //     print_r($value); exit;
        // }
        // print_r($pacttemplate); exit;
        // echo count($pacttemplate);
        // exit;
        // "pact_temp_section.clause_body","pact_temp_section.section_title",
        // ->paginate(20)
          //echo "<pre>";print_r($pacttemplate->toArray());die;
        //$pacttemplate = Pacttemplate::where(['created_by'=>auth()->user()->id])->orderBy('id', 'desc')->paginate(20);

        return view('admin.pacts.index', ['pacttemplate' => $pacttemplate]);

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {
        // echo $request->get('sections');
        // echo 'users.store';exit;
        $this->validate($request, [

            'sections'    =>  'required',
            'title'    =>  'required|max:255',
            'type' => 'required'

        ]);

        
        $pacttemplate = Pacttemplate::create([

            'title'    =>  $request->get('title'),
            'type'     =>  $request->get('type'),
            'temp_section' =>  $request->get('sections'),
            'active'   =>  $request->get('status'),
            'created_by' => auth()->user()->id

        ]);
        // echo $request->get('sections'); exit;
        // print_r($pacttemplate);exit;
        // $category->save();
        // $pacttemplate->roles()->attach($request->get('role'));
        // return redirect()->route('admin.users')->with('flash_success', 'Data Added');
        return redirect()->route('admin.pact-template')->withFlashSuccess('Pact Template Added Successfully!');

    }



    /**

     * Display the specified resource.

     *

     * @param  \App\Products  $products

     * @return \Illuminate\Http\Response

     */

    public function show(Products $products)

    {

        //

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Products  $products

     * @return \Illuminate\Http\Response

     */

    public function edit(Pacttemplate $pacttemplate)

    {
        $pact_temp_section = Pact_temp_section::where(['pacttemp_id'=>$pacttemplate->id])->first();
        // print_r($pact_temp_section); exit;
        //  
        return view('admin.pacts.edit_pact', ['pacttemplate' => $pacttemplate,'pact_temp_section' =>$pact_temp_section]);

    }

    public function addsection(Request $request)
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

        $delivery_msg = $request->old('delivery_msg');
        $complete_confirm_msg = $request->old('complete_confirm_msg');
        $time_complete = $request->old('time_complete');
        $title = $request->old('title');

        // $arr = array(
        //     'pacttemp_id' => $request->get('pacttemp_id'),
        //     'delivery_msg'    =>  $request->get('delivery_msg'),
        //     'section_title'     =>  implode(',',$request->get('section_title')),
        //     'complete_confirm_msg' =>  $request->get('complete_confirm_msg'),
        //     'has_campaign'   =>  $request->get('has_campaign'),
        //     'time_complete'   =>  $request->get('time_complete'));
        // print_r($arr); exit;
        //$arr->save();
        $pact_temp_sectionid = Pact_temp_section::where(['pacttemp_id'=>$request->get('pacttemp_id')])->first();
            $pact_template_edit = Pacttemplate::find($request->get('pacttemp_id'));

            // 'title'    =>  $request->get('title'),
            // 'type'     =>  $request->get('type'),
            $pact_template_edit->title    =  $request->get('title');
            $pact_template_edit->type     =  $request->get('type');
            $pact_template_edit->save();
        // print_r($pact_temp_sectionid);exit;
        if($pact_temp_sectionid){
            $pact_temp_section = Pact_temp_section::find($pact_temp_sectionid->id);

            $pact_temp_section->pacttemp_id = $request->get('pacttemp_id');
            $pact_temp_section->delivery_msg    =  $request->get('delivery_msg');
            $pact_temp_section->section_title     =  implode(',',$request->get('section_title'));
            $pact_temp_section->complete_confirm_msg =  $request->get('complete_confirm_msg');
            $pact_temp_section->has_campaign   =  $request->get('has_campaign');
            $pact_temp_section->time_complete   =  $request->get('time_complete');
            $pact_temp_section->save();
            
        }else{

            $pact_temp_section = Pact_temp_section::create([
                'pacttemp_id' => $request->get('pacttemp_id'),
                'delivery_msg'    =>  $request->get('delivery_msg'),
                'section_title'     =>  implode(',',$request->get('section_title')),
                'complete_confirm_msg' =>  $request->get('complete_confirm_msg'),
                'has_campaign'   =>  $request->get('has_campaign'),
                'time_complete'   =>  $request->get('time_complete')

            ]);
        }
        // echo $request->get('sections'); exit;
        // print_r($pact_temp_section);exit;
        // $category->save();
        // $pacttemplate->roles()->attach($request->get('role'));
        // return redirect()->route('admin.users')->with('flash_success', 'Data Added');
        return redirect()->route('admin.pact-template.edit',[$request->get('pacttemp_id')])->withFlashSuccess('Pact Template Added Successfully!');

    }

    public function addclauses(Request $request)
    {
        
        // echo $request->get('sections');
        // echo 'users.store';exit;
        // $this->validate($request, [

        //     'clause_heading'    =>  'required',
        //     'complete_confirm_msg' => 'required',
        //     'time_complete' => 'required'

        // ]);
        $arr = array(
            'clause_heading' => json_encode($request->get('clause_heading')),
            'clause_body'    =>  json_encode($request->get('clause_body')));
        // print_r($arr);
        // $r = json_decode($arr['clause_heading']);
        // print_r($r);

         //exit;
        //$arr->save();
        $pact_temp_sectionid = Pact_temp_section::where(['pacttemp_id'=>$request->get('pacttemp_id')])->first();
        // print_r($pact_temp_sectionid);exit;
        if($pact_temp_sectionid){
            $pact_temp_section = Pact_temp_section::find($pact_temp_sectionid->id);

            $pact_temp_section->clause_heading = json_encode($request->get('clause_heading'));
            $pact_temp_section->clause_body    =  json_encode($request->get('clause_body'));
            $pact_temp_section->save();
            
        // }else{

        //     $pact_temp_section = Pact_temp_section::create([
        //         'pacttemp_id' => $request->get('pacttemp_id'),
        //         'delivery_msg'    =>  $request->get('delivery_msg'),
        //         'section_title'     =>  implode(',',$request->get('section_title')),
        //         'complete_confirm_msg' =>  $request->get('complete_confirm_msg'),
        //         'has_campaign'   =>  $request->get('has_campaign'),
        //         'time_complete'   =>  $request->get('time_complete')

        //     ]);
        }
        // echo $request->get('sections'); exit;
        // print_r($pact_temp_section);exit;
        // $category->save();
        // $pacttemplate->roles()->attach($request->get('role'));
        // return redirect()->route('admin.users')->with('flash_success', 'Data Added');
        return redirect()->route('admin.pact-template.edit',[$request->get('pacttemp_id')])->withFlashSuccess('Pact Template Added Successfully!');

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\Products  $products

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request)

    {
        $pacttemplate = Pacttemplate::find($request->get('template_editid'));
        // echo 'users.store';exit;
        $this->validate($request, [

            'sections'    =>  'required',
            'title'    =>  'required|max:255',
            'type' => 'required'

        ]);
        // $arr = array('title'    =>  $request->get('title'),
        //     'type'     =>  $request->get('type'),
        //     'sections' =>  $request->get('sections'),
        //     'clauses'  =>  $request->get('clauses'),
        //     'action'   =>  $request->get('status'));
        //$arr->save();
        
        // $pacttemplate = array(

            $pacttemplate->title    =  $request->get('title');
            $pacttemplate->type     =  $request->get('type');
            $pacttemplate->temp_section =  $request->get('sections'); //exit;
            // $pacttemplate->clauses  =  $request->get('clauses');
            $pacttemplate->active   =  $request->get('status');

        // );
        $pacttemplate->save();
        // echo $request->get('sections'); exit;
        // print_r($arr);exit;
        // $pacttemplate->roles()->attach($request->get('role'));
        // return redirect()->route('admin.users')->with('flash_success', 'Data Added');
        return redirect()->route('admin.pact-template')->withFlashSuccess('Pact Template Added Successfully!');

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Products  $products

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)
    {
        $pacttemplate = Pacttemplate::find($id);
        $pacttemplate_clauses = Pact_temp_section::where(['pacttemp_id'=>$id])->get();
        //print_r($pacttemplate); exit;
        if($pacttemplate->delete()){
            foreach($pacttemplate_clauses as $pactval){
                $pact_temp_section = Pact_temp_section::find($pactval->id);
                
                $pact_temp_section->delete();
            }
            return redirect()->route('admin.pact-template')->withFlashSuccess('Template Deleted Successfully!');
        }else{
        
            return redirect()->route('admin.pact-template')->withFlashDanger('Unable to Delete Template!');
        }
    }

}

