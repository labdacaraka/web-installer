@extends('web-installer::layouts.setup')
@section('title', trans('Directory Permissions'))
@section('content')
    @if($permissionCompatible)
        <div class="alert alert-success" role="alert">
            {{ trans('Great! Folder write access permissions have been updated. You now have write access to the folder.') }}
        </div>
    @else
        <div class="alert alert-danger" role="alert">
            {{ trans("Oops! You do not have access to the requested folder. Please contact your administrator to request access permission.") }}
        </div>
    @endif
    <div class="accordion" id="check-compatible">

        <div class="accordion-item">
            <h2 class="accordion-header" id="directoryPermissionCheckHeading">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#directoryPermissionCheck" aria-expanded="true" aria-controls="directoryPermissionCheck">
                    {{ trans('Directory Permissions') }}
                </button>
            </h2>
            <div id="directoryPermissionCheck" class="accordion-collapse collapse show"
                 aria-labelledby="directoryPermissionCheckHeading" data-bs-parent="#checkCompatibilityAccordion">
                <div class="accordion-body">
                    <ul class="list-group">
                        @foreach($permissions as $permission => $attribute)
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">{{ $permission }}</div>
                                </div>
                                <span class="badge bg-{{ $attribute['writable'] ? 'success' : 'danger' }}">
                                    0755
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <div class="d-grid gap-2 mt-3">
        @if($permissionCompatible)
            <a class="btn btn-success" href="{{ route('web-installer.app-settings') }}" type="button"><i class="fa-solid fa-gear"></i> {{ trans('Next! App Setting') }}</a>
        @else
            <a class="btn btn-primary" href="" type="button"><i class="fa-solid fa-rotate"></i> {{ trans('Reload') }}</a>
        @endif
        <a class="btn btn-warning" href="{{ route('web-installer.check-requirements') }}" type="button"><i class="fa-solid fa-circle-chevron-left"></i> {{ trans('Previous') }}</a>
    </div>
@endsection

