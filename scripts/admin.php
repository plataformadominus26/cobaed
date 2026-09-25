<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Admin - Mobile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff6b6b;
            --secondary-color: #4ecdc4;
            --dark-color: #292f36;
            --light-color: #f7fff7;
            --accent-color: #ff9f1c;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            font-size: 14px;
        }
        
        .mobile-container {
            max-width: 100%;
            margin: 0 auto;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Header */
        .mobile-header {
            background-color: var(--dark-color);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 100;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .mobile-header h1 {
            font-size: 1.2rem;
        }
        
        .menu-toggle {
            font-size: 1.5rem;
            background: none;
            border: none;
            color: white;
        }
        
        /* Sidebar */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            height: 100vh;
            background-color: var(--dark-color);
            color: white;
            transition: all 0.3s ease;
            z-index: 101;
            padding-top: 60px;
        }
        
        .mobile-sidebar.active {
            left: 0;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            padding: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-menu li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .sidebar-menu li i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 100;
            display: none;
        }
        
        .sidebar-overlay.active {
            display: block;
        }
        
        /* Main Content */
        .mobile-main {
            padding: 70px 15px 70px;
            min-height: 100vh;
        }
        
        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .card i {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .card h3 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        /* Tables */
        .mobile-table-container {
            overflow-x: auto;
            margin-bottom: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 8px;
            text-align: left;
            border-bottom: 1px solid #eee;
            white-space: nowrap;
        }
        
        th {
            background-color: var(--dark-color);
            color: white;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Forms */
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
        }
        
        /* Tabs */
        .mobile-tabs {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        
        .tab-btn {
            padding: 10px 15px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            font-size: 14px;
            cursor: pointer;
        }
        
        .tab-btn.active {
            border-bottom-color: var(--primary-color);
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Bottom Navigation */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: white;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
            z-index: 100;
        }
        
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #666;
            text-decoration: none;
            font-size: 0.8rem;
        }
        
        .nav-item i {
            font-size: 1.2rem;
            margin-bottom: 3px;
        }
        
        .nav-item.active {
            color: var(--primary-color);
        }
        
        /* Responsive Adjustments */
        @media (min-width: 768px) {
            .mobile-container {
                max-width: 750px;
            }
            
            .dashboard-cards {
                grid-template-columns: repeat(4, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <!-- Header -->
        <header class="mobile-header">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Restaurant Admin</h1>
            <div></div> <!-- Spacer -->
        </header>
        
        <!-- Sidebar -->
        <aside class="mobile-sidebar" id="sidebar">
            <ul class="sidebar-menu">
                <li><a href="#dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#orders"><i class="fas fa-utensils"></i> Orders</a></li>
                <li><a href="#menu"><i class="fas fa-book"></i> Menu</a></li>
                <li><a href="#reservations"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
                <li><a href="#staff"><i class="fas fa-users"></i> Staff</a></li>
                <li><a href="#inventory"><i class="fas fa-boxes"></i> Inventory</a></li>
                <li><a href="#reports"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="#settings"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="#logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Main Content -->
        <main class="mobile-main">
            <!-- Dashboard Section -->
            <section id="dashboardSection" class="tab-content active">
                <h2>Dashboard</h2>
                <div class="dashboard-cards">
                    <div class="card">
                        <i class="fas fa-utensils"></i>
                        <h3>24</h3>
                        <p>Active Orders</p>
                    </div>
                    <div class="card">
                        <i class="fas fa-calendar-alt"></i>
                        <h3>15</h3>
                        <p>Reservations</p>
                    </div>
                    <div class="card">
                        <i class="fas fa-dollar-sign"></i>
                        <h3>$2,450</h3>
                        <p>Today's Revenue</p>
                    </div>
                    <div class="card">
                        <i class="fas fa-users"></i>
                        <h3>8</h3>
                        <p>Staff On Duty</p>
                    </div>
                </div>
                
                <h3>Recent Orders</h3>
                <div class="mobile-table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Table</th>
                                <th>Items</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#142</td>
                                <td>12</td>
                                <td>3</td>
                                <td><span style="color: green;">In Progress</span></td>
                            </tr>
                            <tr>
                                <td>#141</td>
                                <td>8</td>
                                <td>5</td>
                                <td><span style="color: orange;">Preparing</span></td>
                            </tr>
                            <tr>
                                <td>#140</td>
                                <td>5</td>
                                <td>2</td>
                                <td><span style="color: red;">Pending</span></td>
                            </tr>
                            <tr>
                                <td>#139</td>
                                <td>3</td>
                                <td>4</td>
                                <td><span style="color: green;">Completed</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
            
            <!-- Orders Section -->
            <section id="ordersSection" class="tab-content">
                <h2>Orders Management</h2>
                <div class="mobile-tabs">
                    <button class="tab-btn active" data-tab="currentOrders">Current</button>
                    <button class="tab-btn" data-tab="completedOrders">Completed</button>
                    <button class="tab-btn" data-tab="addOrder">Add New</button>
                </div>
                
                <div id="currentOrders" class="tab-content active">
                    <div class="mobile-table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Table</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#142</td>
                                    <td>12</td>
                                    <td>12:30 PM</td>
                                    <td>In Progress</td>
                                    <td><button class="btn">View</button></td>
                                </tr>
                                <tr>
                                    <td>#141</td>
                                    <td>8</td>
                                    <td>12:15 PM</td>
                                    <td>Preparing</td>
                                    <td><button class="btn">View</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div id="completedOrders" class="tab-content">
                    <div class="mobile-table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Table</th>
                                    <th>Time</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#140</td>
                                    <td>5</td>
                                    <td>11:45 AM</td>
                                    <td>$45.20</td>
                                </tr>
                                <tr>
                                    <td>#139</td>
                                    <td>3</td>
                                    <td>11:30 AM</td>
                                    <td>$68.50</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div id="addOrder" class="tab-content">
                    <form>
                        <div class="form-group">
                            <label for="orderTable">Table Number</label>
                            <select id="orderTable" class="form-control">
                                <option value="">Select Table</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <!-- More options -->
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Items</label>
                            <div style="margin-bottom: 10px;">
                                <select class="form-control" style="width: 70%; display: inline-block;">
                                    <option value="">Select Item</option>
                                    <!-- Menu items -->
                                </select>
                                <input type="number" min="1" value="1" class="form-control" style="width: 25%; display: inline-block;">
                            </div>
                            <button type="button" class="btn btn-secondary">Add Item</button>
                        </div>
                        
                        <button type="submit" class="btn btn-block">Create Order</button>
                    </form>
                </div>
            </section>
            
            <!-- Menu Section -->
            <section id="menuSection" class="tab-content">
                <h2>Menu Management</h2>
                <div class="mobile-tabs">
                    <button class="tab-btn active" data-tab="menuItems">Menu Items</button>
                    <button class="tab-btn" data-tab="categories">Categories</button>
                    <button class="tab-btn" data-tab="addMenuItem">Add Item</button>
                </div>
                
                <div id="menuItems" class="tab-content active">
                    <div class="mobile-table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Margherita Pizza</td>
                                    <td>Main</td>
                                    <td>$12.99</td>
                                    <td>Available</td>
                                </tr>
                                <tr>
                                    <td>Caesar Salad</td>
                                    <td>Starters</td>
                                    <td>$8.50</td>
                                    <td>Available</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
        
        <!-- Bottom Navigation -->
        <nav class="mobile-bottom-nav">
            <a href="#dashboard" class="nav-item active" data-section="dashboardSection">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="#orders" class="nav-item" data-section="ordersSection">
                <i class="fas fa-utensils"></i>
                <span>Orders</span>
            </a>
            <a href="#menu" class="nav-item" data-section="menuSection">
                <i class="fas fa-book"></i>
                <span>Menu</span>
            </a>
            <a href="#more" class="nav-item">
                <i class="fas fa-ellipsis-h"></i>
                <span>More</span>
            </a>
        </nav>
    </div>

    <script>
        // Mobile Navigation
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
            });
            
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            });
            
            // Bottom Navigation
            const navItems = document.querySelectorAll('.nav-item');
            const tabContents = document.querySelectorAll('.tab-content');
            
            navItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all nav items
                    navItems.forEach(navItem => {
                        navItem.classList.remove('active');
                    });
                    
                    // Add active class to clicked item
                    this.classList.add('active');
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.remove('active');
                    });
                    
                    // Show the selected content
                    const sectionId = this.getAttribute('data-section');
                    if (sectionId) {
                        document.getElementById(sectionId).classList.add('active');
                    }
                });
            });
            
            // Tab Navigation
            const tabBtns = document.querySelectorAll('.tab-btn');
            
            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    const tabContainer = this.closest('.mobile-tabs').parentElement;
                    
                    // Remove active class from all buttons in this tab group
                    this.closest('.mobile-tabs').querySelectorAll('.tab-btn').forEach(tb => {
                        tb.classList.remove('active');
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Hide all tab contents in this group
                    tabContainer.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    
                    // Show the selected tab
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>