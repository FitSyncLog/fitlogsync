<?php
session_start();
require "db_con.php";

if (isset($_POST['register'])) {

    function validate($data)
    {
        $data = trim($data ?? ''); // Use null coalescing operator to avoid null values
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $username = validate($_POST['username'] ?? '');
    $lastname = validate($_POST['lastname'] ?? '');
    $firstname = validate($_POST['firstname'] ?? '');
    $middlename = validate($_POST['middlename'] ?? '');
    $dateofbrirth = validate($_POST['dateofbrirth'] ?? '');
    $gender = validate($_POST['gender'] ?? '');
    $phonenumber = validate($_POST['phonenumber'] ?? '');
    $email = validate($_POST['email'] ?? '');
    $password = validate($_POST['password'] ?? '');
    $confirm_password = validate($_POST['confirm_password'] ?? '');
    $medical_conditions = validate($_POST['medical_conditions'] ?? '');
    $current_medications = validate($_POST['current_medications'] ?? '');
    $previous_injuries = validate($_POST['previous_injuries'] ?? '');
    $q1 = validate($_POST['q1'] ?? '');
    $q2 = validate($_POST['q2'] ?? '');
    $q3 = validate($_POST['q3'] ?? '');
    $q4 = validate($_POST['q4'] ?? '');
    $q5 = validate($_POST['q5'] ?? '');
    $q6 = validate($_POST['q6'] ?? '');
    $q7 = validate($_POST['q7'] ?? '');
    $q8 = validate($_POST['q8'] ?? '');
    $q9 = validate($_POST['q9'] ?? '');
    $q10 = validate($_POST['q10'] ?? '');
    $waiver_rules = validate($_POST['waiver_rules'] ?? '');
    $waiver_liability = validate($_POST['waiver_liability'] ?? '');
    $waiver_cancel = validate($_POST['waiver_cancel'] ?? '');

    $user_data = 'username=' . $username .
        '&lastname=' . $lastname .
        '&firstname=' . $firstname .
        '&middlename=' . $middlename .
        '&dateofbrirth=' . $dateofbrirth .
        '&gender=' . $gender .
        '&phonenumber=' . $phonenumber .
        '&email=' . $email .
        '&password=' . $password .
        '&confirm_password=' . $confirm_password .
        '&medical_conditions=' . $medical_conditions .
        '&current_medications=' . $current_medications .
        '&previous_injuries=' . $previous_injuries .
        '&q1=' . $q1 .
        '&q2=' . $q2 .
        '&q3=' . $q3 .
        '&q4=' . $q4 .
        '&q5=' . $q5 .
        '&q6=' . $q6 .
        '&q7=' . $q7 .
        '&q8=' . $q8 .
        '&q9=' . $q9 .
        '&q10=' . $q10 .
        '&waiver_rules=' . $waiver_rules .
        '&waiver_liability=' . $waiver_liability .
        '&waiver_cancel=' . $waiver_cancel;

    echo "Username: $username<br>";
    echo "Lastname: $lastname<br>";
    echo "Firstname: $firstname<br>";
    echo "Middlename: $middlename<br>";
    echo "Date of Birth: $dateofbrirth<br>";
    echo "Gender: $gender<br>";
    echo "Phone Number: $phonenumber<br>";
    echo "Email: $email<br>";
    echo "Password: $password<br>";
    echo "Confirm Password: $confirm_password<br>";
    echo "Medical Conditions: $medical_conditions<br>";
    echo "Current Medication: $current_medications<br>";
    echo "Previous Injuries: $previous_injuries<br>";
    echo "Q1: $q1<br>";
    echo "Q2: $q2<br>";
    echo "Q3: $q3<br>";
    echo "Q4: $q4<br>";
    echo "Q5: $q5<br>";
    echo "Q6: $q6<br>";
    echo "Q7: $q7<br>";
    echo "Q8: $q8<br>";
    echo "Q9: $q9<br>";
    echo "Q10: $q10<br>";
    echo "Rules and Policy: $waiver_rules<br>";
    echo "Liability Waiver: $waiver_liability<br>";
    echo "Cancellation and Refund Policy: $waiver_cancel<br>";


}
?>