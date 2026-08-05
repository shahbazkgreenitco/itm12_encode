
// (function () {
//   const html = document.documentElement;
//   const stored = localStorage.getItem('theme');
//   if (stored) html.setAttribute('data-bs-theme', stored);

//   document.addEventListener('click', function (e) {
//     if (!e.target.closest('[data-theme-toggle]')) return;
//     const current = html.getAttribute('data-bs-theme') || 'light';
//     const next = current === 'dark' ? 'light' : 'dark';
//     html.setAttribute('data-bs-theme', next);
//     localStorage.setItem('theme', next);
//   });
// })();


(function () {
  const html = document.documentElement;
  const stored = localStorage.getItem('theme');

  if (stored) html.setAttribute('data-bs-theme', stored);

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-theme-toggle]');
    if (!btn) return;

    const current = html.getAttribute('data-bs-theme') || 'light';
    const next = current === 'dark' ? 'light' : 'dark';

    // Update theme
    html.setAttribute('data-bs-theme', next);
    localStorage.setItem('theme', next);

    // Tooltip logic
    const tooltipText =
      next === 'dark' ? 'Light Mode' : 'Dark Mode';

    btn.setAttribute('title', tooltipText);

    // Bootstrap tooltip refresh
    const tooltip =
      bootstrap.Tooltip.getInstance(btn) ||
      new bootstrap.Tooltip(btn);

    tooltip.setContent({ '.tooltip-inner': tooltipText });
  });
})();