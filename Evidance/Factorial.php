<!DOCTYPE html>
<html>
<head>
    <title>Factorial Calculator</title>
</head>
<body>

<h2>Find Factorial of a Number</h2>

<form method="post">
    Enter a number: 
    <input type="number" name="number" min="0" required>
    <input type="submit" value="Calculate">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $num = $_POST['number'];
    $factorial = 1;

    // Factorial বের করার লজিক
    for ($i = 1; $i <= $num; $i++) {
        $factorial *= $i;
    }

    echo "<h3>Factorial of $num is: $factorial</h3>";
}
?>

</body>
</html>
