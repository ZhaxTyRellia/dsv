<?php
if (isset($_POST['submit_video'])) {
    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK && $_FILES['video_file']['size'] > 0) {
        $file_name = $_FILES['video_file']['name'];
        $file_temp = $_FILES['video_file']['tmp_name'];
        $file_size = $_FILES['video_file']['size'];

        $videoDir = $_SERVER['DOCUMENT_ROOT'] . '/dsv/app/videos';
        if (!is_dir($videoDir)) {
            mkdir($videoDir, 0777, true);
        }
        $videoDir = realpath($videoDir);

        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['mp4'];

        if (in_array($ext, $allowed_ext) && $file_size < 50 * 1024 * 1024) {
            $newVideoName = 'video_' . time() . '.' . $ext;
            $location = $videoDir . DIRECTORY_SEPARATOR . $newVideoName;

            if (move_uploaded_file($file_temp, $location)) {
                $currentDate = date('Y-m-d');

                $stmt = mysqli_prepare($bridge, "INSERT INTO videos (file_name, create_at) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt, "ss", $newVideoName, $currentDate);
                $success = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                if ($success) {
                    $modalOutput .= "<script>showToast('Video uploaded and saved successfully!', 'success');</script>";
                } else {
                    $modalOutput .= "<script>showToast('Failed to save video to database.', 'danger');</script>";
                }
            } else {
                $modalOutput .= "<script>showToast('Failed to move uploaded file to destination.', 'danger');</script>";
            }
        } else {
            $modalOutput .= "<script>showToast('Invalid video extension (Only .mp4 allowed) or file size too large.', 'danger');</script>";
        }
    } else {
        $modalOutput .= "<script>showToast('Please choose a valid video file to upload.', 'warning');</script>";
    }

    $modalOutput .= "<script>setTimeout(function(){ window.location = window.location.pathname; }, 5200);</script>";
}

if (isset($_GET['action']) && $_GET['action'] == 'delete_video') {
    $id = (int)($_GET['id'] ?? 0);

    $query = mysqli_prepare($bridge, "SELECT file_name FROM videos WHERE id = ?");
    mysqli_stmt_bind_param($query, "i", $id);
    mysqli_stmt_execute($query);
    $res = mysqli_stmt_get_result($query);
    $videoData = mysqli_fetch_assoc($res);
    mysqli_stmt_close($query);

    if ($videoData) {
        $fileName = $videoData['file_name'];
        $videoFileLocation = realpath($_SERVER['DOCUMENT_ROOT'] . '/dsv/app/videos') . DIRECTORY_SEPARATOR . $fileName;

        if (!empty($fileName) && file_exists($videoFileLocation)) {
            unlink($videoFileLocation);
        }

        $stmt = mysqli_prepare($bridge, "DELETE FROM videos WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($success) {
            $modalOutput .= "<script>showToast('Video deleted successfully!', 'success');</script>";
        } else {
            $modalOutput .= "<script>showToast('Failed to delete video from database.', 'danger');</script>";
        }
    } else {
        $modalOutput .= "<script>showToast('Video data not found.', 'warning');</script>";
    }

    $modalOutput .= "<script>setTimeout(function(){ window.location = window.location.pathname; }, 5200);</script>";
}
