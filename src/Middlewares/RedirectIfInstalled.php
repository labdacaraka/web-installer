<?php

namespace Labdacaraka\WebInstaller\Middlewares;

class RedirectIfInstalled
{
    public function handle($request, \Closure $next)
    {
        $envatoUsername = config('web-installer.marketplace.envato.username');
        $purchaseCode = config('web-installer.marketplace.envato.purchase_code');
        $itemId = config('web-installer.marketplace.envato.item_id');
        if ($envatoUsername && $purchaseCode && $itemId) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => trans('Already Installed')], 403);
            }

            return redirect('/');
        }

        return $next($request);
    }
}
