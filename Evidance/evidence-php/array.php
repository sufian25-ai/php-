<?php
// Associative array with country names as keys and capital names as values
$countries = [
    "India" => "New Delhi",
    "USA" => "Washington, D.C.",
    "Canada" => "Ottawa",
    "Australia" => "Canberra",
    "Japan" => "Tokyo"
];

// Sorting the array by country name (keys)
ksort($countries);

// Printing the sorted array
echo "<h3>Country and Capital List:</h3>";

foreach ($countries as $country => $capital) {
    echo "<li>The Country name is <strong>$country</strong> and the Capital name is <strong>$capital</strong></li>";
}

?>
