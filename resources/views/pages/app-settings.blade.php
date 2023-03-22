@extends('web-installer::layouts.setup')
@section('title', trans('Application Setup'))
@section('content')
    <form action="{{ route('web-installer.app-setting-store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="applicationName" class="form-label">{{ trans('App Name') }}</label>
            <input type="text" class="form-control @error('app_name') is-invalid @enderror" id="applicationName"
                   placeholder="App Name" name="app_name"
                   value="{{ old('app_name', session('installation.app_settings.app_name') ?? config('app.name')) }}">
            @error('app_name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="applicationUrl" class="form-label">{{ trans('App Url') }}</label>
            <input type="text" class="form-control @error('app_url') is-invalid @enderror" id="applicationUrl" placeholder="App Url" name="app_url" value="{{ old('app_url', session('installation.app_settings.app_url') ?? config('app.url')) }}">
            @error('app_url')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="applicationDebug" class="form-label">{{ trans('App Debug') }}</label>
            <div class="form-check form-switch">
                <input class="form-check-input @error('app_debug') is-invalid @enderror" type="checkbox" name="app_debug" role="switch" id="applicationDebug" @checked(old('app_debug', session('installation.app_settings.app_debug') ?? config('app.debug') ))>
                <label class="form-check-label" for="flexSwitchCheckDefault">{{ trans('Debug True') }}</label>
            </div>
            @error('app_debug')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="applicationEnvironment" class="form-label">{{ trans('App Environment') }}</label>
            <select id="applicationEnvironment" class="form-select @error('app_env') is-invalid @enderror" aria-label="App Environment" name="app_env">
                <option value="">{{ trans('Select environment') }}</option>
                @foreach($environments as $environment)
                    <option
                        value="{{ $environment }}" @selected(old('app_environment', session('installation.app_settings.app_environment') ?? config('app.env') ) == $environment)>{{ ucwords($environment) }}</option>
                @endforeach
            </select>
            @error('app_env')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="applicationTimezone" class="form-label">{{ trans('App Timezone') }}</label>
            <select id="applicationTimezone" class="form-select select2" aria-label="App Timezone" name="app_timezone">
                <option value="">{{ trans('Select timezone') }}</option>
                @foreach($timezoneList as $timezone)
                    <option value="{{ $timezone }}" @selected(old('app_timezone', session('installation.app_settings.app_timezone') ?? config('app.timezone') ) == $timezone)>{{ $timezone }}</option>
                @endforeach
            </select>
            @error('app_environment')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="applicationLocale" class="form-label">{{ trans('App Locale') }}</label>
            <input type="text" class="form-control @error('app_locale') is-invalid @enderror" id="applicationLocale" placeholder="App Locale" name="app_locale" value="{{ old('app_locale', session('installation.app_settings.app_locale') ?? config('app.locale')) }}">
            @error('app_locale')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="d-grid gap-2 mt-3">
            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-save"></i> {{ trans('Save & Continue') }}</button>
            <a class="btn btn-warning" href="{{ route('web-installer.check-permissions') }}" type="button"><i class="fa-solid fa-circle-chevron-left"></i> {{ trans('Previous') }}</a>
        </div>
    </form>
@endsection
