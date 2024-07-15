<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        .form-container {
            max-width: 400px;
            margin: 200px 0;
            padding: 0;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            border-radius: 10px;
            text-align: center;
        }

        header {
            text-align: center;
            font-family: sans-serif;
            background-color: black;
            color: orange;
            text-align: center;
            padding: 5px 0;
            width: 100%;
            border-radius: 10px 10px 0 0;
            margin-bottom: 30px;
        }

        header h3 {
            margin: 0;
            padding: 10px;
        }
        
        table {
            margin-bottom: 20px;
            text-align: center;
            width: 100%;
        }
        
        input[type="Submit"] {
            background-color: #FFB900;
            border-radius: 10px;
            font-size: 20px;
            border: 2px solid;
            cursor: pointer;
            color: white;
            padding: 7px;
            transition-duration: 0.4s;
            display: block;
            margin: 10px auto;
        }

        input[type="Submit"]:hover {
            background-color: white;
            color: #4CAF50;
            border: 2px solid #4CAF50;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form action="" method="post">
            <header>
                <h3>Payment Method</h3>
            </header>
            
            <table>
                <tr>
                    <th>
                        <input type="radio" name="paymentMethod" value="cash">
                        <span>Cash</span>
                    </th>
                    <th>
                        <input type="radio" name="paymentMethod" value="online">
                        <span>Online payment</span>
                    </th>
                </tr>
                <tr></tr>
                <tr></tr>
                <tr></tr>
                <tr></tr>
                <tr></tr>
            </table>
            <input type="submit" name="paymentMethod" value="CONFIRM">
        </form>
    </div>
</body>
</html>