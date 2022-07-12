import LazyLoad from '../vendor/lazyload';

(function () {

  const classes = {
    touched: 'is-touched',
  };

  const selectors = {
    card: '.js-card',
  };

  const init = () => {
    const card = document.querySelector(selectors.card);
    
    card.addEventListener("touchstart", function(event) {
      this.classList.add(classes.touched);
    }, false);

    card.addEventListener("touchend", function(event) {
      this.classList.remove(classes.touched);
    }, false);

    const lazyLoadInstance = new LazyLoad();
  };


  window.onload = init;
  
})();


