import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class MemberController {
  final FlutterSecureStorage _secureStorage = const FlutterSecureStorage();
  
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
      // Get the current logged-in user's ID from secure storage
      String? userId = await _secureStorage.read(key: 'user_id');
      
      if (userId == null) {
        print('No logged-in user found. Cannot enroll a new member.');
        return false;
      }

      final response = await http.post(
        Uri.parse('http://localhost/fitlogsync/Desktop/api/front_desk_create_member.php'),
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
          'enrolled_by': userId,
        }),
      );

      print('Response status code: ${response.statusCode}');
      print('Response body: ${response.body}');

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        
        if (responseData['success'] == true) {
          // Optionally store additional information from response if needed
          if (responseData.containsKey('account_number')) {
            await _secureStorage.write(
              key: 'last_created_account', 
              value: responseData['account_number']
            );
          }
          return true;
        } else {
          print('Server returned error: ${responseData['message']}');
          return false;
        }
      } else {
        // Failed to create member
        print('Failed to create member: HTTP ${response.statusCode} - ${response.body}');
        return false;
      }
    } catch (e) {
      // Handle exceptions
      print('Error creating member: $e');
      return false;
    }
  }
  
  // Helper method to get the logged in user information
  Future<Map<String, dynamic>?> getLoggedInUserInfo() async {
    try {
      String? id = await _secureStorage.read(key: 'user_id');
      String? username = await _secureStorage.read(key: 'username');
      String? email = await _secureStorage.read(key: 'email');
      String? role = await _secureStorage.read(key: 'roles');
      
      if (id == null) {
        return null;
      }
      
      return {
        'id': id,
        'username': username,
        'email': email,
        'roles': role != null ? jsonDecode(role) : [],
      };
    } catch (e) {
      print('Error getting user info: $e');
      return null;
    }
  }
  
  // Method to check if the user is authenticated
  Future<bool> isAuthenticated() async {
    String? token = await _secureStorage.read(key: 'token');
    String? expiresAt = await _secureStorage.read(key: 'expires_at');
    
    if (token == null || expiresAt == null) {
      return false;
    }
    
    // Check if token is expired
    DateTime expiry = DateTime.parse(expiresAt);
    return DateTime.now().isBefore(expiry);
  }
}