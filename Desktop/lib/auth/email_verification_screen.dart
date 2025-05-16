import 'package:flutter/material.dart';
import 'dart:ui';
import 'package:flutter/services.dart'; 
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../views/member/dashboard.dart';
import '../views/super_admin/super_admin_dashboard.dart';
import '../views/admin/admin_dashboard.dart';
import '../views/instructor/instructor_dashboard.dart';
import '../views/front_desk/front_desk_dashboard.dart';
import '/controller/auth_controller.dart';

class EmailVerificationScreen extends StatefulWidget {
  const EmailVerificationScreen({Key? key}) : super(key: key);

  @override
  _EmailVerificationScreenState createState() => _EmailVerificationScreenState();
}

class _EmailVerificationScreenState extends State<EmailVerificationScreen> {
  final TextEditingController _otpController = TextEditingController();
  String? _otpError;
  bool _isLoading = false;
  bool _resendingOTP = false;
  final FlutterSecureStorage storage = const FlutterSecureStorage();
  final AuthController _authController = AuthController();
  String? _email;
  DateTime? _otpExpiration;
  bool _showResendButton = false;
  int _secondsRemaining = 0;

  @override
  void initState() {
    super.initState();
    _loadData();
    _startExpirationTimer();
  }

  Future<void> _loadData() async {
    _email = await storage.read(key: "email");
    final expiration = await storage.read(key: "v_code_expiration");
    if (expiration != null) {
      _otpExpiration = DateTime.parse(expiration);
      _updateResendButtonVisibility();
    }
  }

  void _startExpirationTimer() {
    Future.delayed(const Duration(seconds: 1), () {
      if (mounted) {
        setState(() {
          final now = DateTime.now();
          if (_otpExpiration != null && _otpExpiration!.isAfter(now)) {
            _secondsRemaining = _otpExpiration!.difference(now).inSeconds;
            _showResendButton = _secondsRemaining <= 0;
          } else {
            _showResendButton = true;
            _secondsRemaining = 0;
          }
        });
        if (_secondsRemaining > 0) {
          _startExpirationTimer();
        }
      }
    });
  }

  void _updateResendButtonVisibility() {
    if (_otpExpiration != null) {
      setState(() {
        final now = DateTime.now();
        _showResendButton = !_otpExpiration!.isAfter(now);
        if (!_showResendButton) {
          _secondsRemaining = _otpExpiration!.difference(now).inSeconds;
        } else {
          _secondsRemaining = 0;
        }
      });
    } else {
      setState(() {
        _showResendButton = true;
      });
    }
  }

  Future<void> _verifyOTP() async {
    // Improved OTP validation
    final otp = _otpController.text.trim();
    
    if (otp.isEmpty) {
      setState(() {
        _otpError = 'Please enter the OTP';
      });
      return;
    }

    
    // Check if OTP is exactly 6 digits
    if (otp.length != 6) {
      setState(() {
        _otpError = 'OTP must be 6 digits';
      });
      return;
    }
    
    // Check if OTP contains only numbers
    if (!RegExp(r'^[0-9]{6}$').hasMatch(otp)) {
      setState(() {
        _otpError = 'OTP must contain only digits';
      });
      return;
    }
    
    setState(() {
      _isLoading = true;
      _otpError = null;
    });

    try {
      final email = await storage.read(key: "email");
      if (email == null) {
        throw Exception("Email not found. Please log in again.");
      }
      

      // Verify OTP from backend
      final responseData = await _authController.verifyOTP(
        email,
        otp,
      );
      

      if (responseData["success"] == true) {
        // Store updated user data
        await _authController.storeUserData(responseData);

        
        
        // Get user roles and navigate to appropriate dashboard
        final roles = await _authController.getUserRoles();
        _navigateToDashboard(roles);
      } else {
        setState(() {
          _otpError = responseData["message"] ?? 'Invalid OTP';
        });
      }
    } catch (e) {
      print("OTP Verification Error: $e");
      
      if (mounted) {
        setState(() {
          _otpError = 'Verification failed: $e';
        });
      }
    }

    setState(() => _isLoading = false);
  }

  Future<void> _resendOTP() async {
    if (_email == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Email not found. Please log in again.')),
      );
      return;
    }

    setState(() {
      _resendingOTP = true;
    });

