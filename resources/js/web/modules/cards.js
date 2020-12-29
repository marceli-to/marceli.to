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
    });
  };

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

