<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>500 Internal Server Error</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: #0d0d0d;
            color: #fff;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        .box {
            text-align: center;
            max-width: 650px;
        }

        .code {
            font-size: 8rem;
            font-weight: 800;
            color: #f59e0b;
            margin: 0;
            line-height: 1;
        }

        .msg {
            font-size: 1.8rem;
            letter-spacing: 4px;
            color: #fcd34d;
            margin: 20px 0;
        }

        .details {
            background: #1e1e1e;
            border: 1px solid #333;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 6px;
            text-align: left;
            font-family: monospace;
            font-size: 12px;
            margin-bottom: 25px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-break: break-all;
        }

        .btn {
            display: inline-block;
            padding: 12px 28px;
            border: 2px solid #f59e0b;
            color: #f59e0b;
            text-decoration: none;
            font-weight: bold;
            border-radius: 4px;
            transition: 0.3s;
        }

        .btn:hover {
            background: #f59e0b;
            color: #000;
        }
    </style>
</head>

<body>
    <div class="box">
        <h1 class="code">500</h1>
        <p class="msg">INTERNAL SERVER ERROR</p>

        <?php if (!empty($displayErrorDetails) && !empty($exception)): ?>
            <div class="details">
                <strong>Error:</strong> <?php echo htmlspecialchars($exception->getMessage()); ?><br>
                <strong>File:</strong> <?php echo htmlspecialchars($exception->getFile() . ':' . $exception->getLine()); ?><br>
            </div>
        <?php endif; ?>

        <a href="/dsv/" class="btn">BACK TO DASHBOARD</a>
    </div>
</body>

</html>
