/**
 * Multan Tech Accessories — Admin Console JavaScript (Part B)
 * MultanTech.store
 */

(function () {
  'use strict';

  // ---------------------------------------------------------------------------
  // 1. Theme Management (Day / Night Mode)
  // ---------------------------------------------------------------------------
  const STORAGE_KEY = 'mta_admin_theme';

  function initTheme() {
    const savedTheme = localStorage.getItem(STORAGE_KEY);
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const theme = savedTheme || (prefersDark ? 'dark' : 'light');
    applyTheme(theme);
  }

  function applyTheme(theme) {
    if (theme === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
      updateThemeIcons(true);
    } else {
      document.documentElement.removeAttribute('data-theme');
      updateThemeIcons(false);
    }
    localStorage.setItem(STORAGE_KEY, theme);
  }

  function toggleTheme() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    applyTheme(isDark ? 'light' : 'dark');
  }

  function updateThemeIcons(isDark) {
    const btn = document.getElementById('theme-toggle-btn');
    if (!btn) return;
    btn.setAttribute('aria-label', isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode');
    btn.innerHTML = isDark
      ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>'
      : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>';
  }

  // ---------------------------------------------------------------------------
  // 2. Mobile Sidebar Off-Canvas Navigation
  // ---------------------------------------------------------------------------
  function initMobileSidebar() {
    const toggleBtn = document.getElementById('sidebar-toggle-btn');
    const closeBtn = document.getElementById('sidebar-close-btn');
    const sidebar = document.getElementById('admin-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');

    if (!sidebar) return;

    function openSidebar() {
      sidebar.classList.add('open');
      if (backdrop) backdrop.classList.add('open');
      document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
      sidebar.classList.remove('open');
      if (backdrop) backdrop.classList.remove('open');
      document.body.style.overflow = '';
    }

    if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (backdrop) backdrop.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && sidebar.classList.contains('open')) {
        closeSidebar();
      }
    });
  }

  // ---------------------------------------------------------------------------
  // 3. Export Dialog & Re-Auth Modal
  // ---------------------------------------------------------------------------
  function initModals() {
    const exportBtn = document.getElementById('quick-export-btn');
    const exportModal = document.getElementById('exportModal');
    const reauthModal = document.getElementById('reauthModal');

    function openModal(modal) {
      if (!modal) return;
      modal.classList.add('active');
      modal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
      const input = modal.querySelector('input, button');
      if (input) input.focus();
    }

    function closeModal(modal) {
      if (!modal) return;
      modal.classList.remove('active');
      modal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }

    if (exportBtn && exportModal) {
      exportBtn.addEventListener('click', function (e) {
        e.preventDefault();
        openModal(exportModal);
      });
    }

    document.querySelectorAll('.modal-close, .modal-backdrop').forEach(function (el) {
      el.addEventListener('click', function () {
        const modal = el.closest('.admin-modal-overlay');
        if (modal) closeModal(modal);
      });
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        document.querySelectorAll('.admin-modal-overlay.active').forEach(closeModal);
      }
    });

    // Handle forms requiring re-authentication
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
      el.addEventListener('click', function (e) {
        const msg = el.getAttribute('data-confirm') || 'Are you sure you want to perform this action?';
        if (!confirm(msg)) {
          e.preventDefault();
        }
      });
    });
  }

  // ---------------------------------------------------------------------------
  // 4. Gross Sales KPI Range Filter
  // ---------------------------------------------------------------------------
  function initKpiRangeFilter() {
    const rangeButtons = document.querySelectorAll('.kpi-range-btn');
    if (!rangeButtons.length) return;

    rangeButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const range = btn.getAttribute('data-range') || 'all';
        const url = new URL(window.location.href);
        url.searchParams.set('range', range);
        window.location.href = url.toString();
      });
    });
  }

  // ---------------------------------------------------------------------------
  // 5. Toast Notifications
  // ---------------------------------------------------------------------------
  window.showAdminToast = function (message, type = 'info') {
    let container = document.getElementById('admin-toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'admin-toast-container';
      container.className = 'admin-toast-container';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `admin-toast admin-toast-${type}`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
      <div style="display:flex;align-items:center;gap:0.75rem;">
        <span style="font-weight:600;font-size:0.875rem;">${message}</span>
      </div>
    `;

    container.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(8px)';
      setTimeout(() => toast.remove(), 300);
    }, 3500);
  };

  // ---------------------------------------------------------------------------
  // Boot Controller
  // ---------------------------------------------------------------------------
  document.addEventListener('DOMContentLoaded', function () {
    initTheme();
    initMobileSidebar();
    initModals();
    initKpiRangeFilter();

    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    if (themeToggleBtn) {
      themeToggleBtn.addEventListener('click', toggleTheme);
    }
  });

  window.toggleAdminTheme = toggleTheme;
})();

