document.querySelectorAll('[data-address-toggle]').forEach((select) => {
    const address = document.querySelector(select.dataset.addressToggle);
    if (!address) return;

    const input = address.querySelector('input');
    const update = () => {
        const required = select.value === 'delivery';
        address.hidden = !required;
        input.required = required;
        if (!required) input.value = '';
    };

    select.addEventListener('change', update);
    update();
});
