@extends('web-installer::layouts.setup')
@section('title', trans('Welcome'))
@section('content')
    <div class="alert alert-warning" role="alert">
        {{ trans('Welcome to installation setup! Please follow the instructions to install the application step by step.') }}
    </div>
    <form action="{{ route('web-installer.validate-purchase-code') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="purchaseCode" class="form-label">{{ trans('Purchase Code') }}</label>
            <input type="text" class="form-control @error('purchase_code') is-invalid @enderror" id="purchaseCode" placeholder="Purchase code" name="purchase_code" value="{{ old('purchase_code', session('installation.purchases.purchase_code')) }}">
            @error('purchase_code')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        @include('web-installer::pages.partials.forms.envato-validation-purchase-code-form')
        <div class="d-grid gap-2 mt-3">
            <button class="btn btn-success" type="submit"><i class="fa-solid fa-list-check"></i> {{ trans('Validate Code') }}</button>
            @if(session('installation.purchases.verified'))
                <a class="btn btn-success" href="{{ route('web-installer.check-requirements') }}" type="button"><i class="fa-solid fa-list-check"></i> {{ trans('Check Requirements') }}</a>
            @endif
        </div>
    </form>

@endsection
