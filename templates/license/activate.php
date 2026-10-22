<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verification License</title>
    <meta name="description" content="Display Signage Valas License Verification">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/dsv/app/img/favicon.png">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f6f9;
            background-image: url("/dsv/app/img/login.png");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .license-container {
            width: 100%;
            max-width: 500px;
            padding: 30px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        h2 {
            margin-top: 0;
            color: #1e293b;
            font-size: 1.4rem;
            text-align: center;
        }

        p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.5;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-input {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px 0;
            box-sizing: border-box;
            font-family: monospace;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-submit {
            background: #2563eb;
            color: white;
            padding: 12px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 15px;
            font-weight: 600;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background: #1d4ed8;
        }

        .error {
            color: #dc2626;
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .info-device {
            font-size: 12px;
            color: #475569;
            background: #f1f5f9;
            padding: 12px;
            border-radius: 6px;
            word-break: break-all;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-copy-device {
            display: flex;
            align-items: center;
            gap: 5px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-copy-device:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .btn-copy-device:active {
            transform: scale(0.95);
        }
    </style>
</head>

<body>
    <div class="license-container">
        <h2>Application License Activation</h2>
        <p>The application is not activated for this device. Please enter an authorized activation code.</p>

        <?php if (!empty($error_msg)): ?>
            <div class="error"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <div class="info-device">
            <div>
                <strong>Device ID :</strong>
                <span id="deviceIdText"><?php echo !empty($device_id) ? htmlspecialchars($device_id) : "Not Detected"; ?></span>
            </div>
            <?php if (!empty($device_id)): ?>
                <button type="button" onclick="copyDeviceID()" class="btn-copy-device" title="Copy Device ID">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    <span>Copy</span>
                </button>
            <?php endif; ?>
        </div>

        <form method="POST" action="/dsv/license/activate">
            <label for="serial_number" style="font-weight: 600; font-size: 13px; color: #334155;">Enter Serial Number:</label>
            <input type="text" id="serial_number" name="serial_number" class="form-input" placeholder="Enter your official license code" required autocomplete="off">
            <button type="submit" name="activate_license" class="btn-submit">Activate License</button>
        </form>
    </div>

    <script>
        function copyDeviceID() {
            var deviceIdText = document.getElementById("deviceIdText").innerText;
            navigator.clipboard.writeText(deviceIdText).then(function() {
                var btn = document.querySelector(".btn-copy-device span");
                var originalText = btn.innerText;
                btn.innerText = "Copied!";
                btn.style.color = "#16a34a";
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
