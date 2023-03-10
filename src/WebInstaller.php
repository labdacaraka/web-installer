<?php

namespace Labdacaraka\WebInstaller;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class WebInstaller
{
    protected string $apiUrl;

    protected string $sandboxUrl;

    protected string $verifyEndpoint;

    protected string $purchaseCode;

    /**
     * Verify purchase code
     */
    public function verifyPurchaseCode($purchaseCode = null, $envatoUsername = null, $itemId = null, string $marketplace = 'envato'): bool
    {
        if (! $purchaseCode) {
            $purchaseCode = config('web-installer.marketplace.'.$marketplace.'.purchase_code');
        }
        if (! $envatoUsername) {
            $envatoUsername = config('web-installer.marketplace.'.$marketplace.'.envato_username');
        }
        if (! $itemId) {
            $itemId = config('web-installer.marketplace.'.$marketplace.'.item_id');
        }
        $sandBoxMode = config('web-installer.marketplace.'.$marketplace.'.sandbox');
        $personalToken = $sandBoxMode ? config('web-installer.marketplace.'.$marketplace.'.sandbox_token') : config('web-installer.marketplace.'.$marketplace.'.token');
        $this->apiUrl = config('web-installer.marketplace.'.$marketplace.'.api_url');
        $this->sandboxUrl = config('web-installer.marketplace.'.$marketplace.'.sandbox_url');
        $this->verifyEndpoint = config('web-installer.marketplace.'.$marketplace.'.verify_endpoint');
        $this->purchaseCode = $purchaseCode;
        $verifyEndpoint = $this->verifyEndpoint;
        $verifyEndpoint .= $this->purchaseCode;
        $apiUrl = $this->apiUrl;
        if ($sandBoxMode) {
            $apiUrl = $this->sandboxUrl;
        }
        $apiUrl .= $verifyEndpoint;
        $response = null;
        if ($marketplace == 'envato') {
            $response = Http::contentType('application/json')
                            ->withToken($personalToken)
                            ->withUserAgent('insomnia/2023.1.0')
                            ->timeout(20)
                            ->post($apiUrl);
        }

        if (! $response || $response->failed() || $response->status() != 200) {
            return false;
        }
        $response = $response->json();
//        $envatoUsername = 'buyer_username_or_null';
//        $itemId = 17022701;
        if ($response['item']['id'] != $itemId || $response['buyer'] != $envatoUsername) {
            return false;
        }
        session()->put('installation.purchases', [
            'purchase_code' => $this->purchaseCode,
            'envato_username' => $envatoUsername,
            'envato_item_id' => $itemId,
            'marketplace' => $marketplace,
            'verified' => true,
        ]);

        return true;
    }

    /**
     * Install application
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function install(): string
    {
        $env = new Env();
        $env->setValue('APP_NAME', session()->get('installation.app_settings.app_name'));
        $env->setValue('APP_ENV', session()->get('installation.app_settings.app_env'));
        $env->setValue('APP_DEBUG', session()->get('installation.app_settings.app_debug') == 1);
        $env->setValue('APP_URL', session()->get('installation.app_settings.app_url'));
        $env->setValue('APP_TIMEZONE', session()->get('installation.app_settings.app_timezone'));
        $env->setValue('ENVATO_PURCHASE_CODE', session()->get('installation.purchases.purchase_code'));
        $env->setValue('ENVATO_USERNAME', session()->get('installation.purchases.envato_username'));
        $env->setValue('ENVATO_ITEM_ID', session()->get('installation.purchases.envato_item_id'));
        $env->setValue('MARKETPLACE', session()->get('installation.purchases.marketplace'));
        $env->setValue('DB_CONNECTION', session()->get('installation.database_settings.db_connection'));
        if (session()->get('installation.database_settings.db_connection') == 'sqlite') {
            $env->setValue('DATABASE_URL', session()->get('installation.database_settings.db_url'));
            $env->setValue('DB_HOST', '');
            $env->setValue('DB_PORT', '');
            $env->setValue('DB_DATABASE', '');
            $env->setValue('DB_USERNAME', '');
            $env->setValue('DB_PASSWORD', '');
        } else {
            $env->setValue('DATABASE_URL', '');
            $env->setValue('DB_HOST', session()->get('installation.database_settings.db_host'));
            $env->setValue('DB_PORT', session()->get('installation.database_settings.db_port'));
            $env->setValue('DB_DATABASE', session()->get('installation.database_settings.db_name'));
            $env->setValue('DB_USERNAME', session()->get('installation.database_settings.db_username'));
            $env->setValue('DB_PASSWORD', session()->get('installation.database_settings.db_password'));
        }
        session()->forget('installation');
        Artisan::call('web-installer');

        return Artisan::output();
    }
}
