<?php namespace App\Http\Controllers;

use App\Location;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

class LocationsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		return response()->json(Location::all());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
		return view('locations.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
		$location = new Location;
        
        if(Request::has('location')) {
            $location->location = Request::input('location');
        } else {
            return "Please provide text.";
        }
        
        $location->save();
        
        return Redirect::to('admin');
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
		$location = Location::find($id);

        $location->delete();
        return Redirect::to('locations/list');
	}
	
	public function listLocations()
    {
        $locations = Location::all();
        return view('locations.list')->with('locations', $locations);
    }

}
