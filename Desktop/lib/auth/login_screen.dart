import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../views/member/dashboard.dart';
import 'dart:convert';
import 'dart:ui';
import '../views/super_admin/super_admin_dashboard.dart';
import '../views/admin/admin_dashboard.dart';
import '../views/instructor/instructor_dashboard.dart';
import '../views/front_desk/front_desk_dashboard.dart';
import 'package:url_launcher/url_launcher.dart';
import '/controller/auth_controller.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  final FlutterSecureStorage storage = const FlutterSecureStorage();

  Future<bool> _isLoggedIn() async {
    String? email = await storage.read(key: "email");
    return email != null;
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<bool>(
      future: _isLoggedIn(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Center(child: CircularProgressIndicator());
        }

        return MaterialApp(
          debugShowCheckedModeBanner: false,
          title: 'Login Form',
          theme: ThemeData(
            colorScheme: ColorScheme.fromSeed(seedColor: Colors.blue),
          ),
          home: snapshot.data == true
              ? const DashboardScreen()
              : const LoginScreen(),
        );
      },
    );
  }
}

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  final FlutterSecureStorage storage = const FlutterSecureStorage();
  final AuthController _authController = AuthController();
  bool _obscurePassword = true;
  String? _emailError;
  String? _passwordError;
  bool _isLoading = false;

  void _togglePasswordVisibility() {
    setState(() {
      _obscurePassword = !_obscurePassword;
    });
  }

  Future<void> _launchURL(String url) async {
    final Uri uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    } else {
      throw 'Could not launch $url';
    }
  }

  Future<void> _login() async {
    setState(() {
      _emailError = _emailController.text.isEmpty ? 'Please enter your email' : null;
      _passwordError = _passwordController.text.isEmpty ? 'Please enter your password' : null;
    });

    if (_emailError != null || _passwordError != null) {
      return;
    }

    setState(() => _isLoading = true);

    try {
      final responseData = await _authController.login(_emailController.text, _passwordController.text);

      if (responseData["success"] == true) {
        await storage.write(key: "email", value: _emailController.text);
        if (responseData.containsKey("username")) {
          await storage.write(key: "username", value: responseData["username"]);
        }
        if (responseData.containsKey("firstname")) {
          await storage.write(key: "firstname", value: responseData["firstname"]);
        }
        if (responseData.containsKey("middlename")) {
          await storage.write(key: "middlename", value: responseData["middlename"]);
        }
        if (responseData.containsKey("lastname")) {
          await storage.write(key: "lastname", value: responseData["lastname"]);
        }
        if (responseData.containsKey("phone_number")) {
          await storage.write(key: "phone_number", value: responseData["phone_number"]);
        }
        if (responseData.containsKey("address")) {
          await storage.write(key: "address", value: responseData["address"]);
        }
        if (responseData.containsKey("date_of_birth")) {
          await storage.write(key: "date_of_birth", value: responseData["date_of_birth"]);
        }
        if (responseData.containsKey("gender")) {
          await storage.write(key: "gender", value: responseData["gender"]);
        }

        if (responseData.containsKey("roles") && responseData["roles"] != null) {
          List<dynamic> roles = responseData["roles"];
          await storage.write(key: "roles", value: jsonEncode(roles));
        }

        if (mounted) {
          if (responseData["roles"] != null && responseData["roles"].contains("Super Admin")) {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(builder: (context) => const SuperAdminDashboardScreen()),
            );
          } else if (responseData["roles"] != null && responseData["roles"].contains("Admin")) {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(builder: (context) => const AdminDashboardScreen()),
            );
          } else if (responseData["roles"] != null && responseData["roles"].contains("Front Desk")) {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(builder: (context) => const FrontDeskDashboardScreen()),
            );
          } else if (responseData["roles"] != null && responseData["roles"].contains("Instructor")) {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(builder: (context) => const InstructorDashboardScreen()),
            );
          } else if (responseData["roles"] != null && responseData["roles"].contains("Member")) {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(builder: (context) => const DashboardScreen()),
            );
          }else {
            ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Account not found')));
          }
        }
      } else {
        setState(() {
          _passwordError = responseData["message"] ?? 'Incorrect email or password';
        });
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Login failed: $e')));
      }
    }

    setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        fit: StackFit.expand,
        children: [
          Image.asset('assets/images/hero-bg.jpeg', fit: BoxFit.cover),
          BackdropFilter(
            filter: ImageFilter.blur(sigmaX: 5, sigmaY: 5),
            child: Container(
              color: Colors.black.withAlpha((0.5 * 255).toInt()),
            ),
          ),
          Positioned(
            top: 1,
            left: 20,
            child: Image.asset(
              'assets/logo/fitlogsync2.png',
              width: 150,
              height: 150,
            ),
          ),
          Center(
            child: Container(
              width: 350,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.white.withAlpha((0.9 * 255).toInt()),
                borderRadius: BorderRadius.circular(15),
                boxShadow: [
                  const BoxShadow(
                    color: Colors.black26,
                    blurRadius: 10,
                    spreadRadius: 2,
                    offset: Offset(0, 5),
                  ),
                ],
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const Text(
                    'Login',
                    style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 30),
                  TextField(
                    controller: _emailController,
                    keyboardType: TextInputType.emailAddress,
                    decoration: InputDecoration(
                      labelText: 'Email',
                      border: const OutlineInputBorder(),
                      prefixIcon: const Icon(Icons.email),
                      errorText: _emailError,
                    ),
                  ),
                  const SizedBox(height: 20),
                  TextField(
                    controller: _passwordController,
                    obscureText: _obscurePassword,
                    decoration: InputDecoration(
                      labelText: 'Password',
                      border: const OutlineInputBorder(),
                      prefixIcon: const Icon(Icons.lock),
                      suffixIcon: IconButton(
                        icon: Icon(
                          _obscurePassword ? Icons.visibility_off : Icons.visibility,
                        ),
                        onPressed: _togglePasswordVisibility,
                      ),
                      errorText: _passwordError,
                    ),
                  ),
                  const SizedBox(height: 30),
                  SizedBox(
                    width: 300,
                    height: 50,
                    child: ElevatedButton(
                      onPressed: _isLoading ? null : _login,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.amber.shade600,
                      ),
                      child: _isLoading
                          ? const CircularProgressIndicator(
                              color: Colors.black,
                            )
                          : const Text(
                              'Login',
                              style: TextStyle(
                                fontSize: 18,
                                color: Colors.black,
                              ),
                            ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          Positioned(
            bottom: 20,
            left: 0,
            right: 0,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                TextButton(
                  onPressed: () => _launchURL(
                    'http://localhost/fitlogsync/rule_and_policy.php',
                  ),
                  child: const Text(
                    'Terms and Conditions',
                    style: TextStyle(color: Colors.white),
                  ),
                ),
                const Text('|', style: TextStyle(color: Colors.white)),
                TextButton(
                  onPressed: () => _launchURL(
                    'http://localhost/fitlogsync/liability_waiver.php',
                  ),
                  child: const Text(
                    'Waiver',
                    style: TextStyle(color: Colors.white),
                  ),
                ),
                const Text('|', style: TextStyle(color: Colors.white)),
                TextButton(
                  onPressed: () => _launchURL(
                    'http://localhost/fitlogsync/cancellation_and_refund_policy.php',
                  ),
                  child: const Text(
                    'Membership',
                    style: TextStyle(color: Colors.white),
                  ),
                ),
                const Text('|', style: TextStyle(color: Colors.white)),
                TextButton(
                  onPressed: () => _launchURL(
                    'http://localhost/fitlogsync/forgot-password.php',
                  ),
                  child: const Text(
                    'Forgot Password?',
                    style: TextStyle(color: Colors.white),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
