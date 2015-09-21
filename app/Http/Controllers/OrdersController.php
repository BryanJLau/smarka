<?php namespace App\Http\Controllers;

use App\Order;
use App\Item;   // To look up prices
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
		// For this week's batch of items
		$requiredItems = array();
		
		if(isset($_GET['all'])) {
		    // Want all orders for some reason, use with caution
		    $orders = Order::all();
		    foreach($orders as $order) {
		        // Parse it so that the template knows how to use it
	            $order->item_array = JSON_decode($order->item_array);
            }
		}
		else {
		    // Get unpaid orders by default
		    $orders = Order::where('paid', '=', 0)->get();
		
		    // Calculate totals for this batch
		    foreach($orders as $order) {
	            $order->item_array = JSON_decode($order->item_array);
	            foreach($order->item_array as $item) {
	                if(array_key_exists($item->name, $requiredItems)) {
	                    // The item was already added, just add the quantity
	                    $requiredItems[$item->name] += $item->qty;
	                }
	                else {
	                    // The item isn't added yet, make a new entry
	                    $requiredItems[$item->name] = $item->qty;
	                }
	            }
	        }
		}
	    
        return view('orders.index')->with(array(
            'orders' => $orders,
            'requiredItems' => $requiredItems
        ));
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
        $emailData = array();
		$order = new Order;
        
        if(Request::has('name')) {
            $order->name = Request::input('name');
            $emailData['name'] = $order->name;
        } else {
            http_response_code(400);    // Bad request
            return "Please provide a name.";
        }
        
        if(Request::has('phone')) {
            $order->phone = Request::input('phone');
            $emailData['phone'] = $order->phone;
        } else {
            http_response_code(400);    // Bad request
            return "Please provide a phone number that has texting.";
        }
        
        if(Request::has('address')) {
            $order->address = Request::input('address');
            $emailData['address'] = $order->address;
        }
        
        if(Request::has('location')) {
            $order->location = Request::input('location');
            $emailData['location'] = $order->location;
        } else {
            http_response_code(400);    // Bad request
            return "Please provide a location.";
        }
        
        if(Request::has('email')) {
            if(filter_var(Request::input('email'), 
                    FILTER_VALIDATE_EMAIL) === false) {
                return "Please enter a valid email.";
            }
            $order->email = Request::input('email');
        }
        
		date_default_timezone_set("America/Los_Angeles");
		$dt = date('Y-m-d H:i:s');
        $order->ordered_on = $dt;
        
        $order->total = 0;
        
        // Check for valid JSON array
        if(Request::has('item_array')) {
            $itemArray = Request::input('item_array');
            $emailData['itemArray'] = array();
            if(substr($itemArray, -1) == "]" &&     // First character [
                substr($itemArray, 0, 1) == "[" &&  // Last character ]
                json_decode($itemArray)) {          // Is valid JSON
                
                $order->item_array = Request::input('item_array');
                
                foreach(json_decode($itemArray) as $orderItem) {
                    $menuItem = Item::where('name', $orderItem->name)->first();
                    $itemTotal = $orderItem->qty * $menuItem->price;
                    $orderItem->itemTotal = $itemTotal;
                    // We need all item information to put in the email
                    $emailData['itemArray'][] = $orderItem;
                    $order->total += $itemTotal;
                }
            } else {
                return Request::input('item_array');
            }
        } else {
            http_response_code(400);    // Bad request
            return "Please provide items to buy.";
        }
        
        $order->save();
        
        $emailData['total'] = $order->total;
        
        if(Request::has('email')) {
            \Mail::send('emails.receipt', $emailData, function ($message) {
                $message->from("prettyching821@hotmail.com", "Hom's Kitchen");
                $message->subject('Hom\'s Kitchen Order Confirmation');
                $message->cc('homskitchen@outlook.com');
                $message->to(Request::input('email'));
            });
        }
        
        
        return "Thank you for your order!";
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