    try {
      final responseData = await _authController.resendOTP(_email!);
      
      if (responseData["success"] == true) {
        // Store new verification code and expiration
        await storage.write(
          key: "verification_code",
          value: responseData["verification_code"],
        );
        await storage.write(
          key: "v_code_expiration",
          value: responseData["v_code_expiration"],
        );
        
        // Update expiration time
        _otpExpiration = DateTime.parse(responseData["v_code_expiration"]);
        _updateResendButtonVisibility();
        
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('OTP sent successfully')),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(responseData["message"] ?? 'Failed to send OTP')),
        );
      }
    } catch (e) {
      print("Resend OTP Error: $e");
      
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed to resend OTP: $e')),
        );
      }
    }

    setState(() {
      _resendingOTP = false;
    });
  }

  void _navigateToDashboard(List<dynamic> roles) {
    Widget dashboard;
    
    if (roles.contains("Super Admin")) {
      dashboard = const SuperAdminDashboardScreen();
    } else if (roles.contains("Admin")) {
      dashboard = const AdminDashboardScreen();
    } else if (roles.contains("Front Desk")) {
      dashboard = const FrontDeskDashboardScreen();
    } else if (roles.contains("Instructor")) {
      dashboard = const InstructorDashboardScreen();
    } else if (roles.contains("Member")) {
      dashboard = const DashboardScreen();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('No valid role found for this account')),
      );
      return;
    }
    
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(builder: (context) => dashboard),
    );
  }

  void _navigateToLogin(BuildContext context) {
    Navigator.pop(context);
  }

  String _formatTimeRemaining() {
    final minutes = (_secondsRemaining / 60).floor();
    final seconds = _secondsRemaining % 60;
    return '${minutes.toString().padLeft(2, '0')}:${seconds.toString().padLeft(2, '0')}';
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
                    'Email Verification',
                    style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 15),
                  Text(
                    'We\'ve sent a 6-digit code to your email.\nPlease enter it below to verify your account.',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      color: Colors.grey.shade700,
                      fontSize: 14,
                    ),
                  ),
                  const SizedBox(height: 20),
                  TextField(
                    controller: _otpController,
                    keyboardType: TextInputType.number,
                    maxLength: 6,
                    decoration: InputDecoration(
                      labelText: 'OTP Code',
                      border: const OutlineInputBorder(),
                      prefixIcon: const Icon(Icons.lock),
                      errorText: _otpError,
                      counterText: '',
                      helperText: 'Enter the 6-digit code sent to your email',
                    ),
                    // Input formatter to allow only digits
                    inputFormatters: [
                      FilteringTextInputFormatter.digitsOnly,
                    ],
                  ),
                  const SizedBox(height: 5),
                  if (_secondsRemaining > 0)
                    Padding(
                      padding: const EdgeInsets.symmetric(vertical: 8.0),
                      child: Text(
                        'OTP expires in: ${_formatTimeRemaining()}',
                        style: TextStyle(
                          color: Colors.grey.shade600,
                          fontSize: 12,
                        ),
                      ),
                    ),
                  const SizedBox(height: 20),
                  SizedBox(
                    width: 300,
                    height: 50,
                    child: ElevatedButton(
                      onPressed: _isLoading ? null : _verifyOTP,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.amber.shade600,
                      ),
                      child: _isLoading
                          ? const CircularProgressIndicator(
                              color: Colors.black,
                            )
                          : const Text(
                              'Verify OTP',
                              style: TextStyle(
                                fontSize: 18,
                                color: Colors.black,
                              ),
                            ),
                    ),
                  ),
                  const SizedBox(height: 15),
                  _showResendButton
                      ? TextButton(
                          onPressed: _resendingOTP ? null : _resendOTP,
                          child: _resendingOTP
                              ? const SizedBox(
                                  width: 16,
                                  height: 16,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                  ),
                                )
                              : const Text('Resend OTP'),
                        )
                      : const SizedBox.shrink(),
                ],
              ),
            ),
          ),
          Positioned(
            bottom: 20,
            left: 0,
            right: 0,
            child: Center(
              child: TextButton(
                onPressed: () => _navigateToLogin(context),
                child: const Text(
                  'Back to Login',
                  style: TextStyle(color: Colors.white),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}