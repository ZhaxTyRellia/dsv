<?php
if (isset($_POST['set_identity_option'])) {
    $option = trim($_POST['identity_option'] ?? '');
    if (in_array($option, ['1', '2', '3', '4', '5', '6', '7', '8', '9'])) {
        $id = $row['id'] ?? 1;
        $stmt = mysqli_prepare($bridge, "UPDATE identity SET options = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $option, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($success) {
            $modalOutput .= "<script>showToast('Option updated successfully.', 'success');</script>";
        } else {
            $modalOutput .= "<script>showToast('Failed to update option.', 'danger');</script>";
        }
    } else {
        $modalOutput .= "<script>showToast('Invalid option selected.', 'warning');</script>";
    }

    $modalOutput .= "<script>setTimeout(function(){ window.location = window.location.pathname; }, 5200);</script>";
}
