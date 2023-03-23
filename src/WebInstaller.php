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
                            ->timeout(20)
                            ->get($apiUrl);
        }

        if (! $response || $response->failed() || $response->status() != 200) {
            return false;
        }
        $response = $response->json();
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
        $dbConnection = session()->get('installation.database_settings.db_connection');
        $env = new Env();
        $env->setValue('APP_NAME', session()->get('installation.app_settings.app_name'));
        $env->setValue('APP_ENV', session()->get('installation.app_settings.app_env'));
        $env->setValue('APP_DEBUG', session()->get('installation.app_settings.app_debug') == 1);
        $env->setValue('APP_URL', session()->get('installation.app_settings.app_url'));
        $env->setValue('APP_TIMEZONE', session()->get('installation.app_settings.app_timezone'));
        $env->setValue('APP_LOCALE', session()->get('installation.app_settings.app_locale'));
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

            config()->set('database.connections.'.$dbConnection.'.url', session()->get('installation.database_settings.db_url'));
        } else {
            $env->setValue('DATABASE_URL', '');
            $env->setValue('DB_HOST', session()->get('installation.database_settings.db_host'));
            $env->setValue('DB_PORT', session()->get('installation.database_settings.db_port'));
            $env->setValue('DB_DATABASE', session()->get('installation.database_settings.db_name'));
            $env->setValue('DB_USERNAME', session()->get('installation.database_settings.db_username'));
            $env->setValue('DB_PASSWORD', session()->get('installation.database_settings.db_password'));

            config()->set('database.connections.'.$dbConnection.'.host', session()->get('installation.database_settings.db_host'));
            config()->set('database.connections.'.$dbConnection.'.port', session()->get('installation.database_settings.db_port'));
            config()->set('database.connections.'.$dbConnection.'.database', session()->get('installation.database_settings.db_name'));
            config()->set('database.connections.'.$dbConnection.'.username', session()->get('installation.database_settings.db_username'));
            config()->set('database.connections.'.$dbConnection.'.password', session()->get('installation.database_settings.db_password'));
        }
        session()->forget('installation');

        // wait for 5 seconds to make sure the env file is updated
        sleep(5);
        Artisan::call('web-installer', ['-n' => true]);

        return Artisan::output();
    }

    public function rollbackInstall(): string
    {
        $env = new Env();
        $env->setValue('ENVATO_PURCHASE_CODE', '');
        $env->setValue('ENVATO_USERNAME', '');
        $env->setValue('ENVATO_ITEM_ID', '');
        $env->setValue('MARKETPLACE', '');
        Artisan::call('optimize:clear', ['-n' => true]);

        return Artisan::output();
    }
}
