import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'auth/login_screen.dart';
import 'views/member/dashboard.dart';
import 'views/super_admin/super_admin_dashboard.dart';
import 'views/admin/admin_dashboard.dart';
import 'views/front_desk/front_desk_dashboard.dart';
import 'views/instructor/instructor_dashboard.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  final storage = const FlutterSecureStorage();

  /// **Checks if the session is still valid**
  Future<bool> _isSessionValid() async {
    String? expiresAtStr = await storage.read(key: "expires_at");

    if (expiresAtStr == null) {
      return false; // No session found
    }

    DateTime expiresAt = DateTime.parse(expiresAtStr);
    return DateTime.now().isBefore(expiresAt);
  }

  /// **Determines which screen to show on app start**
  Future<Widget> _getHomeScreen() async {
    bool isSessionValid = await _isSessionValid();

    if (!isSessionValid) {
      await storage.deleteAll(); // Clear expired session data
      return const LoginScreen();
    }

    // Check if OTP verification is pending
    String? vCodeExpiration = await storage.read(key: "v_code_expiration");
    String? verificationCode = await storage.read(key: "verification_code");

    print("vCodeExpiration: $vCodeExpiration");
    print("verificationCode: $verificationCode");

    // If v_code_expiration exists, check if it's still valid
    if (vCodeExpiration != null && verificationCode != null) {
      DateTime expirationDateTime = DateTime.parse(vCodeExpiration);

      bool isExpired = DateTime.now().isAfter(expirationDateTime);
      print("Expiration date: $expirationDateTime");
      print("Current date: ${DateTime.now()}");
      print("Is expired: $isExpired");

      // If OTP has expired, clear session and redirect to login
      if (DateTime.now().isAfter(expirationDateTime)) {
        print("OTP expired, clearing session");
        await storage.deleteAll();
        return const LoginScreen();
      }
    }

    // If no pending OTP verification, proceed with role-based routing
    String? rolesJson = await storage.read(key: "roles");

    if (rolesJson != null) {
      List<dynamic> roles = jsonDecode(rolesJson);

      if (roles.contains("Super Admin")) {
        return const SuperAdminDashboardScreen();
      } else if (roles.contains("Admin")) {
        return const AdminDashboardScreen();
      } else if (roles.contains("Instructor")) {
        return const InstructorDashboardScreen();
      } else if (roles.contains("Member")) {
        return const DashboardScreen();
      } else if (roles.contains("Front Desk")) {
        return const FrontDeskDashboardScreen();
      }
    }

    return const LoginScreen();
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<Widget>(
      future: _getHomeScreen(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const MaterialApp(
            debugShowCheckedModeBanner: false,
            home: Scaffold(body: Center(child: CircularProgressIndicator())),
          );
        }

        return MaterialApp(
          debugShowCheckedModeBanner: false,
          home: snapshot.data ?? const LoginScreen(),
        );
      },
    );
  }
}
