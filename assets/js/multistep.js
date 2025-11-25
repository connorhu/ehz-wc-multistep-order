(() => {
  const state = {
    steps: (window.ehzWcMultistepOrder && window.ehzWcMultistepOrder.steps) || [],
  };

  const progress = document.querySelector('.ehz-wc-multistep');
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
})();
