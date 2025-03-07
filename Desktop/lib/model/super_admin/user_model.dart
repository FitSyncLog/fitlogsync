class User {
  final int? id;
  final String username;
  final String firstname;
  final String middlename;
  final String lastname;
  final String dateOfBirth;
  final String gender;
  final String phoneNumber;
  final String email;
  final String address;
  final String accountNumber;
  final String status;
  final String enrolledBy;
  final String subscriptionStatus;
  final String registrationDate;
  final List<String> roles;
  final String medicalConditions;
  final String currentMedications;
  final String previousInjuries;
  final String q1;
  final String q2;
  final String q3;
  final String q4;
  final String q5;
  final String q6;
  final String q7;
  final String q8;
  final String q9;
  final String q10;
  final String contactPerson;
  final String contactNumber;
  final String relationship;
  final bool rulesAndPolicy;
  final bool liabilityWaiver;
  final bool cancellationAndRefundPolicy;

  User({
    required this.id,
    required this.username,
    required this.firstname,
    required this.middlename,
    required this.lastname,
    required this.dateOfBirth,
    required this.gender,
    required this.phoneNumber,
    required this.email,
    required this.address,
    required this.accountNumber,
    required this.status,
    required this.enrolledBy,
    required this.subscriptionStatus,
    required this.registrationDate,
    required this.roles,
    this.medicalConditions = '',
    this.currentMedications = '',
    this.previousInjuries = '',
    this.q1 = 'No',
    this.q2 = 'No',
    this.q3 = 'No',
    this.q4 = 'No',
    this.q5 = 'No',
    this.q6 = 'No',
    this.q7 = 'No',
    this.q8 = 'No',
    this.q9 = 'No',
    this.q10 = 'No',
    this.contactPerson = '',
    this.contactNumber = '',
    this.relationship = '',
    this.rulesAndPolicy = false,
    this.liabilityWaiver = false,
    this.cancellationAndRefundPolicy = false,
  });

  // Factory method to create a User instance from a JSON map
  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'] != null ? int.tryParse(json['id'].toString()) : null,
      username: json['username'] ?? '',
      firstname: json['firstname'] ?? '',
      middlename: json['middlename'] ?? '',
      lastname: json['lastname'] ?? '',
      dateOfBirth: json['date_of_birth'] ?? '',
      gender: json['gender'] ?? '',
      phoneNumber: json['phone_number'] ?? '',
      email: json['email'] ?? '',
      address: json['address'] ?? '',
      accountNumber: json['account_number'] ?? '',
      status: json['status'] ?? '',
      enrolledBy: json['enrolled_by'] ?? '',
      subscriptionStatus: json['subscription_status'] ?? 'Unknown',
      registrationDate: json['registration_date'] ?? '',
      roles:
          json['roles'] != null && json['roles'] is String
              ? (json['roles'] as String).split(', ')
              : [],
      medicalConditions: json['medical_conditions'] ?? '',
      currentMedications: json['current_medications'] ?? '',
      previousInjuries: json['previous_injuries'] ?? '',
      q1: json['par_q_1'] ?? 'No',
      q2: json['par_q_2'] ?? 'No',
      q3: json['par_q_3'] ?? 'No',
      q4: json['par_q_4'] ?? 'No',
      q5: json['par_q_5'] ?? 'No',
      q6: json['par_q_6'] ?? 'No',
      q7: json['par_q_7'] ?? 'No',
      q8: json['par_q_8'] ?? 'No',
      q9: json['par_q_9'] ?? 'No',
      q10: json['par_q_10'] ?? 'No',
      contactPerson: json['contact_person'] ?? '',
      contactNumber: json['contact_number'] ?? '',
      relationship: json['relationship'] ?? '',
      rulesAndPolicy: json['rules_and_policy'] == 1,
      liabilityWaiver: json['liability_waiver'] == 1,
      cancellationAndRefundPolicy: json['cancellation_and_refund_policy'] == 1,
    );
  }
}
