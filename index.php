<?php
# -----------------------------
# Benjamin Yaw Koko
# index Num :2526500327
# -----------------------------

$result_text = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect input (required + optional)
    $data = [
        "production_year"  => isset($_POST['production_year']) ? $_POST['production_year'] : 0,
        "levy"             => isset($_POST['levy']) ? $_POST['levy'] : 0,
        "mileage"          => isset($_POST['mileage']) ? $_POST['mileage'] : 0,
        "cylinders"        => isset($_POST['cylinders']) ? $_POST['cylinders'] : 0,
        "airbags"          => isset($_POST['airbags']) ? $_POST['airbags'] : 0,
        "engine_volume"    => isset($_POST['engine_volume']) ? $_POST['engine_volume'] : "",
        "manufacturer"     => isset($_POST['manufacturer']) ? $_POST['manufacturer'] : "",
        "model"            => isset($_POST['model']) ? $_POST['model'] : ""
    ];

    $json_data = json_encode($data, JSON_UNESCAPED_SLASHES);
    $payload = base64_encode($json_data);

    // Do not hardcode local machine path.
    // Set PYTHON_PATH in server environment if needed, otherwise use python from PATH.
    $python_path = getenv('PYTHON_PATH');
    if (!$python_path) {
        $python_path = 'python';
    }

    $script_path = __DIR__ . DIRECTORY_SEPARATOR . 'predict_price.py';
    $command = escapeshellarg($python_path) . ' ' . escapeshellarg($script_path) . ' ' . escapeshellarg($payload);
    $output = shell_exec($command . " 2>&1");
    $result_text = trim((string)$output);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Car Price Predictor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 700px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #ff6600;
            margin-bottom: 30px;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        label {
            display: block;
            margin-top: 6px;
            font-weight: bold;
        }
        input[type="number"], input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            background-color: #ff6600;
            color: white;
            border: none;
            padding: 12px 20px;
            margin-top: 25px;
            width: 100%;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #e65c00;
        }
        .result {
            margin-top: 25px;
            font-size: 16px;
            color: #333;
            padding: 15px;
            background-color: #ffe6cc;
            border-radius: 6px;
            white-space: pre-line;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Car Price Predictor</h1>
        <form method="post">
            <div class="grid">
                <div>
                    <label>Production Year:</label>
                    <input type="number" name="production_year" required>
                </div>

                <div>
                    <label>Levy:</label>
                    <input type="number" name="levy" step="0.01" required>
                </div>

                <div>
                    <label>Mileage (km):</label>
                    <input type="number" name="mileage" required>
                </div>

                <div>
                    <label>Cylinders:</label>
                    <input type="number" name="cylinders" required>
                </div>

                <div>
                    <label>Airbags:</label>
                    <input type="number" name="airbags" required>
                </div>

                <div>
                    <label>Engine Volume (optional, e.g. 2.0 Turbo):</label>
                    <input type="text" name="engine_volume">
                </div>

                <div>
                    <label>Manufacturer (optional):</label>
                    <input type="text" name="manufacturer">
                </div>

                <div>
                    <label>Model (optional):</label>
                    <input type="text" name="model">
                </div>
            </div>

            <button type="submit">Predict Price</button>
        </form>

        <?php if ($result_text !== ''): ?>
            <div class="result"><?php echo htmlspecialchars($result_text); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
