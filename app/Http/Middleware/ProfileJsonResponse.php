<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProfileJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response =  $next($request);

        // Check if debugbar is enabled
        if ( ! app()->bound('debugbar') || ! app('debugbar')->isEnabled() ) {
            return $response;
        }

        // Profile the json response
        if ( $response instanceof JsonResponse && $request->has('_debug')) {
            // Get the info from debugbar

            // Returns whole data
//            $response->setData(array_merge($response->getData(true), [
//                '_debugbar' => app('debugbar')->getData(true)
//            ]));

            // Only returns queries
            $response->setData(array_merge([
                '_debugbar' => Arr::only(app('debugbar')->getData(), 'queries')
            ], $response->getData(true)));
        }

        return $response;
    }
}
