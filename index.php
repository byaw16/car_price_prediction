<?php
# -----------------------------
# Benjamin Yaw Koko
# index Num :2526500327
# -----------------------------

$price = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect input
    $data = [
        "production_year" => isset($_POST['production_year']) ? $_POST['production_year'] : 0,
        "levy"            => isset($_POST['levy']) ? $_POST['levy'] : 0,
        "mileage"         => isset($_POST['mileage']) ? $_POST['mileage'] : 0,
        "cylinders"       => isset($_POST['cylinders']) ? $_POST['cylinders'] : 0,
        "airbags"         => isset($_POST['airbags']) ? $_POST['airbags'] : 0
    ];

    $json_data = json_encode($data);
    $json_data_escaped = addslashes($json_data);

    $python_path = 'C:\\Users\\HP\\anaconda3\\python.exe';
    $script_path = 'predict_price.py';
    $command = "\"$python_path\" \"$script_path\" \"$json_data_escaped\"";

    $output = shell_exec($command . " 2>&1");
    $price = trim($output);
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
            max-width: 500px;
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
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input[type="number"] {
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
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #333;
            padding: 15px;
            background-color: #ffe6cc;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Car Price Predictor</h1>
        <form method="post">
            <label>Production Year:</label>
            <input type="number" name="production_year" required>

            <label>Levy:</label>
            <input type="number" name="levy" step="0.01" required>

            <label>Mileage (km):</label>
            <input type="number" name="mileage" required>

            <label>Cylinders:</label>
            <input type="number" name="cylinders" required>

            <label>Airbags:</label>
            <input type="number" name="airbags" required>

            <button type="submit">Predict Price</button>
        </form>

        <?php if ($price): ?>
            <div class="result">Predicted Car Price: <?php echo htmlspecialchars($price); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>