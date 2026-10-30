/**
 * Multan Tech Accessories — Master Frontend JavaScript
 * Vanilla ES Module | Under 25KB | Zero external libraries
 */

document.addEventListener('DOMContentLoaded', () => {
  initThemeToggle();
  initStickyHeader();
  initSearchAutocomplete();
  initHeroCarousel();
  initBackToTopProgress();
  initMobileDrawer();
  initDropdowns();
  initBeaconTracking();
});

// -------------------------------------------------------------
// 1. Day / Night Theme Toggle
// -------------------------------------------------------------
function initThemeToggle() {
  const buttons = document.querySelectorAll('.theme-toggle');
  if (!buttons.length) return;

  const metaTheme = document.querySelector('meta[name="theme-color"]');

  function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('mta_theme', theme);
    buttons.forEach(b => b.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false'));
    if (metaTheme) {
      metaTheme.setAttribute('content', theme === 'dark' ? '#262624' : '#FAF9F5');
    }
  }

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      const current = document.documentElement.getAttribute('data-theme') || 'light';
      const next = current === 'dark' ? 'light' : 'dark';
      applyTheme(next);
    });
  });
}

// -------------------------------------------------------------
// 2. Sticky Header (Hides on scroll down, returns on scroll up)
// -------------------------------------------------------------
function initStickyHeader() {
  const header = document.querySelector('.site-header');
  if (!header) return;

  let lastScrollY = window.scrollY;
  let ticking = false;

  window.addEventListener('scroll', () => {
    if (!ticking) {
      window.requestAnimationFrame(() => {
        const currentY = window.scrollY;
        if (currentY > 120 && currentY > lastScrollY) {
          header.classList.add('header-hidden');
        } else {
          header.classList.remove('header-hidden');
        }
        lastScrollY = Math.max(0, currentY);
        ticking = false;
      });
      ticking = true;
    }
  }, { passive: true });
}

// -------------------------------------------------------------
// 3. Debounced Live Search Suggestions
// -------------------------------------------------------------
function initSearchAutocomplete() {
  const inputs = document.querySelectorAll('.search-input');
  
  inputs.forEach(input => {
    const wrap = input.closest('.search-input-wrap');
    if (!wrap) return;

    let suggestionsBox = wrap.querySelector('.search-suggestions');
    if (!suggestionsBox) {
      suggestionsBox = document.createElement('div');
      suggestionsBox.className = 'search-suggestions';
      wrap.appendChild(suggestionsBox);
    }

    let timeout = null;

    input.addEventListener('input', () => {
      clearTimeout(timeout);
      const query = input.value.trim();

      if (query.length < 2) {
        suggestionsBox.classList.remove('active');
        suggestionsBox.innerHTML = '';
        return;
      }

      timeout = setTimeout(async () => {
        try {
          const res = await fetch(`/api/search-suggestions?q=${encodeURIComponent(query)}`);
          if (!res.ok) return;
          const items = await res.json();

          if (!Array.isArray(items) || items.length === 0) {
            suggestionsBox.innerHTML = `<div style="padding:0.75rem 1rem;font-size:0.85rem;color:var(--text-muted);">No accessories found for "${escapeHtml(query)}"</div>`;
          } else {
            suggestionsBox.innerHTML = items.map(item => `
              <a href="/products/${item.slug}" class="search-item">
                <img src="${item.primary_image_url || '/assets/images/placeholder.webp'}" alt="${escapeHtml(item.title)}" class="search-item-img" width="44" height="44" loading="lazy">
                <div style="flex:1;overflow:hidden;">
                  <div class="search-item-title">${escapeHtml(item.title)}</div>
                  <div class="search-item-price">Rs. ${Number(item.sell_price).toLocaleString()}</div>
                </div>
              </a>
            `).join('');
          }
          suggestionsBox.classList.add('active');
        } catch (e) {
          // Ignore network errors
        }
      }, 250);
    });

    document.addEventListener('click', (e) => {
      if (!wrap.contains(e.target)) {
        suggestionsBox.classList.remove('active');
      }
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        suggestionsBox.classList.remove('active');
      }
    });
  });
}

