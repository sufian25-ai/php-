<!DOCTYPE html>
<html>
<head>
    <title>Grade Calculator</title>
</head>
<body>

<h2>Check Your Grade</h2>

<form method="post">
    Enter your marks (0-100): 
    <input type="number" name="marks" min="0" max="100" required>
    <input type="submit" value="Check Grade">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marks = $_POST['marks'];

    // গ্রেড বের করার লজিক
    if ($marks >= 80 && $marks <= 100) {
        $grade = "A+";
    } elseif ($marks >= 70) {
        $grade = "A";
    } elseif ($marks >= 60) {
        $grade = "A-";
    } elseif ($marks >= 50) {
        $grade = "B";
    } elseif ($marks >= 40) {
        $grade = "C";
    } elseif ($marks >= 33) {
        $grade = "D";
    } else {
        $grade = "F";
    }

    echo "<h3>Your Grade is: $grade</h3>";
}
?>

</body>
</html>
