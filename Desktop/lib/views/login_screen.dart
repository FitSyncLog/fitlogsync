import 'package:flutter/material.dart';
import 'dart:ui';
import 'package:shared_preferences/shared_preferences.dart';
import 'dashboard.dart';
import 'package:url_launcher/url_launcher.dart';
import '/controller/auth_controller.dart'; // Import the auth controller

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  Future<bool> _isLoggedIn() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    return prefs.containsKey("email");
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
          home:
              snapshot.data == true
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
      _emailError =
          _emailController.text.isEmpty ? 'Please enter your email' : null;
      _passwordError =
          _passwordController.text.isEmpty
              ? 'Please enter your password'
              : null;
    });

    if (_emailError != null || _passwordError != null) {
      return;
    }

    setState(() => _isLoading = true);

    try {
      final authController = AuthController();
      Map<String, dynamic> response = await authController.login(
        _emailController.text,
        _passwordController.text,
      );

      print('Login response: $response'); // Debug print

      if (response["success"] == true) {
        SharedPreferences prefs = await SharedPreferences.getInstance();
        await prefs.setString("email", _emailController.text);
        await prefs.setString("username", response["username"]);
        await prefs.setString("firstname", response["firstname"]);
        await prefs.setString("middlename", response["middlename"]);
        await prefs.setString("lastname", response["lastname"]);
        await prefs.setString("date_of_birth", response["date_of_birth"]);
        await prefs.setString("gender", response["gender"]);
        await prefs.setString("phone_number", response["phone_number"]);
        await prefs.setString("address", response["address"]);

        if (mounted) {
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(builder: (context) => const DashboardScreen()),
          );
        }
      } else {
        setState(() {
          _passwordError = 'Incorrect email or password';
        });
      }
    } catch (e) {
      print('Login error: $e'); // Debug print
      if (mounted) {
        ScaffoldMessenger.of(
          context,
        ).showSnackBar(SnackBar(content: Text('Login failed: $e')));
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
                          _obscurePassword
                              ? Icons.visibility_off
                              : Icons.visibility,
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
                      child:
                          _isLoading
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
                  onPressed:
                      () => _launchURL(
                        'http://localhost/fitlogsync/rule_and_policy.php',
                      ),
                  child: const Text(
                    'Terms and Conditions',
                    style: TextStyle(color: Colors.white),
                  ),
                ),
                const Text('|', style: TextStyle(color: Colors.white)),
                TextButton(
                  onPressed:
                      () => _launchURL(
                        'http://localhost/fitlogsync/liability_waiver.php',
                      ),
                  child: const Text(
                    'Waiver',
                    style: TextStyle(color: Colors.white),
                  ),
                ),
                const Text('|', style: TextStyle(color: Colors.white)),
                TextButton(
                  onPressed:
                      () => _launchURL(
                        'http://localhost/fitlogsync/cancellation_and_refund_policy.php',
                      ),
                  child: const Text(
                    'Membership',
                    style: TextStyle(color: Colors.white),
                  ),
                ),
                const Text('|', style: TextStyle(color: Colors.white)),
                TextButton(
                  onPressed:
                      () => _launchURL(
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
