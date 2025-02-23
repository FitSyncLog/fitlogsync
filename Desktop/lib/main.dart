import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'views/login_screen.dart';
import 'views/dashboard.dart';
import 'views/admin/admin_dashboard.dart';
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

      if (roles.contains("Admin")) {
        return const AdminDashboardScreen();
      } else if (roles.contains("Instructor")) {
        return const InstructorDashboardScreen();
      } else {
        return const DashboardScreen();
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
