<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Import Calculation</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <div class="container">
        <h2>Car Import Calculation</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label for="price">Price of Car:</label>
                <input type="number" id="price" name="price" step="100" required>
            </div>
            <div class="form-group">
                <label for="date">First Registration:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="carType">Type of Car:</label>
                <select id="carType" name="carType">
                    <option value="trades_car">Trades Car</option>
                    <option value="passenger_car">Passenger Car</option>
                </select>
            </div>
            <div class="form-group">
                <label for="engine_size">Engine Size:</label>
                <select id="engine_size" name="engine_size">
                    <option value="small">Small</option>
                    <option value="medium">Medium</option>
                    <option value="large">Large</option>
                </select>
            </div>
            <div class="form-group">
                <label for="fuel_type">Fuel Type:</label>
                <select id="fuel_type" name="fuel_type">
                    <option value="gasoline">Gasoline</option>
                    <option value="diesel">Diesel</option>
                    <option value="electric">Electric</option>
                    <option value="hybrid">Hybrid</option>
                    <option value="hydrogen">Hydrogen</option>
                    <option value="ethanol">Ethanol</option>
                </select>
            </div>
            <button type="submit">Calculate Dogana Price</button>
        </form>
        <div id="result" class="result"></div>
    </div>
    <?php
    function getTariffRate($first_registration, $car_type, $engine_size, $fuel_type) {
        $tariff_rates = array(
            "trades_car" => array(
                "engine_size" => array("small" => 0.1, "medium" => 0.15, "large" => 0.2),
                "fuel_type" => array("gasoline" => 0.05, "diesel" => 0.1, "electric" => 0.02, "hybrid" => 0.03, "hydrogen" => 0.02, "ethanol" => 0.08)
            ),
            "passenger_car" => array(
                "engine_size" => array("small" => 0.05, "medium" => 0.1, "large" => 0.15),
                "fuel_type" => array("gasoline" => 0.1, "diesel" => 0.15, "electric" => 0.03, "hybrid" => 0.05, "hydrogen" => 0.03, "ethanol" => 0.1)
            )
        );

        return $tariff_rates[$car_type]["engine_size"][$engine_size] ?? 0.2
            + $tariff_rates[$car_type]["fuel_type"][$fuel_type] ?? 0.2;
    }


    function calculateCustomsDuty($car_value, $tariff_rate, $vat_rate, $excise_tax_rate, $administrative_fee) {
        $customs_duty = $car_value * $tariff_rate;
        $vat = ($car_value + $customs_duty) * $vat_rate;
        $excise_tax = ($car_value + $customs_duty) * $excise_tax_rate;
        $total_duty = $customs_duty + $vat + $excise_tax + $administrative_fee;
        return $total_duty;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $input = $_POST; 

        try {
            $car_value = floatval($input['price']);
            $first_registration = $input['date'];
            $car_type = $input['carType'];
            $engine_size = $input['engine_size']; 
            $fuel_type = $input['fuel_type']; 
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(array('error' => 'Bad Request: Invalid input data'));
            exit();
        }

        $tariff_rate = getTariffRate($first_registration, $car_type, $engine_size, $fuel_type);
        $vat_rate = 0.18;
        $excise_tax_rate = 0.05;
        $administrative_fee = 50;

        $customs_duty = calculateCustomsDuty($car_value, $tariff_rate, $vat_rate, $excise_tax_rate, $administrative_fee);
        echo '<script>';
        echo 'document.getElementById("result").innerHTML = "<h3>Result:</h3><p>Dogana Value: $' . number_format($customs_duty, 2) . '</p>";';
        echo '</script>';
    }
    ?>
</body>
</html>
