<figure class="responsive-image {{ $cssClass ?? '' }}">
  @if ($hasLazy)
    <img data-src="/img/{{$template}}/{{$image->name}}" width="1200" height="800" class="lazyload" alt="{{$image->caption}}">
  @else
    <img src="/img/{{$template}}/{{$image->name}}" width="1200" height="800" alt="{{$image->caption}}">
  @endif
  @if ($hasCaption && $image->caption)
    <figcaption>
      {{$image->caption}}
    </figcaption>
  @endif
</figure>