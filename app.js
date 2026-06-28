document.addEventListener('submit', function (e) {
  const form = e.target;
  const submitBtn = form.querySelector('button[type="submit"]');
  if (!submitBtn || submitBtn.dataset.loadingText === undefined) return;

  submitBtn.disabled = true;
  submitBtn.classList.add('is-loading');

  const spinner = '<svg class="icon spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 3v3m0 12v3m9-9h-3M6 12H3m15.36-6.36-2.12 2.12M7.76 16.24l-2.12 2.12m12.72 0-2.12-2.12M7.76 7.76 5.64 5.64"/></svg>';
  submitBtn.innerHTML = spinner + '<span>' + submitBtn.dataset.loadingText + '</span>';
});
