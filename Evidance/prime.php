<!DOCTYPE html>
<html>
<head>
    <title>Prime Number Checker</title>
</head>
<body>

<h2>Check Prime Number</h2>

<form method="post">
    Enter a number: 
    <input type="number" name="number" required>
    <input type="submit" value="Check">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $num = $_POST['number'];

    // প্রাইম নাম্বার চেক করার ফাংশন
    function isPrime($n) {
        if ($n <= 1) return false;
        if ($n == 2) return true;
        if ($n % 2 == 0) return false;

        for ($i = 3; $i <= sqrt($n); $i += 2) {
            if ($n % $i == 0) {
                return false;
            }
        }
        return true;
    }

    // রেজাল্ট দেখানো
    if (isPrime($num)) {
        echo "<h3>$num is a Prime Number ✅</h3>";
    } else {
        echo "<h3>$num is NOT a Prime Number ❌</h3>";
    }
}
?>

</body>
</html>
