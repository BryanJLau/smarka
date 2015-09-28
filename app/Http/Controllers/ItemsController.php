<?php namespace App\Http\Controllers;

use App\Item;
use App\Http\Requests;
use App\Http\Controllers\Controller;
//use App\Http\Controllers\File;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;

use Input;  // For file uploads

class ItemsController extends Controller {
    // Middleware
    public function __construct()
    {
        $this->middleware('admin.session',
            ['only' => ['store', 'update', 'destroy', 'changePictures']]);
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		if(isset($_GET['all'])) {
            $items = Item::all();
            return response()->json($items);
		}
		else {
		    date_default_timezone_set("America/Los_Angeles");
		    // Ordering should not happen between 9 PM Friday through Saturday
		    if((date('l') == 'Friday' && date('G') >= 21) ||
		        date('l') == 'Saturday') {
                return Response::make("[]", 503);   // Service Unavailable
            }
		    else {
		        $items = Item::where('active', 1)->get();
                return response()->json($items);
            }
        }
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
		return view('items.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
		$item = new Item;
        
        // Hash for storing unique filenames
        $item->hash = hash('md5', date('Y-m-d H:i:s'));
        
        if(Request::has('name')) {
            $item->name = Request::input('name');
        } else {
            return Response::make("Please provide a name.", 400);
        }
        
        if(Request::has('description')) {
            $item->description = Request::input('description');
        } else {
            return Response::make("Please provide a description.", 400);
        }
        
        if(Request::has('price')) {
            $item->price = Request::input('price');
        } else {
            return Response::make("Please provide a price.", 400);
        }
        
        $item->active = (Request::has('active') &&
            Request::input('active') == "true");
        
        if(Request::file('picture1')) {
            $destination = 'uploads';
            // Give it a unique filename
            $extension = Input::file('picture1')->getClientOriginalExtension();
            if(strtolower($extension) != 'jpg') {
                return Response::make("Please upload only .jpg files.", 400);
            } else {
                $filename = $item->hash . "_1." . strtolower($extension);
                Input::file('picture1')->move($destination, $filename);
            }
        } else {
            return Response::make("Please provide a main picture.", 400);
        }
        
        if(Request::file('picture2')) {
            $destination = 'uploads';
            // Give it a unique filename
            $extension = Input::file('picture2')->getClientOriginalExtension();
            if(strtolower($extension) != 'jpg') {
                return Response::make("Please upload only .jpg files.", 400);
            } else {
                $filename = $item->hash . "_2." . strtolower($extension);
                Input::file('picture2')->move($destination, $filename);
            }
            
            $item->picture2 = true;
        }
        
        $item->save();
        return Response::make("Success", 201);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
		$item = Item::find($id);
        return response()->json($item);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
		return view('items.edit')->with('id', $id);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
		$item = Item::find($id);
        
        if(Request::has('name') && Request::input('name') != "") {
            $item->name = Request::input('name');
        }
        
        if(Request::has('description') && Request::input('description') != "") {
            $item->description = Request::input('description');
        }
        
        if(Request::has('price') && Request::input('price') != "") {
            $item->price = Request::input('price');
        }
        
        $item->active = (Request::has('active') &&
            Request::input('active') == "true");
        
        if(Request::file('picture1')) {
            $destination = 'uploads';
            // Give it a unique filename
            $extension = Input::file('picture1')->getClientOriginalExtension();
            if(strtolower($extension) != 'jpg') {
                return Response::make("Please upload only .jpg files.", 400);
            } else {
                $filename = $item->hash . "_1." . strtolower($extension);
            
                File::delete('uploads/'.$item->hash.'_1.jpg');
                Input::file('picture1')->move($destination, $filename);
            }
        }
        
        if(Request::has('dp2') && Request::input('dp2') == "true" ||
                Request::file('picture2')) {
            File::delete('uploads/'.$item->hash.'_2.jpg');
            $item->picture2 = false;
        }
        
        if(Request::file('picture2')) {
            $destination = 'uploads';
            // Give it a unique filename
            $extension = Input::file('picture2')->getClientOriginalExtension();
            if(strtolower($extension) != 'jpg') {
                return Response::make("Please upload only .jpg files.", 400);
            } else {
                $filename = $item->hash . "_2." . strtolower($extension);
                Input::file('picture2')->move($destination, $filename);
            }
            
            $item->picture2 = true;
        }
        
        $item->save();
        return Response::make("Success", 205);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
		$item = Item::find($id);
        File::delete('uploads/'.$item->hash.'_1.jpg');
        if($item->picture2)
            File::delete('uploads/'.$item->hash.'_2.jpg');
		$item->delete();
        return Response::make("Success", 205);
	}
	
	/**
	 * Change the pictures of an item
	 *
	 * @param  int  $id
	 * @param  File $picture1
	 * @param  File $picture2
	 * @param  bool $dp2
	 * @return Response
	 */
	public function changePictures()
	{
	    $item = Item::find(Request::input('id'));
	    
	    // Delete the secondary picture first
	    if(Request::has('dp2') && Request::input('dp2') == "true" ||
                Request::file('picture2')) {
            File::delete('uploads/'.$item->hash.'_2.jpg');
            $item->picture2 = false;
        }
        
        if(Request::file('picture1')) {
            $destination = 'uploads';
            // Give it a unique filename
            $extension = Input::file('picture1')->getClientOriginalExtension();
            if(strtolower($extension) != 'jpg') {
                return Response::make("Please upload only .jpg files.", 400);
            } else {
                $filename = $item->hash . "_1." . strtolower($extension);
            
                File::delete('uploads/'.$item->hash.'_1.jpg');
                Input::file('picture1')->move($destination, $filename);
            }
        }
        
        if(Request::file('picture2')) {
            $destination = 'uploads';
            // Give it a unique filename
            $extension = Input::file('picture2')->getClientOriginalExtension();
            if(strtolower($extension) != 'jpg') {
                return Response::make("Please upload only .jpg files.", 400);
            } else {
                $filename = $item->hash . "_2." . strtolower($extension);
                Input::file('picture2')->move($destination, $filename);
            }
            
            $item->picture2 = true;
        }
        
        $item->save();
        return Response::make("Success", 205);
	}
}
