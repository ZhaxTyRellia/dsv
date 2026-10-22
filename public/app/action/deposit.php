<?php
// ==========================================
// 1. TAMBAH DATA DEPOSITO (INSERT)
// ==========================================
if (isset($_POST['submit_deposit'])) {
    // Tambahkan validasi range $dep_index di deposit.php
    $dep_index = (int)($_POST['deposit_index'] ?? 1);
    if ($dep_index < 1 || $dep_index > 8) {
        $dep_index = 1;
    }
    $tableName = "deposit_" . $dep_index;

    $tenor         = trim($_POST['tenor'] ?? '');
    $interest_rate = (float)($_POST['interest_rate'] ?? 0); // Pastikan dikonversi ke float
    $min_amount    = (float)($_POST['min_amount'] ?? 0);    // Pastikan dikonversi ke float
    $description   = trim($_POST['description'] ?? '');

    $stmt = mysqli_prepare($bridge, "INSERT INTO `{$tableName}` (tenor, interest_rate, min_amount, description) VALUES (?, ?, ?, ?)");

    // Perhatikan binding: 's' (string), 'd' (double/float), 'd' (double/float), 's' (string)
    mysqli_stmt_bind_param($stmt, "sdds", $tenor, $interest_rate, $min_amount, $description);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($success) {
        $modalOutput .= "<script>showToast('Data Deposito {$dep_index} berhasil ditambahkan!', 'success');</script>";
    } else {
        $modalOutput .= "<script>showToast('Gagal menambahkan data Deposito.', 'danger');</script>";
    }

    $modalOutput .= "<script>setTimeout(function(){ window.location = window.location.pathname; }, 5200);</script>";
}

// ==========================================
// 2. HAPUS DATA DEPOSITO (DELETE)
// ==========================================
if (isset($_GET['action']) && $_GET['action'] == 'delete_deposit') {
    $dep_index = (int)($_GET['dep_index'] ?? 1);
    $id        = (int)($_GET['id'] ?? 0);
    $tableName = "deposit_" . $dep_index;

    $stmt = mysqli_prepare($bridge, "DELETE FROM `{$tableName}` WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($success) {
        $modalOutput .= "<script>showToast('Data Deposito {$dep_index} berhasil dihapus!', 'success');</script>";
    } else {
        $modalOutput .= "<script>showToast('Gagal menghapus data Deposito.', 'danger');</script>";
    }

    $modalOutput .= "<script>setTimeout(function(){ window.location = window.location.pathname; }, 5200);</script>";
}

// ==========================================
// 3. EDIT DATA DEPOSITO (UPDATE)
// ==========================================
if (isset($_POST['update_deposit_item'])) {
    $dep_index = (int)($_POST['deposit_index'] ?? 1);
    $id        = (int)($_POST['deposit_id'] ?? 0);
    $tableName = "deposit_" . $dep_index;

    $tenor         = trim($_POST['tenor'] ?? '');
    $interest_rate = (float)($_POST['interest_rate'] ?? 0);
    $min_amount    = (float)($_POST['min_amount'] ?? 0);
    $description   = trim($_POST['description'] ?? '');

    $stmt = mysqli_prepare($bridge, "UPDATE `{$tableName}` SET tenor = ?, interest_rate = ?, min_amount = ?, description = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sddsi", $tenor, $interest_rate, $min_amount, $description, $id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($success) {
        $modalOutput .= "<script>showToast('Data Deposito {$dep_index} berhasil diperbarui!', 'success');</script>";
    } else {
        $modalOutput .= "<script>showToast('Gagal memperbarui data Deposito.', 'danger');</script>";
    }

    $modalOutput .= "<script>setTimeout(function(){ window.location = window.location.pathname; }, 5200);</script>";
}
