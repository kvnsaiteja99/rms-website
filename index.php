<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chef Restaurant Billing System</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJ4vQA8z3E6k6U4yxUPOkStwRRG5Qm1fIlfuvH6LhdIEQ1y15uw8gCJPtXxl" crossorigin="anonymous">

    <!-- Custom Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Montserrat:wght@700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">

    <style>
        body {
            background-image: url('your-4k-image.jpg'); /* Add your 4K image here */
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }
        .login {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }
        h1 {
            text-align: center;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 30px;
        }
        h2 {
            font-family: 'Roboto', sans-serif;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            margin-bottom: 15px;
        }
        button {
            width: 100%;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Chef Restaurant</h1> <!-- Updated the restaurant name here -->
        <div class="login">
            <h2>Customer Login</h2>
            <form action="customer/login.php" method="post">
                <input type="text" class="form-control" name="name" placeholder="Enter Name" required>
                <input type="number" class="form-control" name="table_number" placeholder="Enter Table Number" required>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
        <div class="login mt-4">
            <h2>Kitchen Login</h2>
            <form action="kitchen/kitchen_login.php" method="post">
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
        <div class="login mt-4">
            <h2>Cashier Login</h2>
            <form action="cashier/cashier_login.php" method="post">
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybGp6a9pYbXG/gFZ02dT8FLH7Tw3xo8qOnD/jrV7gTY8Q0d49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0orfjR8cEMcNmA0kCkFhT3y7nQYj8m9bEZbFd5CkHk10VA8k4" crossorigin="anonymous"></script>
</body>
</html>
