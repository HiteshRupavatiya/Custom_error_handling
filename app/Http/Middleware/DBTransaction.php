<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DBTransaction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        DB::beginTransaction();
        try {
            $responce = $next($request);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        if ($responce->getStatusCode() > 399) {
            DB::rollBack();
        } else {
            DB::commit();
        }

        return $responce;
    }
}
