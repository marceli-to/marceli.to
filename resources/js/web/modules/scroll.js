(function () {

  /* Source: https://underscorejs.org/underscore-esm.js
  During a given window of time. Normally, the throttled function will run
  as much as it can, without ever going more than once per `wait` duration;
  but if you'd like to disable the execution on the leading edge, pass
  `{leading: false}`. To disable execution on the trailing edge, ditto.
  */
  function _throttle(func, wait, options) {
    var timeout, context, args, result;
    var previous = 0;
    if (!options) options = {};

    var later = function () {
      previous = options.leading === false ? 0 : Date.now();
      timeout = null;
      result = func.apply(context, args);
      if (!timeout) context = args = null;
    };

    var throttled = function () {
      var _now = Date.now();
      if (!previous && options.leading === false) previous = _now;
      var remaining = wait - (_now - previous);
      context = this;
      args = arguments;
      if (remaining <= 0 || remaining > wait) {
        if (timeout) {
          clearTimeout(timeout);
          timeout = null;
        }
        previous = _now;
        result = func.apply(context, args);
        if (!timeout) context = args = null;
      } else if (!timeout && options.trailing !== false) {
        timeout = setTimeout(later, remaining);
      }
      return result;
    };

    throttled.cancel = function () {
      clearTimeout(timeout);
      previous = 0;
      timeout = context = args = null;
    };

    return throttled;
  }

  let body = document.querySelector("body");
  let about = document.querySelector('.js-about');
  let header = document.querySelector('.js-header');
  let indicator = document.querySelector('.js-indicator');
  
  let previousScrollPosition = 0;

  const isScrollingDown = () => {
    let scrollDown = false;
    let scrollPosition = window.pageYOffset;
    if (scrollPosition > previousScrollPosition) {
      scrollDown = true;
    }
    previousScrollPosition = scrollPosition;
    return scrollDown;
  };

  const handleScroll = () => {
    if (isScrollingDown()) {
      body.classList.add("is-dark");
      about.classList.add("is-hidden");
      header.classList.add("is-hidden");
      indicator.style.display = 'none';
    } 
    else {
      body.classList.remove("is-dark");
      about.classList.remove("is-hidden");
      header.classList.remove("is-hidden");
    }
  };

  // throttle function from lodash library
  const scrollThrottle = _throttle(handleScroll, 100);
  window.addEventListener("scroll", scrollThrottle);

  //window.onload = init;
  
})();
