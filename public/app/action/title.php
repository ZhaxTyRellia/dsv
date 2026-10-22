<?php
if (isset($_POST['update_titles'])) {
    $title_1 = trim($_POST['title_1'] ?? '');
    $title_2 = trim($_POST['title_2'] ?? '');
    $title_3 = trim($_POST['title_3'] ?? '');
    $title_4 = trim($_POST['title_4'] ?? '');
    $title_5 = trim($_POST['title_5'] ?? '');
    $title_6 = trim($_POST['title_6'] ?? '');
    $title_7 = trim($_POST['title_7'] ?? '');
    $title_8 = trim($_POST['title_8'] ?? '');
    $title_9 = trim($_POST['title_9'] ?? '');

    $result = mysqli_query($bridge, "SELECT id_information FROM titles ORDER BY id_information DESC LIMIT 1");
    if ($result && $tRow = mysqli_fetch_assoc($result)) {
        $id_information = intval($tRow['id_information']);
        $stmt = mysqli_prepare($bridge, "UPDATE titles SET title_1=?, title_2=?, title_3=?, title_4=?, title_5=?, title_6=?, title_7=?, title_8=?, title_9=? WHERE id_information=?");
        mysqli_stmt_bind_param($stmt, "sssssssssi", $title_1, $title_2, $title_3, $title_4, $title_5, $title_6, $title_7, $title_8, $title_9, $id_information);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $stmt = mysqli_prepare($bridge, "INSERT INTO titles (title_1, title_2, title_3, title_4, title_5, title_6, title_7, title_8, title_9) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssssss", $title_1, $title_2, $title_3, $title_4, $title_5, $title_6, $title_7, $title_8, $title_9);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    if ($success) {
        $modalOutput .= "<script>showToast('Section titles updated successfully.', 'success');</script>";
    } else {
        $modalOutput .= "<script>showToast('Failed to update section titles.', 'danger');</script>";
    }

    $modalOutput .= "<script>setTimeout(function(){ window.location = window.location.pathname; }, 5200);</script>";
}
