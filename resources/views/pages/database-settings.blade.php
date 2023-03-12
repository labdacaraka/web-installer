@extends('web-installer::layouts.setup')
@section('title', 'Database Setting')
@section('content')
    <form action="{{ route('web-installer.database-settings-store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="databaseConnection" class="form-label">{{ trans('Database Connection') }}</label>
            <select id="databaseConnection" class="form-select select2" aria-label="Database Connection" name="db_connection">
                <option value="">{{ trans('Select DB Connection') }}</option>
                @foreach($dbConnections as $connection => $value)
                    <option value="{{ $connection }}" @selected(old('db_connection', session('installation.database_settings.db_connection') ?? config('database.default') ) == $connection)>{{ ucwords($connection) }}</option>
                @endforeach
            </select>
            @error('db_connection')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="databaseHost" class="form-label">{{ trans('Database Host') }}</label>
            <input id="databaseHost" name="db_host" type="text" class="form-control @error('db_host') is-invalid @enderror" placeholder="Database Host"  value="{{ old('db_host', session('installation.database_settings.db_host') ?? 'localhost') }}">
            @error('db_host')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="databasePort" class="form-label">{{ trans('Database Port') }}</label>
            <input id="databasePort" name="db_port" type="text" class="form-control @error('db_port') is-invalid @enderror" placeholder="Database Port"  value="{{ old('db_port', session('installation.database_settings.db_port') ?? '3306') }}">
            @error('db_port')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="databaseName" class="form-label">{{ trans('Database Name') }}</label>
            <input id="databaseName" name="db_name" type="text" class="form-control @error('db_name') is-invalid @enderror" placeholder="Database Name"  value="{{ old('db_name', session('installation.database_settings.db_name')) }}">
            @error('db_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="databaseUsername" class="form-label">{{ trans('Database Username') }}</label>
            <input id="databaseUsername" name="db_username" type="text" class="form-control @error('db_username') is-invalid @enderror" placeholder="Database Username"  value="{{ old('db_username', session('installation.database_settings.db_username')) }}">
            @error('db_username')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="databasePassword" class="form-label">{{ trans('Database Password') }}</label>
            <input id="databasePassword" name="db_password" type="text" class="form-control @error('db_password') is-invalid @enderror" placeholder="Database Password"  value="{{ old('db_password', session('installation.database_settings.db_password')) }}">
            @error('db_password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3 d-none">
            <label for="databaseUrl" class="form-label">{{ trans('Database Url') }}</label>
            <input id="databaseUrl" name="db_url" type="text" class="form-control @error('db_url') is-invalid @enderror" placeholder="Database Url"  value="{{ old('db_url', session('installation.database_settings.db_url')) }}" disabled>
            @error('db_url')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>


        <div class="d-grid gap-2 mt-3">
            <a class="btn btn-warning" href="{{ route('web-installer.check-permissions') }}" type="button"><i class="fa-solid fa-chevron-left"></i> {{ trans('Previous') }}</a>
            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-save"></i> {{ trans('Save & Continue') }}</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('change', '#databaseConnection', function () {
                let connection = $(this).val();
                let databaseUrl = $('#databaseUrl');
                let databaseHost = $('#databaseHost');
                let databasePort = $('#databasePort');
                let databaseName = $('#databaseName');
                let databaseUsername = $('#databaseUsername');
                let databasePassword = $('#databasePassword');
                if (connection === 'sqlite') {
                    databaseUrl.parent().removeClass('d-none');
                    databaseHost.parent().addClass('d-none');
                    databasePort.parent().addClass('d-none');
                    databaseName.parent().addClass('d-none');
                    databaseUsername.parent().addClass('d-none');
                    databasePassword.parent().addClass('d-none');

                    databaseHost.val('');
                    databasePort.val('');
                    databaseName.val('');
                    databaseUsername.val('');
                    databasePassword.val('');

                    databaseUrl.prop('disabled',false);
                    databaseHost.prop('disabled', true);
                    databasePort.prop('disabled', true);
                    databaseName.prop('disabled', true);
                    databaseUsername.prop('disabled', true);
                    databasePassword.prop('disabled', true);
                }else{
                    databaseUrl.parent().addClass('d-none');
                    databaseHost.parent().removeClass('d-none');
                    databasePort.parent().removeClass('d-none');
                    databaseName.parent().removeClass('d-none');
                    databaseUsername.parent().removeClass('d-none');
                    databasePassword.parent().removeClass('d-none');

                    databaseUrl.val('');
                    databaseUrl.prop('disabled',true);
                    databaseHost.prop('disabled', false);
                    databasePort.prop('disabled', false);
                    databaseName.prop('disabled', false);
                    databaseUsername.prop('disabled', false);
                    databasePassword.prop('disabled', false);

                }
            });
        });
    </script>
@endpush
