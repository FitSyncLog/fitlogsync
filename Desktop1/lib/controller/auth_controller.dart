import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class AuthController {
  final String baseUrl = 'http://localhost/fitlogsync/Desktop/api/';
  final FlutterSecureStorage storage = const FlutterSecureStorage();

  // Login method
  Future<Map<String, dynamic>> login(String email, String password) async {
    if (email.isEmpty || password.isEmpty) {
      return {"success": false, "message": "Email and password are required"};
    }

    try {
      final response = await http.post(
        Uri.parse('$baseUrl/login.php'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'email': email, 'password': password}),
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        throw Exception('Failed to login: ${response.statusCode}');
      }
    } catch (e) {
      print('Login error: $e');
      throw Exception('Failed to connect to server: $e');
    }
  }

  // Verify OTP method
  // Verify OTP method
  Future<Map<String, dynamic>> verifyOTP(String email, String otp) async {
    // Validate OTP is a 6-digit number
    if (otp.isEmpty) {
      return {"success": false, "message": "OTP code is required"};
    }

    if (otp.length != 6 || !RegExp(r'^[0-9]{6}$').hasMatch(otp)) {
      return {"success": false, "message": "OTP must be a 6-digit number"};
    }

    try {
      final response = await http.post(
        Uri.parse('$baseUrl/verify_otp.php'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'email': email, 'verification_code': otp}),
      );

      if (response.statusCode == 200) {
        final result = jsonDecode(response.body);

        // If verification was successful, update the verification status
        if (result["success"] == true) {
          // Clear verification data
          await storage.delete(key: "verification_code");
          await storage.delete(key: "v_code_expiration");
        }

        return result;
      } else {
        throw Exception('Failed to verify OTP: ${response.statusCode}');
      }
    } catch (e) {
      print('OTP verification error: $e');
      throw Exception('Failed to connect to server: $e');
    }
  }

  // Resend OTP method
  Future<Map<String, dynamic>> resendOTP(String email) async {
    if (email.isEmpty) {
      return {"success": false, "message": "Email is required"};
    }

    try {
      final response = await http.post(
        Uri.parse('$baseUrl/resend_otp.php'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'email': email}),
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        throw Exception('Failed to resend OTP: ${response.statusCode}');
      }
    } catch (e) {
      print('Resend OTP error: $e');
      throw Exception('Failed to connect to server: $e');
    }
  }

  // Store user data in secure storage
  Future<void> storeUserData(Map<String, dynamic> userData) async {
    // Store auth token and expiration
    await storage.write(key: "auth_token", value: userData["token"]);
    await storage.write(key: "expires_at", value: userData["expires_at"]);
    // Store user details
    await storage.write(key: "email", value: userData["email"]);

    // Add this line to store the user_id
    if (userData.containsKey("user_id")) {
      await storage.write(
        key: "user_id",
        value: userData["user_id"].toString(),
      );
    }
    
    if (userData.containsKey("verification_code")) {
      await storage.write(
        key: "verification_code",
        value: userData["verification_code"],
      );
    }

    if (userData.containsKey("v_code_expiration")) {
      await storage.write(
        key: "v_code_expiration",
        value: userData["v_code_expiration"],
      );
    }

    // Store user profile data
    final userFields = [
      "username",
      "firstname",
      "middlename",
      "lastname",
      "date_of_birth",
      "gender",
      "phone_number",
      "address",
    ];

    for (var field in userFields) {
      if (userData.containsKey(field) && userData[field] != null) {
        await storage.write(key: field, value: userData[field]);
      }
    }

    // Store roles
    if (userData.containsKey("roles") && userData["roles"] != null) {
      await storage.write(key: "roles", value: jsonEncode(userData["roles"]));
    }
  }

  // Get user roles from secure storage
  Future<List<dynamic>> getUserRoles() async {
    final rolesJson = await storage.read(key: "roles");
    if (rolesJson != null) {
      return jsonDecode(rolesJson);
    }
    return [];
  }

  // Check if OTP is valid
  Future<bool> isOTPValid() async {
    final expirationTime = await storage.read(key: "v_code_expiration");
    if (expirationTime == null) return false;

    final expirationDateTime = DateTime.parse(expirationTime);
    return expirationDateTime.isAfter(DateTime.now());
  }

  // Logout method
  Future<void> logout() async {
    await storage.deleteAll();
  }
}
