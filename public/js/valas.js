function editExchange(id, code, name, buy, sell, flag) {
    const idInput = document.getElementById('edit_id');
    const nameInput = document.getElementById('edit_currency_name');
    const buyInput = document.getElementById('edit_buy_rate');
    const sellInput = document.getElementById('edit_sell_rate');
    const selectElement = document.getElementById('edit_currency_code');
    const modal = document.getElementById('editExchangeModal');

    if (idInput) idInput.value = id;
    if (nameInput) nameInput.value = name;
    if (buyInput) buyInput.value = buy;
    if (sellInput) sellInput.value = sell;
    if (selectElement) selectElement.value = code.toUpperCase() + '|' + flag.toLowerCase();

    if (modal && typeof modal.showModal === 'function') {
        modal.showModal();
    }
}

function submitEdit() {
    const form = document.getElementById('editValasForm');
    if (form) {
        form.submit();
    }
}
