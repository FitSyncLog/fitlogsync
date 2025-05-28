<?php include 'session-management.php'; ?>

<?php
// Check if the user was login
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Prepare the query to check permissions
$page_name = "create-new-coupon.php";
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
        <title>Create New Transaction | FiT-LOGSYNC</title>
        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
        <link href="css/sb-admin-2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">
        <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" />
        <script src="assets/js/sweetalert2.all.min.js"></script>
        <script src="assets/js/sessionExpired.js"></script>
        <!-- Add Flatpickr CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_orange.css">

        <style>
            /* Plan Card Styles */
            .plan-card {
                transition: all 0.3s ease;
                border: 2px solid transparent;
            }
            
            .plan-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            }
            
            .plan-card.border-warning {
                border-color: #ffc107;
                transform: translateY(-5px);
                box-shadow: 0 0.5rem 1rem rgba(255, 193, 7, 0.15);
            }

            /* Custom Radio Button Styles */
            .custom-control-input:checked ~ .custom-control-label::before {
                border-color: #ffc107;
                background-color: #ffc107;
            }

            /* Select2 Custom Styles */
            .select2-container--default .select2-selection--single {
                height: calc(1.5em + 0.75rem + 2px);
                padding: 0.375rem 0.75rem;
                border: 1px solid #d1d3e2;
            }
            
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 1.5;
                padding-left: 0;
            }
            
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 100%;
            }

            /* Section Transitions */
            .card {
                transition: all 0.3s ease;
            }

            #quotationCard {
                animation: slideDown 0.3s ease;
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Coupon Message Styles */
            #couponMessage.text-success {
                animation: fadeIn 0.3s ease;
            }

            #couponMessage.text-danger {
                animation: shake 0.5s ease;
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }

            /* Date Selection Styles */
            .date-selection-container {
                background-color: #fff;
                border-radius: 0.35rem;
                padding: 1.25rem;
                margin-top: 1.5rem;
                box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            }

            .flatpickr-calendar.material_orange {
                box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            }

            .date-info {
                padding: 0.75rem;
                background: #f8f9fc;
                border-radius: 0.35rem;
                margin-top: 1rem;
            }
        </style>

        <script>
        function checkSubscription() {
            const userId = document.getElementById('member_id').value;
            const startDate = document.getElementById('startDate').value;
            const planId = document.getElementById('selected_plan_id').value;
            const submitBtn = document.getElementById('submit_btn');
            const dateInput = document.getElementById('startDate');

            if (!userId || !startDate) return;

            fetch('indexes/check-subscription.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `user_id=${userId}&selected_date=${startDate}&plan_id=${planId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Active Subscription Found',
                        text: data.message,
                        confirmButtonColor: '#ffc107'
                    });
                    dateInput.value = ''; // Clear the date input
                    submitBtn.disabled = true;
                    flatpickr("#startDate").clear(); // Clear the flatpickr instance
                } else {
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to verify subscription status',
                    confirmButtonColor: '#ffc107'
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2
            $('.select2').select2();

            // Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const userId = urlParams.get('user_id');
            const memberName = urlParams.get('member_name');
            const subscriptionEnd = urlParams.get('subscription_end');

            // Set member if provided in URL
            if (userId) {
                $('#member_id').val(userId).trigger('change');
            }

            // Initialize Flatpickr date picker with subscription check
            const startDatePicker = flatpickr("#startDate", {
                enableTime: false,
                dateFormat: "Y-m-d",
                minDate: "today",
                theme: "material_orange",
                defaultDate: subscriptionEnd ? new Date(subscriptionEnd) : null,
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates[0]) {
                        updateEndDate(selectedDates[0]);
                        checkSubscription();
                    }
                }
            });

            // Add event listeners for both member selection and date selection
            const memberSelect = document.getElementById('member_id');
            
            if (memberSelect) {
                memberSelect.addEventListener('change', function() {
                    const startDate = document.getElementById('startDate').value;
                    if (startDate) {
                        checkSubscription();
                    }
                });
            }

            // Form validation
            $('form').on('submit', function(e) {
                const selectedPlanId = $('#selected_plan_id').val();
                const startDate = $('#startDate').val();
                const memberId = $('#member_id').val();

                if (!selectedPlanId || selectedPlanId === '') {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select a plan',
                        confirmButtonColor: '#ffc107'
                    });
                    return false;
                }

                if (!startDate) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select a start date',
                        confirmButtonColor: '#ffc107'
                    });
                    return false;
                }

                if (!memberId) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select a member',
                        confirmButtonColor: '#ffc107'
                    });
                    return false;
                }

                // Double check subscription before submit
                e.preventDefault();
                fetch('indexes/check-subscription.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `user_id=${memberId}&selected_date=${startDate}&plan_id=${selectedPlanId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'error') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Active Subscription Found',
                            text: data.message,
                            confirmButtonColor: '#ffc107'
                        });
                    } else {
                        // If no active subscription, submit the form
                        const form = this;
                        Swal.fire({
                            title: 'Create Transaction',
                            text: 'Are you sure you want to create this transaction?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#ffc107',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, create it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to verify subscription status',
                        confirmButtonColor: '#ffc107'
                    });
                });
            });
        });
        </script>
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
                            <h1 class="h3 mb-0 text-gray-800">Create New Transaction</h1>
                            <a class="btn btn-secondary" href="manage-payments.php">Back</a>

                        </div>


                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="text-center text-muted my-3">
                                    <i class="fas fa-solid fa-cash-register fa-10x"></i>
                                </div>

                                <form action="indexes/create-new-transaction.php" method="POST" id="transactionForm">
                                    <input type="hidden" name="newTransaction" value="1">
                                    <input type="hidden" id="selected_plan_id" name="selected_plan_id">
                                    <div class="modal-body">
                                        <!-- Member Selection Section -->
                                        <div class="card shadow mb-4">
                                            <div class="card-header py-3">
                                                <h6 class="m-0 font-weight-bold text-warning">
                                                    <i class="fas fa-user mr-2"></i>Member Information
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group mb-0">
                                                    <label for="member_id">Member's Name</label>
                                                    <select class="form-control select2" id="member_id" name="member_id" required>
                                                        <option value="">Select a member</option>
                                                        <?php
                                                        // Fetch active members with role_id = 5
                                                        $member_query = "SELECT u.user_id, u.account_number, u.lastname, u.firstname 
                                                                       FROM users u 
                                                                       INNER JOIN user_roles ur ON u.user_id = ur.user_id 
                                                                       WHERE ur.role_id = 5 AND u.status = 'Active' 
                                                                       ORDER BY u.lastname, u.firstname";
                                                        $member_result = $conn->query($member_query);
                                                        
                                                        while ($member = $member_result->fetch_assoc()) {
                                                            $display_text = $member['account_number'] . ' - ' . 
                                                                          $member['lastname'] . ', ' . $member['firstname'];
                                                            echo '<option value="' . $member['user_id'] . '">' . 
                                                                 htmlspecialchars($display_text) . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Plan Selection Section -->
                                        <div class="card shadow mb-4">
                                            <div class="card-header py-3">
                                                <h6 class="m-0 font-weight-bold text-warning">
                                                    <i class="fas fa-clipboard-list mr-2"></i>Plan Selection
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <?php
                                                    // Fetch active plans
                                                    $plans_query = "SELECT * FROM plans WHERE status = 1";
                                                    $plans_result = $conn->query($plans_query);
                                                    
                                                    while ($plan = $plans_result->fetch_assoc()): ?>
                                                        <div class="col-xl-3 col-md-6 mb-4">
                                                            <div class="card h-100 plan-card" data-plan-id="<?= $plan['plan_id'] ?>" 
                                                                 data-plan-name="<?= htmlspecialchars($plan['plan_name']) ?>"
                                                                 data-plan-price="<?= $plan['price'] ?>"
                                                                 data-plan-duration="<?= $plan['duration'] ?>"
                                                                 data-plan-description="<?= htmlspecialchars($plan['description']) ?>"
                                                                 style="cursor: pointer; transition: all 0.3s;">
                                                                <div class="card-header bg-light py-3 text-center">
                                                                    <h6 class="mb-0 font-weight-bold text-warning">
                                                                        <?= htmlspecialchars($plan['plan_name']) ?>
                                                                    </h6>
                                                                </div>
                                                                <div class="card-body text-center">
                                                                    <h2 class="mb-0">
                                                                        <sup class="h5">₱</sup><?= number_format($plan['price'], 2) ?>
                                                                    </h2>
                                                                    <p class="text-muted mb-3">per month</p>
                                                                    <div class="bg-light p-3 rounded">
                                                                        <p class="mb-1"><strong>Duration:</strong> <?= $plan['duration'] ?> month(s)</p>
                                                                        <p class="mb-0 small"><?= htmlspecialchars($plan['description']) ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endwhile; ?>
                                                </div>

                                                <!-- Add Date Selection Container -->
                                                <div class="date-selection-container">
                                                    <h6 class="font-weight-bold text-warning mb-3">
                                                        <i class="fas fa-calendar-alt mr-2"></i>Plan Schedule
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="startDate">Start Date</label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control" id="startDate" name="start_date" required>
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">
                                                                            <i class="fas fa-calendar"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>End Date</label>
                                                                <div class="date-info">
                                                                    <span id="endDateDisplay">Select a start date and plan to see the end date</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Category and Coupon Section -->
                                        <div class="card shadow mb-4">
                                            <div class="card-header py-3">
                                                <h6 class="m-0 font-weight-bold text-warning">
                                                    <i class="fas fa-tags mr-2"></i>Discounts & Coupons
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <!-- Category Selection -->
                                                    <div class="col-md-6">
                                                        <h6 class="font-weight-bold mb-3">Category</h6>
                                                        <div class="bg-light p-3 rounded">
                                                            <div class="custom-control custom-radio mb-2">
                                                                <input type="radio" id="regular" name="category" value="regular" class="custom-control-input" checked>
                                                                <label class="custom-control-label" for="regular">Regular</label>
                                                            </div>
                                                            <?php
                                                            // Fetch active discounts
                                                            $discounts_query = "SELECT * FROM discounts WHERE status = 1 AND discount_id IN (1, 2, 3)";
                                                            $discounts_result = $conn->query($discounts_query);
                                                            
                                                            while ($discount = $discounts_result->fetch_assoc()) {
                                                                $discount_label = '';
                                                                if ($discount['discount_type'] == 'percentage') {
                                                                    $discount_label = $discount['discount_value'] . '%';
                                                                } else {
                                                                    $discount_label = '₱' . number_format($discount['discount_value'], 2);
                                                                }
                                                                ?>
                                                                <div class="custom-control custom-radio mb-2">
                                                                    <input type="radio" 
                                                                           id="discount_<?= $discount['discount_id'] ?>" 
                                                                           name="category" 
                                                                           value="<?= $discount['discount_id'] ?>" 
                                                                           class="custom-control-input"
                                                                           data-discount-type="<?= $discount['discount_type'] ?>"
                                                                           data-discount-value="<?= $discount['discount_value'] ?>">
                                                                    <label class="custom-control-label" for="discount_<?= $discount['discount_id'] ?>">
                                                                        <?= htmlspecialchars($discount['discount_name']) ?> 
                                                                        <small class="text-muted">(<?= $discount_label ?>)</small>
                                                                    </label>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>

                                                    <!-- Coupon Code -->
                                                    <div class="col-md-6">
                                                        <h6 class="font-weight-bold mb-3">Coupon Code</h6>
                                                        <div class="bg-light p-3 rounded">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" id="couponCode" name="coupon_code" 
                                                                       placeholder="Enter coupon code" style="border-right: none;">
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-warning text-white" type="button" id="applyCoupon">
                                                                        <i class="fas fa-check mr-1"></i>Apply
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <small id="couponMessage" class="form-text mt-2"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quotation Calculator -->
                                        <div class="card shadow mb-4" id="quotationCard" style="display: none;">
                                            <div class="card-header py-3">
                                                <h6 class="m-0 font-weight-bold text-warning">
                                                    <i class="fas fa-calculator mr-2"></i>Plan Quotation
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="bg-light p-3 rounded mb-3">
                                                            <h5 id="selectedPlanName" class="font-weight-bold text-warning mb-2"></h5>
                                                            <p id="selectedPlanDescription" class="text-muted mb-2"></p>
                                                            <p class="mb-0"><strong>Duration:</strong> <span id="planDuration"></span> month(s)</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card bg-light">
                                                            <div class="card-body">
                                                                <h6 class="font-weight-bold text-warning mb-3">Summary</h6>
                                                                <div class="d-flex justify-content-between mb-2">
                                                                    <span>Monthly Rate:</span>
                                                                    <span>₱<span id="monthlyRate">0.00</span></span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-2">
                                                                    <span>Duration:</span>
                                                                    <span><span id="monthsDisplay">0</span> month(s)</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-2">
                                                                    <span>Subtotal:</span>
                                                                    <span>₱<span id="subtotalAmount">0.00</span></span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-2" id="discountRow" style="display: none !important;">
                                                                    <span>Discount (<span id="discountName">None</span>):</span>
                                                                    <span class="text-danger">-₱<span id="discountAmount">0.00</span></span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-2" id="couponRow" style="display: none !important;">
                                                                    <span>Coupon (<span id="couponName">None</span>):</span>
                                                                    <span class="text-danger">-₱<span id="couponAmount">0.00</span></span>
                                                                </div>
                                                                <hr>
                                                                <div class="d-flex justify-content-between">
                                                                    <strong>Total Amount:</strong>
                                                                    <strong class="text-success">₱<span id="totalAmount">0.00</span></strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer bg-light">
                                        <a class="btn btn-secondary" href="manage-payments.php">
                                            <i class="fas fa-times mr-1"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-warning text-white" id="submit_btn" name="newTransaction">
                                            <i class="fas fa-check mr-1"></i>Create Transaction
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>



        <!-- jQuery and Bootstrap -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- Add Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <!-- Rest -->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="js/sb-admin-2.min.js"></script>
        <script src="vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="js/demo/datatables-demo.js"></script>
        <!-- Add Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <script>
            $(document).ready(function() {
                // Initialize Select2
                $('.select2').select2({
                    placeholder: 'Search by account number or name...',
                    allowClear: true,
                    width: '100%',
                    // Configure search
                    matcher: function(params, data) {
                        // If there are no search terms, return all of the data
                        if ($.trim(params.term) === '') {
                            return data;
                        }

                        // Do not display the item if there is no 'text' property
                        if (typeof data.text === 'undefined') {
                            return null;
                        }

                        // Search in both account number and name
                        if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                            return data;
                        }

                        // Return `null` if the term should not be displayed
                        return null;
                    }
                });

                // Function to update end date
                function updateEndDate(startDate) {
                    if (!selectedPlan) {
                        $('#endDateDisplay').text('Select a plan first');
                        return;
                    }

                    if (!startDate) {
                        $('#endDateDisplay').text('Select a start date');
                        return;
                    }

                    // Create a new date object to avoid modifying the original
                    const endDate = new Date(startDate);
                    // Add the plan duration in months
                    endDate.setMonth(endDate.getMonth() + selectedPlan.duration);
                    // Subtract one day to get the correct end date
                    endDate.setDate(endDate.getDate() - 1);

                    // Format the date
                    const formattedEndDate = endDate.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    $('#endDateDisplay').html(`
                        <strong class="text-warning">
                            <i class="fas fa-calendar-check mr-1"></i>${formattedEndDate}
                        </strong>
                        <br>
                        <small class="text-muted">
                            (${selectedPlan.duration} month${selectedPlan.duration > 1 ? 's' : ''} from start date)
                        </small>
                    `);
                }

                // Plan Selection and Quotation Calculator
                let selectedPlan = null;
                let appliedCoupon = null;

                                // Function to calculate total with sequential discounts (discount first, then coupon)
                function calculateTotal() {
                    if (!selectedPlan) return;

                    const subtotal = selectedPlan.price * selectedPlan.duration;
                    $('#subtotalAmount').text(subtotal.toFixed(2));

                    let afterDiscount = subtotal;
                    let totalDiscount = 0;

                    // Calculate category discount first
                    const selectedCategory = $('input[name="category"]:checked');
                    if (selectedCategory.val() !== 'regular') {
                        const discountType = selectedCategory.data('discount-type');
                        const discountValue = parseFloat(selectedCategory.data('discount-value'));
                        const discountName = selectedCategory.next('label').contents().first().text().trim();

                        let categoryDiscount = 0;
                        if (discountType === 'percentage') {
                            categoryDiscount = subtotal * (discountValue / 100);
                        } else {
                            categoryDiscount = discountValue;
                        }

                        $('#discountName').text(discountName);
                        $('#discountRow').show();
                        $('#discountAmount').text(categoryDiscount.toFixed(2));
                        totalDiscount += categoryDiscount;
                        afterDiscount = subtotal - categoryDiscount;
                    } else {
                        // Reset discount values when regular is selected
                        $('#discountRow').hide();
                        $('#discountName').text('None');
                        $('#discountAmount').text('0.00');
                    }

                    // Calculate coupon discount based on the already-discounted amount
                    if (appliedCoupon) {
                        let couponDiscount = 0;
                        if (appliedCoupon.type === 'percentage') {
                            couponDiscount = afterDiscount * (appliedCoupon.value / 100);
                        } else {
                            couponDiscount = appliedCoupon.value;
                        }

                        $('#couponName').text(appliedCoupon.name);
                        $('#couponRow').show();
                        $('#couponAmount').text(couponDiscount.toFixed(2));
                        totalDiscount += couponDiscount;
                    } else {
                        $('#couponRow').hide();
                    }

                    const total = subtotal - totalDiscount;
                    $('#totalAmount').text(total.toFixed(2));
                }

                // Handle coupon code validation
                $('#applyCoupon').click(function() {
                    const couponCode = $('#couponCode').val().trim();
                    if (!couponCode) {
                        $('#couponMessage').removeClass().addClass('text-danger').text('Please enter a coupon code.');
                        return;
                    }

                    $.ajax({
                        url: 'indexes/validate-coupon.php',
                        method: 'POST',
                        data: { coupon_code: couponCode },
                        success: function(response) {
                            if (response.valid) {
                                $('#couponMessage').removeClass().addClass('text-success').text(response.message);
                                appliedCoupon = {
                                    id: response.coupon_id,
                                    name: response.coupon_name,
                                    type: response.coupon_type,
                                    value: response.coupon_value
                                };
                                if (selectedPlan) {
                                    calculateTotal();
                                }
                            } else {
                                $('#couponMessage').removeClass().addClass('text-danger').text(response.message);
                                appliedCoupon = null;
                                if (selectedPlan) {
                                    calculateTotal();
                                }
                            }
                        },
                        error: function() {
                            $('#couponMessage').removeClass().addClass('text-danger').text('Error validating coupon code.');
                            appliedCoupon = null;
                            if (selectedPlan) {
                                calculateTotal();
                            }
                        }
                    });
                });

                // Clear coupon when input changes
                $('#couponCode').on('input', function() {
                    $('#couponMessage').text('');
                    appliedCoupon = null;
                    if (selectedPlan) {
                        calculateTotal();
                    }
                });

                // Handle plan card selection
                $('.plan-card').click(function() {
                    // Remove active class from all cards
                    $('.plan-card').removeClass('border-warning');
                    
                    // Add active class to selected card
                    $(this).addClass('border-warning');

                    // Get plan details and update selectedPlan object
                    selectedPlan = {
                        id: $(this).data('plan-id'),
                        name: $(this).data('plan-name'),
                        price: parseFloat($(this).data('plan-price')),
                        duration: parseInt($(this).data('plan-duration')),
                        description: $(this).data('plan-description')
                    };

                    // Update hidden input with the selected plan ID
                    $('#selected_plan_id').val(selectedPlan.id);

                    // Show quotation card
                    $('#quotationCard').show();

                    // Update quotation details
                    $('#selectedPlanName').text(selectedPlan.name);
                    $('#selectedPlanDescription').text(selectedPlan.description);
                    $('#planDuration').text(selectedPlan.duration);
                    
                    // Update Quotation Summary
                    $('#monthlyRate').text(selectedPlan.price.toFixed(2));
                    $('#monthsDisplay').text(selectedPlan.duration);
                    
                    // Calculate total with discount
                    calculateTotal();

                    // Update end date if start date is already selected
                    const startDate = $('#startDate').val();
                    if (startDate) {
                        updateEndDate(new Date(startDate));
                    }
                });

                // Handle category change
                $('input[name="category"]').change(function() {
                    if (selectedPlan) {
                        // Get the selected category
                        const selectedCategory = $(this).val();
                        
                        // Reset discount row if 'regular' is selected
                        if (selectedCategory === 'regular') {
                            $('#discountRow').hide();
                            $('#discountName').text('None');
                            $('#discountAmount').text('0.00');
                        }
                        
                        calculateTotal();
                    }
                });
            });
        </script>
    </body>

    </html>
    <?php
} else {
    header("Location: indexes/logout.php");
    exit();
}