@extends('web-installer::layouts.setup')
@section('title', trans('Check Requirements'))
@section('content')
    @if($allCompatible)
        <div class="alert alert-success" role="alert">
            {{ trans('Great! All requirements for the PHP based web server have been fulfilled.') }}
        </div>
    @else
        <div class="alert alert-danger" role="alert">
            {{ trans("Oops! The server does not meet the minimum requirements to run PHP Laravel. Please contact our customer support team for further assistance.") }}
        </div>
    @endif
    <div class="accordion" id="check-compatible">
        <div class="accordion-item">
            <h2 class="accordion-header" id="phpVersionCheckHeading">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#phpVersionCheck" aria-expanded="true" aria-controls="phpVersionCheck">
                    {{ trans('PHP Version') }}
                </button>
            </h2>
            <div id="phpVersionCheck" class="accordion-collapse collapse show" aria-labelledby="phpVersionCheckHeading"
                 data-bs-parent="#checkCompatibilityAccordion">
                <div class="accordion-body">
                    <ol class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">{{ trans('Minimum PHP Version') }}</div>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $minimumPhpVersion }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">{{ trans('Current PHP Version') }}</div>
                            </div>
                            <span
                                class="badge bg-{{ $phpVersionCompatible ? 'success' : 'danger' }} rounded-pill">{{ $currentPhpVersion }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">{{ trans('PHP Version Compatibility') }}</div>
                            </div>
                            <span>
                                @if($phpVersionCompatible)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger"></i>
                                @endif
                            </span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="phpExtensionCheckHeading">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#phpExtensionCheck" aria-expanded="true" aria-controls="phpExtensionCheck">
                    {{ trans('PHP Extensions') }}
                </button>
            </h2>
            <div id="phpExtensionCheck" class="accordion-collapse collapse show"
                 aria-labelledby="phpExtensionCheckHeading" data-bs-parent="#checkCompatibilityAccordion">
                <div class="accordion-body">
                    <ul class="list-group">
                        @foreach($phpExtensionsStatuses as $extension => $status)
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">{{ $extension }}</div>
                                </div>
                                <span>
                                    @if($status)
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i>
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="phpSettingCheckHeading">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#phpSettingCheck" aria-expanded="true" aria-controls="phpSettingCheck">
                    {{ trans('PHP Settings') }}
                </button>
            </h2>
            <div id="phpSettingCheck" class="accordion-collapse collapse show" aria-labelledby="phpSettingCheckHeading"
                 data-bs-parent="#checkCompatibilityAccordion">
                <div class="accordion-body">
                    <ul class="list-group">
                        @foreach($phpSettings as $setting => $attribute)
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">{{ $setting }} ({{ $attribute['current']  }}
                                        >= {{ $attribute['required'] }})
                                    </div>
                                </div>
                                <span>
                                    @if($attribute['compatible'])
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i>
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <div class="d-grid gap-2 mt-3">
        @if($allCompatible)
            <a class="btn btn-success" href="{{ route('web-installer.check-permissions') }}" type="button"><i class="fa-solid fa-folder"></i> {{ trans('Next! Check Directory Permissions') }}</a>
        @else
            <a class="btn btn-primary" href="" type="button"><i class="fa-solid fa-rotate"></i> {{ trans('Reload') }}</a>
        @endif
            <a class="btn btn-warning" href="{{ route('web-installer.welcome') }}" type="button"><i class="fa-solid fa-circle-chevron-left"></i> {{ trans('Previous') }}</a>
    </div>
@endsection

