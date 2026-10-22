<?php
if (isset($_POST['change_identity_btn'])) {
    $title = trim($_POST['title'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $marquee = trim($_POST['marquee'] ?? '');
    $id = $row['id'] ?? 1;

    $newLogo = $logoPath ?? '';

    error_reporting(E_ALL & ~E_NOTICE);

    $logoDir = $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/img/logo';

    if (!is_dir($logoDir)) {
        mkdir($logoDir, 0777, true);
    }

    $logoDir = realpath($logoDir);

    // Aksi 1: Hapus Logo
    if (isset($_POST['remove_logo']) && $_POST['remove_logo'] == '1') {
        if (!empty($logoPath)) {
            $oldFileName = basename($logoPath);
            $oldFileLocation = $logoDir . DIRECTORY_SEPARATOR . $oldFileName;
            if (file_exists($oldFileLocation)) {
                unlink($oldFileLocation);
            }
        }
        $newLogo = '';
    } elseif (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK && $_FILES['logo']['size'] > 0) {
        $file_name = $_FILES['logo']['name'];
        $file_temp = $_FILES['logo']['tmp_name'];
        $file_size = $_FILES['logo']['size'];
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed_ext) && $file_size < 5 * 1024 * 1024) {
            $newLogoName = 'logo_' . time() . '.' . $ext;

            $location = $logoDir . DIRECTORY_SEPARATOR . $newLogoName;

            $dbLocation = "{$newLogoName}";

            if (move_uploaded_file($file_temp, $location)) {
                if (!empty($logoPath)) {
                    $oldFileName = basename($logoPath);
                    $oldFileLocation = $logoDir . DIRECTORY_SEPARATOR . $oldFileName;
                    if (file_exists($oldFileLocation)) {
                        unlink($oldFileLocation);
                    }
                }
                $newLogo = $dbLocation;
            }
        } else {
            $modalOutput .= "<script>showToast('Invalid logo file extension or size too large.', 'danger');</script>";
        }
    }

    $stmt = mysqli_prepare($bridge, "UPDATE identity SET logo = ?, title = ?, name = ?, marquee = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssssi", $newLogo, $title, $name, $marquee, $id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($success) {
        $modalOutput .= "<script>showToast('Identity and logo updated successfully.', 'success');</script>";
    } else {
        $modalOutput .= "<script>showToast('Failed to update identity data.', 'danger');</script>";
    }

    $modalOutput .= "<script>setTimeout(function(){ window.location = window.location.pathname; }, 5200);</script>";
}
