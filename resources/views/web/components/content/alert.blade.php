<div class="alert alert--{{$type}}" role="alert">
  {{$message}}
  {{-- @if ($errors->any())
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  @endif --}}
</div>