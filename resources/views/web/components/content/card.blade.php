<section class="post">
  <a href="{{$uri}}" target="_blank" rel="noreferrer" title="{{$website}} besuchen" class="card js-card">
    <picture>
      <source media="(min-width: 1200px)" data-srcset="/assets/img/projects/{{$image}}-lg.webp" type="image/webp">
      <source media="(min-width: 1200px)" data-srcset="/assets/img/projects/{{$image}}-lg.jpg">        
      <source media="(min-width: 768px)" data-srcset="/assets/img/projects/{{$image}}-md.webp" type="image/webp">
      <source media="(min-width: 768px)" data-srcset="/assets/img/projects/{{$image}}-md.jpg">
      <source data-srcset="/assets/img/projects/{{$image}}-sm.webp" type="image/webp">
      <source data-srcset="/assets/img/projects/{{$image}}-sm.jpg">
      <img data-src="/assets/img/projects/{{$image}}-sm.jpg" alt="{{$client}}" title="{{$client}}" height="400" width="600" class="is-responsive lazy">
    </picture>
    <div class="card__content">
      <div class="card__description">
        <div>
          <p>
            {{$description}}<br>
            <span>{{$website}}</span>
          </p>
        </div>
      </div>
      <div class="card__info">
        <div>
          <label>Kunde</label>
          <div>{!! $client !!}</div>
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