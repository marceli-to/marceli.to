import debounce from '../vendor/debounce';

var Cards = (function() {
	
	// selectors
	var selectors = {
    html: 'html',
    body: 'body',
	};

  // Init
  var _initialize = function() {
    _bind();
  };

  // Bind events
  var _bind = function() {
    $('.card').on('touchstart', function(e) {
      $(this).addClass('touched');
    });
    $('.card').on('touchend', function(e) {
      $(this).removeClass('touched');
    });
    $(window).scroll(function() {
      _hideScrollIndicator();
      _scroll();
    });
  };

  var _scroll = debounce(function(){
    var lastScrollTop = 0;
    window.addEventListener("scroll", function(){  
      var st = window.pageYOffset || document.documentElement.scrollTop;  
      if (st > lastScrollTop){
        document.querySelector('.js-about').classList.remove('is-visible');
        document.querySelector('.js-about').classList.add('is-hidden');
        document.querySelector('.js-header').classList.remove('is-visible');
        document.querySelector('.js-header').classList.add('is-hidden');
      } 
      else {
        document.querySelector('.js-about').classList.remove('is-hidden');
        document.querySelector('.js-about').classList.add('is-visible');
        document.querySelector('.js-header').classList.remove('is-hidden');
        document.querySelector('.js-header').classList.add('is-visible');
      }
      lastScrollTop = st;
    }, false);
  }, 100);


  var _hideScrollIndicator = debounce(function() {
    $('.scroll-indicator').hide();
  }, 50);

  return {
    init: _initialize,
  };
	
})();

// Initialize
$(function() {
  Cards.init();
});

