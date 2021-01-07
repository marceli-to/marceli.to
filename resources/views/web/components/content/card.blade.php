<section class="post">
  <a href="{{$uri}}" target="_blank" rel="noreferrer" title="{{$website}} besuchen" class="card">
    <picture>
      <source media="(min-width: 1200px)" srcset="/assets/img/projects/{{$image}}-lg.webp" type="image/webp">
      <source media="(min-width: 1200px)" srcset="/assets/img/projects/{{$image}}-lg.jpg">        
      <source media="(min-width: 768px)" srcset="/assets/img/projects/{{$image}}-md.webp" type="image/webp">
      <source media="(min-width: 768px)" srcset="/assets/img/projects/{{$image}}-md.jpg">
      <source srcset="/assets/img/projects/{{$image}}-sm.webp" type="image/webp">
      <source srcset="/assets/img/projects/{{$image}}-sm.jpg">
      <img src="/assets/img/projects/{{$image}}-sm.jpg" alt="{{$client}}" title="{{$client}}" class="is-responsive lazyload">
    </picture>
    <div class="card__content">
      <div class="card__description">
        <div>
          <p>{{$description}}</p>
          <p><span>{{$website}}</span></p>
        </div>
      </div>
      <div class="card__info">
        <div>
          <label>Kunde</label>
          <div>{{$client}}</div>
        </div>
        <div>
          <label>Konzept & Design</label>
          <div>{{$design}}</div>
        </div>
        <div>
          <label>Jahr</label>
          <div>{!! $year !!}</div>
        </div>
      </div>
    </div>
  </a>
</section>