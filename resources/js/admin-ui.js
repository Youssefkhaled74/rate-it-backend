/**
 * Rate It Admin UI Kit - Alpine.js Helpers
 * 
 * Initialize global Alpine components and utilities
 * Include this after Alpine.js loads
 */

document.addEventListener('alpine:init', () => {
  /**
   * Main app component - manages theme, direction, and global state
   */
  Alpine.data('app', () => ({
    sidebarOpen: false,
    notificationsOpen: false,
    profileMenuOpen: false,
    theme: localStorage.getItem('admin_theme') || 'light',
    direction: localStorage.getItem('admin_direction') || 'ltr',
    
    init() {
      // Close sidebar on large screens
      window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
          this.sidebarOpen = false;
        }
      });
      
      // Close menus on escape key
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          this.closeMenus();
        }
      });
      
      // Sync theme from localStorage
      this.syncTheme();
    },
    
    toggleTheme() {
      this.theme = this.theme === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', this.theme);
      localStorage.setItem('admin_theme', this.theme);
      this.$dispatch('theme-changed', { theme: this.theme });
    },
    
    toggleDirection() {
      this.direction = this.direction === 'rtl' ? 'ltr' : 'rtl';
      document.documentElement.setAttribute('dir', this.direction);
      document.documentElement.lang = this.direction === 'rtl' ? 'ar' : 'en';
      localStorage.setItem('admin_direction', this.direction);
      this.$dispatch('direction-changed', { direction: this.direction });
    },
    
    syncTheme() {
      const theme = localStorage.getItem('admin_theme') || 'light';
      this.theme = theme;
      document.documentElement.setAttribute('data-theme', theme);
    },
    
    closeMenus() {
      this.notificationsOpen = false;
      this.profileMenuOpen = false;
    }
  }));

  /**
   * Toast notification component
   */
  Alpine.data('toast', () => ({
    show: false,
    message: '',
    type: 'success',
    duration: 5000,
    
    init() {
      if (this.$el.dataset.autoshow === 'true') {
        this.display();
      }
    },
    
    display(message = '', type = 'success', duration = 5000) {
      this.message = message || this.message;
      this.type = type;
      this.duration = duration;
      this.show = true;
      
      setTimeout(() => {
        this.show = false;
      }, duration);
    }
  }));

  /**
   * Modal component wrapper
   */
  Alpine.data('modal', () => ({
    open: false,
    
    init() {
      const id = this.$el.dataset.modal;
      if (id) {
        window.addEventListener(`modal-${id}-open`, () => {
          this.open = true;
        });
        window.addEventListener(`modal-${id}-close`, () => {
          this.open = false;
        });
      }
    },
    
    close() {
      this.open = false;
    }
  }));

  /**
   * Table selection handler
   */
  Alpine.data('tableSelect', () => ({
    selected: [],
    selectAll: false,
    
    toggleAll(items) {
      if (this.selectAll) {
        this.selected = items.map(item => item.id);
      } else {
        this.selected = [];
      }
    },
    
    toggleItem(id) {
      const index = this.selected.indexOf(id);
      if (index > -1) {
        this.selected.splice(index, 1);
      } else {
        this.selected.push(id);
      }
    },
    
    isSelected(id) {
      return this.selected.includes(id);
    },
    
    getSelectedCount() {
      return this.selected.length;
    }
  }));

  /**
   * Form validation helper
   */
  Alpine.data('formValidator', () => ({
    errors: {},
    touched: {},
    
    setError(field, message) {
      this.errors[field] = message;
    },
    
    clearError(field) {
      delete this.errors[field];
    },
    
    setTouched(field) {
      this.touched[field] = true;
    },
    
    hasError(field) {
      return this.touched[field] && this.errors[field];
    },
    
    getError(field) {
      return this.errors[field] || '';
    }
  }));

  /**
   * Dropdown component helper
   */
  Alpine.data('dropdown', () => ({
    open: false,
    
    toggle() {
      this.open = !this.open;
    },
    
    close() {
      this.open = false;
    }
  }));

  /**
   * Lazy loading image helper
   */
  Alpine.data('lazyImage', () => ({
    loaded: false,
    error: false,
    
    init() {
      if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              this.load();
              observer.unobserve(entry.target);
            }
          });
        });
        observer.observe(this.$el);
      } else {
        this.load();
      }
    },
    
    load() {
      this.loaded = true;
    },
    
    onError() {
      this.error = true;
    }
  }));

  /**
   * Textarea auto-resize helper
   */
  Alpine.data('autoResizeTextarea', () => ({
    resize() {
      this.$el.style.height = 'auto';
      this.$el.style.height = this.$el.scrollHeight + 'px';
    }
  }));
});

/**
 * Global utility functions
 */
window.AdminUI = {
  /**
   * Show toast notification
   */
  toast(message, type = 'success', duration = 5000) {
    const toast = document.createElement('div');
    toast.innerHTML = `
      <div class="fixed bottom-6 right-6 max-w-sm z-50 px-6 py-4 rounded-2xl 
        ${type === 'success' ? 'bg-[var(--success-light)] border border-[var(--success)]' : ''}
        ${type === 'error' ? 'bg-[var(--danger-light)] border border-[var(--danger)]' : ''}
        ${type === 'warning' ? 'bg-[var(--warning-light)] border border-[var(--warning)]' : ''}
        shadow-lg">
        <p class="text-sm font-semibold">${message}</p>
      </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), duration);
  },
  
  /**
   * Copy text to clipboard
   */
  copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
      this.toast('Copied to clipboard!', 'success');
    });
  },
  
  /**
   * Format currency
   */
  formatCurrency(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: currency
    }).format(amount);
  },
  
  /**
   * Format date
   */
  formatDate(date, format = 'short') {
    const options = format === 'short' 
      ? { month: 'short', day: 'numeric', year: 'numeric' }
      : { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' };
    return new Intl.DateTimeFormat('en-US', options).format(new Date(date));
  },
  
  /**
   * Confirm action dialog
   */
  confirm(message, onConfirm, onCancel = null) {
    if (window.confirm(message)) {
      onConfirm();
    } else if (onCancel) {
      onCancel();
    }
  },
  
  /**
   * Debounce function for input handlers
   */
  debounce(fn, delay = 300) {
    let timeout;
    return function(...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => fn.apply(this, args), delay);
    };
  },
  
  /**
   * Check if dark mode is enabled
   */
  isDarkMode() {
    return document.documentElement.getAttribute('data-theme') === 'dark';
  },
  
  /**
   * Check if RTL is enabled
   */
  isRTL() {
    return document.documentElement.getAttribute('dir') === 'rtl';
  }
};

export default window.AdminUI;
