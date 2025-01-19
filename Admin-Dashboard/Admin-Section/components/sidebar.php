<nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <div class="text-center">
                    <img src="../img/photos/Logo.png" class="img-fluid rounded-circle" width="144" height="144" />
                    <a class="sidebar-brand" href="#">
                        <p class="align-middle">Franco - Pascual</p>
                    </a>
                </div>
                <ul class="sidebar-nav">
                    <li class="sidebar-header">Data</li>
                    <li class="sidebar-item <?php echo ($currentPage == '_branches.php') ? 'active' : ''; ?>">
                        <a class="sidebar-link" href="../Admin-Section/_branches.php">
                            <i class="align-middle" data-feather="trending-up"></i>
                            <span class="align-middle">Clinic Analytics</span>
                        </a>
                    </li>
                    <li class="sidebar-header">Maintenance</li>
                    <li class="sidebar-item <?php echo ($currentPage == '_secretary.php') ? 'active' : ''; ?>">
                        <a class="sidebar-link" href="../Admin-Section/_secretary.php">
                            <i class="align-middle" data-feather="user-plus"></i>
                            <span class="align-middle">Secretary</span>
                        </a>
                    </li>

                </ul>
            </div>
        </nav>  