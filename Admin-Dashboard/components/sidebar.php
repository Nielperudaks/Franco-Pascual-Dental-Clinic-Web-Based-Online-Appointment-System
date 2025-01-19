<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <div class="text-center">
            <img src="img/photos/Logo.png" class="img-fluid rounded-circle" width="144" height="144" />
            <a class="sidebar-brand" href="index.php">
                <p class="align-middle">Franco - Pascual</p>
            </a>
        </div>

        <ul class="sidebar-nav">
            <li class="sidebar-header">
                Data
            </li>

            <li class="sidebar-item <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="../Admin-Dashboard/index.php">
                    <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
                </a>
            </li>

            <li class="sidebar-header">
                Records
            </li>
            <li class="sidebar-item <?php echo ($currentPage == 'Transactions.php') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="../Admin-Dashboard/Transactions.php">
                    <i class="align-middle" data-feather="book"></i> <span class="align-middle">Transactions</span>
                </a>
            </li>

            <li class="sidebar-item <?php echo ($currentPage == 'Microtransactions.php') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="Micro-transactions/Microtransactions.php">
                    <i class="align-middle" data-feather="credit-card"></i> <span class="align-middle">Payments</span>
                </a>
            </li>

            <li class="sidebar-item <?php echo ($currentPage == 'Clients.php') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="../Client-Records/Clients.php">
                    <i class="align-middle" data-feather="user"></i> <span class="align-middle">Clients</span>
                </a>
            </li>

            <
            <li class="sidebar-header">
                Maintenance
            </li>
            <li class="sidebar-item <?php echo ($currentPage == 'maintenance.php') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="../Maintenance/maintenance.php">
                    <i class="align-middle" data-feather="settings"></i> <span class="align-middle">Management Settings</span>
                </a>
            </li>

            
    </div>
</nav>