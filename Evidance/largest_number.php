<!DOCTYPE html>
<html>
<head>
    <title>Find Largest & Smallest Number</title>
</head>
<body>

<h2>Largest  & Smallest Number</h2>

<form method="post">
    Enter numbers (comma separated): 
    <input type="text" name="numbers" required>
    <input type="submit" value="Check Largest">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = $_POST['numbers'];

    // স্ট্রিং থেকে সংখ্যা আলাদা করা
    $numbers = explode(",", $input);

    // সংখ্যা গুলোকে ট্রিম করা (ফাঁকা স্পেস সরানো)
    $numbers = array_map('trim', $numbers);

    // সবগুলো ইনপুট কি সংখ্যা কিনা তা চেক করা
    if (count($numbers) >= 3 && is_numeric($numbers[0]) && is_numeric($numbers[1]) && is_numeric($numbers[2])) {
        // সবচেয়ে বড় সংখ্যা বের করা
        $largest = max($numbers);
        $smallest = min($numbers);
        echo "<h3>Max Number is: $largest</h3>";
         echo "<h3>Min Number is: $smallest</h3>";

    } else {
        echo "<p style='color:red;'>plese input the number। যেমনঃ 10, 20, 30</p>";
    }
}
?>

</body>
</html>
