<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../login/db.php';
if (!isset($pdo)) { die("DB not loaded"); }


$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: ../login.html?error=session_expired");
    exit;
}

$stmt = $pdo->prepare("SELECT installation FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$installation = $stmt->fetchColumn();

$followerStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE installation = ?");
$followerStmt->execute([$installation]);
$followerCount = $followerStmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Combatives Dashboard</title>
  <link rel="stylesheet" href="dashboard.css" />
  <link rel="stylesheet" href="dash_panel.css" />
</head>

<body onload="loadDashboardView()">

    

<header class="dashboard-header">
  <div class="header-row">
    <div class="sidebar-toggle" onclick="toggleSidebar()">☰</div>
    <h1>Combatives Hub</h1>
  </div>

  <div class="user-info">
  <span>📍 <?php echo htmlspecialchars($installation ?? ''); ?></span>
  <span>👥 Followers: <?php echo $followerCount; ?></span>
  <button onclick="window.location.href='../installations/<?php echo strtolower(str_replace(' ', '-', $installation)); ?>.html'">View</button>
  <button onclick="window.location.href='../installations/installations.html'">Change</button>
</div>



  <nav class="sidebar" id="sidebar">
    <ul class="nav-links">
      <li><a href="#">Dashboard</a></li>
      <li><a href="#">Schedule</a></li>
      <li><a href="#">Programs</a></li>
      <li><a href="#">Resources</a></li>
      <li><a href="#">Settings</a></li>
    </ul>
  </nav>
</header>

<!-- Slide-Out Sidebar Panel -->
<div id="sidebarPanel" class="sidebar-panel hidden">
  <div class="sidebar-header">
    <span class="sidebar-close" onclick="toggleSidebar()">✕</span>
    <h3>Navigation</h3>
  </div>
  <ul class="sidebar-links">
    <li><a href="dashboard.html">Dashboard</a></li>
    <li><a href="program_resources.html">Program Resources</a></li>
    <li><a href="../installations/installations.html">Installations List</a></li>
    <li><a href="contact.html">Contact</a></li>
    <li><a href="settings.html">Settings</a></li>
    <li><a href="#">Logout</a></li>
    <li class="version-info">Last Update: <strong>06/29/2025</strong></li>
    <li class="version-info">Version: <strong>0.1.0</strong></li>
    

  </ul>
</div>

<!-- Optional dark overlay -->
<div id="overlay" class="overlay hidden" onclick="toggleSidebar()"></div>



<!--Main calender area-->
<section class="calendar-section">
  <h2>Schedule</h2>
  <div id="student-schedule" class="calendar-container">
  <div class="calendar-header">
    <button id="prevMonth">◀</button>
    <h3 id="monthYear"></h3>
    <button id="nextMonth">▶</button>
  </div>
  <div class="calendar-grid" id="calendarGrid"></div>
</div>
</section>


<hr class="section-divider" />



<!--quick actions toolbar-->
<div class="admin-actions admin-only">
    <button>Add upcoming classes</button> <!--goes to calender-->
    <button>Notify Followers</button> <!--will figure this out-->
    <button>Manage Program</button>
</div>


<hr class="section-divider" />


<!--Scheduling logic-->
<section id="schedule-section" class="dashboard-section">
  <h2>Upcoming Classes</h2>
  <div class="schedule-list">
    <div class="schedule-item">
      <h3>Class Name</h3>
      <p>Date: MM/DD/YYYY</p>
      <p>Time: HH:MM AM/PM</p>
      <p>Instructor: Name</p>
      <button>Join Class</button>     <!--------------make this a class to change button????????-->
    </div>
    <!-- Repeat schedule-item for more classes -->
  </div>

</section>
   <!-- Shared elements like nav/sidebar go here -->

  <!-- Program Manager Dashboard View -->
  <div id="manager-view" class="dashboard-section hidden">
    <h2>Program Manager Dashboard</h2>
    <!-- Your layout for managers -->
  </div>

  <!-- Student Dashboard View -->
  <div id="student-view" class="dashboard-section hidden">
    <h2>Student Dashboard</h2>
    <!-- Your layout for students -->
  </div>

  <!-- Instructor Dashboard View -->
  <div id="instructor-view" class="dashboard-section hidden">
    <h2>Instructor Dashboard</h2>
    <!-- Your layout for instructors -->
<script>
    function loadDashboardView() {
  const role = localStorage.getItem("userRole");

  document.getElementById("manager-view").classList.toggle("hidden", role !== "manager");
  document.getElementById("student-view").classList.toggle("hidden", role !== "student");
  document.getElementById("instructor-view").classList.toggle("hidden", role !== "instructor");
}
</script>


<!--sidebar trigger-->
<script>
  function toggleSidebar() {
    const panel = document.getElementById("sidebarPanel");
    const overlay = document.getElementById("overlay");

    panel.classList.toggle("show");
    panel.classList.toggle("hidden");
    overlay.classList.toggle("hidden");
  }
</script>


<!--calender logic-->
<script>
  const calendarGrid = document.getElementById('calendarGrid');
  const monthYear = document.getElementById('monthYear');
  let currentDate = new Date();

  function renderCalendar(date) {
    calendarGrid.innerHTML = '';
    const year = date.getFullYear();
    const month = date.getMonth();
    const firstDay = new Date(year, month, 1).getDay();
    const totalDays = new Date(year, month + 1, 0).getDate();

    monthYear.textContent = `${date.toLocaleString('default', { month: 'long' })} ${year}`;

    // Add blank days before first of month
    for (let i = 0; i < firstDay; i++) {
      calendarGrid.innerHTML += '<div></div>';
    }

    for (let day = 1; day <= totalDays; day++) {
      const dayCell = document.createElement('div');
      dayCell.textContent = day;
      dayCell.addEventListener('click', () => {
        alert(`Clicked on ${month + 1}/${day}/${year}`);
        // future: show modal or class info
      });
      calendarGrid.appendChild(dayCell);
    }
  }

  document.getElementById('prevMonth').onclick = () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar(currentDate);
  };

  document.getElementById('nextMonth').onclick = () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar(currentDate);
  };

  renderCalendar(currentDate);
