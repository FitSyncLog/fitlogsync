<?php include 'session-management.php'; ?>

<?php
// Check if the user is logged in
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Prepare the query to check permissions
$page_name = "manage-reports.php";
$role_id = $_SESSION['role_id'];
$query = "SELECT * FROM permissions WHERE page_name = ? AND role_id = ? AND permission = 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $page_name, $role_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manage Reports | FiT-LOGSYNC</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">
    <style>
        .report-header-img {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .date-range-btn {
            margin: 0 5px;
            transition: all 0.3s;
        }
        .date-range-btn:hover {
            transform: translateY(-2px);
        }
        .date-range-btn.active {
            background-color: #F6C23E !important;
            color: white !important;
        }
        .report-type-card {
            transition: all 0.3s;
            cursor: pointer;
        }
        .report-type-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .report-type-card.selected {
            border: 2px solid #F6C23E;
        }
        .report-icon {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #F6C23E;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include 'layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'layout/navbar.php'; ?>
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Manage Reports</h1>
                    <p class="mb-4">Generate and download various reports based on date ranges.</p>

                    <!-- Header Image -->
                    <img src="assets/reports-header.png" alt="Reports Header" class="report-header-img shadow">

                    <!-- Report Generation Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning">Generate Reports</h6>
                        </div>
                        <div class="card-body">
                            <form id="reportForm" action="indexes/generate-report.php" method="POST">
                                <!-- Report Type Selection -->
                                <div class="row mb-4">
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div class="card report-type-card h-100" data-report="sales">
                                            <div class="card-body text-center">
                                                <i class="fas fa-chart-line report-icon"></i>
                                                <h5 class="card-title">Sales Report</h5>
                                                <p class="card-text">View sales statistics and trends</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div class="card report-type-card h-100" data-report="coupons">
                                            <div class="card-body text-center">
                                                <i class="fas fa-ticket-alt report-icon"></i>
                                                <h5 class="card-title">Coupon Report</h5>
                                                <p class="card-text">Analyze coupon usage and effectiveness</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div class="card report-type-card h-100" data-report="discounts">
                                            <div class="card-body text-center">
                                                <i class="fas fa-percent report-icon"></i>
                                                <h5 class="card-title">Discount Report</h5>
                                                <p class="card-text">Track discount applications and impact</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div class="card report-type-card h-100" data-report="members">
                                            <div class="card-body text-center">
                                                <i class="fas fa-users report-icon"></i>
                                                <h5 class="card-title">Member Report</h5>
                                                <p class="card-text">View member statistics and status</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="report_type" id="selectedReportType">

                                <!-- Date Range Selection -->
                                <div class="form-group">
                                    <label class="font-weight-bold">Select Date Range</label>
                                    <div class="btn-group d-flex flex-wrap mb-3">
                                        <button type="button" class="btn btn-outline-warning date-range-btn" data-range="today">Today</button>
                                        <button type="button" class="btn btn-outline-warning date-range-btn" data-range="yesterday">Yesterday</button>
                                        <button type="button" class="btn btn-outline-warning date-range-btn" data-range="week">Last 7 Days</button>
                                        <button type="button" class="btn btn-outline-warning date-range-btn" data-range="month">Last 30 Days</button>
                                        <button type="button" class="btn btn-outline-warning date-range-btn" data-range="custom">Custom Range</button>
                                    </div>
                                    <input type="text" class="form-control" id="dateRange" name="date_range" required readonly>
                                </div>

                                <button type="submit" class="btn btn-warning" disabled id="generateBtn">
                                    <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Recent Reports Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning">Recently Generated Reports</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="recentReports" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Report Type</th>
                                            <th>Date Range</th>
                                            <th>Generated On</th>
                                            <th>Generated By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch recent reports
                                        $query = "SELECT r.*, CONCAT(u.firstname, ' ', u.lastname) as generated_by 
                                                FROM report_logs r 
                                                JOIN users u ON r.user_id = u.user_id 
                                                ORDER BY r.generated_at DESC 
                                                LIMIT 10";
                                        $result = $conn->query($query);
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . ucfirst($row['report_type']) . " Report</td>";
                                            echo "<td>" . date('M d, Y', strtotime($row['date_range_start'])) . " - " . 
                                                      date('M d, Y', strtotime($row['date_range_end'])) . "</td>";
                                            echo "<td>" . date('M d, Y H:i:s', strtotime($row['generated_at'])) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['generated_by']) . "</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize date range picker
            $('#dateRange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                updateGenerateButton();
            });

            $('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                updateGenerateButton();
            });

            // Report type selection
            $('.report-type-card').click(function() {
                $('.report-type-card').removeClass('selected');
                $(this).addClass('selected');
                $('#selectedReportType').val($(this).data('report'));
                updateGenerateButton();
            });

            // Date range quick buttons
            $('.date-range-btn').click(function() {
                $('.date-range-btn').removeClass('active');
                $(this).addClass('active');
                
                const range = $(this).data('range');
                let start, end;

                switch(range) {
                    case 'today':
                        start = moment();
                        end = moment();
                        break;
                    case 'yesterday':
                        start = moment().subtract(1, 'days');
                        end = moment().subtract(1, 'days');
                        break;
                    case 'week':
                        start = moment().subtract(6, 'days');
                        end = moment();
                        break;
                    case 'month':
                        start = moment().subtract(29, 'days');
                        end = moment();
                        break;
                    case 'custom':
                        $('#dateRange').data('daterangepicker').show();
                        return;
                }

                $('#dateRange').data('daterangepicker').setStartDate(start);
                $('#dateRange').data('daterangepicker').setEndDate(end);
                $('#dateRange').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
                updateGenerateButton();
            });

            // Initialize DataTable for recent reports
            $('#recentReports').DataTable({
                order: [[3, 'desc']], // Sort by Generated On column by default
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
            });

            // Form submission
            $('#reportForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response) {
                        const reportType = formData.get('report_type');
                        const timestamp = moment().format('YYYYMMDD_HHmmss');
                        const fileName = `${reportType}_report_${timestamp}.pdf`;
                        
                        const blob = new Blob([response], { 
                            type: 'application/pdf'
                        });
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = fileName;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);

                        // Reload the page to update the recent reports table
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        alert('Error generating report: ' + error);
                    }
                });
            });

            function updateGenerateButton() {
                const reportType = $('#selectedReportType').val();
                const dateRange = $('#dateRange').val();
                $('#generateBtn').prop('disabled', !reportType || !dateRange);
            }
        });
    </script>
</body>
</html>
<?php
} else {
    header("Location: dashboard.php?AccessDenied=You do not have permission to access this page.");
    exit();
}
?> 