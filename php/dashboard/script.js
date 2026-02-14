// Route: admin/dashboard/script.js

let lenis;

document.addEventListener("DOMContentLoaded", () => {
    lenis = new Lenis({
        lerp: 0.12,
        wheelMultiplier: 1.2,
        smoothWheel: true,
        touchMultiplier: 2,
    });

    function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
    }
    requestAnimationFrame(raf);

    const header = document.querySelector('.header-section');
    const checkScroll = () => {
        if (window.scrollY > 10) { header.classList.add('scrolled'); } else { header.classList.remove('scrolled'); }
    };
    window.addEventListener('scroll', checkScroll);
    checkScroll();

});

lucide.createIcons();

function toggleDrawer() {
    const drawer = document.getElementById('drawer');
    const overlay = document.getElementById('overlay');
    const body = document.body;
    const isOpening = !drawer.classList.contains('open');
    drawer.classList.toggle('open');
    overlay.classList.toggle('open');
    body.classList.toggle('no-scroll');
    if (lenis) {
        if (isOpening) { lenis.stop(); } else { lenis.start(); }
    }
}