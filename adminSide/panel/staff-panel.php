<?php
session_start(); // Ensure session is started

$timeout_duration = 300; // 15 minutes

// Check if the user is logged in
if (isset($_SESSION['logged_account_id'])) {
    // Check if the last activity time is set
    if (isset($_SESSION['last_activity'])) {
        // Calculate the session's lifetime
        $session_life = time() - $_SESSION['last_activity'];
        
        // If the session has expired, destroy the session and redirect to login
        if ($session_life > $timeout_duration) {
            session_unset(); // Unset all session variables
            session_destroy(); // Destroy the session
            header("Location: ../sessionTimedOut.php");
            exit;
        }
    }
    // Update the last activity time
    $_SESSION['last_activity'] = time(); // Update last activity time to current time
} else {
    // User is not logged in, redirect to login page
    header("Location: login.php");
    exit;
}


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Manager') {
    // If the user is not a manager, redirect them to the home page or an "unauthorized" page
    header("Location: ../unauthorized.php");
    exit();
}

require_once '../posBackend/checkIfLoggedIn.php';
?>
<?php include '../inc/dashHeader.php'; ?>
    <style>
        .wrapper{ width: 60%; padding-left: 200px; padding-top: 20px  }
    </style>

<div class="wrapper">
    <div class="container-fluid pt-5 pl-600">
        <div class="row">
            <div class="m-50">
                <div class="mt-5 mb-3">
                    <h2 class="pull-left">Staff Details</h2>
                    <a href="../staffCrud/createStaff.php" class="btn btn-outline-dark"><i class="fa fa-plus"></i> Add Staff</a>
                </div>
                <div class="mb-3">
                    <form method="POST" action="#">
                        <div class="row">
                            <div class="col-md-6">
                                <input required type="text" id="search" name="search" class="form-control" placeholder="Enter Staff ID, Name">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-dark">Search</button>
                            </div>
                            <div class="col" style="text-align: right;" >
                                <a href="staff-panel.php" class="btn btn-light">Show All</a>
                            </div>
                        </div>
                    </form>
                </div>
                <?php
                // Include config file
                require_once "../config.php";

                if (isset($_POST['search'])) {
                    if (!empty($_POST['search'])) {
                        $search = $_POST['search'];

                        // Modified query to search staff members by staff_name or staff_id
                        /*
                        $sql = "SELECT *
                                FROM Staffs stf
                                INNER JOIN Accounts acc ON stf.account_id = acc.account_id
                                WHERE stf.staff_name LIKE '%$search%' OR stf.staff_id = '$search'
                                ORDER BY stf.staff_id";
                         * 
                         */
                        // Using a prepared statement
                        $stmt = $link->prepare("SELECT * FROM Staffs WHERE staff_name LIKE CONCAT('%', ?, '%') OR staff_id = ?");
                        $stmt->bind_param("ss", $search, $search); // Bind the parameter

                        // Execute the statement
                        $stmt->execute();

                        // Get the result set
                        $result = $stmt->get_result(); 
                    } else {
                        // Default query to fetch all staff members
                        /*
                        $sql = "SELECT *
                                FROM Staffs stf
                                INNER JOIN Accounts acc ON stf.account_id = acc.account_id
                                ORDER BY stf.staff_id";
                         * 
                         */
                        $sql = "SELECT * FROM Staffs ORDER BY account_id";
                        $result = mysqli_query($link, $sql);
                    }
                } else {
                    // Default query to fetch all staff members
                    /*
                    $sql = "SELECT *
                            FROM Staffs stf
                            INNER JOIN Accounts acc ON stf.account_id = acc.account_id
                            ORDER BY stf.staff_id";
                     * 
                     */
                    $sql = "SELECT * FROM Staffs ORDER BY account_id";
                    $result = mysqli_query($link, $sql);
                }


                if ($result) {
                    if (mysqli_num_rows($result) > 0) {
                        echo '<table class="table table-bordered table-striped">';
                        echo "<thead>";
                        echo "<tr>";
                        echo "<th style='width:5em;'>Staff ID</th>";
                        echo "<th>Staff Name</th>";
                        echo "<th style='width:7em;'>Role</th>";
                        echo "<th>Account ID</th>";
                        //echo "<th>Email</th>";
                        //echo "<th>Phone Number</th>";
                       // echo "<th style='width:5em;'>Delete</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['staff_id'] . "</td>";
                            echo "<td>" . $row['staff_name'] . "</td>";
                            echo "<td>" . $row['role'] . "</td>";
                            echo "<td>" . $row['account_id'] . "</td>";
                            //echo "<td>" . $row['email'] . "</td>";
                            //echo "<td>" . $row['phone_number'] . "</td>";
                           // echo "<td>";
                        //    echo '<a href="../staffCrud/delete_staffVerify.php?id=' . $row['staff_id'] . '" title="Delete Record" data-toggle="tooltip" onclick="return confirm(\'Admin permission Required!\n\nAre you sure you want to delete this Staff?\n\nThis will alter other modules related to this Staff!\n\')"><span class="fa fa-trash text-black"></span></a>';
                         //   echo "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>";
                        echo "</table>";
                        // Free result set
                        mysqli_free_result($result);
                    } else {
                        echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close connection
                mysqli_close($link);
                ?>
            </div>
        </div>
    </div>
</div>

<?php include '../inc/dashFooter.php'; ?>
