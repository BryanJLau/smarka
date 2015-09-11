<?php namespace App\Http\Controllers;

use App\Order;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;

class OrdersController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		if(isset($_GET['all'])) {
		    // Want all orders for some reason, use with caution
		    $orders = Order::all();
		}
		else {
		    // Get unpaid orders by default
		    $orders = Order::where('paid', '=', 0)->get();
		}
		foreach($orders as $order) {
		        $order->item_array = JSON_decode($order->item_array);
		    }
            return view('orders.index')->with('orders', $orders);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
		$order = new Order;
        
        if(Request::has('name')) {
            $order->name = Request::input('name');
        } else {
            http_response_code(400);    // Bad request
            return "Please provide a name.";
        }
        
        if(Request::has('phone')) {
            $order->phone = Request::input('phone');
        } else {
            http_response_code(400);    // Bad request
            return "Please provide a phone number that has texting.";
        }
        
        if(Request::has('address')) {
            $order->address = Request::input('address');
        }
        
		date_default_timezone_set("America/Los_Angeles");
        $item->ordered_on = (new DateTime())->format('Y-m-d H:i:s');
        
        // Check for valid JSON array
        if(Request::has('item_array')) {
            $itemArray = Request::input('item_array');
            if(substr($itemArray, -1) == "]" &&     // First character [
                substr($itemArray, 0, 1) == "[" &&  // Last character ]
                json_decode($itemArray)) {          // Is valid JSON
            $order->item_array = Request::input('item_array');
            }
        } else {
            http_response_code(400);    // Bad request
            return "Please provide items to buy.";
        }
        
        $order->save();
        
        return "Success";
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
		// Only toggle paid status
		$order = Order::find($id);
		$order->paid = !$order->paid;
		$order->save();
		
		return Redirect::to('orders');
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
	
	/**
	 * Complete all orders
	 */
	public function payAll() {
	    // Get unpaid orders by default
	    $orders = Order::where('paid', '=', 0)->get();
	    foreach($orders as $order) {
	        $order->paid = 1;
	        $order->save();
        }
        return Redirect::to('orders');
	}

}
