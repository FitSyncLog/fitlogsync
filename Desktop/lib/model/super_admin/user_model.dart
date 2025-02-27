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
  final String subscriptionStatus; // NEW FIELD
  final String registrationDate;
  final List<String> roles;

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
    required this.subscriptionStatus, // NEW FIELD
    required this.registrationDate,
    required this.roles,
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
      subscriptionStatus: json['subscription_status'] ?? 'Unknown', // NEW FIELD
      registrationDate: json['registration_date'] ?? '',
      roles: json['roles'] != null && json['roles'] is String
          ? (json['roles'] as String).split(', ')
          : [], // Handles null & empty cases
    );
  }
}