</script>


<!-- Admin-only actions -->
<script>
  const userRole = 'manager'; // ← update this for testing

if (userRole === 'manager' || userRole === 'instructor') {
  document.querySelectorAll('.admin-only').forEach(el => {
    el.style.display = 'block';
  });
}

</script>


<!-- Snackbar -->
<div id="snackbar"></div>

<script>
  window.onload = function () {
    loadDashboardView();

    const nickname = localStorage.getItem("nickname");
    if (nickname) {
      const snackbar = document.getElementById("snackbar");
      snackbar.textContent = `Welcome, ${nickname}!`;
      snackbar.className = "show";
      setTimeout(() => snackbar.className = snackbar.className.replace("show", ""), 3000);
    }
  };
</script>

<style>
  #snackbar {
    visibility: hidden;
    min-width: 250px;
    margin-left: -125px;
    background-color: #333;
    color: #fff;
    text-align: center;
    border-radius: 4px;
    padding: 12px;
    position: fixed;
    z-index: 1;
    left: 50%;
    bottom: 30px;
    font-size: 17px;
  }

  #snackbar.show {
    visibility: visible;
    animation: fadein 0.5s, fadeout 0.5s 2.5s;
  }

  @keyframes fadein {
    from { bottom: 0; opacity: 0; }
    to { bottom: 30px; opacity: 1; }
  }

  @keyframes fadeout {
    from { bottom: 30px; opacity: 1; }
    to { bottom: 0; opacity: 0; }
  }
</style>


<!--modal for changing installations when button is clicked on dash-->
<div id="installDialog" class="modal hidden">
  <div class="modal-content">
    <span class="close" onclick="closeChangeInstallDialog()">×</span>
    <h2>Select Installation</h2>
    <ul id="installList">
      <li>Fort Bragg <button>Follow</button></li>
      <li>Fort Gordon <button>Follow</button></li>
      <li>Fort Cavazos <button>Follow</button></li>
      <!-- Add more installations -->
    </ul>
  </div>
</div>

<style>
.modal {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background-color: rgba(0, 0, 0, 0.5); display: flex;
  align-items: center; justify-content: center; z-index: 999;
}
.modal-content {
  background: white; padding: 20px; border-radius: 8px; width: 300px;
}
.hidden { display: none; }
</style>

<script>
function openChangeInstallDialog() {
  document.getElementById('installDialog').classList.remove('hidden');
}

function closeChangeInstallDialog() {
  document.getElementById('installDialog').classList.add('hidden');
}
</script>


</body>




