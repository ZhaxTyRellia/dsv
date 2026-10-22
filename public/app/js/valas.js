function editExchange(id, code, name, buy, sell, flag) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_currency_name').value = name;
    document.getElementById('edit_buy_rate').value = buy;
    document.getElementById('edit_sell_rate').value = sell;

    const selectElement = document.getElementById('edit_currency_code');
    selectElement.value = code.toUpperCase() + '|' + flag.toLowerCase();

    document.getElementById('editExchangeModal').showModal();
}

function submitEdit() {
    const id = document.getElementById('edit_id').value;
    const combinedValue = document.getElementById('edit_currency_code').value;
    const name = document.getElementById('edit_currency_name').value;
    const buy = document.getElementById('edit_buy_rate').value;
    const sell = document.getElementById('edit_sell_rate').value;

    if (!combinedValue || name.trim() === "" || buy.trim() === "" || sell.trim() === "") {
        alert("All data must be filled in!");
        return;
    }

    const [code, flag] = combinedValue.split('|');

    window.location.href = window.location.pathname +
        "?action=edit" +
        "&id=" + id +
        "&currency_code=" + encodeURIComponent(code) +
        "&currency_name=" + encodeURIComponent(name) +
        "&buy_rate=" + encodeURIComponent(buy) +
        "&sell_rate=" + encodeURIComponent(sell) +
        "&flag_image=" + encodeURIComponent(flag);
}