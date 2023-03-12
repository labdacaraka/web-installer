@extends('web-installer::layouts.setup')
@section('title', trans('Final Step'))
@section('content')
    <div class="alert alert-warning" role="alert">
        {{ trans('Last Step. Run All Setting!') }}
    </div>
    <form action="{{ route('web-installer.validate-purchase-code') }}" method="POST">
        @csrf
        <div class="d-grid gap-2 mt-3">
            @if(session('installation.database_settings'))
                <a class="btn btn-primary" type="submit"><i class="fa-solid fa-circle-play"></i> {{ trans('Install Now') }}</a>
            @endif
        </div>
    </form>

@endsection
