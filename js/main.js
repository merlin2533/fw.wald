/**
 * Freiwillige Feuerwehr Walddorfhäslach
 * Main JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
  initMobileNav();
  initStickyHeader();
  initCookieBanner();
  initScrollAnimations();
  initTabs();
  initAccordions();
});

/* ============================================
   Mobile Navigation
   ============================================ */
function initMobileNav() {
  const toggle = document.getElementById('nav-toggle');
  const nav = document.getElementById('nav');
  const overlay = document.getElementById('nav-overlay');
  const close = document.getElementById('nav-close');

  if (!toggle || !nav) return;

  function openNav() {
    nav.classList.add('nav--open');
    if (overlay) overlay.classList.add('nav-overlay--visible');
    document.body.style.overflow = 'hidden';
  }

  function closeNav() {
    nav.classList.remove('nav--open');
    if (overlay) overlay.classList.remove('nav-overlay--visible');
    document.body.style.overflow = '';
  }

  toggle.addEventListener('click', openNav);
  if (close) close.addEventListener('click', closeNav);
  if (overlay) overlay.addEventListener('click', closeNav);

  // Schließe Nav bei Klick auf Link
  nav.querySelectorAll('.nav__link').forEach(link => {
    link.addEventListener('click', closeNav);
  });

  // Schließe Nav bei Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeNav();
  });
}

/* ============================================
   Sticky Header mit Schatten beim Scrollen
   ============================================ */
function initStickyHeader() {
  const header = document.getElementById('header');
  if (!header) return;

  let lastScroll = 0;

  window.addEventListener('scroll', () => {
    const currentScroll = window.scrollY;

    if (currentScroll > 10) {
      header.classList.add('header--scrolled');
    } else {
      header.classList.remove('header--scrolled');
    }

    lastScroll = currentScroll;
  }, { passive: true });
}

/* ============================================
   Cookie Banner
   ============================================ */
function initCookieBanner() {
  const banner = document.getElementById('cookie-banner');
  const acceptBtn = document.getElementById('cookie-accept');
  const declineBtn = document.getElementById('cookie-decline');

  if (!banner) return;

  // Prüfe ob bereits entschieden
  const cookieChoice = localStorage.getItem('cookie-consent');
  if (cookieChoice) return;

  // Zeige Banner nach kurzer Verzögerung
  setTimeout(() => {
    banner.classList.add('cookie-banner--visible');
  }, 1000);

  function hideBanner(choice) {
    localStorage.setItem('cookie-consent', choice);
    banner.classList.remove('cookie-banner--visible');
  }

  if (acceptBtn) {
    acceptBtn.addEventListener('click', () => hideBanner('accepted'));
  }

  if (declineBtn) {
    declineBtn.addEventListener('click', () => hideBanner('declined'));
  }
}

/* ============================================
   Scroll-Animationen (Fade-In)
   ============================================ */
function initScrollAnimations() {
  const elements = document.querySelectorAll('.animate-in');
  if (!elements.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
      if (entry.isIntersecting) {
        // Gestaffelter Delay für Elemente in der gleichen Reihe
        setTimeout(() => {
          entry.target.classList.add('animate-in--visible');
        }, index * 100);
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  });

  elements.forEach(el => observer.observe(el));
}

/* ============================================
   Tabs
   ============================================ */
function initTabs() {
  const tabContainers = document.querySelectorAll('.tabs');

  tabContainers.forEach(container => {
    const tabs = container.querySelectorAll('.tab');
    const parent = container.parentElement;

    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const target = tab.dataset.tab;

        // Deaktiviere alle Tabs
        tabs.forEach(t => t.classList.remove('tab--active'));
        tab.classList.add('tab--active');

        // Zeige passendes Content-Panel
        if (parent) {
          parent.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('tab-content--active');
          });

          const targetContent = parent.querySelector(`[data-tab-content="${target}"]`);
          if (targetContent) {
            targetContent.classList.add('tab-content--active');
          }
        }
      });
    });
  });
}

/* ============================================
   Accordions
   ============================================ */
function initAccordions() {
  const items = document.querySelectorAll('.accordion-item__header');

  items.forEach(header => {
    header.addEventListener('click', () => {
      const item = header.parentElement;
      const body = item.querySelector('.accordion-item__body');
      const isOpen = item.classList.contains('accordion-item--open');

      // Schließe alle anderen im selben Container
      const container = item.parentElement;
      if (container) {
        container.querySelectorAll('.accordion-item--open').forEach(openItem => {
          if (openItem !== item) {
            openItem.classList.remove('accordion-item--open');
            const openBody = openItem.querySelector('.accordion-item__body');
            if (openBody) {
              openBody.style.maxHeight = null;
              openBody.style.paddingTop = '0';
              openBody.style.paddingBottom = '0';
            }
          }
        });
      }

      // Toggle aktuelles Element
      if (isOpen) {
        item.classList.remove('accordion-item--open');
        if (body) {
          body.style.maxHeight = null;
          body.style.paddingTop = '0';
          body.style.paddingBottom = '0';
        }
      } else {
        item.classList.add('accordion-item--open');
        if (body) {
          body.style.paddingTop = '';
          body.style.paddingBottom = '';
          body.style.maxHeight = body.scrollHeight + 40 + 'px';
        }
      }
    });
  });

  // Initialer Zustand: Alle geschlossen, Body versteckt
  document.querySelectorAll('.accordion-item').forEach(item => {
    const body = item.querySelector('.accordion-item__body');
    if (body && !item.classList.contains('accordion-item--open')) {
      body.style.maxHeight = '0';
      body.style.paddingTop = '0';
      body.style.paddingBottom = '0';
      body.style.overflow = 'hidden';
      body.style.transition = 'max-height 0.3s ease, padding 0.3s ease';
    }
  });
}

/* ============================================
   Smooth Scroll für Anker-Links
   ============================================ */
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const targetId = this.getAttribute('href');
    if (targetId === '#') return;

    const target = document.querySelector(targetId);
    if (target) {
      e.preventDefault();
      const headerOffset = 100;
      const elementPosition = target.getBoundingClientRect().top;
      const offsetPosition = elementPosition + window.scrollY - headerOffset;

      window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
      });
    }
  });
});
