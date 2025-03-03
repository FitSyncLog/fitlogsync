<?php include 'session-management.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manage All Help Messages | FiT-LOGSYNC</title>
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

        .table {
            border: none;
        }

        .table th,
        .table td {
            border: none;
        }

        .bold-text {
            font-weight: bold;
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
        <?php include 'layout/super-admin-sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'layout/super-admin-navbar.php'; ?>
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Manage All Help Messages</h1>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center col-3">Name</th>
                                            <th class="text-center col-5">Message</th>
                                            <th class="text-center col-2">Date and Time</th>
                                            <th class="text-center col-2">Status</th>
                                            <th class="text-center col-2">Action</th>
                                        </tr>
                                    </thead>

                                    <?php
                                    date_default_timezone_set("Asia/Manila");
                                    $query = "SELECT * FROM messages ORDER BY date_and_time DESC";
                                    $result = mysqli_query($conn, $query);

                                    if (!$result) {
                                        die("Query Failed: " . mysqli_error($conn));
                                    }
                                    ?>

                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)):
                                            $status = $row['status'];
                                            $boldClass = $status == 'Unread' ? 'bold-text' : '';
                                            ?>
                                            <tr id="row-<?php echo $row['id']; ?>">
                                                <td class="<?php echo $boldClass; ?>"
                                                    style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    <?php echo htmlspecialchars($row['name']); ?>
                                                </td>
                                                <td class="text-left <?php echo $boldClass; ?>"
                                                    style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    <?php
                                                    echo htmlspecialchars($row['subject']);
                                                    echo " - ";
                                                    echo htmlspecialchars($row['message']);
                                                    ?>
                                                </td>
                                                <td class="<?php echo $boldClass; ?>">
                                                    <?php
                                                    $currentDate = date("Y-m-d");
                                                    $currentYear = date("Y");

                                                    $rowDate = date("Y-m-d", strtotime($row['date_and_time']));
                                                    $rowYear = date("Y", strtotime($row['date_and_time']));
                                                    $rowMonthDay = date("F j", strtotime($row['date_and_time']));
                                                    $rowTime = date("g:i A", strtotime($row['date_and_time']));

                                                    if ($rowDate === $currentDate) {
                                                        // If the date is today, show only the time
                                                        echo $rowTime;
                                                    } elseif ($rowYear === $currentYear) {
                                                        // If the date is in the current year but not today, show Month and Day
                                                        echo $rowMonthDay;
                                                    } else {
                                                        // If the date is not in the current year, show Month, Day, and Year
                                                        echo $rowMonthDay . ", " . $rowYear;
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    $status = htmlspecialchars($row['status']);
                                                    $badgeClass = '';

                                                    switch ($status) {
                                                        case 'Read':
                                                            $badgeClass = 'badge-warning';
                                                            break;
                                                        case 'Unread':
                                                            $badgeClass = 'badge-danger';
                                                            break;
                                                        case 'Replied':
                                                            $badgeClass = 'badge-success';
                                                            break;
                                                        default:
                                                            $badgeClass = 'badge-light';
                                                    }
                                                    ?>
                                                    <span
                                                        class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                        data-target="#viewMessageModal<?php echo $row['id']; ?>">View</button>
                                                </td>
                                            </tr>

                                            <!-- View Message Modal -->
                                            <div class="modal fade" id="viewMessageModal<?php echo $row['id']; ?>"
                                                tabindex="-1" role="dialog"
                                                aria-labelledby="viewMessageModalLabel<?php echo $row['id']; ?>"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div
                                                            class="modal-header bg-warning text-white d-flex justify-content-between align-items-center">
                                                            <h5 class="modal-title"
                                                                id="viewMessageModalLabel<?php echo $row['id']; ?>">
                                                                Message Details
                                                            </h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Message Information -->
                                                            <div class="section">
                                                                <div><strong>From:</strong>
                                                                    <?php echo htmlspecialchars($row['name']); ?></div>
                                                                <div><strong>Email:</strong>
                                                                    <?php echo htmlspecialchars($row['email']); ?></div>
                                                                <div><strong>Date and Time:</strong>
                                                                    <?php echo date("F j, Y g:i A", strtotime($row['date_and_time'])); ?>
                                                                </div>
                                                                <hr>
                                                                <p><strong>Subject:</strong>
                                                                    <?php echo htmlspecialchars($row['subject']); ?></p>
                                                                <?php echo htmlspecialchars($row['message']); ?>
                                                            </div>
                                                            <?php if ($status === 'Replied'): ?>
                                                                <hr>
                                                                <div><strong>Respond By:</strong>
                                                                    <?php echo htmlspecialchars($row['replied_by']); ?></div>
                                                                <div><strong>Respond Date and Time:</strong>
                                                                    <?php echo date("F j, Y g:i A", strtotime($row['reply_date'])); ?>
                                                                </div>
                                                                <br>
                                                                <?php echo htmlspecialchars($row['message_reply']); ?>
                                                            <?php endif; ?>
                                                            <div class="modal-footer"
                                                                style="display: flex; justify-content: center; gap: 10px;">
                                                                <?php if ($status !== 'Replied'): ?>
                                                                    <button type="button" class="btn btn-warning reply-button"
                                                                        data-toggle="modal"
                                                                        data-target="#replyMessageModal<?php echo $row['id']; ?>"
                                                                        data-dismiss="modal">Reply</button>
                                                                <?php endif; ?>
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <!-- Reply Message Modal -->
                                                <div class="modal fade" id="replyMessageModal<?php echo $row['id']; ?>"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="replyMessageModalLabel<?php echo $row['id']; ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg"
                                                        role="document">
                                                        <div class="modal-content">
                                                            <div
                                                                class="modal-header bg-warning text-white d-flex justify-content-between align-items-center">
                                                                <h5 class="modal-title"
                                                                    id="replyMessageModalLabel<?php echo $row['id']; ?>">
                                                                    Reply Message
                                                                </h5>
                                                            </div>

                                                            <div class="modal-body">
                                                                <!-- Message Information -->
                                                                <form action="indexes/reply-message.php" method="POST">
                                                                    <div class="section">
                                                                        <div><strong>From:</strong>
                                                                            <?php echo htmlspecialchars($row['name']); ?>
                                                                        </div>
                                                                        <div><strong>Email:</strong>
                                                                            <?php echo htmlspecialchars($row['email']); ?>
                                                                        </div>
                                                                        <div><strong>Date and Time:</strong>
                                                                            <?php echo date("F j, Y g:i A", strtotime($row['date_and_time'])); ?>
                                                                        </div>

                                                                        <hr>

                                                                        <div><strong>Subject:</strong>
                                                                            <?php echo htmlspecialchars($row['subject']); ?>
                                                                        </div>
                                                                        <div>
                                                                            <?php echo htmlspecialchars($row['message']); ?>
                                                                        </div>
                                                                    </div>
                                                                    <hr>

                                                                    <input type="hidden" name="message_id"
                                                                        value="<?php echo $row['id']; ?>">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="replyTextarea<?php echo $row['id']; ?>">Reply:</label>
                                                                        <textarea class="form-control"
                                                                            id="replyTextarea<?php echo $row['id']; ?>"
                                                                            name="reply_message" rows="5"></textarea>
                                                                    </div>
                                                                    <div class="modal-footer"
                                                                        style="display: flex; justify-content: center; gap: 10px;">
                                                                        <button type="submit" class="btn btn-warning"
                                                                            name="replyButton">Send</button>
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </form>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

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
        document.addEventListener('DOMContentLoaded', function () {
            // Add event listener to all view buttons
            document.querySelectorAll('[data-target^="#viewMessageModal"]').forEach(button => {
                button.addEventListener('click', function () {
                    const messageId = this.getAttribute('data-target').replace('#viewMessageModal', '');

                    // Perform AJAX request to update status
                    fetch(`update-message-status.php?id=${messageId}`, {
                        method: 'GET', // Use GET since the ID is in the URL
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update the UI to reflect the status change
                                const row = document.querySelector(`#row-${messageId}`);
                                if (row) {
                                    // Remove bold class from all cells in the row
                                    row.querySelectorAll('td').forEach(td => {
                                        td.classList.remove('bold-text');
                                    });

                                    // Update the badge class
                                    const badge = row.querySelector('.badge');
                                    if (badge) {
                                        badge.classList.remove('badge-danger');
                                        badge.classList.add('badge-warning');
                                        badge.textContent = 'Read';
                                    }
                                }
                            } else {
                                if (data.error === 'Status is not Unread') {
                                    console.log('Status is already Read or Replied');
                                } else {
                                    console.error('Failed to update status:', data.error);
                                }
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
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