<?php
// user_dashboard.php
session_start();
// Enforce user authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit();
}
include_once "../config/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="user_dashboard.css">
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
            <h1>User Dashboard</h1>
            <nav>
                <a href="user_dashboard.php">Dashboard</a> |
                <a href="profile.php">Profile</a> |
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
                    <?php
                    // --- Add Disaster Form ---
                    if (isset($_POST['submit_disaster'])) {
                        $stmt = $conn->prepare("INSERT INTO disasters (name, type, location, date, severity) VALUES (?, ?, ?, ?, ?)");
                        $stmt->bind_param("sssss", $_POST['name'], $_POST['type'], $_POST['location'], $_POST['date'], $_POST['severity']);
                        if ($stmt->execute()) {
                            echo "<div class='alert alert-success'>✅ Disaster added successfully!</div>";
                        } else {
                            echo "<div class='alert'>❌ Error: " . $stmt->error . "</div>";
                        }
                    }
                    ?>
                    <form method="POST">
                      <div class="form-group">
                        <label class="form-label">Disaster Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Cyclone Amphan" required>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Type</label>
                        <input type="text" name="type" class="form-control" placeholder="e.g. Flood, Earthquake" required>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" placeholder="e.g. West Bengal" required>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" required>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Severity</label>
                        <select name="severity" class="form-select" required>
                          <option value="Low">Low</option>
                          <option value="Medium">Medium</option>
                          <option value="High">High</option>
                        </select>
                      </div>
                      <button type="submit" name="submit_disaster" class="btn btn-add">➕ Add Disaster</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Resources Tab -->
        <div class="tab-content">
            <div class="card">
                <div class="card-header">
                    <span class="icon">📦</span> Resources
                </div>
                <div class="card-body">
                    <?php
                    // --- Add Resource Form ---
                    if (isset($_POST['submit_resource'])) {
                        $stmt = $conn->prepare("INSERT INTO resources (resource_type, quantity, location) VALUES (?, ?, ?)");
                        $stmt->bind_param("sis", $_POST['resource_type'], $_POST['quantity'], $_POST['location']);
                        if ($stmt->execute()) {
                            echo "<div class='alert alert-success'>✅ Resource added successfully!</div>";
                        } else {
                            echo "<div class='alert'>❌ Error: " . $stmt->error . "</div>";
                        }
                    }
                    ?>
                    <form method="POST">
                      <div class="form-group">
                        <label class="form-label">Resource Type</label>
                        <input type="text" name="resource_type" class="form-control" placeholder="e.g. Water, Food, Medicine" required>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" placeholder="e.g. 500" required>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" placeholder="e.g. Warehouse A" required>
                      </div>
                      <button type="submit" name="submit_resource" class="btn btn-add">➕ Add Resource</button>
                    </form>
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
                    <?php
                    $team = ['team_name' => '', 'members_count' => '', 'contact' => ''];
                    if (isset($_GET['edit_team'])) {
                        $id = $_GET['edit_team'];
                        $res = $conn->query("SELECT * FROM rescue_teams WHERE id=$id");
                        $team = $res->fetch_assoc();
                    }
                    if (isset($_POST['submit_team'])) {
                        $stmt = $conn->prepare("INSERT INTO rescue_teams (team_name, members_count, contact) VALUES (?, ?, ?)");
                        $stmt->bind_param("sis", $_POST['team_name'], $_POST['members_count'], $_POST['contact']);
                        if ($stmt->execute()) {
                            echo "<div class='alert alert-success'>✅ Team added successfully!</div>";
                        } else {
                            echo "<div class='alert'>❌ Error: " . $stmt->error . "</div>";
                        }
                    }
                    if (isset($_POST['update_team'])) {
                        $stmt = $conn->prepare("UPDATE rescue_teams SET team_name=?, members_count=?, contact=? WHERE id=?");
                        $stmt->bind_param("sisi", $_POST['team_name'], $_POST['members_count'], $_POST['contact'], $_POST['id']);
                        if ($stmt->execute()) {
                            echo "<div class='alert alert-success'>✅ Team updated successfully!</div>";
                        } else {
                            echo "<div class='alert'>❌ Error: " . $stmt->error . "</div>";
                        }
                    }
                    ?>
                    <form method="POST">
                      <input type="hidden" name="id" value="<?= $_GET['edit_team'] ?? '' ?>">
                      <div class="form-group">
                        <label class="form-label">Team Name</label>
                        <input class="form-control" type="text" name="team_name" value="<?= $team['team_name'] ?>" placeholder="e.g. Alpha Team" required>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Members Count</label>
                        <input class="form-control" type="number" name="members_count" value="<?= $team['members_count'] ?>" placeholder="e.g. 10" required>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Contact</label>
                        <input class="form-control" type="text" name="contact" value="<?= $team['contact'] ?>" placeholder="e.g. 9876543210" required>
                      </div>
                      <button class="btn btn-add" type="submit" name="<?= isset($_GET['edit_team']) ? 'update_team' : 'submit_team' ?>">
                        <?= isset($_GET['edit_team']) ? '✏️ Update' : '➕ Add' ?> Team
                      </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Victims Tab -->
        <div class="tab-content">
            <div class="card">
                <div class="card-header">
                    <span class="icon">🧑‍🤝‍🧑</span> Victims
                </div>
                <div class="card-body">
                    <?php
                    // --- Add Victim Form ---
                    if (isset($_POST['submit_victim'])) {
                        $name = $_POST['name'];
                        $age = $_POST['age'];
                        $status = $_POST['status'];
                        $disaster_id = $_POST['disaster_id'];
                        $assigned_team_id = $_POST['assigned_team_id'];
                        $disasterCheck = $conn->prepare("SELECT id FROM disasters WHERE id = ?");
                        $disasterCheck->bind_param("i", $disaster_id);
                        $disasterCheck->execute();
                        $disasterCheck->store_result();
                        $teamCheck = $conn->prepare("SELECT id FROM rescue_teams WHERE id = ?");
                        $teamCheck->bind_param("i", $assigned_team_id);
                        $teamCheck->execute();
                        $teamCheck->store_result();
                        if ($disasterCheck->num_rows === 0) {
                            echo "<div class='alert'>❌ Error: Disaster ID <strong>$disaster_id</strong> does not exist.</div>";
                        } elseif ($teamCheck->num_rows === 0) {
                            echo "<div class='alert'>❌ Error: Team ID <strong>$assigned_team_id</strong> does not exist.</div>";
                        } else {
                            $stmt = $conn->prepare("INSERT INTO victims (name, age, status, disaster_id, assigned_team_id) VALUES (?, ?, ?, ?, ?)");
                            $stmt->bind_param("sdsii", $name, $age, $status, $disaster_id, $assigned_team_id);
                            if ($stmt->execute()) {
                                echo "<div class='alert alert-success'>✅ Victim added successfully!</div>";
                            } else {
                                echo "<div class='alert'>❌ Error: " . $stmt->error . "</div>";
                            }
                        }
                    }
                    ?>
                    <form method="POST">
                      <div class="form-group">
                        <label class="form-label">Victim Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter victim's name" required>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Age</label>
                        <input type="number" name="age" class="form-control" placeholder="Enter age" required>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                          <option value="Safe">Safe</option>
                          <option value="Injured">Injured</option>
                          <option value="Missing">Missing</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Disaster ID</label>
                        <input type="number" name="disaster_id" class="form-control" placeholder="e.g. 1" required>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Assigned Team ID</label>
                        <input type="number" name="assigned_team_id" class="form-control" placeholder="e.g. 2" required>
                      </div>
                      <button type="submit" name="submit_victim" class="btn btn-add">➕ Add Victim</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Disaster Management System</p>
    </footer>
</body>
</html>
