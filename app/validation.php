<?php
require_once 'maincore.php';

if (!$is_licensed) {
?>
    <!DOCTYPE HTML>
    <html lang="en">

    <head>
        <title>Verification License</title>
        <meta name="description" content="The small framework with powerful features">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" type="image/png" href="app/img/favicon.png">
        <style>
            body {
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                background-color: #f4f6f9;
                background-image: url("../dsv/app/img/login.png");
                background-repeat: no-repeat;
                background-position: center center;
                background-size: cover;
            }

            .license-container {
                width: 100%;
                max-width: 500px;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 8px;
                font-family: sans-serif;
                background: #fff;
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
                box-sizing: border-box;
                justify-items: center;
            }

            .form-input {
                width: 100%;
                padding: 10px;
                margin: 10px 0;
                box-sizing: border-box;
                font-family: monospace;
            }

            .btn-submit {
                background: #28a745;
                color: white;
                padding: 10px 15px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                width: 100%;
            }

            .error {
                color: red;
                font-weight: bold;
                margin-bottom: 15px;
            }

            .info-device {
                font-size: 12px;
                color: #555;
                background: #eee;
                padding: 8px;
                border-radius: 4px;
                word-break: break-all;
            }

            .btn-copy-device {
                display: flex;
                align-items: center;
                gap: 5px;
                background: #ffffff;
                border: 1px solid #ced4da;
                color: #495057;
                padding: 4px 8px;
                font-size: 11px;
                font-weight: 600;
                border-radius: 4px;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .btn-copy-device:hover {
                background: #e9ecef;
                border-color: #adb5bd;
                color: #212529;
            }

            .btn-copy-device:active {
                transform: scale(0.95);
            }
        </style>
    </head>

    <body>
        <div class="license-container">
            <h2>Application License Activation</h2>
            <p>The application is not activated for this device. Please contact the administrator to obtain an activation code.</p>

            <?php if (isset($error_msg)): ?>
                <div class="error"><?php echo htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <div class="info-device" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>Device ID :</strong>
                    <span id="deviceIdText"><?php echo isset($device_id) && $device_id ? htmlspecialchars($device_id) : "Not Detected"; ?></span>
                </div>
                <?php if (isset($device_id) && $device_id): ?>
                    <button type="button" onclick="copyDeviceID()" class="btn-copy-device" title="Copy Device ID">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <span>Copy</span>
                    </button>
                <?php endif; ?>
            </div>

            <form method="POST" action="">
                <label for="serial_number">Enter Serial Number:</label>
                <input type="text" id="serial_number" name="serial_number" class="form-input" placeholder="Enter your official license code" required autocomplete="off">
                <button type="submit" name="activate_license" class="btn-submit">Activate</button>
            </form>
        </div>

        <script>
            function copyDeviceID() {
                var deviceIdText = document.getElementById("deviceIdText").innerText;

                navigator.clipboard.writeText(deviceIdText).then(function() {
                    var btn = document.querySelector(".btn-copy-device span");
                    var originalText = btn.innerText;
                    btn.innerText = "Copied!";
                    btn.style.color = "#28a745";

                    setTimeout(function() {
                        btn.innerText = originalText;
                        btn.style.color = "";
                    }, 1500);
                }).catch(function(err) {
                    alert("Failed to copy text: ", err);
                });
            }
        </script>
    </body>

    </html>
<?php
    exit();
}
?>