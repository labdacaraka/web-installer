@extends('web-installer::layouts.setup')
@section('title', trans('Final Step'))
@section('content')
    <div id="ajaxAlertContainer">
    </div>
    <div id="ajaxDefaultLoginAccountsContainer">
    </div>
    <div class="alert alert-warning" role="alert">
        {{ trans('Last Step. Run All Setting!') }}
    </div>
    <form id="ajaxFormRunInstall" action="{{ route('web-installer.run-install') }}" method="POST">
        @csrf
        <div class="d-grid gap-2 mt-3">
            @if(session('installation.database_settings'))
                <button class="btn btn-primary" type="submit"><i
                        class="fa-solid fa-circle-play"></i> {{ trans('Install Now') }}</button>
            @endif
        </div>
    </form>
@endsection
@push('scripts')
    <script>
        const alertHtml = (type, message) => {
            return `<div class="alert alert-${type}" role="alert">${message}</div>`;
        };
        const defaultLoginAccountsHtml = (data) => {
            let html = '';
            data.forEach((user) => {
                html += `
                <div class="alert alert-info" role="alert">
                    <h5 class="alert-heading">Default Login Accounts (${user.name})</h5>
                    <p>Credentials: ${user.email} / ${user.password}</p>
                </div>
            `;
            });
            return html;
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
                type: method,
                data: data,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#ajaxAlertContainer').html(alertHtml('success', response.message));
                        if (response.data) {
                            if (response.data.default_login_accounts) {
                                $('#ajaxDefaultLoginAccountsContainer').html(defaultLoginAccountsHtml(response.data.default_login_accounts));
                            }
                        }
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    }
                },
                error: function (response) {
                    if (response.responseJSON) {
                        $('#ajaxAlertContainer').html(alertHtml('danger', response.responseJSON.message));
                    }else {
                        $('#ajaxAlertContainer').html(alertHtml('danger', response.statusText));
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
