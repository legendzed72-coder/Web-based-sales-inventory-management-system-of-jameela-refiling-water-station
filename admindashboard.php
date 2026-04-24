<?php
/**
 * Admin Dashboard for AQUAPAY
 */
require_once 'auth_check.php';
require_once 'config.php';
// only admin may access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: userdashboard.php');
    exit;
}

// determine which module to show
$page = isset($_GET['page']) ? $_GET['page'] : 'report';
$allowed = ['report','pending','history','payroll','setting','store','products'];
if (!in_array($page, $allowed)) {
    $page = 'report';
}

// Get current user info
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Get statistics
$total_users = 0;
$total_orders = 0;
$total_products = 0;

// Get total users
$result = $conn->query("SELECT COUNT(*) as count FROM users");
if ($result) {
    $row = $result->fetch_assoc();
    $total_users = $row['count'];
}

// Get total orders (use store_orders table)
$result = $conn->query("SELECT COUNT(*) as count FROM store_orders");
if ($result) {
    $row = $result->fetch_assoc();
    $total_orders = $row['count'];
}

// Get total products
$result = $conn->query("SELECT COUNT(*) as count FROM products");
if ($result) {
    $row = $result->fetch_assoc();
    $total_products = $row['count'];
}

// Get recent orders from store_orders
$recent_orders = [];
$result = $conn->query("SELECT so.id, so.status, so.created_at, u.username 
                        FROM store_orders so 
                        JOIN users u ON so.user_id = u.id 
                        ORDER BY so.created_at DESC 
                        LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_orders[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AQUAPAY</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pFAAq5F7Y98/vXqzF5Vn0Vw+RObO6I5TSMx0T2FqiZldc5Ui1uDy0y0zVnQaG3Pirn6a0i5JloNlPvj53HXYGg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* generic button classes */
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary { background: #0077b6; color: #fff; }
        .btn-danger { background: #dc3545; color: #fff; }
        /* admin layout */
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #e0f7fa, #ffffff);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            position: relative;
        }
        .sidebar .profile img {
            width: 100px;
            border-radius: 50%;
        }
        .sidebar .profile h3 {
            margin-top: 10px;
            font-size: 18px;
            text-align: center;
        }
        .side-nav {
            margin-top: 30px;
            width: 100%;
        }
        .side-nav a {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            color: #0056b3;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .side-nav a i {
            margin-right: 10px;
        }
        .side-nav a.active,
        .side-nav a:hover {
            background: rgba(0,123,255,0.1);
        }
        .logout-btn {
            margin-top: auto;
            margin-bottom: 20px;
        }
        .logout-btn a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: #0077b6;
            color: #fff;
            border-radius: 50%;
            text-decoration: none;
            font-size: 20px;
        }
        .main-content {
            flex: 1;
            padding: 20px;
            background: #f8f9fa;
        }
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px,1fr));
            gap: 20px;
        }
        .card {
            background: rgba(255,255,255,0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .weekly-report-box {
            background: linear-gradient(180deg,#e3f2fd,#bbdefb);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .weekly-report-box h4 {
            margin: 0 0 5px 0;
            color: #003366;
        }
        .daily-table {
            width: 100%;
            border-collapse: collapse;
        }
        .daily-table th,
        .daily-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        .daily-table th {
            background: #e0f7fa;
        }
        /* widen annual report card to full width */
        #annual-report {
            grid-column: 1 / -1;
        }
        /* store/order form styling */
        .store-form label {
            font-weight:bold;
            color:#003366;
            text-transform:uppercase;
        }
        .store-form input {
            width: 80%;
            padding: 6px 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .store-buttons {
            margin: 10px 0;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .product-card {
            text-align: center;
            padding: 10px;
        }
        .product-card .prod-image {
            height: 100px;
            margin-bottom: 10px;
        }
        .product-card img {
            max-height: 100%;
            max-width: 100%;
        }
        .product-card .no-image {
            width: 100px;
            height: 100px;
            background: #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
        }
        /* payroll form styling */
        .payroll-inputs {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .payroll-box {
            background: linear-gradient(180deg, #e3f2fd, #bbdefb);
            border: 1px solid rgba(0,0,0,0.1);
            padding: 20px;
            border-radius: 10px;
            width: 180px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .payroll-box label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #003366;
            text-transform: uppercase;
            font-size: 12px;
        }
        .payroll-box input {
            width: 100%;
            padding: 6px 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include 'dashboardheader.php'; ?>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="profile">
                <img src="avatar.png" alt="Profile">
                <h3><?= htmlspecialchars($username) ?></h3>
            </div>
            <nav class="side-nav">
                <a href="?page=report" class="<?= $page==='report'?'active':''?>"><i class="fa fa-chart-bar"></i> Report</a>
                <a href="?page=products" class="<?= $page==='products'?'active':''?>"><i class="fa fa-cube"></i> Products</a>
                <a href="?page=store" class="<?= $page==='store'?'active':''?>"><i class="fa fa-store"></i> Store</a>
                <a href="?page=pending" class="<?= $page==='pending'?'active':''?>"><i class="fa fa-box"></i> Pending orders</a>
                <a href="?page=history" class="<?= $page==='history'?'active':''?>"><i class="fa fa-clock"></i> HISTORY</a>
                <a href="?page=payroll" class="<?= $page==='payroll'?'active':''?>"><i class="fa fa-money-bill"></i> PAYROLL</a>
                <a href="?page=setting" class="<?= $page==='setting'?'active':''?>"><i class="fa fa-cog"></i> SETTING</a>
            </nav>
        </aside>
        <main class="main-content">
            <?php
                $modulePath = __DIR__ . '/modules/' . $page . '.php';
                if (file_exists($modulePath)) {
                    include $modulePath;
                } else {
                    echo '<div class="card"><p>Module not found.</p></div>';
                }
            ?>
        </main>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    var ctx = document.getElementById('salesChart');
    if(ctx) {
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Item1','Item2','Item3'],
          datasets: [{
            label: 'Sales',
            backgroundColor: '#0077b6',
            data: [5,12,15]
          }]
        },
        options: { responsive: true }
      });
    }
    </script>
</body>
</html>
