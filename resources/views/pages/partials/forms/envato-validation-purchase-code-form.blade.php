<div class="envato-form">
    <div class="mb-3">
        <label for="envatoUsername" class="form-label">{{ trans('Envato Username') }}</label>
        <input type="text" class="form-control @error('envato_username') is-invalid @enderror" id="envatoUsername" placeholder="example: john_doe" name="envato_username" value="{{ old('envato_username', session('installation.purchases.envato_username')) }}">
        @error('envato_username')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="envatoItemId" class="form-label">{{ trans('Envato Item Id') }}</label>
        <input type="text" class="form-control @error('envato_item_id') is-invalid @enderror" id="envatoItemId" placeholder="example: 1212121" name="envato_item_id" value="{{ old('envato_item_id', session('installation.purchases.envato_item_id')) }}">
        @error('envato_item_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>
</div>
