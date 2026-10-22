<?php
if (isset($_POST['save_theme_setting'])) {
    $themeVal = mysqli_real_escape_string($bridge, $_POST['theme_layout'] ?? 'show-0.css');

    $query = "UPDATE identity SET themes = '$themeVal' WHERE id = 1";
    $update = mysqli_query($bridge, $query);

    if ($update) {
        $modalOutput .= "<script>showToast('Theme successfully updated!', 'success');</script>";
    } else {
        $modalOutput .= "<script>showToast('Failed to update theme: " . mysqli_real_escape_string($bridge, mysqli_error($bridge)) . "', 'danger');</script>";
    }

    $modalOutput .= "<script>setTimeout(function(){ window.location = window.location.pathname; }, 5200);</script>";
}
