import 'package:flutter/material.dart';
import '../controller/auth_controller.dart';
import 'views/login_screen.dart';
import 'views/dashboard.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<bool>(
      future: AuthController().isLoggedIn(),
      builder: (context, snapshot) {
        return MaterialApp(
          debugShowCheckedModeBanner: false,
          home: snapshot.data == true ? const DashboardScreen() : const LoginScreen(),
        );
      },
    );
  }
}
