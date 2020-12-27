@if ($type == 'shop')
  <figure class="card is-shop">
    <a href="{{ localized_route('page.shop.show', ['slug' => UrlHelper::slug($title) .'-'. UrlHelper::slug($subtitle), 'product' => $image->product->id]) }}" title="{{$title}} – {{$subtitle}}">
      <x-card-image :image="$image" template="cache" maxWidth="800" maxHeight="800" />
      <figcaption>
        <h2>{{$title}}</h2>
        <em>{{$subtitle}}</em>
      </figcaption>
    </a>
  </figure>
@endif

@if ($type == 'illu')
  <figure class="card is-illu">
    <a href="{{ localized_route('page.illustration.show', ['slug' => UrlHelper::slug($title) .'-'. UrlHelper::slug($subtitle), 'illustration' => $image->illustration->id]) }}" title="{{$title}} – {{$subtitle}}">
      <x-card-image :image="$image" template="cache" maxWidth="800" maxHeight="600" />
      <figcaption>
        <h2>{{$title}}</h2>
        <em>{{$subtitle}}</em>
      </figcaption>
    </a>
  </figure>
@endif

@if ($type == 'news')
  <figure class="card is-news">
    <a href="{{ localized_route('page.news.show', ['slug' =>UrlHelper::slug($title) .'-'. UrlHelper::slug($subtitle), 'news' => $id]) }}" title="{{$subtitle}} – {{$title}}">
      <x-card-image :image="$image" template="cache" maxWidth="800" maxHeight="600" />
      <figcaption>
        <em>News | {{$subtitle}}</em>
        <h2>{{$title}}</h2>
      </figcaption>  
    </a>
  </figure>
@endif