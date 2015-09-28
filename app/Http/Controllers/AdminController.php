<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AdminController extends Controller {
    // Middleware
    public function __construct()
    {
        $this->middleware('admin.session', ['only' => ['getIndex']]);
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		//
		return view('admin.index');
	}

    public function getLogin()
    {
        return view('admin.login');
    }
    
    public function postLogin()
    {
        if(isset($_POST['password']) && isset($_POST['email'])) {
            if($_POST['password'] == getenv('MAIL_PASSWORD') &&
                    $_POST['email'] == getenv('MAIL_USERNAME')) {
                session([
                    'email' => $_POST['email'],
                    'password' => $_POST['password']
                ]);
                return Redirect::to('admin');
            }
        }
        return view('admin.login');
    }
}
