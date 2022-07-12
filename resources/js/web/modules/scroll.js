(function () {

  const header = gsap.from('.js-header', { 
    yPercent: -105,
    paused: true,
    duration: 0.1,
    ease: "bounce.out",
  }).progress(1);
  
  const about = gsap.from('.js-about', { 
    yPercent: 100,
    paused: true,
    duration: 0.1,
    ease: "bounce.out",
  }).progress(1);

  const body = gsap.from('body', { 
    backgroundColor: '#111',
    paused: true,
    delay: 1.4,
    duration: .2,
    
  }).progress(1);
  
  ScrollTrigger.create({
    start: 200,
    end: 99999,
    onUpdate: (self) => {
      if (self.direction === -1) {
        body.play();
        about.play();
        header.play();
      }
      else {
        body.reverse();
        about.reverse();
        header.reverse();
      }
    }
  });

})();
