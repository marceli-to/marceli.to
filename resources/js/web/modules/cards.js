import LazyLoad from '../vendor/lazyload';

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
  };

  var lazyLoadInstance = new LazyLoad();

  return {
    init: _initialize,
  };
	
})();

// Initialize
$(function() {
  Cards.init();
});

