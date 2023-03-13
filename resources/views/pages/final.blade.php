@extends('web-installer::layouts.setup')
@section('title', trans('Final Step'))
@section('content')
    <div id="ajaxAlertContainer">
    </div>
    <div class="alert alert-warning" role="alert">
        {{ trans('Last Step. Run All Setting!') }}
    </div>
    <form id="ajaxFormRunInstall" action="{{ route('web-installer.run-install') }}" method="POST">
        @csrf
        <div class="d-grid gap-2 mt-3">
            @if(session('installation.database_settings'))
                <button class="btn btn-primary" type="submit"><i class="fa-solid fa-circle-play"></i> {{ trans('Install Now') }}</button>
            @endif
        </div>
    </form>
@endsection
@push('scripts')
    <script>
        const alertHtml = (type, message) => {
            return `<div class="alert alert-${type}" role="alert">${message}</div>`;
        };
        $(document).on('submit', '#ajaxFormRunInstall', function (e) {
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action');
                let method = form.attr('method');
                let data = form.serialize();
                const buttonSubmit = form.find('button[type="submit"]');
                buttonSubmit.prop('disabled', true);
                // add button loading
                buttonSubmit.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${buttonSubmit.html()}`);
                $.ajax({
                    url: url,
                    method: method,
                    data: data,
                    success: function (response) {
                        console.log(response);
                        if (response.success) {
                            $('#ajaxAlertContainer').html(alertHtml('success', response.message));
                        }
                    },
                    error: function (response) {
                        if(response) {
                            $('#ajaxAlertContainer').html(alertHtml('danger', response.responseJSON.message));
                        }
                    },
                    complete: function () {
                        buttonSubmit.prop('disabled', false);
                        buttonSubmit.html(buttonSubmit.html().replace(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> `, ''));
                    }
                });
            });
    </script>
@endpush
