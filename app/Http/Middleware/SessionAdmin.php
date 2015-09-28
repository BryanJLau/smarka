<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;

class SessionAdmin {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
	    // Check for session authentication
	    if(session('password') == getenv('MAIL_PASSWORD') &&
	            session('email') == getenv('MAIL_USERNAME'))
		    return $next($request);
	    else
	        // We don't need to be descriptive because only admins should
	        // ever get this response.
	        return Response::make("401 Unauthorized", 401);
	}

}
