<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-warning sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
        <div class="sidebar-brand-icon">
            <img id="sidebarLogo" src="assets/fitlogsync-sidebar-close.png" alt="FitLogSync" style="height: 60px;">
        </div>
        <div class="sidebar-brand-text">
            <img src="assets/fitlogsync-sidebar-mono.png" alt="FiT-LOGSYNC" style="height: 30px;">
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

    <?php 
    $role_id = $_SESSION['role_id'];
    if ($role_id == 5) { ?>


    <!-- Nav Item - My Subscription -->
    <li class="nav-item <?php if ($current_page == 'my-subscription.php') echo 'active'; ?>">
        <a class="nav-link <?php if ($current_page == 'my-subscription.php') echo 'bg-white text-dark'; ?>" href="my-subscription.php">
            <i class="fas fa-fw fa-calendar-check <?php if ($current_page == 'my-subscription.php') echo 'text-dark'; ?>"></i>
            <span>My Subscription</span>
        </a>
    </li>
    <?php } ?>

    <?php
    $page_name = "manage-payments.php";
    $role_id = $_SESSION['role_id'];
    $query = "SELECT * FROM permissions WHERE page_name = ? AND role_id = ? AND permission = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $page_name, $role_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        ?>
        <li class="nav-item <?php if ($current_page == 'manage-payments.php')
            echo 'active'; ?>">
            <a class="nav-link <?php if ($current_page == 'manage-payments.php')
                echo 'bg-white text-dark'; ?>" href="manage-payments.php">
                <i class="fas fa-solid fa-cash-register <?php if ($current_page == 'manage-payments.php')
                    echo 'text-dark'; ?>"></i>
                <span>Manage Payments</span>
            </a>
        </li>
        <?php
    }
    ?>

    

    <!-- Other navigation items... -->
    <?php
    $page_name = "manage-members.php";
    $role_id = $_SESSION['role_id'];
    $query = "SELECT * FROM permissions WHERE page_name = ? AND role_id = ? AND permission = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $page_name, $role_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        ?>
        <li class="nav-item <?php if ($current_page == 'manage-members.php')
            echo 'active'; ?>">
            <a class="nav-link <?php if ($current_page == 'manage-members.php')
                echo 'bg-white text-dark'; ?>" href="manage-members.php">
                <i class="fas fa-solid fa-fw fa-users <?php if ($current_page == 'manage-members.php')
                    echo 'text-dark'; ?>"></i>
                <span>Manage Members</span>
            </a>
        </li>
        <?php
    }
    ?>


    <!-- Other navigation items... -->
    <?php
    $page_name = "manage-front-desk.php";
    $role_id = $_SESSION['role_id'];
    $query = "SELECT * FROM permissions WHERE page_name = ? AND role_id = ? AND permission = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $page_name, $role_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        ?>
        <li class="nav-item <?php if ($current_page == 'manage-front-desk.php')
            echo 'active'; ?>">
            <a class="nav-link <?php if ($current_page == 'manage-front-desk.php')
                echo 'bg-white text-dark'; ?>" href="manage-front-desk.php">
                <i class="fas fa-solid fa-user-tie <?php if ($current_page == 'manage-front-desk.php')
                    echo 'text-dark'; ?>"></i>
                <span>Manage Front Desk</span>
            </a>
        </li>
        <?php
    }
    ?>

    <?php
    $page_name = "manage-instructors.php";
    $role_id = $_SESSION['role_id'];
    $query = "SELECT * FROM permissions WHERE page_name = ? AND role_id = ? AND permission = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $page_name, $role_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        ?>
        <li class="nav-item <?php if ($current_page == 'manage-instructors.php')
            echo 'active'; ?>">
            <a class="nav-link <?php if ($current_page == 'manage-instructors.php')
                echo 'bg-white text-dark'; ?>" href="manage-instructors.php">
                <i class="fas fa-solid fa-dumbbell <?php if ($current_page == 'manage-instructors.php')
                    echo 'text-dark'; ?>"></i>
                <span>Manage Instructor</span>
            </a>
        </li>
        <?php
    }
    ?>


    <?php
    $page_name = "manage-plan.php";
    $role_id = $_SESSION['role_id'];
    $query = "SELECT * FROM permissions WHERE page_name = ? AND role_id = ? AND permission = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $page_name, $role_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        ?>
        <li class="nav-item <?php if ($current_page == 'manage-plan.php')
            echo 'active'; ?>">
            <a class="nav-link <?php if ($current_page == 'manage-plan.php')
                echo 'bg-white text-dark'; ?>" href="manage-plan.php">
                <i class="fas fa-solid fa-calendar-days <?php if ($current_page == 'manage-plan.php')
                    echo 'text-dark'; ?>"></i>
                <span>Manage Plans</span>
            </a>
        </li>
        <?php
    }
    ?>

    <?php
    $page_name = "manage-coupons.php";
    $role_id = $_SESSION['role_id'];
    $query = "SELECT * FROM permissions WHERE page_name = ? AND role_id = ? AND permission = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $page_name, $role_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        ?>
        <li class="nav-item <?php if ($current_page == 'manage-coupons.php')
            echo 'active'; ?>">
            <a class="nav-link <?php if ($current_page == 'manage-coupons.php')
                echo 'bg-white text-dark'; ?>" href="manage-coupons.php">
                <i class="fas fa-solid fa-ticket <?php if ($current_page == 'manage-coupons.php')
                    echo 'text-dark'; ?>"></i>
                <span>Manage Coupons</span>
            </a>
        </li>
        <?php
    }
    ?>

    <?php
    $page_name = "manage-discount.php";
    $role_id = $_SESSION['role_id'];
    $query = "SELECT * FROM permissions WHERE page_name = ? AND role_id = ? AND permission = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $page_name, $role_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        ?>
        <li class="nav-item <?php if ($current_page == 'manage-discount.php')
            echo 'active'; ?>">
            <a class="nav-link <?php if ($current_page == 'manage-discount.php')
                echo 'bg-white text-dark'; ?>" href="manage-discount.php">
                <i class="fas fa-solid fa-tags <?php if ($current_page == 'manage-discount.php')
                    echo 'text-dark'; ?>"></i>
                <span>Manage Discounts</span>
            </a>
        </li>
        <?php
    }
    ?>

    <?php
    $page_name = "permission-settings.php";
    $role_id = $_SESSION['role_id'];
    $query = "SELECT * FROM permissions WHERE page_name = ? AND role_id = ? AND permission = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $page_name, $role_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        ?>
        <li class="nav-item <?php if ($current_page == 'permission-settings.php')
            echo 'active'; ?>">
            <a class="nav-link <?php if ($current_page == 'permission-settings.php')
                echo 'bg-white text-dark'; ?>" href="permission-settings.php">
                <i class="fas fa-solid fa-circle-exclamation <?php if ($current_page == 'permission-settings.php')
                    echo 'text-dark'; ?>"></i>
                <span>Role Permissions Settings</span>
            </a>
        </li>
        <?php
    }
    ?>



    <!-- Manage Landing Page -->
    <!-- <li class="nav-item <?php if (in_array($current_page, ['manage-home.php', 'manage-faqs.php', 'manage-contacts.php']))
        echo 'active'; ?>">
        <a class="nav-link collapsed <?php if (in_array($current_page, ['manage-home.php', 'manage-facilities.php', 'manage-faqs.php', 'manage-contacts.php']))
            echo ''; ?>" href="#" data-toggle="collapse" data-target="#collapseHelpMessages" aria-expanded="true"
            aria-controls="collapseHelpMessages">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Manage Landing Page</span>
        </a>
        <div id="collapseHelpMessages" class="collapse <?php if (in_array($current_page, ['manage-home.php', 'manage-faqs.php', 'manage-contacts.php']))
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
    </li> -->

    <!-- Manage Plan -->
    <!-- <li class="nav-item <?php if ($current_page == 'manage-plan.php')
        echo 'active'; ?>">
        <a class="nav-link <?php if ($current_page == 'manage-plan.php')
            echo 'bg-white text-dark'; ?>" href="manage-plan.php">
            <i class="fas fa-list-alt <?php if ($current_page == 'manage-plan.php')
                echo 'text-dark'; ?>"></i>
            <span>Manage Plan</span>
        </a>
    </li> -->

    <!-- <li class="nav-item <?php if (in_array($current_page, ['manage-all-help-messages.php', 'manage-read-help-messages.php', 'manage-unread-help-messages.php', 'manage-replied-help-messages.php']))
        echo 'active'; ?>">
        <a class="nav-link collapsed <?php if (in_array($current_page, ['manage-all-help-messages.php', 'manage-facilities.php', 'manage-read-help-messages.php', 'manage-unread-help-messages.php', 'manage-replied-help-messages.php']))
            echo ''; ?>" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true"
            aria-controls="collapseUtilities">
            <i class="fa fa-envelope"></i>
            <span>Manage Help Messages</span>
        </a>
        <div id="collapseUtilities" class="collapse <?php if (in_array($current_page, ['manage-all-help-messages.php', 'manage-read-help-messages.php', 'manage-unread-help-messages.php', 'manage-replied-help-messages.php']))
            echo 'show'; ?>" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item <?php if ($current_page == 'manage-all-help-messages.php')
                    echo 'active text-warning'; ?>" href="manage-all-help-messages.php">All Messages</a>
                <a class="collapse-item <?php if ($current_page == 'manage-read-help-messages.php')
                    echo 'active text-warning'; ?>" href="manage-read-help-messages.php">Read Messages</a>
                <a class="collapse-item <?php if ($current_page == 'manage-unread-help-messages.php')
                    echo 'active text-warning'; ?>" href="manage-unread-help-messages.php">Undread Messages</a>
                <a class="collapse-item <?php if ($current_page == 'manage-replied-help-messages.php')
                    echo 'active text-warning'; ?>" href="manage-replied-help-messages.php">Replied Messages</a>
            </div>
        </div>
    </li> -->

    <!-- Divider (Optional: Adds a separator before the toggler) -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Moved to Bottom) -->
    <div class="text-center d-none d-md-inline mt-auto" style="padding: 1rem;">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->