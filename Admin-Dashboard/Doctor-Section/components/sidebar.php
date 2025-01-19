<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <div class="text-center">
            <img src="../img/photos/Logo.png" class="img-fluid rounded-circle" width="144" height="144" />
            <a class="sidebar-brand" href="index.php">
                <p class="align-middle">Franco - Pascual</p>
            </a>
        </div>

        <ul class="sidebar-nav">
            <li class="sidebar-header">
                Appointment
            </li>
            <li class="sidebar-item <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="../Doctor-Section/index.php">
                    <i class="align-middle" data-feather="calendar"></i> <span class="align-middle">Calendar</span>
                </a>
            </li>


            <li class="sidebar-header">
                Records
            </li>

            <li class="sidebar-item <?php echo ($currentPage == 'Transactions.php') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="../Doctor-Section/Transactions.php">
                    <i class="align-middle" data-feather="book"></i> <span class="align-middle">Transactions</span>
                </a>
            </li>

            <li class="sidebar-item <?php echo ($currentPage == 'Clients.php') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="../Doctor-Section/Clients.php">
                    <i class="align-middle" data-feather="user"></i> <span class="align-middle">Clients</span>
                </a>
            </li>
            <li class="sidebar-header">
                            Others
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="../logout.php">
                                <i class="align-middle" data-feather="log-out"></i> <span
                                    class="align-middle">Log out</span>
                            </a>
                        </li>

            

    </div>
</nav>