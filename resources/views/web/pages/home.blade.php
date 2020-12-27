@extends('web.layout.app')
@section('seo_title', 'Home')
@section('content')
<section class="post">
  <div class="card">
    <figure>
      <img src="/assets/img/day.jpg" height="1000" width="1000" class="is-responsive">
    </figure>
    <footer class="card__footer">
      <div>
        <label>Client</label>
        <div>Day, Zürich</div>
      </div>
      <div>
        <label>Concept & Design</label>
        <div>WBG AG – Visuelle Kommunikation, Zürich</div>
      </div>
    </footer>
    <div class="card__content">
      <span>Visit day.ch</span>
    </div>
  </div>
</section>
<section class="post">
  <div class="card">
    <figure>
      <img src="/assets/img/hame.jpg" height="1000" width="1000" class="is-responsive">
    </figure>
    <footer class="card__footer">
      <div>
        <label>Client</label>
        <div>Hauser Meier Architektinnen, Zürich</div>
      </div>
      <div>
        <label>Concept & Design</label>
        <div>Bivgrafik GmbH, Zürich</div>
      </div>
    </footer>
    <div class="card__content">
      <span>Visit hausermeier.ch</span>
    </div>
  </div>
</section>
<section class="post">
  <div class="card">
    <figure>
      <img src="/assets/img/intercable.jpg" height="1000" width="1000" class="is-responsive">
    </figure>
    <footer class="card__footer">
      <div>
        <label>Client</label>
        <div>Ferratec AG, Rudolfstetten</div>
      </div>
      <div>
        <label>Concept & Design</label>
        <div>marceli.to, Winterthur</div>
      </div>
    </footer>
    <div class="card__content">
      <span>Visit intercable-schweiz.ch</span>
    </div>
  </div>
</section>
<section class="post">
  <div class="card">
    <figure>
      <img src="/assets/img/oxid.jpg" height="1000" width="1000" class="is-responsive">
    </figure>
    <footer class="card__footer">
      <div>
        <label>Client</label>
        <div>Oxid Architektur GmbH, Zürich</div>
      </div>
      <div>
        <label>Concept & Design</label>
        <div>Bivgrafik GmbH, Zürich</div>
      </div>
    </footer>
    <div class="card__content">
      <span>Visit oxid-architektur.ch</span>
    </div>
  </div>
</section>
@endsection