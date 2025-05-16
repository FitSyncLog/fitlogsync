<?php include 'session-management.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manage Unread Help Messages | FiT-LOGSYNC</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" type="image/x-icon" href="../assets/fitlogsync.ico">
    <link rel="stylesheet" href="../assets/css/sweetalert2.min.css">
    <script src="../assets/js/sweetalert2.all.min.js"></script>
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" />

    <script src="../assets/js/sessionExpired.js"></script>

    <style>
        .form-check-input[type="radio"] {
            accent-color: #F6C23E;
        }
    </style>
</head>

<body id="page-top">
    <?php
    if (isset($_GET['Success'])) {
        $message = htmlspecialchars($_GET['Success']);
        echo "<script>
                Swal.fire({
                position: 'center',
                icon: 'success',
                title: '{$message}',
                showConfirmButton: false,
                timer: 1500
                });
            </script>";
    }

    if (isset($_GET['Failed'])) {
        $message = htmlspecialchars($_GET['Failed']);
        echo "<script>
                Swal.fire({
                position: 'center',
                icon: 'error',
                title: '{$message}',
                showConfirmButton: false,
                timer: 1500
                });
            </script>";
    }
    ?>

    <div id="wrapper">
        <?php include 'layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'layout/navbar.php'; ?>
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Manage Unread Help Messages</h1>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Date and Time</th>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Email</th>
                                            <th class="text-center">Subject</th>
                                            <th class="text-center">Message</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>

                                    <?php
                                    $query = "SELECT * FROM messages WHERE status = 'Unread'";
                                    $result = mysqli_query($conn, $query);

                                    if (!$result) {
                                        die("Query Failed: " . mysqli_error($conn));
                                    }
                                    ?>

                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>

                                                <td>
                                                    <?php
                                                    $name = $row['name'];
                                                    echo htmlspecialchars(date("F j, Y", strtotime($name)));
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    echo $name = $row['name'];
                                                    ?>
                                                </td>

                                                <td class="text-center">
                                                    <?php
                                                    echo $name = $row['email'];
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    echo $name = $row['subject'];
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    echo $name = $row['message'];
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                        data-target="#viewMemberModal<?php echo $row['id']; ?>">View</button>
                                                </td>
                                            </tr>


                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script>
        document.getElementById('delete-button').addEventListener('click', function (event) {
            event.preventDefault(); // Prevent the default link behavior
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');

            Swal.fire({
                title: 'Are you sure?',
                text: `Are you sure you want to delete ${userName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `delete-member.php?user_id=${userId}`;
                }
            });
        });
    </script>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="../js/demo/datatables-demo.js"></script>
</body>

</html>