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

  Future<Widget> _getHomeScreen() async {
    String? rolesJson = await storage.read(key: "roles");

    if (rolesJson != null) {
      List<String> roles = List<String>.from(jsonDecode(rolesJson));

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
          return const Center(child: CircularProgressIndicator());
        }

        return MaterialApp(
          debugShowCheckedModeBanner: false,
          home: snapshot.data ?? const LoginScreen(),
        );
      },
    );
  }
}
