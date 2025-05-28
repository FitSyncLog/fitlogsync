import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ProfileController {
  final FlutterSecureStorage _storage = const FlutterSecureStorage();
  final String baseUrl = 'http://localhost/fitlogsync/Desktop/database/'; // Replace with your API URL
  
  // Get user profile data from secure storage
  Future<Map<String, String>> getUserProfileFromStorage() async {
    final Map<String, String> profileData = {};
    
    final List<String> keys = [
      'user_id',
      'username', 
      'email', 
      'firstname', 
      'middlename', 
      'lastname',
      'phone_number',
      'address',
      'date_of_birth',
      'gender',
      'emergency_contact_person',
      'emergency_contact_number',
      'emergency_relationship',
    ];
    
    for (String key in keys) {
      String? value = await _storage.read(key: key);
      if (value != null) {
        profileData[key] = value;
      }
    }
    
    return profileData;
  }
  
  // Save user profile data to secure storage
  Future<void> saveUserProfileToStorage(Map<String, String> profileData) async {
    for (String key in profileData.keys) {
      await _storage.write(key: key, value: profileData[key]);
    }
  }
  
  // Update user profile on server
  Future<Map<String, dynamic>> updateUserProfile({
    required String userId,
    String? username,
    String? email,
    String? firstName,
    String? middleName,
    String? lastName,
    String? phoneNumber,
    String? address,
    String? dateOfBirth,
    String? gender,
    String? password,
    String? confirmPassword,
    String? contactPerson,
    String? contactNumber,
    String? relationship,
  }) async {
    try {
      // Build request body
      final Map<String, dynamic> requestBody = {
        'user_id': userId,
      };
      
      // Add user data if provided
      final Map<String, dynamic> userData = {};
      if (username != null) userData['username'] = username;
      if (email != null) userData['email'] = email;
      if (firstName != null) userData['firstname'] = firstName;
      if (middleName != null) userData['middlename'] = middleName;
      if (lastName != null) userData['lastname'] = lastName;
      if (phoneNumber != null) userData['phone_number'] = phoneNumber;
      if (address != null) userData['address'] = address;
      if (dateOfBirth != null) userData['date_of_birth'] = dateOfBirth;
      if (gender != null) userData['gender'] = gender;
      if (password != null) userData['password'] = password;
      if (confirmPassword != null) userData['confirm_password'] = confirmPassword;
      
      if (userData.isNotEmpty) {
        requestBody['user'] = userData;
      }
      
      // Add emergency contact data if provided
      if (contactPerson != null || contactNumber != null || relationship != null) {
        requestBody['emergency_contact'] = {
          'contact_person': contactPerson ?? '',
          'contact_number': contactNumber ?? '',
          'relationship': relationship ?? ''
        };
      }
      
      // Send update request
      final response = await http.post(
        Uri.parse('$baseUrl/update_profile.php'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode(requestBody),
      );
      
      final Map<String, dynamic> responseData = jsonDecode(response.body);
      
      // If update successful, update local storage
      if (responseData['success'] == true) {
        final Map<String, String> storageData = {};
        
        if (username != null) storageData['username'] = username;
        if (email != null) storageData['email'] = email;
        if (firstName != null) storageData['firstname'] = firstName;
        if (middleName != null) storageData['middlename'] = middleName;
        if (lastName != null) storageData['lastname'] = lastName;
        if (phoneNumber != null) storageData['phone_number'] = phoneNumber;
        if (address != null) storageData['address'] = address;
        if (dateOfBirth != null) storageData['date_of_birth'] = dateOfBirth;
        if (gender != null) storageData['gender'] = gender;
        if (contactPerson != null) storageData['emergency_contact_person'] = contactPerson;
        if (contactNumber != null) storageData['emergency_contact_number'] = contactNumber;
        if (relationship != null) storageData['emergency_relationship'] = relationship;
        
        await saveUserProfileToStorage(storageData);
      }
      
      return responseData;
    } catch (e) {
      return {
        'success': false,
        'message': 'Error updating profile: ${e.toString()}'
      };
    }
  }
  
  // Updated method to handle relationship as a String value instead of TextEditingController
  Future<void> updateProfileFromScreen(
    BuildContext context,
    TextEditingController usernameController,
    TextEditingController emailController,
    TextEditingController firstNameController,
    TextEditingController middleNameController,
    TextEditingController lastNameController,
    TextEditingController phoneNumberController,
    TextEditingController addressController,
    TextEditingController dateOfBirthController,
    String? selectedGender,
    TextEditingController passwordController,
    TextEditingController confirmPasswordController,
    TextEditingController contactPersonController,
    TextEditingController contactNumberController,
    String? selectedRelationship, // Changed from TextEditingController to String?
  ) async {
    final String? userId = await _storage.read(key: 'user_id');
    
    if (userId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('User ID not found'))
      );
      return;
    }
    
    final response = await updateUserProfile(
      userId: userId,
      username: usernameController.text,
      email: emailController.text,
      firstName: firstNameController.text,
      middleName: middleNameController.text,
      lastName: lastNameController.text,
      phoneNumber: phoneNumberController.text,
      address: addressController.text,
      dateOfBirth: dateOfBirthController.text,
      gender: selectedGender,
      password: passwordController.text.isNotEmpty ? passwordController.text : null,
      confirmPassword: confirmPasswordController.text.isNotEmpty ? confirmPasswordController.text : null,
      contactPerson: contactPersonController.text,
      contactNumber: contactNumberController.text,
      relationship: selectedRelationship, // Use the selected relationship directly
    );
    
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(response['message']))
    );
  }
}