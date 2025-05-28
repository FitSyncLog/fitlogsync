<?php
// Fetch member details
$details_query = "SELECT u.*, 
                 CONCAT(enrolled.firstname, ' ', enrolled.lastname) as enrolled_by_name
                 FROM users u 
                 LEFT JOIN users enrolled ON u.enrolled_by = enrolled.user_id
                 WHERE u.user_id = ?";
$details_stmt = $conn->prepare($details_query);
$details_stmt->bind_param("i", $user_id);
$details_stmt->execute();
$details = $details_stmt->get_result()->fetch_assoc();

// Format account number
$formatted_account = substr($details['account_number'], 0, 4) . '-' .
                    substr($details['account_number'], 4, 4) . '-' .
                    substr($details['account_number'], 8, 4) . '-' .
                    substr($details['account_number'], 12, 4);

// Get subscription status
// $subscription_query = "SELECT s.*, p.plan_name 
//                       FROM subscriptions s 
//                       LEFT JOIN plans p ON s.plan_id = p.plan_id
//                       WHERE s.user_id = ? 
//                       ORDER BY s.expiration_date DESC 
//                       LIMIT 1";
// $subscription_stmt = $conn->prepare($subscription_query);
// $subscription_stmt->bind_param("i", $user_id);
// $subscription_stmt->execute();
// $subscription = $subscription_stmt->get_result()->fetch_assoc();

// $subscription_status = "No Active Subscription";
// if ($subscription) {
//     $today = new DateTime();
//     $expiration = new DateTime($subscription['expiration_date']);
//     if ($today <= $expiration) {
//         $subscription_status = "Active - " . $subscription['plan_name'] . " (Until " . date('F d, Y', strtotime($subscription['expiration_date'])) . ")";
//     } else {
//         $subscription_status = "Expired - Last Plan: " . $subscription['plan_name'];
//     }
// }

// Fetch emergency contact details
$emergency_contact_query = "SELECT * FROM emergency_contacts WHERE user_id = ?";
$emergency_contact_stmt = $conn->prepare($emergency_contact_query);
$emergency_contact_stmt->bind_param("i", $user_id);
$emergency_contact_stmt->execute();
$emergency_contact = $emergency_contact_stmt->get_result()->fetch_assoc();

// Fetch medical background
$medical_backgrounds_query = "SELECT * FROM medical_backgrounds WHERE user_id = ?";
$medical_backgrounds_stmt = $conn->prepare($medical_backgrounds_query);
$medical_backgrounds_stmt->bind_param("i", $user_id);
$medical_backgrounds_stmt->execute();
$medical_backgrounds = $medical_backgrounds_stmt->get_result()->fetch_assoc();

// Fetch waiver information
$waivers_query = "SELECT * FROM waivers WHERE user_id = ?";
$waivers_stmt = $conn->prepare($waivers_query);
$waivers_stmt->bind_param("i", $user_id);
$waivers_stmt->execute();
$waivers = $waivers_stmt->get_result()->fetch_assoc();
?>

