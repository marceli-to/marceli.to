import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

export function initScrollAnimations() {
    gsap.registerPlugin(ScrollTrigger);

    const timeline = gsap.timeline({
        scrollTrigger: {
            trigger: "body",
            start: "top top",
            end: "400px top",
            scrub: true,
        }
    });

    timeline
        .to("header", {
            opacity: 0,
            y: -100,
            pointerEvents: "none"
        })
        .to("footer", {
            opacity: 0,
            y: 100,
            pointerEvents: "none"
        }, "<")
        .to("body", {
            backgroundColor: "#000000",
            color: "#f5f5f5"
        }, "<");

    // Show header/footer on scroll up
    let lastScrollY = window.scrollY;
    let scrollUpDistance = 0;
    let isVisible = true;

    window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;

        if (currentScrollY < lastScrollY) {
            // Scrolling up
            scrollUpDistance += lastScrollY - currentScrollY;

            if (scrollUpDistance >= 100 && !isVisible) {
                isVisible = true;
                gsap.to("header", {
                    opacity: 1,
                    y: 0,
                    pointerEvents: "auto",
                    duration: 0.3
                });
                gsap.to("footer", {
                    opacity: 1,
                    y: 0,
                    pointerEvents: "auto",
                    duration: 0.3
                });
                gsap.to("body", {
                    backgroundColor: "#f9fafb",
                    color: "#111111",
                    duration: 0.3
                });
            }
        } else {
            // Scrolling down
            scrollUpDistance = 0;

            if (currentScrollY > 400 && isVisible) {
                isVisible = false;
                gsap.to("header", {
                    opacity: 0,
                    y: -100,
                    pointerEvents: "none",
                    duration: 0.3
                });
                gsap.to("footer", {
                    opacity: 0,
                    y: 100,
                    pointerEvents: "none",
                    duration: 0.3
                });
                gsap.to("body", {
                    backgroundColor: "#222",
                    color: "#f5f5f5",
                    duration: 0.3
                });
            }
        }

        lastScrollY = currentScrollY;
    });
}
