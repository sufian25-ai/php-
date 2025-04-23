<?php
// Initialize the result as an empty string
$factorial = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the input number from the form
    $number = $_POST['number'];

    // Check if the number is a positive integer
    if ($number >= 0) {
        // Calculate the factorial using a loop
        $factorial = 1;
        for ($i = 1; $i <= $number; $i++) {
            $factorial *= $i;
        }
    } else {
        $factorial = "Please enter a positive integer.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factorial Calculator</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Factorial Calculator</h2>
    
    <form method="post" action="" class="d-flex justify-content-center">
        <input type="number" class="form-control w-auto" id="number" name="number" required min="0" placeholder="Enter number">
        <button type="submit" class="btn btn-primary ms-2">Calculate</button>
    </form>

    <!-- Display the result if calculated -->
    <?php if ($factorial !== ""): ?>
        <div class="mt-3 text-center">
            <p> The factorial of <?php echo htmlspecialchars($number); ?> is: <strong><?php echo $factorial; ?></strong></p>
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
