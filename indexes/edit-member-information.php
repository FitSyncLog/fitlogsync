<?php
session_start();

include "../../indexes/db_con.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $date_of_birth = $_POST['dateofbirth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phone_number = $_POST['phonenumber'];
    $email = $_POST['email'];
    $contact_person = $_POST['contact_person'];
    $contact_number = $_POST['contact_number'];
    $relationship = $_POST['relationship'];
    $medical_conditions = $_POST['medical_conditions'];
    $current_medications = $_POST['current_medications'];
    $previous_injuries = $_POST['previous_injuries'];
    $par_q_1 = $_POST['q1'];
    $par_q_2 = $_POST['q2'];
    $par_q_3 = $_POST['q3'];
    $par_q_4 = $_POST['q4'];
    $par_q_5 = $_POST['q5'];
    $par_q_6 = $_POST['q6'];
    $par_q_7 = $_POST['q7'];
    $par_q_8 = $_POST['q8'];
    $par_q_9 = $_POST['q9'];
    $par_q_10 = $_POST['q10'];
    $rules_and_policy = isset($_POST['waiver_rules']) ? 1 : 0;
    $liability_waiver = isset($_POST['waiver_liability']) ? 1 : 0;
    $cancellation_and_refund_policy = isset($_POST['waiver_cancel']) ? 1 : 0;

    // Update users table
    $query = "UPDATE users SET
              username = ?,
              firstname = ?,
              middlename = ?,
              lastname = ?,
              date_of_birth = ?,
              gender = ?,
              address = ?,
              phone_number = ?,
              email = ?
              WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssssssssi', $username, $firstname, $middlename, $lastname, $date_of_birth, $gender, $address, $phone_number, $email, $user_id);
    $stmt->execute();

    // Update emergency_contacts table
    $query = "UPDATE emergency_contacts SET
              contact_person = ?,
              contact_number = ?,
              relationship = ?
              WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssi', $contact_person, $contact_number, $relationship, $user_id);
    $stmt->execute();

    // Update medical_backgrounds table
    $query = "UPDATE medical_backgrounds SET
              medical_conditions = ?,
              current_medications = ?,
              previous_injuries = ?,
              par_q_1 = ?,
              par_q_2 = ?,
              par_q_3 = ?,
              par_q_4 = ?,
              par_q_5 = ?,
              par_q_6 = ?,
              par_q_7 = ?,
              par_q_8 = ?,
              par_q_9 = ?,
              par_q_10 = ?
              WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssssssssssssi', $medical_conditions, $current_medications, $previous_injuries, $par_q_1, $par_q_2, $par_q_3, $par_q_4, $par_q_5, $par_q_6, $par_q_7, $par_q_8, $par_q_9, $par_q_10, $user_id);
    $stmt->execute();

    // Update waivers table
    $query = "UPDATE waivers SET
              rules_and_policy = ?,
              liability_waiver = ?,
              cancellation_and_refund_policy = ?
              WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iiii', $rules_and_policy, $liability_waiver, $cancellation_and_refund_policy, $user_id);
    $stmt->execute();

    header("Location: ../edit-member.php?user_id=$user_id&Success=Member information updated successfully");
    exit();
} else {
    header("Location: ../edit-member.php?Failed=Invalid request");
    exit();
}
?>
