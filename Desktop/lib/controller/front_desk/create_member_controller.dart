import 'package:http/http.dart' as http;
import 'dart:convert';

class MemberController {
  Future<bool> createMember({
    required String username,
    required String firstname,
    required String lastname,
    required String middlename,
    required String dateofbirth,
    required String gender,
    required String address,
    required String phonenumber,
    required String email,
    required String password,
    required String confirmPassword,
    required String medicalConditions,
    required String currentMedications,
    required String previousInjuries,
    required String contactPerson,
    required String contactNumber,
    required String relationship,
    required String q1,
    required String q2,
    required String q3,
    required String q4,
    required String q5,
    required String q6,
    required String q7,
    required String q8,
    required String q9,
    required String q10,
    required bool waiverRules,
    required bool waiverLiability,
    required bool waiverCancel,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('http://localhost/fitlogsync/Desktop/database/front_desk_create_member.php'),
        headers: {'Content-Type': 'application/json; charset=UTF-8'},
        body: jsonEncode({
          'username': username,
          'firstname': firstname,
          'lastname': lastname,
          'middlename': middlename,
          'dateofbirth': dateofbirth,
          'gender': gender,
          'address': address,
          'phonenumber': phonenumber,
          'email': email,
          'password': password,
          'confirm_password': confirmPassword,
          'medical_conditions': medicalConditions,
          'current_medications': currentMedications,
          'previous_injuries': previousInjuries,
          'contact_person': contactPerson,
          'contact_number': contactNumber,
          'relationship': relationship,
          'q1': q1,
          'q2': q2,
          'q3': q3,
          'q4': q4,
          'q5': q5,
          'q6': q6,
          'q7': q7,
          'q8': q8,
          'q9': q9,
          'q10': q10,
          'waiver_rules': waiverRules,
          'waiver_liability': waiverLiability,
          'waiver_cancel': waiverCancel,
        }),
      );

      if (response.statusCode == 200) {
        // Successfully created member
        return true;
      } else {
        // Failed to create member
        print('Failed to create member: ${response.body}');
        return false;
      }
    } catch (e) {
      // Handle exceptions
      print('Error creating member: $e');
      return false;
    }
  }
}
