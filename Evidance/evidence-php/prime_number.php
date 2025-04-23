<!DOCTYPE html>
<html>
<head>
    <title>Prime Checker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #e8f0fe;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 350px;
        }
        .alert-success {
            background-color: #087f23;
            color: white;
            font-weight: bold;
        }
        .alert-danger {
            background-color: #b71c1c;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="box">
    <h4 class="text-center mb-3">Prime Number Checker</h4>
    <form method="post">
        <input type="number" name="number" class="form-control mb-3" placeholder="Enter a number" required>
        <button type="submit" class="btn btn-primary w-100">Check</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
        $n = intval($_POST["number"]);
        $isPrime = true;

        if ($n <= 1) $isPrime = false;
        elseif ($n == 2) $isPrime = true;
        elseif ($n % 2 == 0) $isPrime = false;
        else 
        {
            for ($i = 3; $i <= sqrt($n); $i += 2)
             {
                if ($n % $i == 0) 
                {
                    $isPrime = false;
                    break;
                }
            }
        }

        echo '<div class="mt-3 alert ' . ($isPrime ? 'alert-success' : 'alert-danger') . '">';
        echo $isPrime ? "$n is a Prime Number." : "$n is NOT a Prime Number.";
        echo '</div>';
    }
    
    ?>
</div>

</body>
</html>
