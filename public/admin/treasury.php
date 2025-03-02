<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treasury</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" type="image/png" href="../assets/icons/templogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>
<body>
    <div class="container">
        <!-- Logo linked to Admin Dashboard -->
        <a href="adminDashboard.php">
            <img src="../assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        
        <h1>Treasury</h1>

         <div class="profile-notifications">
            <!-- Bell Icon for Notifications -->
            <i class="fas fa-bell" id="notificationIcon"></i>
            
            <!-- Profile Menu -->
            <div class="profile-menu">
                <img src="../assets/img/profile.jpg" alt="Admin Profile" class="profile-pic" id="profilePic">
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="profile.php">My Profile</a>
                    <a href="#" onclick="confirmLogout()">Log Out</a>
                </div>
            </div>
        </div>
    </div>

    <div>
        <!-- Treasury Stats Summary -->
        <div class="treasury-stats">
            <div class="stat-card">
                <h3>Total Dues</h3>
                <p>₱200,000</p>
            </div>
            <div class="stat-card">
                <h3>Collected Dues</h3>
                <p>₱120,000</p>
            </div>
            <div class="stat-card">
                <h3>Pending Payments</h3>
                <p>₱80,000</p>
            </div>
        </div>

        <!-- Dues and Expenses Management -->
        <div class="section">
            <h2>Dues Management</h2>
            <table>
                <tr>
                    <th>Homeowner</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <td>John Doe</td>
                    <td>Pending</td>
                    <td>₱5,000</td>
                    <td><button>Edit</button></td>
                </tr>
                <tr>
                    <td>Jane Smith</td>
                    <td>Paid</td>
                    <td>₱3,000</td>
                    <td><button>Edit</button></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2>Expenses</h2>
            <table>
                <tr>
                    <th>Expense</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <td>Maintenance</td>
                    <td>₱15,000</td>
                    <td>2025-02-01</td>
                    <td><button>Edit</button></td>
                </tr>
                <tr>
                    <td>Staff Salaries</td>
                    <td>₱30,000</td>
                    <td>2025-02-05</td>
                    <td><button>Edit</button></td>
                </tr>
            </table>
        </div>

        <!-- Income Overview -->
        <div class="section">
            <h2>Income Overview</h2>
            <table>
                <tr>
                    <th>Source</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
                <tr>
                    <td>Amenities Reservation</td>
                    <td>₱20,000</td>
                    <td>2025-02-10</td>
                </tr>
                <tr>
                    <td>Homeowner Contributions</td>
                    <td>₱50,000</td>
                    <td>2025-02-12</td>
                </tr>
            </table>
        </div>

        <!-- Graph or Chart for Financial Overview (optional) -->
        <div class="section">
            <h2>Financial Overview</h2>
            <!-- You can integrate a chart here, e.g., using Chart.js or another chart library -->
            <canvas id="financialChart"></canvas>
        </div>

        <!-- Action Buttons -->
        <div class="actions">
            <button>Add Payment</button>
            <button>Update Dues</button>
            <button>Add Expense</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Example of rendering a simple chart (you can customize this as needed)
        const ctx = document.getElementById('financialChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Dues', 'Collected', 'Expenses'],
                datasets: [{
                    label: 'Financial Overview',
                    data: [200000, 120000, 50000],
                    backgroundColor: ['#FF5733', '#33FF57', '#3357FF'],
                    borderColor: ['#FF5733', '#33FF57', '#3357FF'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

         // for profile and logout dropdown
        document.addEventListener("DOMContentLoaded", function () {
            const profilePic = document.getElementById("profilePic");
            const dropdownMenu = document.getElementById("dropdownMenu");

            // Toggle dropdown on profile picture click
            profilePic.addEventListener("click", function (event) {
                event.stopPropagation(); // Prevent immediate closing when clicking profile picture
                dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
            });

            // Close dropdown when clicking anywhere else
            document.addEventListener("click", function () {
                dropdownMenu.style.display = "none";
            });

            // Prevent closing when clicking inside the dropdown menu
            dropdownMenu.addEventListener("click", function (event) {
                event.stopPropagation();
            });
        });

        function confirmLogout() {
            let confirmAction = confirm("Are you sure you want to log out?");
            if (confirmAction) {
                window.location.href = "../logout.php"; // Redirect to logout.php if confirmed
            }
        }    

    </script>
</body>
</html>