// -------------------------------------------------------------
// 4. Accessible Real-Time Carousel with Live Dot Tracking (A6)
// -------------------------------------------------------------
function initHeroCarousel() {
  const carousels = document.querySelectorAll('.hero-carousel, .carousel');
  if (!carousels.length) return;

  const isReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  carousels.forEach(carousel => {
    const track = carousel.querySelector('.carousel-track');
    const slides = carousel.querySelectorAll('.carousel-slide');
    const dots = carousel.querySelectorAll('.carousel-dot');
    const prevBtn = carousel.querySelector('.carousel-arrow-prev');
    const nextBtn = carousel.querySelector('.carousel-arrow-next');

    if (slides.length <= 1) return;

    let currentIndex = 0;
    let timer = null;
    let isScrolling = false;
    let scrollTimeout = null;

    function updateActiveDot(index) {
      if (index < 0 || index >= slides.length) return;
      currentIndex = index;
      dots.forEach((dot, i) => {
        const isActive = (i === currentIndex);
        dot.classList.toggle('active', isActive);
        dot.setAttribute('aria-current', isActive ? 'true' : 'false');
      });
    }

    function goToSlide(index) {
      const targetIndex = (index + slides.length) % slides.length;
      updateActiveDot(targetIndex);
      track.scrollTo({
        left: slides[targetIndex].offsetLeft,
        behavior: isReducedMotion ? 'auto' : 'smooth'
      });
    }

    function startAutoplay() {
      if (isReducedMotion) return;
      stopAutoplay();
      timer = setInterval(() => {
        goToSlide(currentIndex + 1);
      }, 5000);
    }

    function stopAutoplay() {
      if (timer) clearInterval(timer);
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', () => {
        goToSlide(currentIndex + 1);
        startAutoplay();
      });
    }

    if (prevBtn) {
      prevBtn.addEventListener('click', () => {
        goToSlide(currentIndex - 1);
        startAutoplay();
      });
    }

    dots.forEach((dot, i) => {
      dot.setAttribute('aria-current', i === 0 ? 'true' : 'false');
      dot.addEventListener('click', () => {
        goToSlide(i);
        startAutoplay();
      });
    });

    // Real-Time Passive Scroll Listener throttled with requestAnimationFrame
    track.addEventListener('scroll', () => {
      if (!isScrolling) {
        window.requestAnimationFrame(() => {
          const slideWidth = track.clientWidth || (slides[0] ? slides[0].offsetWidth : 0);
          if (slideWidth > 0) {
            const rawIndex = track.scrollLeft / slideWidth;
            const newIndex = Math.min(slides.length - 1, Math.max(0, Math.round(rawIndex)));
            if (newIndex !== currentIndex) {
              updateActiveDot(newIndex);
            }
          }
          isScrolling = false;
        });
        isScrolling = true;
      }

      // Reset autoplay countdown after manual swipe
      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(startAutoplay, 4000);
    }, { passive: true });

    // Modern 'scrollend' event fallback where supported
    if ('onscrollend' in window) {
      track.addEventListener('scrollend', () => {
        const slideWidth = track.clientWidth || (slides[0] ? slides[0].offsetWidth : 0);
        if (slideWidth > 0) {
          const finalIndex = Math.min(slides.length - 1, Math.max(0, Math.round(track.scrollLeft / slideWidth)));
          updateActiveDot(finalIndex);
        }
      }, { passive: true });
    }

    // IntersectionObserver backup for momentum scrolling
    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting && entry.intersectionRatio >= 0.55) {
            const idx = Array.prototype.indexOf.call(slides, entry.target);
            if (idx !== -1 && idx !== currentIndex) {
              updateActiveDot(idx);
            }
          }
        });
      }, {
        root: track,
        threshold: [0.55, 0.9]
      });

      slides.forEach(slide => observer.observe(slide));
    }

    // Interaction pause rules: pause on touch, hover and focus
    carousel.addEventListener('mouseenter', stopAutoplay);
    carousel.addEventListener('mouseleave', startAutoplay);
    carousel.addEventListener('focusin', stopAutoplay);
    carousel.addEventListener('focusout', startAutoplay);
    carousel.addEventListener('touchstart', stopAutoplay, { passive: true });
    carousel.addEventListener('touchend', () => {
      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(startAutoplay, 4000);
    }, { passive: true });

    startAutoplay();
  });
}

// -------------------------------------------------------------
// 5. Back-to-Top with Real-Time SVG Scroll Progress Ring
// -------------------------------------------------------------
function initBackToTopProgress() {
  const btn = document.getElementById('btn-back-to-top');
  const circle = document.querySelector('.progress-ring-circle');
  if (!btn || !circle) return;

  const circumference = 140; // 2 * PI * r (r=22.28)
  circle.style.strokeDasharray = `${circumference}`;

  function updateProgress() {
    const scrollTop = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    const progress = docHeight > 0 ? scrollTop / docHeight : 0;
    const offset = circumference - (progress * circumference);

    circle.style.strokeDashoffset = `${offset}`;

    if (scrollTop > 300) {
      btn.classList.add('visible');
    } else {
      btn.classList.remove('visible');
    }
  }

  window.addEventListener('scroll', () => {
    window.requestAnimationFrame(updateProgress);
  }, { passive: true });

  btn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

// -------------------------------------------------------------
// 6. Mobile Off-Canvas Drawer
// -------------------------------------------------------------
function initMobileDrawer() {
  const openBtn = document.getElementById('mobile-drawer-open');
  const closeBtn = document.getElementById('mobile-drawer-close');
  const drawer = document.getElementById('mobile-drawer');
  const backdrop = document.getElementById('mobile-drawer-backdrop');

  if (!drawer || !backdrop) return;

  function openDrawer() {
    drawer.classList.add('open');
    backdrop.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    drawer.classList.remove('open');
    backdrop.classList.remove('open');
    document.body.style.overflow = '';
  }

  if (openBtn) openBtn.addEventListener('click', openDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  backdrop.addEventListener('click', closeDrawer);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && drawer.classList.contains('open')) {
      closeDrawer();
    }
  });
}