<div class="accordion" id="memberDetailsAccordion">
    <!-- Personal Information -->
    <div class="card">
        <div class="card-header bg-white" id="personalInfoHeading">
            <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left text-warning" type="button" data-toggle="collapse" data-target="#personalInfo" aria-expanded="true" aria-controls="personalInfo">
                    <i class="fas fa-user mr-2"></i>Personal Information
                </button>
            </h2>
        </div>
        <div id="personalInfo" class="collapse show" aria-labelledby="personalInfoHeading" data-parent="#memberDetailsAccordion">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Account Number:</strong> <?php echo $formatted_account; ?></p>
                        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($details['lastname']); ?></p>
                        <p><strong>First Name:</strong> <?php echo htmlspecialchars($details['firstname']); ?></p>
                        <p><strong>Middle Name:</strong> <?php echo htmlspecialchars($details['middlename']); ?></p>
                        <p><strong>Gender:</strong> <?php echo htmlspecialchars($details['gender']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($details['address']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Date of Birth:</strong> <?php echo date("F j, Y", strtotime($details['date_of_birth'])); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($details['status']); ?></p>
                        <p><strong>Registration Date:</strong> <?php echo date("F j, Y", strtotime($details['registration_date'])); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($details['email']); ?></p>
                        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($details['phone_number']); ?></p>
                        <p><strong>Registered By:</strong> <?php echo htmlspecialchars($details['enrolled_by_name']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact of Emergency -->
    <div class="card">
        <div class="card-header bg-white" id="emergencyContactHeading">
            <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left collapsed text-warning" type="button" data-toggle="collapse" data-target="#emergencyContact" aria-expanded="false" aria-controls="emergencyContact">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Contact of Emergency
                </button>
            </h2>
        </div>
        <div id="emergencyContact" class="collapse" aria-labelledby="emergencyContactHeading" data-parent="#memberDetailsAccordion">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>Contact Person:</strong> <?php echo htmlspecialchars($emergency_contact['contact_person'] ?? 'N/A'); ?></p>
                        <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($emergency_contact['contact_number'] ?? 'N/A'); ?></p>
                        <p><strong>Relationship:</strong> <?php echo htmlspecialchars($emergency_contact['relationship'] ?? 'N/A'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Background -->
    <div class="card">
        <div class="card-header bg-white" id="medicalBackgroundHeading">
            <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left collapsed text-warning" type="button" data-toggle="collapse" data-target="#medicalBackground" aria-expanded="false" aria-controls="medicalBackground">
                    <i class="fas fa-notes-medical mr-2"></i>Medical Background
                </button>
            </h2>
        </div>
        <div id="medicalBackground" class="collapse" aria-labelledby="medicalBackgroundHeading" data-parent="#memberDetailsAccordion">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>Medical Conditions:</strong> <?php echo htmlspecialchars($medical_backgrounds['medical_conditions'] ?? 'None'); ?></p>
                        <p><strong>Current Medications:</strong> <?php echo htmlspecialchars($medical_backgrounds['current_medications'] ?? 'None'); ?></p>
                        <p><strong>Previous Injuries:</strong> <?php echo htmlspecialchars($medical_backgrounds['previous_injuries'] ?? 'None'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Physical Activity Readiness Questions (PAR-Q) -->
    <div class="card">
        <div class="card-header bg-white" id="parqHeading">
            <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left collapsed text-warning" type="button" data-toggle="collapse" data-target="#parq" aria-expanded="false" aria-controls="parq">
                    <i class="fas fa-running mr-2"></i>Physical Activity Readiness Questions (PAR-Q)
                </button>
            </h2>
        </div>
        <div id="parq" class="collapse" aria-labelledby="parqHeading" data-parent="#memberDetailsAccordion">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        $par_q_questions = [
                            'par_q_1' => 'Has your doctor ever said that you have a heart condition and that you should only do physical activity recommended by a doctor?',
                            'par_q_2' => 'Do you feel pain in your chest when you perform physical activity?',
                            'par_q_3' => 'In the past month, have you had chest pain when you were not doing physical activity?',
                            'par_q_4' => 'Do you lose your balance because of dizziness or do you ever lose consciousness?',
                            'par_q_5' => 'Do you have a bone or joint problem that could be worsened by a change in your physical activity?',
                            'par_q_6' => 'Is your doctor currently prescribing any medication for your blood pressure or heart condition?',
                            'par_q_7' => 'Do you have any chronic medical conditions that may affect your ability to exercise safely?',
                            'par_q_8' => 'Are you pregnant or have you given birth in the last 6 months?',
                            'par_q_9' => 'Do you have any recent injuries or surgeries that may limit your physical activity?',
                            'par_q_10' => 'Do you know of any other reason why you should not do physical activity?'
                        ];

                        foreach ($par_q_questions as $field => $question) {
                            echo "<div class='col-md-12'>";
                            echo "<strong>Q: </strong>" . htmlspecialchars($question);
                            echo "<p><strong>Answer: </strong>" . htmlspecialchars($medical_backgrounds[$field] ?? 'N/A') . "</p>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Waiver and Agreements -->
    <div class="card">
        <div class="card-header bg-white" id="waiverHeading">
            <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left collapsed text-warning" type="button" data-toggle="collapse" data-target="#waiver" aria-expanded="false" aria-controls="waiver">
                    <i class="fas fa-file-signature mr-2"></i>Waiver and Agreements
                </button>
            </h2>
        </div>
        <div id="waiver" class="collapse" aria-labelledby="waiverHeading" data-parent="#memberDetailsAccordion">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <label style="pointer-events: none; cursor: default;">
                            <input type="checkbox" <?php echo ($waivers['rules_and_policy'] == '1') ? 'checked' : ''; ?> style="accent-color: #F6C23E; pointer-events: none; opacity: 1;">
                            <strong>Agree to the Rules and Policy</strong>
                        </label>
                    </div>
                    <div class="col-md-12">
                        <label style="pointer-events: none; cursor: default;">
                            <input type="checkbox" <?php echo ($waivers['liability_waiver'] == '1') ? 'checked' : ''; ?> style="accent-color: #F6C23E; pointer-events: none; opacity: 1;">
                            <strong>Agree to the Liability Waiver</strong>
                        </label>
                    </div>
                    <div class="col-md-12">
                        <label style="pointer-events: none; cursor: default;">
                            <input type="checkbox" <?php echo ($waivers['cancellation_and_refund_policy'] == '1') ? 'checked' : ''; ?> style="accent-color: #F6C23E; pointer-events: none; opacity: 1;">
                            <strong>Agree to the Cancellation and Refund Policy</strong>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 