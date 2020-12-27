<div class="form-group @if ($errors->has($name)) has-error @endif">
  @if($label ?? null)
    <label class="{{ ($required ?? false) ? 'is-required' : '' }}" for="{{ $name }}">
      {{ $label }} {{ ($required ?? false) ? '*' : '' }}
    </label>
  @endif
  <input class="{{ $css ?? '' }}" type="{{ $type ?? 'text' }}" name="{{ $name }}" placeholder="{{ $placeholder ?? '' }}"  value="{{ $userValue ? $userValue :  old($name, $value ?? '') }}">
</div>