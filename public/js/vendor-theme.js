(function(){
  const root = document.documentElement;
  const STORAGE_KEY = 'vendor_theme';

  function getPreferredTheme(){
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved === 'dark' || saved === 'light') return saved;
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    return prefersDark ? 'dark' : 'light';
  }

  function applyTheme(mode){
    if (mode === 'dark') {
      root.classList.add('dark');
    } else {
      root.classList.remove('dark');
    }
    document.querySelectorAll('[data-theme-label]')
      .forEach(el => el.textContent = mode === 'dark' ? 'Dark' : 'Light');
  }

  function toggleTheme(){
    const next = root.classList.contains('dark') ? 'light' : 'dark';
    localStorage.setItem(STORAGE_KEY, next);
    applyTheme(next);
    return next;
  }

  applyTheme(getPreferredTheme());

  document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
    btn.addEventListener('click', () => toggleTheme());
  });

  window.toggleVendorTheme = toggleTheme;
})();
