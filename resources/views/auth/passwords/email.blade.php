@extends('web.layout.app')
@section('seo_title', 'Passwort vergessen')
@section('seo_description', '')
@section('content')
<section>
  <x-header title="Passwort vergessen" />
  <article>
    <h2>Passwort vergessen?</h2>
    <div>
      <p>{{__('messages.password_recovery')}}</p>
      @if ($errors->any())
        <x-alert type="danger" message="{{__('messages.general_error')}}" />
      @endif
      @if (session('status'))
        <x-alert type="success" message="{{ session('status') }}" />
      @endif
      <form method="POST" class="auth auth--recover" action="{{ route('password.email') }}">
        @csrf
        <x-text-field label="E-Mail" type="email" name="email" />
        <div class="form-buttons align-end">
          <x-button label="senden" name="register" btnClass="btn-primary js-btn-loader" type="submit" />
        </div>
      </form>
    </div>
  </article>
</section>
@endsection