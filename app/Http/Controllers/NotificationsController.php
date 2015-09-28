<?php namespace App\Http\Controllers;

use App\Notification;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
//use Illuminate\Http\Request;

class NotificationsController extends Controller {
    // Middleware
    public function __construct()
    {
        $this->middleware('admin.session', ['only' => ['store']]);
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		return response()->json(Notification::all()->last());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
		return view('notifications.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
		$notification = new Notification;
        
        if(Request::has('text') && Request::input('text') != "") {
            $notification->text = Request::input('text');
            $notification->save();
            return Response::make("Success", 201);
        } else {
            // Bad request, missing parameters
            return Response::make("Please provide a notification.", 400);
        }
	}

    // The below functions should not be needed and will not be implemented

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
	}

}
