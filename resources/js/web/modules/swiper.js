/**
 * Dependencies
 */
import Swiper from '../vendor/swiper.js';

var SwiperUi = (function() {

  var selectors = {
    html:   'html',
    body:   'body',
    swiper: '.js-swiper',
    swiperThumb: '.js-swiper-thumb'
  };

  var mySwiper;
     
  var _initialize = function() {
    _bind();
  };

  var _bind = function() {
    mySwiper = new Swiper(selectors.swiper, {
      slidesPerView: 'auto',
      centeredSlides: true,
      speed: 600,
      autoplay: {
        delay: 6000,
      },
      spaceBetween: 0,
      effect: 'fade',
      fadeEffect: {
        crossFade: true
      },
      navigation: {
        nextEl: '.swiper-btn-next',
        prevEl: '.swiper-btn-prev',
      },
    });
  };
  
  return {
    init:  _initialize,
  };
	
})();

// Initialize
$(function() {
  if ($('body').find('.swiper-container').length) {
    SwiperUi.init();
  }
});