<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Default to today's date
$selectedDate = date('Y-m-d');

// If form is submitted, update the selected date
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedDate = $_POST['date'];
}

// Fetch total revenue for the selected date
$sqlRevenue = "SELECT SUM(total) as total_revenue FROM orders WHERE date = '$selectedDate'";
$resultRevenue = $conn->query($sqlRevenue);
$totalRevenue = ($resultRevenue->num_rows > 0) ? $resultRevenue->fetch_assoc()['total_revenue'] : 0;

// Fetch total expenses for the selected date
$sqlExpenses = "SELECT SUM(amount) as total_expenses FROM expenses WHERE date = '$selectedDate'";
$resultExpenses = $conn->query($sqlExpenses);
$totalExpenses = ($resultExpenses->num_rows > 0) ? $resultExpenses->fetch_assoc()['total_expenses'] : 0;

$conn->close();

// Calculate net revenue
$netRevenue = $totalRevenue - $totalExpenses;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Net Revenue</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/food.css">
    <link rel="stylesheet" href="./css/expenses.css">
<script>
        function greetUser() {
            var currentTime = new Date();
            var currentHour = currentTime.getHours();
            var greeting;

            if (currentHour < 12) {
                greeting = "Good morning";
            } else if (currentHour < 18) {
                greeting = "Good afternoon";
            } else {
                greeting = "Good evening";
            }

            var cashierName = "<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>";
            document.getElementById("greeting").innerHTML = greeting + ", " + cashierName;
        }
    </script>
</head>

<body onload="greetUser()">
<?php include 'sidebar.php'; ?>
    <div class="history_all">
        <div class="welcome_base">
            <div class="greetings">
                <h1 id="greeting"></h1>
                <!-- <p>Welcome to Olu's Kitchen, </p> -->
            </div>
            <div class="profile"></div>
        </div>
        <h2>Net Revenue</h2>
        <form method="POST" action="net_revenue.php">
            <div class="forms">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" value="<?php echo $selectedDate; ?>" required>
            </div>
            <div class="forms">
                <button type="submit">Query</button>
            </div>
        </form>
        <h3>Year: <?php echo $selectedDate; ?></h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Total Revenue</th>
                    <th>Total Expenses</th>
                    <th>Net Revenue</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $selectedDate; ?></td>
                    <td>GH₵ <?php echo number_format($totalRevenue, 2); ?></td>
                    <td>GH₵ <?php echo number_format($totalExpenses, 2); ?></td>
                    <td>GH₵ <?php echo number_format($netRevenue, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
