<?php

namespace Labdacaraka\WebInstaller\Middlewares;

use Illuminate\Http\Request;

class InstallCheck
{
    public function handle(Request $request, \Closure $next)
    {
        if($request->is('install') || $request->is('install/*')){
            return $next($request);
        }
        $envatoUsername = config('web-installer.marketplace.envato.username');
        $purchaseCode = config('web-installer.marketplace.envato.purchase_code');
        $itemId = config('web-installer.marketplace.envato.item_id');
        if (! $envatoUsername || ! $purchaseCode || ! $itemId) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => trans('You need install first')], 403);
            }

            return redirect('/install');
        }

        return $next($request);
    }
}
