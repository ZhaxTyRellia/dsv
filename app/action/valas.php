<?php
if (isset($_POST['submit_kurs'])) {
    $currency_data = trim($_POST['currency_data'] ?? '');

    $currency_code = '';
    $flag_image    = '';

    if (!empty($currency_data) && strpos($currency_data, '|') !== false) {
        list($currency_code, $flag_image) = explode('|', $currency_data);
    }

    $currency_name = trim($_POST['currency_name'] ?? '');
    $sell_rate     = trim($_POST['sell_rate'] ?? '0');
    $buy_rate      = trim($_POST['buy_rate'] ?? '0');

    $stmt = mysqli_prepare($bridge, "INSERT INTO valas (currency_code, currency_name, flag_image, buy_rate, sell_rate) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $currency_code, $currency_name, $flag_image, $buy_rate, $sell_rate);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($success) {
        $modalOutput .= "<script>showToast('Exchange rate data successfully saved!', 'success');</script>";
    } else {
        $modalOutput .= "<script>showToast('Failed to save exchange rate data.', 'danger');</script>";
    }

    $modalOutput .= "<script>setTimeout(function(){ window.location = window.location.pathname; }, 5200);</script>";
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = (int)($_GET['id'] ?? 0);

    $stmt = mysqli_prepare($bridge, "DELETE FROM valas WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($success) {
        $modalOutput .= "<script>showToast('Exchange rate data successfully deleted!', 'success');</script>";
    } else {
        $modalOutput .= "<script>showToast('Failed to delete exchange rate data.', 'danger');</script>";
    }

    $modalOutput .= "<script>setTimeout(function(){ window.location = window.location.pathname; }, 5200);</script>";
}

if (isset($_GET['action']) && $_GET['action'] == 'edit') {
    $id            = (int)($_GET['id'] ?? 0);
    $currency_code = trim($_GET['currency_code'] ?? '');
    $currency_name = trim($_GET['currency_name'] ?? '');
    $flag_image    = trim($_GET['flag_image'] ?? '');
    $buy_rate      = trim($_GET['buy_rate'] ?? '0');
    $sell_rate     = trim($_GET['sell_rate'] ?? '0');

    $stmt = mysqli_prepare($bridge, "UPDATE valas SET currency_code = ?, currency_name = ?, flag_image = ?, buy_rate = ?, sell_rate = ? WHERE id = ?");

    mysqli_stmt_bind_param($stmt, "sssssi", $currency_code, $currency_name, $flag_image, $buy_rate, $sell_rate, $id);

    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($success) {
        $modalOutput .= "<script>showToast('Exchange rate data successfully updated!', 'success');</script>";
    } else {
        $modalOutput .= "<script>showToast('Failed to update exchange rate data.', 'danger');</script>";
    }

    $modalOutput .= "<script>setTimeout(function(){ window.location = window.location.pathname; }, 5200);</script>";
}
