<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to unauthorized page or login page
    header("Location: unauthorized.php");
    exit();
}

include 'db.php';

// Default to the current year
$selectedYear = date('Y');

// If form is submitted, update the selected year
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedYear = $_POST['year'];
}

// Initialize variables for total revenue, total expenses, and net revenue
$totalRevenue = 0;
$totalExpenses = 0;

// Fetch total revenue for the selected year
$sqlRevenue = "SELECT SUM(total) as total_revenue FROM orders WHERE YEAR(date) = $selectedYear";
$resultRevenue = $conn->query($sqlRevenue);
if ($resultRevenue->num_rows > 0) {
    $totalRevenue = $resultRevenue->fetch_assoc()['total_revenue'];
}

// Fetch total expenses for the selected year
$sqlExpenses = "SELECT SUM(amount) as total_expenses FROM expenses WHERE YEAR(date) = $selectedYear";
$resultExpenses = $conn->query($sqlExpenses);
if ($resultExpenses->num_rows > 0) {
    $totalExpenses = $resultExpenses->fetch_assoc()['total_expenses'];
}

// Calculate net revenue
$netRevenue = $totalRevenue - $totalExpenses;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yearly Net Revenue</title>
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
    <div class="all">
        <div class="welcome_base">
            <div class="greetings">
                <h1 id="greeting"></h1>
                <!-- <p>Welcome to Olu's Kitchen, </p> -->
            </div>
            <div class="profile"></div>
        </div>
        <h2>Yearly Net Revenue</h2>
        <form method="POST" action="yearly_net_revenue.php">
            <div class="forms">
                <label for="year">Year</label>
                <select id="year" name="year" required>
                    <?php
                    // Generate options for the select element from 2024 to 2094
                    for ($year = 2024; $year <= 2094; $year++) {
                        $selected = ($year == $selectedYear) ? "selected" : "";
                        echo "<option value='$year' $selected>$year</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="forms">
                <button type="submit">Calculate Net Revenue</button>
            </div>
        </form>
        <?php
        // Display the yearly net revenue in a table format
        echo "<h3>Year: $selectedYear</h3>";
        echo "<table>";
        echo "<tr><th>Total Revenue</th><th>Total Expenses</th><th>Net Revenue</th></tr>";
        echo "<tr>";
        echo "<td>GH₵ " . number_format($totalRevenue, 2) . "</td>";
        echo "<td>GH₵ " . number_format($totalExpenses, 2) . "</td>";
        echo "<td>GH₵ " . number_format($netRevenue, 2) . "</td>";
        echo "</tr>";
        echo "</table>";
        ?>
    </div>
</body>
</html>
