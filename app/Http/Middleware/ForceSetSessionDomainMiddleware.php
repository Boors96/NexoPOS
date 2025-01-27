<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class ForceSetSessionDomainMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /**
         * Set version to close setup
         */
        $domain = Str::replaceFirst( 'http://', '', url( '/' ) );
        $domain = Str::replaceFirst( 'https://', '', $domain );
        $domain = explode( ':', $domain )[0];
        
        if ( ! env( 'SESSION_DOMAIN', false ) ) {
            DotenvEditor::load();
            DotenvEditor::setKey( 'SESSION_DOMAIN', Str::replaceFirst( 'http://', '', explode( ':', $domain )[0] ) );
            DotenvEditor::save();
        }
        
        if ( ! env( 'SANCTUM_STATEFUL_DOMAINS', false ) ) {
            DotenvEditor::load();
            DotenvEditor::setKey( 'SANCTUM_STATEFUL_DOMAINS', collect([ $domain, 'nexopos.test' ])->unique()->join(',') );
            DotenvEditor::save();
        }

        return $next($request);
    }
}
