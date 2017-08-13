<?php

namespace App\Http\Middleware;

use App\Exceptions\JsonException;
use Closure;
use Illuminate\Http\Request;

class ValidateJsonInput
{
    /**
     * Handle an incoming request.
     *
     * @source https://github.com/guzzle/guzzle/blob/master/src/functions.php#L300
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     * @throws \App\Exceptions\JsonException
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isJson()) {
            json_decode($request->getContent());

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw JsonException::invalidJson(json_last_error_msg());
            }
        }

        return $next($request);
    }
}
