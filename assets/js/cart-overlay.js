(() => {
  const overlay = document.querySelector('.ehz-cart-overlay');
  const trigger = document.querySelector('[data-ehz-cart-trigger]');
  if (!overlay || !trigger) {
    return;
  }

  const closeTargets = overlay.querySelectorAll('[data-ehz-cart-close]');
  const continueButton = overlay.querySelector('[data-ehz-cart-continue]');
  const dialog = overlay.querySelector('.ehz-cart-overlay__panel');

  const open = () => {
    overlay.classList.add('is-open');
    overlay.removeAttribute('hidden');
    trigger.setAttribute('aria-expanded', 'true');
    dialog?.focus();
  };

  const close = () => {
    overlay.classList.remove('is-open');
    overlay.setAttribute('hidden', '');
    trigger.setAttribute('aria-expanded', 'false');
    trigger.focus();
  };

  trigger.addEventListener('click', (event) => {
    event.preventDefault();
    open();
  });

  closeTargets.forEach((node) => {
    node.addEventListener('click', close);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
      close();
    }
  });

  continueButton?.addEventListener('click', () => {
    const target =
      continueButton.getAttribute('data-ehz-cart-target') ||
      (window.ehzWcCartOverlay && window.ehzWcCartOverlay.checkoutUrl);
    if (target) {
      window.location.href = target;
    }
  });
})();
