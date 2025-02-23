<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-warning sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
        <div class="sidebar-brand-icon">
            <img id="sidebarLogo" src="../assets/fitlogsync-sidebar-close.png" alt="FitLogSync" style="height: 60px;">
        </div>
        <div class="sidebar-brand-text">
            <img src="../assets/fitlogsync-sidebar-mono.png" alt="FiT-LOGSYNC" style="height: 30px;">
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <?php $current_page = basename($_SERVER['PHP_SELF']); ?>

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?php if ($current_page == 'dashboard.php')
        echo 'active'; ?>">
        <a class="nav-link <?php if ($current_page == 'dashboard.php')
            echo 'bg-white text-dark'; ?>" href="dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt <?php if ($current_page == 'dashboard.php')
                echo 'text-dark'; ?>"></i>
            <span>Dashboard</span>
        </a>
    </li>


    <li class="nav-item <?php if (in_array($current_page, ['manage-home.php', 'manage-facilities.php', 'manage-reviews.php', 'manage-instructors.php', 'manage-pricing.php', 'faqs.php', 'manage-contacts.php']))
        echo 'active'; ?>">
        <a class="nav-link collapsed <?php if (in_array($current_page, ['manage-home.php', 'manage-facilities.php', 'manage-reviews.php', 'manage-instructors.php', 'manage-pricing.php', 'faqs.php', 'manage-contacts.php']))
            echo ''; ?>" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true"
            aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Manage Landing Page</span>
        </a>
        <div id="collapseUtilities" class="collapse <?php if (in_array($current_page, ['manage-home.php', 'manage-facilities.php', 'manage-reviews.php', 'manage-instructors.php', 'manage-pricing.php', 'faqs.php', 'manage-contacts.php']))
            echo 'show'; ?>" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item <?php if ($current_page == 'manage-home.php')
                    echo 'active text-warning'; ?>" href="manage-home.php">Home</a>
                <a class="collapse-item <?php if ($current_page == 'manage-facilities.php')
                    echo 'active text-warning'; ?>" href="manage-facilities.php">Facilities</a>
                <a class="collapse-item <?php if ($current_page == 'manage-reviews.php')
                    echo 'active text-warning'; ?>" href="manage-reviews.php">Reviews</a>
                <a class="collapse-item <?php if ($current_page == 'manage-instructors.php')
                    echo 'active text-warning'; ?>" href="manage-instructors.php">Instructors</a>
                <a class="collapse-item <?php if ($current_page == 'manage-pricing.php')
                    echo 'active text-warning'; ?>" href="manage-pricing.php">Pricing</a>
                <a class="collapse-item <?php if ($current_page == 'faqs.php')
                    echo 'active text-warning'; ?>" href="faqs.php">F.A.Qs</a>
                <a class="collapse-item <?php if ($current_page == 'manage-contacts.php')
                    echo 'active text-warning'; ?>" href="manage-contacts.php">Contact</a>
            </div>
        </div>

    </li>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->