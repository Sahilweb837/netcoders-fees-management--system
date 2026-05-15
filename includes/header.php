<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo APP_NAME; ?> | Dashboard</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <!-- Custom Orange Theme CSS -->
    <style>
        :root {
            --header-height: 3.5rem;
            --first-color: #ff5532;
            --second-color: #575757;
            --text-color: #111;
            --body-color: #fff;
            --shadow: rgba(0, 0, 0, 0.1) 0px 10px 50px;
            --normal-font-size: .938rem;
            --small-font-size: .813rem;
            --smaller-font-size: .75rem;
            --z-tooltip: 10;
            --z-fixed: 100;
        }

        .main-sidebar { background-color: #1a1a1a !important; }
        .nav-sidebar .nav-link.active { background-color: var(--first-color) !important; color: #fff !important; }
        .brand-link { border-bottom: 1px solid #333; background-color: #1a1a1a !important; color: #fff !important; }
        .navbar-orange { background-color: var(--first-color) !important; }
        .btn-primary { background-color: var(--first-color) !important; border-color: var(--first-color) !important; }
        .btn-primary:hover { background-color: #e64a2a !important; }
        .small-box.bg-info { background-color: var(--first-color) !important; }
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active { background-color: var(--first-color) !important; }
        .accent-primary .btn-link, .accent-primary a { color: var(--first-color); }
        .card-primary.card-outline { border-top: 3px solid var(--first-color); }
        .text-primary { color: var(--first-color) !important; }
        
        /* Modern aesthetics */
        .card { border-radius: 12px; box-shadow: var(--shadow); }
        .main-header { border-bottom: 1px solid #eee; }
        .content-wrapper { background-color: #f8f9fa; }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item d-none d-sm-inline-block">
                <span class="nav-link text-dark font-weight-bold">
                    <i class="far fa-clock mr-1 text-primary"></i> <span id="globalClock"></span>
                </span>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user-circle"></i> <?php echo $_SESSION['name']; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <a href="<?php echo BASE_URL; ?>logout.php" class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <?php include 'sidebar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Flash Messages -->
        <?php $flash = getFlash(); if ($flash): ?>
            <div class="container-fluid pt-3">
                <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <?php echo $flash['message']; ?>
                </div>
            </div>
        <?php endif; ?>
