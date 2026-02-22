/**
 * Lazy Loading für Bilder
 * Verwendet natives loading="lazy" + Intersection Observer Fallback
 */

// Lazy Loading initialisieren
function initLazyLoading() {
  const images = document.querySelectorAll('img[data-src]');

  // Prüfe ob native lazy loading unterstützt wird
  if ('loading' in HTMLImageElement.prototype) {
    // Native lazy loading verwenden
    images.forEach(img => {
      img.src = img.dataset.src;
      if (img.dataset.srcset) {
        img.srcset = img.dataset.srcset;
      }
      img.classList.remove('lazy');
      img.classList.add('lazy-loaded');
    });
  } else {
    // Intersection Observer als Fallback
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          if (img.dataset.srcset) {
            img.srcset = img.dataset.srcset;
          }
          img.classList.remove('lazy');
          img.classList.add('lazy-loaded');
          observer.unobserve(img);
        }
      });
    }, {
      rootMargin: '50px 0px', // Lade 50px vor Viewport
      threshold: 0.01
    });

    images.forEach(img => imageObserver.observe(img));
  }
}

// Lazy Loading für dynamisch geladene Bilder
function makeLazyLoadable(img) {
  // Wenn Bild bereits eine src hat, in data-src verschieben
  if (img.src && !img.dataset.src) {
    img.dataset.src = img.src;
    img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"%3E%3C/svg%3E'; // 1x1 transparent SVG
    img.classList.add('lazy');
  }

  // Initialisiere Lazy Loading für dieses Bild
  if ('loading' in HTMLImageElement.prototype) {
    img.loading = 'lazy';
    img.src = img.dataset.src;
    img.classList.remove('lazy');
    img.classList.add('lazy-loaded');
  }
}

// Bei DOM-Ready initialisieren
document.addEventListener('DOMContentLoaded', initLazyLoading);

// Export für externe Verwendung
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { initLazyLoading, makeLazyLoadable };
}
