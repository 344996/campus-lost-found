document.addEventListener('submit', function (event) {
  const form = event.target;
  const message = form.getAttribute('data-confirm');
  if (message && !window.confirm(message)) {
    event.preventDefault();
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

