<?php

namespace Labdacaraka\WebInstaller\Controllers;

use DateTimeZone;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Labdacaraka\WebInstaller\WebInstaller;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class InstallerController extends Controller
{
    public function index(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('web-installer::pages.welcome');
    }

    public function validatePurchaseCode(Request $request): RedirectResponse
    {
        session()->forget('installation.purchases');

        $rules = [
            'purchase_code' => ['required', 'string'],
            'envato_username' => ['required', 'string'],
            'envato_item_id' => ['required', 'string'],
        ];
        $request->validate($rules);

        $purchaseCode = $request->input('purchase_code');
        $envatoUsername = $request->input('envato_username');
        $itemId = $request->input('envato_item_id');

        $verifyPurchaseCodeStatus = app(WebInstaller::class)->verifyPurchaseCode($purchaseCode, $envatoUsername, $itemId);
        if ($verifyPurchaseCodeStatus) {
            return redirect()->route('web-installer.check-requirements')->with('success', 'Purchase code is valid');
        } else {
            return redirect()->back()->with('error', 'Purchase code is invalid');
        }
    }

    /**
     * Check requirements
     */
    public function checkRequirements(): \Illuminate\Contracts\Foundation\Application|Factory|View|Application|RedirectResponse
    {
        session()->forget('installation.check_requirements');
        if (! session()->has('installation.purchases')) {
            return redirect()->route('web-installer.welcome');
        }
        $requiredPhpExtensions = config('web-installer.required_php_extensions');
        $currentPhpVersion = PHP_VERSION;
        $minimumPhpVersion = config('web-installer.minimum_php_version');
        $phpVersionCompatible = true;
        // Check PHP version
        if (version_compare($currentPhpVersion, $minimumPhpVersion, '<')) {
            $phpVersionCompatible = false;
        }
        // Check PHP extensions
        $phpExtensions = get_loaded_extensions();
        $phpExtensionsCompatible = true;
        $phpExtensionsStatuses = [];
        foreach ($requiredPhpExtensions as $requiredPhpExtension) {
            if (! in_array($requiredPhpExtension, $phpExtensions)) {
                $phpExtensionsCompatible = false;
            }
            $phpExtensionsStatuses[$requiredPhpExtension] = in_array($requiredPhpExtension, $phpExtensions);
        }

        // Check PHP settings
        $requiredPhpSettings = config('web-installer.required_php_settings');
        $phpSettingCompatible = true;
        $phpSettings = [];
        foreach ($requiredPhpSettings as $requiredPhpSetting => $requiredPhpSettingValue) {
            $phpSettings[$requiredPhpSetting] = [
                'current' => ini_get($requiredPhpSetting),
                'required' => $requiredPhpSettingValue,
                'compatible' => ini_get($requiredPhpSetting) >= $requiredPhpSettingValue,
            ];
            if (! $phpSettings[$requiredPhpSetting]['compatible']) {
                $phpSettingCompatible = false;
            }
        }
        // Check if all requirements are compatible
        $allCompatible = $phpVersionCompatible && $phpExtensionsCompatible && $phpSettingCompatible;
        if ($allCompatible) {
            session()->put('installation.check_requirements', true);
        }

        return view('web-installer::pages.check-requirements', compact('minimumPhpVersion', 'currentPhpVersion', 'phpExtensionsStatuses', 'phpSettings', 'phpVersionCompatible', 'phpExtensionsCompatible', 'phpSettingCompatible', 'allCompatible'));
    }

    public function checkPermissions(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        session()->forget('installation.check_permissions');
        if (! session()->has('installation.check_requirements')) {
            return redirect()->route('web-installer.check-requirements');
        }
        $requiredPermissions = config('web-installer.writeable_directories');
        $permissions = [];
        $permissionCompatible = true;
        foreach ($requiredPermissions as $requiredPermission) {
            $permissions[$requiredPermission] = [
                'writable' => is_writable(base_path($requiredPermission)),
                'path' => base_path($requiredPermission),
            ];
            if (! $permissions[$requiredPermission]['writable']) {
                $permissionCompatible = false;
            }
        }
        if ($permissionCompatible) {
            session()->put('installation.check_permissions', true);
        }

        return view('web-installer::pages.check-permissions', compact('permissions', 'permissionCompatible'));
    }

    public function appSettings(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        if (! session()->has('installation.check_permissions')) {
            return redirect()->route('web-installer.check-permissions');
        }
        $environments = config('web-installer.available_environments');
        $timezoneList = DateTimeZone::listIdentifiers();

        return view('web-installer::pages.app-settings', compact('environments', 'timezoneList'));
    }

    /**
     * Store app settings
     */
    public function appSettingStore(Request $request): RedirectResponse
    {
        session()->forget('installation.app_settings');
        $request->validate([
            'app_name' => ['required', 'string'],
            'app_url' => ['required', 'string'],
            'app_env' => ['required', 'string'],
            'app_debug' => ['nullable', 'string'],
            'app_timezone' => ['required', 'string'],
        ]);
        $appSettings = $request->only(['app_name', 'app_url', 'app_env', 'app_timezone']);
        $appSettings['app_debug'] = $request->has('app_debug');
        session()->put('installation.app_settings', $appSettings);

        return redirect()->route('web-installer.database-settings')->with('success', 'App settings saved successfully');
    }

    public function databaseSettings(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        if (! session()->has('installation.app_settings')) {
            return redirect()->route('web-installer.app-settings');
        }
        $dbConnections = config('database.connections');

        return view('web-installer::pages.database-settings', compact('dbConnections'));
    }

    public function databaseSettingStore(Request $request): RedirectResponse
    {
        session()->forget('installation.database_settings');
        $dbConnections = config('database.connections');
        $dbConnectionExceptSqlite = array_diff(array_keys($dbConnections), ['sqlite']);
        $request->validate([
            'db_connection' => ['required', 'string', Rule::in(array_keys($dbConnections))],
            'db_host' => ['nullable', Rule::requiredIf(in_array($request->input('db_connection'), $dbConnectionExceptSqlite)), 'string'],
            'db_port' => ['nullable', Rule::requiredIf(in_array($request->input('db_connection'), $dbConnectionExceptSqlite)), 'string'],
            'db_name' => ['nullable', Rule::requiredIf(in_array($request->input('db_connection'), $dbConnectionExceptSqlite)), 'string'],
            'db_username' => ['nullable', Rule::requiredIf(in_array($request->input('db_connection'), $dbConnectionExceptSqlite)), 'string'],
            'db_password' => ['nullable', Rule::requiredIf(in_array($request->input('db_connection'), $dbConnectionExceptSqlite)), 'string'],
            'db_url' => ['nullable', Rule::requiredIf($request->input('db_connection') == 'sqlite'), 'string'],
        ]);
        $dbSettings = $request->only(['db_connection', 'db_host', 'db_port', 'db_name', 'db_username', 'db_password', 'db_url']);
        // Check database connection
        try {
            // Set config
            config()->set('database.default', $dbSettings['db_connection']);
            if ($dbSettings['db_connection'] == 'sqlite') {
                config()->set('database.connections.'.$dbSettings['db_connection'].'.url', $dbSettings['db_url']);
            } else {
                config()->set('database.connections.'.$dbSettings['db_connection'].'.host', $dbSettings['db_host']);
                config()->set('database.connections.'.$dbSettings['db_connection'].'.port', $dbSettings['db_port']);
                config()->set('database.connections.'.$dbSettings['db_connection'].'.database', $dbSettings['db_name']);
                config()->set('database.connections.'.$dbSettings['db_connection'].'.username', $dbSettings['db_username']);
                config()->set('database.connections.'.$dbSettings['db_connection'].'.password', $dbSettings['db_password']);
            }
            DB::connection($dbSettings['db_connection'])->getPdo();
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        session()->put('installation.database_settings', $dbSettings);

        return redirect()->route('web-installer.database-settings')->with('success', 'Database connection successful');
    }

    public function final(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        if (! session()->has('installation.database_settings')) {
            return redirect()->route('web-installer.database-settings');
        }

        return view('web-installer::pages.final');
    }

    /**
     * Final installation
     *
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function runInstall(Request $request): \Illuminate\Http\JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }
        try {
            $output = app(WebInstaller::class)->install();

            return response()->json(['success' => true, 'message' => trans('Installation completed successfully'), 'output' => $output]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
