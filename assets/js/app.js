document.addEventListener('submit', function (event) {
  const form = event.target;
  const message = form.getAttribute('data-confirm');
  if (message && !window.confirm(message)) {
    event.preventDefault();
    return;
  }

  if (form.method.toLowerCase() === 'post' && !form.hasAttribute('data-no-loading')) {
    const submitter = event.submitter || form.querySelector('button[type="submit"], input[type="submit"]');
    if (submitter && !submitter.disabled) {
      submitter.disabled = true;
      submitter.setAttribute('aria-busy', 'true');
      if (submitter.tagName === 'BUTTON') {
        submitter.dataset.originalText = submitter.textContent;
        submitter.textContent = submitter.dataset.loadingText || 'Processing...';
      }
    }
  }
});

document.addEventListener('change', function (event) {
  const input = event.target;
  if (input.type !== 'file' || input.name !== 'image' || !input.files.length) {
    return;
  }

  const file = input.files[0];
  const help = input.closest('label')?.querySelector('.file-help');
  if (help) {
    help.textContent = file.name;
  }
});
