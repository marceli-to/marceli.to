var about = (function() {
	
	// selectors
	var selectors = {
    html:     'html',
    body:     'body',
    about:     '.js-about',
    aboutBtn:  '.js-about-btn',
	};

  // css classes
  var classes = {
    active:   'is-active',
    visible:  'is-visible',
    hidden:   'is-hidden',
    open:     'is-open',
    hasAbout:  'has-about',
  };

  // Init
  var _initialize = function() {
    _bind();
  };

  // Bind events
  var _bind = function() {
    $(selectors.body).on('click', selectors.aboutBtn, function(){
      _toggle($(this));
    });
  };

  var _toggle = function() {
    $(selectors.about).toggleClass(classes.visible);
    $(selectors.aboutBtn).toggleClass(classes.hasAbout);
  };

  /* --------------------------------------------------------------
    * RETURN PUBLIC METHODS
    * ------------------------------------------------------------ */

  return {
    init:  _initialize,
  };
	
})();

// Initialize
$(function() {
  about.init();
});

