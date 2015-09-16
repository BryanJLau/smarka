<?php namespace App\Http\Middleware;

// Source: http://laravel.io/forum/11-14-2014-disabling-the-csrf-middleware-in-laravel-5

class VerifyCsrf extends \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken
{
    /**
     * Routes we want to exclude.
     *
     * @var array
     */
    protected $routes = [
            'orders',
    ];

     /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, \Closure $next)
    {
        if ($this->isReading($request) 
            || $this->excludedRoutes($request) 
            || $this->tokensMatch($request))
        {
            return $this->addCookieToResponse($request, $next($request));
        }

        throw new \TokenMismatchException;
    }

    /**
     * This will return a bool value based on route checking.

     * @param  Request $request
     * @return boolean
     */
    protected function excludedRoutes($request)
    {
        foreach($this->routes as $route)
            if ($request->is($route))
                return true;

            return false;
    }

}
