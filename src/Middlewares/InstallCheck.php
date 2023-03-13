<?php

namespace Labdacaraka\WebInstaller\Middlewares;

use Illuminate\Http\Request;

class InstallCheck
{
    public function handle(Request $request, \Closure $next)
    {
        $envatoUsername = config('web-installer.marketplace.envato.username');
        $purchaseCode = config('web-installer.marketplace.envato.purchase_code');
        $itemId = config('web-installer.marketplace.envato.item_id');
        if(!$envatoUsername || !$purchaseCode || !$itemId){
            return redirect('/install');
        }
        return $next($request);
    }

}
