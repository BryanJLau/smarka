<?php namespace App\Http\Controllers;

use App\Item;
use App\Http\Requests;
use App\Http\Controllers\Controller;
//use App\Http\Controllers\File;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;

use Input;  // For file uploads

class ItemsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		date_default_timezone_set("America/Los_Angeles");
		// Ordering should not happen between 9 PM Friday through Saturday
		if((date('l') == 'Friday' && date('G') >= 21) ||
		    date('l') == 'Saturday') {
		    http_response_code(503);    // Service Unavailable
		    return response()->json([]);
        }
		else {
		    $items = Item::where('active', 1)->get();
            //return view('items.index', compact('items'));
            return response()->json($items);
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
            return "Please provide a name.";
        }
        
        if(Request::has('description')) {
            $item->description = Request::input('description');
        } else {
            return "Please provide a description.";
        }
        
        if(Request::has('price')) {
            $item->price = Request::input('price');
        } else {
            return "Please provide a price.";
        }
        
        $item->active = isset($_POST['active']);
        
        if(Request::file('picture1')) {
            $destination = 'uploads';
            // Give it a unique filename
            $extension = Input::file('picture1')->getClientOriginalExtension();
            if(strtolower($extension) != 'jpg') {
                return "Please upload only .jpg files.";
            } else {
                $filename = $item->hash . "_1." . strtolower($extension);
                Input::file('picture1')->move($destination, $filename);
            }
        } else {
            return "Please provide a picture.";
        }
        
        if(Request::file('picture2')) {
            $destination = 'uploads';
            // Give it a unique filename
            $extension = Input::file('picture2')->getClientOriginalExtension();
            if(strtolower($extension) != 'jpg') {
                return "Please upload only .jpg files.";
            } else {
                $filename = $item->hash . "_2." . strtolower($extension);
                Input::file('picture2')->move($destination, $filename);
            }
            
            $item->picture2 = true;
        }
        
        $item->save();
        
        return Redirect::to('items/list');
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
        
        if(Request::has('name')) {
            $item->name = Request::input('name');
        }
        
        if(Request::has('description')) {
            $item->description = Request::input('description');
        }
        
        if(Request::has('price')) {
            $item->price = Request::input('price');
        }
        
        $item->active = isset($_POST['active']);
        
        if(Request::file('picture1')) {
            $destination = 'uploads';
            // Give it a unique filename
            $extension = Input::file('picture1')->getClientOriginalExtension();
            if(strtolower($extension) != 'jpg') {
                return "Please upload only .jpg files.";
            } else {
                $filename = $item->hash . "_1." . strtolower($extension);
            
                File::delete('uploads/'.$item->hash.'_1.jpg');
                Input::file('picture1')->move($destination, $filename);
            }
        }
        
        if(Request::has('dp2') && Request::input('dp2') ||
                Request::file('picture2')) {
            File::delete('uploads/'.$item->hash.'_2.jpg');
            $item->picture2 = false;
        }
        
        if(Request::file('picture2')) {
            $destination = 'uploads';
            // Give it a unique filename
            $extension = Input::file('picture2')->getClientOriginalExtension();
            if(strtolower($extension) != 'jpg') {
                return "Please upload only .jpg files.";
            } else {
                $filename = $item->hash . "_2." . strtolower($extension);
                Input::file('picture2')->move($destination, $filename);
            }
            
            $item->picture2 = true;
        }
        
        $item->save();
        
        return Redirect::to('items/list');
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
        File::delete('uploads/'.$item->name.'_1.jpg');
        if($item->picture2)
            File::delete('uploads/'.$item->name.'_2.jpg');
		$item->delete();
		return Redirect::to('items');
	}

    
    /**
     * Displays a table list of all items
     */
    public function listItems()
    {
        $items = Item::all();
        return view('items.list')->with('items', $items);
    }
}
