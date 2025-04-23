<!DOCTYPE html>
<html>
<head>
    <title>Find largest Number</title>
</head>
<body>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        Enter Numbers:
        <input type="text" name="numbers">
        <input type="submit" name="submit" value="Show Largest">
    </form>
</body>
</html>

<?php

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $numbers = $_POST['numbers'];

    $numArray = array_map('trim', explode(',',$numbers));

    $numArray = array_filter($numArray,'is_numeric');

    if(!empty($numArray))
    {
        $largest = $numArray[0];

        foreach ($numArray as $num) 
        {
            if($num > $largest)
            {
                $largest = $num;
            }
        }

        echo "The largest number is:".$largest;
    }

    else
    {
        echo "Please enter valid numbers";
    }
}
?>