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

    <!-- Manage Instructor -->
    <li class="nav-item <?php if ($current_page == 'manage-instructors.php')
        echo 'active'; ?>">
        <a class="nav-link <?php if ($current_page == 'manage-instructors.php')
            echo 'bg-white text-dark'; ?>" href="manage-instructors.php">
            <i class="fas fa-chalkboard-teacher <?php if ($current_page == 'manage-instructors.php')
                echo 'text-dark'; ?>"></i>
            <span>Manage Instructors</span>
        </a>
    </li>


    <li class="nav-item <?php if (in_array($current_page, ['manage-members.php', 'edit-member.php', 'create-new-member.php', 'manage-active-members.php', 'manage-pending-members.php', 'manage-banned-members.php', 'manage-suspended-members.php']))
        echo 'active'; ?>">
        <a class="nav-link collapsed <?php if (in_array($current_page, ['manage-members.php', 'create-new-member.php', 'manage-active-members.php', 'manage-pending-members.php', 'manage-banned-members.php', 'manage-suspended-members.php']))
            echo ''; ?>" href="manage-members.php" data-toggle="collapse" data-target="#collapseMembers"
            aria-expanded="true" aria-controls="collapseMembers">
            <i class="fas fa-fw fa-users"></i>
            <span>Manage Members</span>
        </a>
        <div id="collapseMembers" class="collapse <?php if (in_array($current_page, ['manage-members.php', 'edit-member.php', 'create-new-member.php', 'manage-active-members.php', 'manage-pending-members.php', 'manage-banned-members.php', 'manage-suspended-members.php']))
            echo 'show'; ?>" aria-labelledby="headingMembers" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item <?php if ($current_page == 'manage-members.php' || 'create-new-member.php')
                    echo 'active text-warning'; ?>" href="manage-members.php">All Members</a>
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?php if ($current_page == 'manage-pending-members.php')
                        echo 'active text-warning'; ?>" href="manage-pending-members.php">Pending Members</a>
                    <a class="collapse-item <?php if ($current_page == 'manage-active-members.php')
                        echo 'active text-warning'; ?>" href="manage-active-members.php">Active Members</a>
                    <a class="collapse-item <?php if ($current_page == 'manage-banned-members.php')
                        echo 'active text-warning'; ?>" href="manage-banned-members.php">Banned Members</a>
                    <a class="collapse-item <?php if ($current_page == 'manage-suspended-members.php')
                        echo 'active text-warning'; ?>" href="manage-suspended-members.php">Suspended Members</a>
                </div>
            </div>
    </li>



    <li class="nav-item <?php if (in_array($current_page, ['manage-home.php', 'manage-faqs.php', 'manage-contacts.php']))
        echo 'active'; ?>">
        <a class="nav-link collapsed <?php if (in_array($current_page, ['manage-home.php', 'manage-facilities.php', 'manage-faqs.php', 'manage-contacts.php']))
            echo ''; ?>" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true"
            aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Manage Landing Page</span>
        </a>
        <div id="collapseUtilities" class="collapse <?php if (in_array($current_page, ['manage-home.php', 'manage-faqs.php', 'manage-contacts.php']))
            echo 'show'; ?>" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item <?php if ($current_page == 'manage-home.php')
                    echo 'active text-warning'; ?>" href="manage-home.php">Home</a>
                <a class="collapse-item <?php if ($current_page == 'manage-faqs.php')
                    echo 'active text-warning'; ?>" href="manage-faqs.php">F.A.Qs</a>
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