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
     * Write to env file
     */
    public function putPermanentEnv($key, $value): void
    {
        $path = app()->environmentFilePath();

        $escapedOne = preg_quote('="'.env($key).'"', '/');
        $escapedTwo = preg_quote('='.env($key), '/');

        file_put_contents($path, preg_replace(
            "/^{$key}{$escapedOne}|^{$key}{$escapedTwo}/m",
            "$key=\"$value\"",
            file_get_contents($path)
        ));
    }

    /**
     * Install application
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function install(): string
    {
        $this->putPermanentEnv('APP_NAME', session()->get('installation.app_settings.app_name'));
        $this->putPermanentEnv('APP_ENV', session()->get('installation.app_settings.app_env'));
        $this->putPermanentEnv('APP_DEBUG', session()->get('installation.app_settings.app_debug'));
        $this->putPermanentEnv('APP_URL', session()->get('installation.app_settings.app_url'));
        $this->putPermanentEnv('APP_TIMEZONE', session()->get('installation.app_settings.app_timezone'));
//        $this->putPermanentEnv('APP_LOCALE', session()->get('installation.app_settings.app_locale'));
//        $this->putPermanentEnv('APP_FALLBACK_LOCALE', session()->get('installation.app_settings.app_fallback_locale'));
        $this->putPermanentEnv('ENVATO_PURCHASE_CODE', session()->get('installation.purchases.purchase_code'));
        $this->putPermanentEnv('ENVATO_USERNAME', session()->get('installation.purchases.envato_username'));
        $this->putPermanentEnv('ENVATO_ITEM_ID', session()->get('installation.purchases.envato_item_id'));
        $this->putPermanentEnv('DB_CONNECTION', session()->get('installation.database_settings.db_connection'));
        if (session()->get('installation.database_settings.db_connection') == 'sqlite') {
            $this->putPermanentEnv('DATABASE_URL', session()->get('installation.database_settings.db_url'));
            $this->putPermanentEnv('DB_HOST', '');
            $this->putPermanentEnv('DB_PORT', '');
            $this->putPermanentEnv('DB_DATABASE', '');
            $this->putPermanentEnv('DB_USERNAME', '');
            $this->putPermanentEnv('DB_PASSWORD', '');
        } else {
            $this->putPermanentEnv('DATABASE_URL', '');
            $this->putPermanentEnv('DB_HOST', session()->get('installation.database_settings.db_host'));
            $this->putPermanentEnv('DB_PORT', session()->get('installation.database_settings.db_port'));
            $this->putPermanentEnv('DB_DATABASE', session()->get('installation.database_settings.db_name'));
            $this->putPermanentEnv('DB_USERNAME', session()->get('installation.database_settings.db_username'));
            $this->putPermanentEnv('DB_PASSWORD', session()->get('installation.database_settings.db_password'));
        }
        Artisan::call('web-installer');
        Artisan::output();

        return 'success';
    }
}
