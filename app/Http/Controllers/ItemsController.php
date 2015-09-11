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
		$items = Item::all();
        //return view('items.index', compact('items'));
        return response()->json(
            array(
                'items' => $items
            )
        );
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
            $filename = Request::input('name').'_1.'.$extension;
            
            Input::file('picture1')->move($destination, $filename);
        } else {
            return "Please provide a picture.";
        }
        
        if(Request::file('picture2')) {
            $destination = 'uploads';
            // Give it a unique filename
            $extension = Input::file('picture2')->getClientOriginalExtension();
            $filename = Request::input('name').'_2.'.$extension;
            
            Input::file('picture2')->move($destination, $filename);
            
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
            $filename = $item->name.'_1.'.$extension;
            
            File::delete('uploads/'.$item->name.'_1.jpg');
            Input::file('picture1')->move($destination, $filename);
        }
        
        if(isset($_POST['active']) || Request::file('picture2')) {
            File::delete('uploads/'.$item->name.'_2.jpg');
        }
        
        if(Request::file('picture2')) {
            $destination = 'uploads';
            // Give it a unique filename
            $extension = Input::file('picture2')->getClientOriginalExtension();
            $filename = $item->name.'_2.'.$extension;
            
            Input::file('picture2')->move($destination, $filename);
            
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
		Item::find($id)->delete();
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
