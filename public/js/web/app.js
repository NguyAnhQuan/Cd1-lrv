(() => {
  function initSlider(root) {
    const viewport = root.querySelector("[data-ft-slider-viewport]");
    const prevBtn = root.querySelector("[data-ft-slider-prev]");
    const nextBtn = root.querySelector("[data-ft-slider-next]");
    const track = root.querySelector(".ft-slider__track");

    if (!viewport || !track) return;

    const getSlides = () =>
      Array.from(track.querySelectorAll(".ft-slider__slide"));
    const getRealSlides = () =>
      Array.from(track.querySelectorAll('.ft-slider__slide:not([data-ft-clone="1"])'));

    const cleanupClones = () => {
      track.querySelectorAll('[data-ft-clone="1"]').forEach((n) => n.remove());
    };

    const setupInfinite = () => {
      cleanupClones();
      const real = getRealSlides();
      if (real.length <= 1) return;

      const first = real[0];
      const slideW = first.getBoundingClientRect().width || 1;
      const visible = Math.max(1, Math.round(viewport.clientWidth / slideW));
      const cloneCount = Math.min(real.length, visible + 1);

      const head = real.slice(0, cloneCount).map((n) => {
        const c = n.cloneNode(true);
        c.setAttribute("data-ft-clone", "1");
        return c;
      });
      const tail = real.slice(-cloneCount).map((n) => {
        const c = n.cloneNode(true);
        c.setAttribute("data-ft-clone", "1");
        return c;
      });

      // prepend tail clones
      tail.forEach((c) => track.insertBefore(c, track.firstChild));
      // append head clones
      head.forEach((c) => track.appendChild(c));

      const realAfter = getRealSlides();
      if (realAfter.length) {
        // jump to first real slide (after prepended clones)
        viewport.scrollLeft = realAfter[0].offsetLeft;
      }
    };

    const scrollByPage = (dir) => {
      const delta = Math.max(240, Math.round(viewport.clientWidth * 0.9));
      viewport.scrollBy({ left: dir * delta, behavior: "smooth" });
    };

    prevBtn?.addEventListener("click", () => scrollByPage(-1));
    nextBtn?.addEventListener("click", () => scrollByPage(1));

    // Wheel scroll horizontal while hovering slider (no need SHIFT)
    viewport.addEventListener(
      "wheel",
      (e) => {
        // If user is trying to scroll vertically, convert to horizontal.
        const absX = Math.abs(e.deltaX);
        const absY = Math.abs(e.deltaY);
        if (absY <= absX) return;
        e.preventDefault();
        viewport.scrollLeft += e.deltaY;
      },
      { passive: false }
    );

    // Infinite loop correction (snap back when reaching clones)
    let raf = 0;
    const correctIfNeeded = () => {
      raf = 0;
      const real = getRealSlides();
      if (real.length <= 1) return;

      const firstReal = real[0];
      const lastReal = real[real.length - 1];
      const slidesAll = getSlides();

      const firstAll = slidesAll[0];
      const lastAll = slidesAll[slidesAll.length - 1];
      if (!firstAll || !lastAll) return;

      const tol = 2;
      const x = viewport.scrollLeft;
      const minRealX = firstReal.offsetLeft;
      const maxRealX = lastReal.offsetLeft;

      // If scrolled into leading clones area, jump to corresponding real end
      if (x <= minRealX - tol) {
        viewport.scrollLeft = maxRealX;
        return;
      }

      // If scrolled into trailing clones area (past last real), jump to start
      if (x >= maxRealX + (lastAll.offsetLeft - lastReal.offsetLeft) - tol) {
        viewport.scrollLeft = minRealX;
      }
    };

    viewport.addEventListener("scroll", () => {
      if (raf) return;
      raf = requestAnimationFrame(correctIfNeeded);
    });

    // Setup on load and on resize (rebuild clones for new viewport size)
    setupInfinite();
    let resizeT = 0;
    window.addEventListener("resize", () => {
      window.clearTimeout(resizeT);
      resizeT = window.setTimeout(setupInfinite, 120);
    });
  }

  function initHeroSearch() {
    const form = document.querySelector("[data-ft-hero-search]");
    const scope = document.getElementById("ft-hero-scope");
    if (!form || !scope) return;

    const syncAction = () => {
      const intl = form.getAttribute("data-action-international") || "";
      const dom = form.getAttribute("data-action-domestic") || "";
      form.action = scope.value === "international" ? intl : dom;
    };

    scope.addEventListener("change", syncAction);
    syncAction();
  }

  function boot() {
    document.querySelectorAll("[data-ft-slider]").forEach(initSlider);
    initHeroSearch();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();

