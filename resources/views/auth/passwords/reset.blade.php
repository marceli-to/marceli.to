@extends('web.layout.app')
@section('seo_title', 'Passwort zurücksetzen')
@section('seo_description', '')
@section('content')
<section>
  <x-header title="Passwort zurücksetzen" />
  <article>
    <h2>Passwort zurücksetzen</h2>
    <div>
      <p>{{__('messages.password_reset')}}</p>
      @if ($errors->any())
        <x-alert type="danger" message="{{__('messages.general_error')}}" />
      @endif
      @if (session('status'))
        <x-alert type="success" message="{{ session('status') }}" />
      @endif
      <form method="POST" class="auth auth--reset" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-text-field label="E-Mail" type="email" name="email" />
        <x-text-field label="Passwort" type="password" name="password" />
        <x-text-field label="Passwort bestätigen" type="password" name="password_confirmation" required autocomplete="new-password" />
        <div class="form-buttons align-end">
          <x-button label="zurücksetzen" name="reset_password" btnClass="btn-primary js-btn-loader" type="submit" />
        </div>
      </form>
    </div>
  </article>
</section>
@endsection

