<?php
include "indexes/db_con.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register | Fit-LOGSYNC</title>

    <link rel="stylesheet" type="text/css" href="bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <!-- jQuery and jQuery UI -->
    <link rel="stylesheet" href="assets/css/jquery-ui.css">
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/jquery-ui.js"></script>


    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">
    <style>
        .form-check-input:checked {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }
    </style>


</head>

<body>

    <?php include 'layout/index_header.php'; ?>

    <section class="hero section light-background d-flex justify-content-center align-items-center"
        style="min-height: 100vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-xl-12">
                    <div class="card border-0 rounded-4">
                        <div class="card-body p-4">
                            <h3 class="mb-4 text-center"><strong>Registration Form</strong></h3>
                            <form action="#" method="POST">
                                <!-- Personal Information -->
                                <h5 class="text-warning text-center"><strong>Personal Information</strong></h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="username" id="username"
                                                placeholder="Username" required>
                                            <label for="username">Username</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="lastname" id="lastname"
                                                placeholder="Last Name" required>
                                            <label for="lastname">Last Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="firstname" id="firstname"
                                                placeholder="First Name" required>
                                            <label for="firstname">First Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="middlename" id="middlename"
                                                placeholder="Middle Name">
                                            <label for="middlename">Middle Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="date" class="form-control" name="dob" id="dob"
                                                placeholder="Date of Birth" required>
                                            <label for="dob">Date of Birth</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" name="gender" id="gender" required>
                                                <option value="" disabled selected>Select Gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="prefer_not_to_say">Prefer not to say</option>
                                            </select>
                                            <label for="gender">Gender</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="phone" id="phone"
                                                placeholder="Phone Number" required>
                                            <label for="phone">Phone Number</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" name="email" id="email"
                                                placeholder="Email Address" required>
                                            <label for="email">Email Address</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="password" class="form-control" name="password" id="password"
                                                placeholder="Password" required>
                                            <label for="password">Password</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="password" class="form-control" name="confirm_password"
                                                id="confirm_password" placeholder="Retype Password" required>
                                            <label for="confirm_password">Retype Password</label>
                                        </div>
                                    </div>
                                </div>


                                <hr class="my-4">

                                <h5 class="text-warning text-center"><strong>Medical Background</strong></h5>

                                <div class="form-floating mb-3">
                                    <input id="medical_conditions" type="text" class="form-control"
                                        placeholder="Existing Medical Conditions">
                                    <label for="medical_conditions">Existing Medical Conditions</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <textarea class="form-control" name="current_medications" id="current_medications"
                                        placeholder="Current Medications" style="height: 100px"></textarea>
                                    <label for="current_medications">Current Medications</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <textarea class="form-control" name="previous_injuries" id="previous_injuries"
                                        placeholder="Previous Injuries" style="height: 100px"></textarea>
                                    <label for="previous_injuries">Previous Injuries</label>
                                </div>





                                <hr class="my-4">

                                <!-- PAR-Q -->
                                <h5 class="text-warning text-center"><strong>Physical Activity Readiness Questions
                                        (PAR-Q)</strong></h5>
                                <div class="mb-3">
                                    <ul class="list-group">
                                        <!-- Example Question -->
                                        <li class="list-group-item">
                                            <strong>Q1:</strong> Has your doctor ever said that you have a heart
                                            condition and that you should only do physical activity recommended by a
                                            doctor?
                                            <div class="mt-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q1" id="q1-yes"
                                                        value="yes" required>
                                                    <label class="form-check-label text-success"
                                                        for="q2-yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q1" id="q1-no"
                                                        value="no" required>
                                                    <label class="form-check-label" for="q1-no">No</label>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Q2:</strong> Do you feel pain in your chest when you perform
                                            physical activity?
                                            <div class="mt-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q2" id="q2-yes"
                                                        value="yes" required>
                                                    <label class="form-check-label text-success"
                                                        for="q2-yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q2" id="q2-no"
                                                        value="no" required>
                                                    <label class="form-check-label" for="q2-no">No</label>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Q3:</strong> In the past month, have you had chest pain when you
                                            were not doing physical activity?
                                            <div class="mt-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q3" id="q3-yes"
                                                        value="yes" required>
                                                    <label class="form-check-label text-success"
                                                        for="q3-yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q3" id="q3-no"
                                                        value="no" required>
                                                    <label class="form-check-label" for="q3-no">No</label>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Q4:</strong> Do you lose your balance because of dizziness or do you
                                            ever lose consciousness?
                                            <div class="mt-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q4" id="q4-yes"
                                                        value="yes" required>
                                                    <label class="form-check-label text-success"
                                                        for="q4-yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q4" id="q4-no"
                                                        value="no" required>
                                                    <label class="form-check-label" for="q4-no">No</label>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Q5:</strong> Do you have a bone or joint problem that could be
                                            worsened by a change in your physical activity?
                                            <div class="mt-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q5" id="q5-yes"
                                                        value="yes" required>
                                                    <label class="form-check-label text-success"
                                                        for="q5-yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q5" id="q5-no"
                                                        value="no" required>
                                                    <label class="form-check-label" for="q5-no">No</label>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Q6:</strong> Is your doctor currently prescribing any medication for
                                            your blood pressure or heart condition?
                                            <div class="mt-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q6" id="q6-yes"
                                                        value="yes" required>
                                                    <label class="form-check-label text-success"
                                                        for="q6-yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q6" id="q6-no"
                                                        value="no" required>
                                                    <label class="form-check-label" for="q6-no">No</label>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Q7:</strong> Do you have any chronic medical conditions that may
                                            affect your ability to exercise safely?
                                            <div class="mt-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q7" id="q7-yes"
                                                        value="yes" required>
                                                    <label class="form-check-label text-success"
                                                        for="q7-yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q7" id="q7-no"
                                                        value="no" required>
                                                    <label class="form-check-label" for="q7-no">No</label>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Q8:</strong> Are you pregnant or have you given birth in the last 6
                                            months?
                                            <div class="mt-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q8" id="q8-yes"
                                                        value="yes" required>
                                                    <label class="form-check-label text-success"
                                                        for="q8-yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q8" id="q8-no"
                                                        value="no" required>
                                                    <label class="form-check-label" for="q8-no">No</label>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Q9:</strong> Do you have any recent injuries or surgeries that may
                                            limit your physical activity?
                                            <div class="mt-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q9" id="q9-yes"
                                                        value="yes" required>
                                                    <label class="form-check-label text-success"
                                                        for="q9-yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q9" id="q9-no"
                                                        value="no" required>
                                                    <label class="form-check-label" for="q9-no">No</label>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Q10:</strong> Do you know of any other reason why you should not do
                                            physical activity?
                                            <div class="mt-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q10" id="q10-yes"
                                                        value="yes" required>
                                                    <label class="form-check-label text-success"
                                                        for="q10-yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="q10" id="q10-no"
                                                        value="no" required>
                                                    <label class="form-check-label" for="q10-no">No</label>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                                <hr class="my-4">

                                <!-- Waiver -->
                                <h5 class="text-warning text-center"><strong>Waiver</strong></h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="waiver_rules" required>
                                    <label class="form-check-label">
                                        I agree to the <a href="rule_and_policy.php" target="_blank">Rules and
                                            Policy</a>.
                                    </label>

                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="waiver_liability" required>
                                    <label class="form-check-label">
                                        I agree to the <a href="liability_waiver.php" target="_blank">Liability
                                            Waiver</a>.
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="waiver_cancel" required>
                                    <label class="form-check-label">
                                        I agree to the <a href="cancellation_and_refund_policy.php"
                                            target="_blank">Cancellation and
                                            Refund Policy</a>.
                                    </label>
                                </div>
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-warning btn-lg">Register</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'layout/footer.php'; ?>
    <!-- Preloader -->
    <div id="preloader">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>



</body>

<script>
    $(function () {
        // Build the availableConditions array from PHP
        var availableConditions = [
            <?php
            $conditions = [];
            while ($row = $result->fetch_assoc()) {
                $conditions[] = '"' . $row['medical_condition'] . '"';
            }
            echo implode(',', $conditions);
            ?>
        ];

        // Helper functions for handling input
        function split(val) {
            return val.split(/,\s*/);
        }

        function extractLast(term) {
            return split(term).pop();
        }

        // Initialize autocomplete
        $("#medical_conditions")
            .on("keydown", function (event) {
                if (event.keyCode === $.ui.keyCode.TAB &&
                    $(this).autocomplete("instance").menu.active) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                minLength: 1,
                source: function (request, response) {
                    response($.ui.autocomplete.filter(
                        availableConditions, extractLast(request.term)));
                },
                focus: function () {
                    return false;
                },
                select: function (event, ui) {
                    var terms = split(this.value);
                    terms.pop();
                    terms.push(ui.item.value);
                    terms.push("");
                    this.value = terms.join(", ");
                    return false;
                }
            });
    });
</script>



</html>