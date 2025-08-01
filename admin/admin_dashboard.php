<?php
// admin_dashboard.php
session_start();
// Enforce admin authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}
include "../config/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <script>
        // Pure JS tabs
        document.addEventListener('DOMContentLoaded', function () {
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');
            tabBtns.forEach((btn, idx) => {
                btn.addEventListener('click', () => {
                    tabBtns.forEach(b => b.classList.remove('active'));
                    tabContents.forEach(tc => tc.classList.remove('active'));
                    btn.classList.add('active');
                    tabContents[idx].classList.add('active');
                });
            });
            // Activate first tab by default
            tabBtns[0].classList.add('active');
            tabContents[0].classList.add('active');
        });
    </script>
</head>
<body>
    <header>
        <div class="container">
            <h1>Admin Dashboard</h1>
            <nav>
                <a href="admin_dashboard.php">Dashboard</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>
    <main class="container">
        <div class="tabs">
            <button class="tab-btn" type="button">Disasters</button>
            <button class="tab-btn" type="button">Resources</button>
            <button class="tab-btn" type="button">Rescue Teams</button>
            <button class="tab-btn" type="button">Victims</button>
        </div>

        <!-- Disasters Tab -->
        <div class="tab-content">
            <div class="card">
                <div class="card-header">
                    <span class="icon">⚠️</span> Disaster Records
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Date</th>
                                    <th>Severity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Handle delete for disasters
                                if (isset($_GET['delete_disaster'])) {
                                    $id = intval($_GET['delete_disaster']);
                                    $conn->query("DELETE FROM disasters WHERE id=$id");
                                    echo "<div class='alert'>Disaster deleted.</div>";
                                }
                                $result = $conn->query("SELECT * FROM disasters");
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $id = htmlspecialchars($row['id']);
                                        echo "<tr>
                                            <td>{$row['id']}</td>
                                            <td>{$row['name']}</td>
                                            <td>{$row['type']}</td>
                                            <td>{$row['location']}</td>
                                            <td>{$row['date']}</td>
                                            <td>{$row['severity']}</td>
                                            <td>
                                                <a class='btn btn-edit' href='admin_dashboard.php?edit_disaster=$id'>Edit</a>
                                                <a class='btn btn-delete' href='admin_dashboard.php?delete_disaster=$id' onclick=\"return confirm('Are you sure you want to delete this disaster?')\">Delete</a>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center'>No records found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resources Tab -->
        <div class="tab-content">
            <div class="card">
                <div class="card-header">
                    <span class="icon">📦</span> Resource Inventory
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Location</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Handle delete for resources
                                if (isset($_GET['delete_resource'])) {
                                    $id = intval($_GET['delete_resource']);
                                    $conn->query("DELETE FROM resources WHERE id=$id");
                                    echo "<div class='alert'>Resource deleted.</div>";
                                }
                                $result = $conn->query("SELECT * FROM resources");
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $id = htmlspecialchars($row['id']);
                                        echo "<tr>
                                            <td>{$row['id']}</td>
                                            <td>{$row['resource_type']}</td>
                                            <td>{$row['quantity']}</td>
                                            <td>{$row['location']}</td>
                                            <td>
                                                <a class='btn btn-edit' href='admin_dashboard.php?edit_resource=$id'>Edit</a>
                                                <a class='btn btn-delete' href='admin_dashboard.php?delete_resource=$id' onclick=\"return confirm('Are you sure you want to delete this resource?')\">Delete</a>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>No resources found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teams Tab -->
        <div class="tab-content">
            <div class="card">
                <div class="card-header">
                    <span class="icon">👥</span> Rescue Teams
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Members</th>
                                    <th>Contact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($_GET['delete'])) {
                                    $id = intval($_GET['delete']);
                                    $conn->query("DELETE FROM rescue_teams WHERE id=$id");
                                    echo "<div class='alert'>Team deleted.</div>";
                                }
                                $result = $conn->query("SELECT * FROM rescue_teams");
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $id = htmlspecialchars($row['id']);
                                        $name = htmlspecialchars($row['team_name']);
                                        $members = htmlspecialchars($row['members_count']);
                                        $contact = htmlspecialchars($row['contact']);
                                        echo "<tr>
                                            <td>$id</td>
                                            <td>$name</td>
                                            <td>$members</td>
                                            <td>$contact</td>
                                            <td>
                                                <a class='btn btn-edit' href='admin_dashboard.php?edit_team=$id'>Edit</a>
                                                <a class='btn btn-delete' href='admin_dashboard.php?delete=$id' onclick=\"return confirm('Are you sure you want to delete this team?')\">Delete</a>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>No teams found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Victims Tab -->
        <div class="tab-content">
            <div class="card">
                <div class="card-header">
                    <span class="icon">🧑‍🤝‍🧑</span> Victim Records
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Age</th>
                                    <th>Status</th>
                                    <th>Disaster ID</th>
                                    <th>Team ID</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Handle delete for victims
                                if (isset($_GET['delete_victim'])) {
                                    $id = intval($_GET['delete_victim']);
                                    $conn->query("DELETE FROM victims WHERE id=$id");
                                    echo "<div class='alert'>Victim deleted.</div>";
                                }
                                $result = $conn->query("SELECT * FROM victims");
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $id = htmlspecialchars($row['id']);
                                        echo "<tr>
                                            <td>" . htmlspecialchars($row['id']) . "</td>
                                            <td>" . htmlspecialchars($row['name']) . "</td>
                                            <td>" . htmlspecialchars($row['age']) . "</td>
                                            <td>" . htmlspecialchars($row['status']) . "</td>
                                            <td>" . htmlspecialchars($row['disaster_id']) . "</td>
                                            <td>" . htmlspecialchars($row['assigned_team_id']) . "</td>
                                            <td>
                                                <a class='btn btn-edit' href='admin_dashboard.php?edit_victim=$id'>Edit</a>
                                                <a class='btn btn-delete' href='admin_dashboard.php?delete_victim=$id' onclick=\"return confirm('Are you sure you want to delete this victim?')\">Delete</a>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center'>No victim records found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Disaster Management System</p>
    </footer>
</body>
</html>