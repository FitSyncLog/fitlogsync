class User {
  final int? id;
  final String username;
  String firstname;
  String middlename;
  String lastname;
  String dateOfBirth;
  String gender;
  String phoneNumber;
  String email;
  String address;
  final String accountNumber;
  String status;
  final String enrolledBy;
  String subscriptionStatus;
  final String registrationDate;
  final List<String> roles;
  String medicalConditions;
  String currentMedications;
  String previousInjuries;
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
  String contactPerson;
  String contactNumber;
  String relationship;
  bool rulesAndPolicy;
  bool liabilityWaiver;
  bool cancellationAndRefundPolicy;

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
  
  // Method to convert the User instance to a JSON map
  Map<String, dynamic> toJson() {
    return {
      'id': id?.toString(),
      'username': username,
      'firstname': firstname,
      'middlename': middlename,
      'lastname': lastname,
      'date_of_birth': dateOfBirth,
      'gender': gender,
      'phone_number': phoneNumber,
      'email': email,
      'address': address,
      'account_number': accountNumber,
      'status': status,
      'enrolled_by': enrolledBy,
      'subscription_status': subscriptionStatus,
      'registration_date': registrationDate,
      'roles': roles.join(', '),
      'medical_conditions': medicalConditions,
      'current_medications': currentMedications,
      'previous_injuries': previousInjuries,
      'par_q_1': q1,
      'par_q_2': q2,
      'par_q_3': q3,
      'par_q_4': q4,
      'par_q_5': q5,
      'par_q_6': q6,
      'par_q_7': q7,
      'par_q_8': q8,
      'par_q_9': q9,
      'par_q_10': q10,
      'contact_person': contactPerson,
      'contact_number': contactNumber,
      'relationship': relationship,
      'rules_and_policy': rulesAndPolicy ? 1 : 0,
      'liability_waiver': liabilityWaiver ? 1 : 0,
      'cancellation_and_refund_policy': cancellationAndRefundPolicy ? 1 : 0,
    };
  }
}