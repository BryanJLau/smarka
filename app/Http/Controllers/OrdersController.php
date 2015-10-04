<?php namespace App\Http\Controllers;

use App\Order;
use App\Item;   // To look up prices
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;

class OrdersController extends Controller {
    // Middleware
    public function __construct()
    {
        $this->middleware('admin.session', ['only' => ['index', 'update', 'payAll']]);
    }

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
		    $orders = Order::orderBy('id', 'desc')->get();
		    foreach($orders as $order) {
		        // Parse it so that the template knows how to use it
	            $order->item_array = JSON_decode($order->item_array);
            }
		}
		else {
		    // Get unpaid orders by default
		    $orders = Order::where('paid', '=', 0)->orderBy('id', 'desc')
		        ->get();
		
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
	    
	    return Response::json(array(
            'orders' => $orders,
            'requiredItems' => $requiredItems
        ));
        /*
        return view('orders.index')->with(array(
            'orders' => $orders,
            'requiredItems' => $requiredItems
        ));*/
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
		date_default_timezone_set("America/Los_Angeles");
	    // Ordering should not happen between 9 PM Friday through Saturday
	    if((date('l') == 'Friday' && date('G') >= 21) ||
	        date('l') == 'Saturday') {
            return Response::make("[]", 503);   // Service Unavailable
        }
		
        $emailData = array();
		$order = new Order;
        
        if(Request::has('name')) {
            $order->name = Request::input('name');
            $emailData['name'] = $order->name;
        } else {
            return Response::make("Please provide a name.", 400);
        }
        
        if(Request::has('phone')) {
            $order->phone = Request::input('phone');
            $emailData['phone'] = $order->phone;
        } else {
            return Response::make("Please provide a phone number that has \
                texting.", 400);
        }
        
        if(Request::has('notes')) {
            $order->notes = Request::input('notes');
            $emailData['notes'] = $order->notes;
        }
        else {
            $order->notes = $emailData['notes'] = "";
        }
        
        if(Request::has('location')) {
            $order->location = Request::input('location');
            $emailData['location'] = $order->location;
        } else {
            return Response::make("Please provide a location.", 400);
        }
        
        if(Request::has('email')) {
            if(filter_var(Request::input('email'), 
                    FILTER_VALIDATE_EMAIL) === false) {
                return "Please enter a valid email.";
            }
            $order->email = $emailData['email'] = Request::input('email');
        } else {
            $order->email = $emailData['email'] = "";
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
            return Response::make("Please provide items to buy.", 400);
        }
        
        $order->save();
        
        $emailData['total'] = $order->total;
        
        if(Request::has('email')) {
            \Mail::send('emails.receipt', $emailData, function ($message) {
                $message->from(
                    getenv('MAIL_USERNAME') ?
                        getenv('MAIL_USERNAME') : env('MAIL_USERNAME'),
                    "Hom's Kitchen"
                );
                $message->subject('Hom\'s Kitchen Order Confirmation');
                $message->to(Request::input('email'));
            });
        }
        
        \Mail::send('emails.receipt', $emailData, function ($message) {
            $message->from(
                getenv('MAIL_USERNAME') ?
                    getenv('MAIL_USERNAME') : env('MAIL_USERNAME'),
                "Hom's Kitchen"
            );
            $message->subject('Hom\'s Kitchen Order Confirmation');
            $message->to('homskitchen@outlook.com');
        });
        
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
