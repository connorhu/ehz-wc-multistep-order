(() => {
  const state = {
    steps: (window.ehzWcMultistepOrder && window.ehzWcMultistepOrder.steps) || [],
  };

  const progress = document.querySelector('.ehz-wc-multistep');
  const shippingExtras = document.querySelector('[data-ehz-shipping-extras]');
  if (!progress || !state.steps.length) {
    return;
  }

  const update = (index) => {
    progress.querySelectorAll('.ehz-step').forEach((node, idx) => {
      node.classList.toggle('is-active', idx === index);
      node.classList.toggle('is-complete', idx < index);
    });
  };

  update(0);

  if (!shippingExtras) {
    return;
  }

  const shippingRadios = Array.from(
    document.querySelectorAll('input[type="radio"][name^="shipping_method"]')
  );
  const phoneContainer = shippingExtras.querySelector('[data-ehz-foxpost-phone]');
  const phoneInput = shippingExtras.querySelector('[data-ehz-foxpost-phone-input]');
  const addressToggle = document.querySelector('#ship-to-different-address-checkbox');

  const shouldShowPhone = (method) => {
    return (
      method.includes('foxpost') &&
      (method.includes('automat') || method.includes('automata') || method.includes('locker'))
    );
  };

  const shouldRequireAddress = (method) => {
    if (!method) return false;

    if (method.includes('foxpost') && (method.includes('home') || method.includes('hazhoz'))) {
      return true;
    }

    return method.includes('posta');
  };

  const toggleAddress = (required) => {
    if (!addressToggle) return;

    if (required) {
      addressToggle.checked = true;
      addressToggle.dispatchEvent(new Event('change', { bubbles: true }));
    }
  };

  const togglePhoneField = (visible) => {
    if (!phoneContainer) return;
    phoneContainer.hidden = !visible;

    if (!visible && phoneInput) {
      phoneInput.value = '';
    }
  };

  const handleShippingChange = () => {
    const selected = shippingRadios.find((input) => input.checked);
    if (!selected) {
      togglePhoneField(false);
      return;
    }

    const methodId = selected.value.toLowerCase();
    togglePhoneField(shouldShowPhone(methodId));
    toggleAddress(shouldRequireAddress(methodId));
  };

  shippingRadios.forEach((input) => {
    input.addEventListener('change', handleShippingChange);
  });

  handleShippingChange();
})();
