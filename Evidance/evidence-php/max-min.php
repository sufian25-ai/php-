<?php
// Initialize variables
$maxNumber = "";
$minNumber = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the input from the form (three numbers separated by commas)
    $numbers = $_POST['numbers'];

    // Convert the string into an array by splitting at commas
    $numbersArray = explode(",", $numbers);

    // Make sure there are exactly 3 numbers
    if(count($numbersArray) == 5) {
        // Find the maximum and minimum number
        $maxNumber = max($numbersArray);
        $minNumber = min($numbersArray);
    } else {
        $maxNumber = "Please enter  numbers separated by commas.";
        $minNumber = "";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Max and Min Number</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .input-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .input-container input {
            width: 200px;
        }
        .output {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center">Find the Max and Min Number</h2>
    <form method="post" action="" class="mt-4 text-center">
        <div class="mb-3 input-container">
           
            <input type="text" class="form-control" id="numbers" name="numbers" required >
            <button type="submit" class="btn btn-primary ms-2">Find Max and Min</button>
        </div>
    </form>

    <!-- Display the result if calculated -->
    <?php if ($maxNumber !== ""): ?>
        <div class="output">
            <h3><strong>Maximum Number:</strong> <?php echo $maxNumber; ?></h3>
            <h3><strong>Minimum Number:</strong> <?php echo $minNumber; ?></h3>
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
