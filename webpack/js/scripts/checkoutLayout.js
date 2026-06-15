"use strict";

/**
 * Reorganiza o Checkout Block: coloca "Endereço" e "Número" na mesma linha.
 */
export default function checkoutLayout() {
  const pair = (addressInputId, numberInputSelector) => {
    const addressInput = document.getElementById(addressInputId);
    const numberInput = document.querySelector(numberInputSelector);

    if (!addressInput || !numberInput) return false;

    const addressRow = addressInput.closest('.wc-block-components-text-input') || addressInput.parentElement;
    const numberRow = numberInput.closest('.wc-block-components-text-input') || numberInput.parentElement;

    if (!addressRow || !numberRow) return false;
    if (addressRow.parentElement?.classList.contains('c-checkout-row-2')) return true; // já organizado

    const wrapper = document.createElement('div');
    wrapper.classList.add('c-checkout-row-2');

    addressRow.parentNode.insertBefore(wrapper, addressRow);
    wrapper.appendChild(addressRow);
    wrapper.appendChild(numberRow);

    return true;
  };

  const run = () => {
    const billingDone = pair('billing-address_1', '[id^="billing-arterra"]');
    const shippingDone = pair('shipping-address_1', '[id^="shipping-arterra"]');
    return billingDone && shippingDone;
  };

  if (run()) return;

  const observer = new MutationObserver(() => {
    if (run()) observer.disconnect();
  });

  observer.observe(document.body, { childList: true, subtree: true });
}
