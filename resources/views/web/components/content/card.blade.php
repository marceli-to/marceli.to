<section class="post">
  <a href="{{$uri}}" target="_blank" rel="noreferrer" title="{{$website}} besuchen" class="card">
    <figure>
      <img data-src="/assets/img/projects/{{$image}}" height="1000" width="1000" title="{{$client}}" class="is-responsive lazyload">
    </figure>
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
          <div>{{$year}}</div>
        </div>
      </div>
    </div>
  </a>
</section>