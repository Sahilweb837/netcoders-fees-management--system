<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo BASE_URL; ?>" class="brand-link text-center py-3" style="background: #ffffff; border-bottom: 1px solid #eee;">
        <img src="<?php echo BASE_URL; ?>image.png" alt="Logo" class="brand-image" style="float: none; max-height: 55px; width: auto; filter: none;">
        <div class="mt-2 brand-text font-weight-bold" style="display: block; font-size: 1.3rem; color: #ff5532; letter-spacing: -0.5px;"><?php echo APP_NAME; ?></div>
    </a>

    <!-- Sidebar -->
    <div class="sidebar" style="background: #ffffff;">
        <style>
            .nav-sidebar .nav-link.active {
                background-color: #ff5532 !important;
                color: #fff !important;
                box-shadow: 0 4px 10px rgba(255, 85, 50, 0.3);
            }
            .nav-sidebar .nav-link:hover {
                background-color: #f8f9fa;
                color: #ff5532 !important;
            }
            .sidebar-light-primary .nav-sidebar > .nav-item > .nav-link.active {
                background-color: #ff5532 !important;
            }
            .brand-link {
                background-color: #ffffff !important;
                border-bottom: 1px solid #f0f0f0 !important;
            }
            .brand-link .brand-text {
                color: #ff5532 !important;
            }
        </style>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">MANAGEMENT</li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>
                            Students
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo BASE_URL; ?>students/admission.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>New Admission</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo BASE_URL; ?>students/list.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Students</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo BASE_URL; ?>students/attendance.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Daily Attendance</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo BASE_URL; ?>attendance/logs.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Attendance History</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-book"></i>
                        <p>
                            Courses & Batches
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo BASE_URL; ?>courses/list.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Courses</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo BASE_URL; ?>batches/list.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Batches</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>
                            Staff Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo BASE_URL; ?>staff/list.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Staff List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo BASE_URL; ?>staff/attendance.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Attendance</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo BASE_URL; ?>staff/salary.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Staff Salaries</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-header">FINANCE</li>

                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>fees/collect.php" class="nav-link">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Collect Fees</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>invoices/list.php" class="nav-link">
                         <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Fee Receipts</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>expenses/list.php" class="nav-link">
                        <i class="nav-icon fas fa-wallet"></i>
                        <p>Expenses</p>
                    </a>
                </li>

                <li class="nav-header">REPORTS</li>
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>reports/revenue.php" class="nav-link">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>Revenue Report</p>
                    </a>
                </li>

                <?php if (in_array($_SESSION['role'], ['root_admin', 'super_admin'])): ?>
                <li class="nav-header">SYSTEM</li>
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>users/list.php" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>System Users</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>users/logs.php" class="nav-link">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Activity Logs</p>
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>logout.php" class="nav-link text-danger">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
