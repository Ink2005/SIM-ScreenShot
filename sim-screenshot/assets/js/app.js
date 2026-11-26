document.addEventListener('DOMContentLoaded', () => {
  const body = document.body;
  const reducedMotion = window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Responsive sidebar drawer.
  const toggle = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');

  const closeSidebar = () => {
    if (!sidebar || !toggle) return;
    sidebar.classList.remove('sidebar-open');
    body.classList.remove('sidebar-visible');
    toggle.setAttribute('aria-expanded', 'false');
  };

  if (toggle && sidebar) {
    toggle.addEventListener('click', () => {
      const isOpen = sidebar.classList.toggle('sidebar-open');
      body.classList.toggle('sidebar-visible', isOpen);
      toggle.setAttribute('aria-expanded', String(isOpen));
    });

    document.addEventListener('click', (event) => {
      if (
        sidebar.classList.contains('sidebar-open') &&
        !sidebar.contains(event.target) &&
        !toggle.contains(event.target)
      ) {
        closeSidebar();
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') closeSidebar();
    });

    sidebar.querySelectorAll('.nav-link').forEach((link) => {
      link.addEventListener('click', closeSidebar);
    });
  }

  // Scroll state for the translucent sticky header.
  const updateScrollState = () => {
    body.classList.toggle('has-scrolled', window.scrollY > 8);
  };
  updateScrollState();
  window.addEventListener('scroll', updateScrollState, { passive: true });

  if (reducedMotion) return;

  body.classList.add('motion-enabled');

  // Progressive reveal across all existing content modules.
  if ('IntersectionObserver' in window) {
    const targets = document.querySelectorAll([
      '.content > .page-heading',
      '.content .stat-card',
      '.content .panel',
      '.content .division-card',
      '.content .filter-bar',
      '.content .table-wrap',
      '.content .form-grid',
      '.content .detail-grid',
      '.content .report-sheet',
      '.content .report-summary > div',
      '.content > .empty-state'
    ].join(','));

    if (targets.length) {
      const observer = new IntersectionObserver((entries, currentObserver) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            currentObserver.unobserve(entry.target);
          }
        });
      }, {
        threshold: 0.06,
        rootMargin: '0px 0px -28px 0px'
      });

      targets.forEach((element, index) => {
        element.classList.add('motion-reveal');
        element.style.setProperty(
          '--sim-reveal-delay',
          `${Math.min(index % 6, 5) * 70}ms`
        );
        observer.observe(element);
      });
    } else {
      body.classList.add('motion-ready');
    }
  }

  // Stagger visible table and activity rows without changing their content.
  document.querySelectorAll('.table-wrap tbody tr, .simple-list .simple-row').forEach((row, index) => {
    row.classList.add('sim-row-enter');
    row.style.setProperty('--sim-row-delay', `${Math.min(index, 12) * 32}ms`);
  });

  // Count up numeric dashboard metrics. Currency and non-numeric labels are untouched.
  const numberFormatter = new Intl.NumberFormat('id-ID');
  const animateNumber = (element) => {
    if (element.dataset.simCounted === '1') return;
    const original = element.textContent.trim();
    if (!/^\d[\d.,]*$/.test(original)) return;

    const target = Number(original.replace(/[.,]/g, ''));
    if (!Number.isFinite(target) || target <= 0 || target > 100000000) return;

    element.dataset.simCounted = '1';
    const duration = 1050;
    const startTime = performance.now();

    const tick = (now) => {
      const progress = Math.min((now - startTime) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 4);
      element.textContent = numberFormatter.format(Math.round(target * eased));
      if (progress < 1) requestAnimationFrame(tick);
      else element.textContent = original;
    };

    element.textContent = numberFormatter.format(0);
    requestAnimationFrame(tick);
  };

  const metricElements = document.querySelectorAll('.stat-card > strong:not(.stat-money)');
  if ('IntersectionObserver' in window && metricElements.length) {
    const metricObserver = new IntersectionObserver((entries, currentObserver) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          animateNumber(entry.target);
          currentObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });

    metricElements.forEach((element) => metricObserver.observe(element));
  } else {
    metricElements.forEach(animateNumber);
  }

  // Pointer-following highlight and gentle 3D tilt on desktop only.
  const finePointer = window.matchMedia &&
    window.matchMedia('(hover: hover) and (pointer: fine)').matches;

  if (finePointer) {
    document.querySelectorAll('.stat-card, .division-card').forEach((card) => {
      card.addEventListener('pointermove', (event) => {
        const rect = card.getBoundingClientRect();
        const x = (event.clientX - rect.left) / rect.width;
        const y = (event.clientY - rect.top) / rect.height;
        const rotateY = (x - 0.5) * 5;
        const rotateX = (0.5 - y) * 5;

        card.style.setProperty('--sim-spot-x', `${x * 100}%`);
        card.style.setProperty('--sim-spot-y', `${y * 100}%`);
        card.style.setProperty('--sim-tilt-x', `${rotateY.toFixed(2)}deg`);
        card.style.setProperty('--sim-tilt-y', `${rotateX.toFixed(2)}deg`);
        card.classList.add('sim-tilt-active');
      });

      card.addEventListener('pointerleave', () => {
        card.style.removeProperty('--sim-spot-x');
        card.style.removeProperty('--sim-spot-y');
        card.style.removeProperty('--sim-tilt-x');
        card.style.removeProperty('--sim-tilt-y');
        card.classList.remove('sim-tilt-active');
      });
    });
  }

  // Button ripple feedback. Native submit/click behavior remains intact.
  document.querySelectorAll('.btn').forEach((button) => {
    button.addEventListener('pointerdown', (event) => {
      const rect = button.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height) * 2.15;
      button.style.setProperty('--sim-ripple-x', `${event.clientX - rect.left}px`);
      button.style.setProperty('--sim-ripple-y', `${event.clientY - rect.top}px`);
      button.style.setProperty('--sim-ripple-size', `${size}px`);
      button.style.setProperty(
        '--sim-ripple-color',
        button.classList.contains('btn-primary')
          ? 'rgba(255,255,255,.30)'
          : 'rgba(185,31,50,.12)'
      );
      button.classList.remove('is-rippling');
      // Force the CSS animation to restart on repeated clicks.
      void button.offsetWidth;
      button.classList.add('is-rippling');
      button.classList.add('is-pressed');
    });

    ['pointerup', 'pointercancel', 'pointerleave', 'blur'].forEach((eventName) => {
      button.addEventListener(eventName, () => button.classList.remove('is-pressed'));
    });

    button.addEventListener('animationend', (event) => {
      if (event.animationName === 'sim-button-ripple') {
        button.classList.remove('is-rippling');
      }
    });
  });

  // Page navigation uses the browser's normal navigation behavior.

});