// -------------------------------------------------------------
// 7. Accessible Dropdowns ("More" menu)
// -------------------------------------------------------------
function initDropdowns() {
  const dropdowns = document.querySelectorAll('.dropdown');

  dropdowns.forEach(dd => {
    const trigger = dd.querySelector('.dropdown-trigger');
    if (!trigger) return;

    trigger.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdowns.forEach(other => {
        if (other !== dd) other.classList.remove('open');
      });
      dd.classList.toggle('open');
    });
  });

  document.addEventListener('click', () => {
    dropdowns.forEach(dd => dd.classList.remove('open'));
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      dropdowns.forEach(dd => dd.classList.remove('open'));
    }
  });
}

// -------------------------------------------------------------
// 8. Popularity Beacon Tracking
// -------------------------------------------------------------
function initBeaconTracking() {
  const productIdMeta = document.querySelector('meta[name="product-id"]');
  if (productIdMeta) {
    const id = productIdMeta.getAttribute('content');
    if (id && navigator.sendBeacon) {
      navigator.sendBeacon('/api/beacon', new URLSearchParams({ product_id: id, event: 'views' }));
    }
  }

  // Track WhatsApp button clicks
  document.addEventListener('click', (e) => {
    const waLink = e.target.closest('a[href*="wa.me"]');
    if (waLink) {
      const prodCard = waLink.closest('[data-product-id]');
      const id = prodCard ? prodCard.getAttribute('data-product-id') : null;
      if (id && navigator.sendBeacon) {
        navigator.sendBeacon('/api/beacon', new URLSearchParams({ product_id: id, event: 'whatsapp_clicks' }));
      }
    }
  });
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

// -------------------------------------------------------------
// 9. WhatsApp Product Sharing & GA4 Consent Event (A1)
// -------------------------------------------------------------
window.shareOnWhatsApp = function(url, title) {
  // Fire GA4 share event only after consent
  if (typeof gtag === 'function' && window.mtaAnalyticsConsent) {
    try {
      gtag('event', 'share', {
        method: 'WhatsApp',
        content_type: 'product',
        item_id: url
      });
    } catch (e) {}
  }
};

window.shareProductWhatsApp = function(title, price, url) {
  const fullUrl = url || window.location.href;
  const cleanUrl = fullUrl.split('?')[0];
  const sep = cleanUrl.includes('?') ? '&' : '?';
  const targetUrl = cleanUrl + sep + 'utm_source=whatsapp&utm_medium=share';
  const priceText = price || 'Ask Price on WhatsApp';
  const text = title + "\n" + priceText + "\n" + targetUrl;
  
  window.shareOnWhatsApp(targetUrl, title);
  window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank', 'noopener,noreferrer');
};

// -------------------------------------------------------------
// 10. Copy Link with Toast & Textarea Fallback (A1)
// -------------------------------------------------------------
window.copyProductLink = function(url) {
  const cleanUrl = (url || window.location.href).split('?')[0];
  
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(cleanUrl).then(() => {
      showToast('Link copied');
    }).catch(() => {
      fallbackCopyText(cleanUrl);
    });
  } else {
    fallbackCopyText(cleanUrl);
  }
};

function fallbackCopyText(text) {
  const textArea = document.createElement('textarea');
  textArea.value = text;
  textArea.style.position = 'fixed';
  textArea.style.left = '-9999px';
  textArea.style.top = '0';
  textArea.setAttribute('readonly', '');
  document.body.appendChild(textArea);
  textArea.focus();
  textArea.select();
  try {
    const success = document.execCommand('copy');
    showToast(success ? 'Link copied' : 'Failed to copy link');
  } catch (err) {
    showToast('Link copied');
  }
  document.body.removeChild(textArea);
}

window.showToast = function(msg) {
  let toast = document.getElementById('mta-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'mta-toast';
    toast.className = 'mta-toast';
    document.body.appendChild(toast);
  }
  toast.textContent = msg;
  toast.classList.add('show');
  clearTimeout(toast._timeout);
  toast._timeout = setTimeout(() => {
    toast.classList.remove('show');
  }, 2400);
};
