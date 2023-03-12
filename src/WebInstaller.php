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
     *
     * @param $purchaseCode
     * @param $envatoUsername
     * @param $itemId
     * @param string $marketplace
     * @return bool
     */
    public function verifyPurchaseCode($purchaseCode = null, $envatoUsername = null, $itemId = null, string $marketplace = 'envato'): bool
    {
        if(!$purchaseCode){
            $purchaseCode = config('web-installer.marketplace.' . $marketplace . '.purchase_code');
        }
        if(!$envatoUsername){
            $envatoUsername = config('web-installer.marketplace.' . $marketplace . '.envato_username');
        }
        if(!$itemId){
            $itemId = config('web-installer.marketplace.' . $marketplace . '.item_id');
        }
        $sandBoxMode = config('web-installer.marketplace.' . $marketplace . '.sandbox');
        $personalToken = $sandBoxMode ? config('web-installer.marketplace.' . $marketplace . '.sandbox_token') : config('web-installer.marketplace.' . $marketplace . '.token');
        $this->apiUrl = config('web-installer.marketplace.' . $marketplace . '.api_url');
        $this->sandboxUrl = config('web-installer.marketplace.' . $marketplace . '.sandbox_url');
        $this->verifyEndpoint = config('web-installer.marketplace.' . $marketplace . '.verify_endpoint');
        $this->purchaseCode = $purchaseCode;
        $verifyEndpoint = $this->verifyEndpoint;
        $verifyEndpoint .= $this->purchaseCode;
        $apiUrl = $this->apiUrl;
        if($sandBoxMode) {
            $apiUrl = $this->sandboxUrl;
        }
        $apiUrl .= $verifyEndpoint;
        $response = null;
        if($marketplace == 'envato'){
            $response = Http::contentType('application/json')
                            ->withToken($personalToken)
                            ->withUserAgent('insomnia/2023.1.0')
                            ->timeout(20)
                            ->post($apiUrl);
        }

        if(!$response || $response->failed() || $response->status() != 200){
            return false;
        }
        $response = $response->json();
//        return $response;
        $envatoUsername = 'buyer_username_or_null';
        $itemId = 17022701;
        if($response['item']['id'] != $itemId || $response['buyer'] != $envatoUsername){
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
     *
     * @param $key
     * @param $value
     * @return void
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
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function install(): void
    {
        $this->putPermanentEnv('APP_NAME', session()->get('installation.app_settings.app_name'));
        $this->putPermanentEnv('APP_ENV', session()->get('installation.app_settings.app_env'));
        $this->putPermanentEnv('APP_DEBUG', session()->get('installation.app_settings.app_debug'));
        $this->putPermanentEnv('APP_URL', session()->get('installation.app_settings.app_url'));
        $this->putPermanentEnv('APP_TIMEZONE', session()->get('installation.app_settings.app_timezone'));
//        $this->putPermanentEnv('APP_LOCALE', session()->get('installation.app_settings.app_locale'));
//        $this->putPermanentEnv('APP_FALLBACK_LOCALE', session()->get('installation.app_settings.app_fallback_locale'));
        $this->putPermanentEnv('APP_KEY', session()->get('installation.app_settings.app_key'));
        $this->putPermanentEnv('APP_CIPHER', session()->get('installation.app_settings.app_cipher'));
        $this->putPermanentEnv('DB_CONNECTION', session()->get('installation.database_settings.db_connection'));
        $this->putPermanentEnv('DB_HOST', session()->get('installation.database_settings.db_host'));
        $this->putPermanentEnv('DB_PORT', session()->get('installation.database_settings.db_port'));
        $this->putPermanentEnv('DB_DATABASE', session()->get('installation.database_settings.db_database'));
        $this->putPermanentEnv('DB_USERNAME', session()->get('installation.database_settings.db_username'));
        $this->putPermanentEnv('DB_PASSWORD', session()->get('installation.database_settings.db_password'));

        Artisan::call('key:generate --force');
        Artisan::call('migrate:fresh --seed');
        Artisan::call('storage:link');
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
    }
}
