import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'dart:convert';
import 'dart:async';
import '../../auth/login_screen.dart';
import '../profile/profile_screen.dart';
import 'super_admin_manage_members.dart';
import 'super_admin_manage_instructors.dart';
import '../../controller/super_admin/user_counts_controller.dart';

class SuperAdminDashboardScreen extends StatefulWidget {
  const SuperAdminDashboardScreen({super.key});

  @override
  State<SuperAdminDashboardScreen> createState() =>
      _SuperAdminDashboardScreenState();
}

class _SuperAdminDashboardScreenState extends State<SuperAdminDashboardScreen> {
  final FlutterSecureStorage storage = const FlutterSecureStorage();
  final UserCountsController userCountsController = UserCountsController();
  Timer? _sessionTimer;
  String username = "Loading...";
  String email = "Loading...";
  int memberCount = 0;
  int instructorCount = 0;


  @override
  void initState() {
    super.initState();
    _loadUserData();
    _fetchCounts();
    _startSessionTimer();
  }

  Future<void> _loadUserData() async {
    String? storedUsername = await storage.read(key: "username");
    String? storedEmail = await storage.read(key: "email");
    setState(() {
      username = storedUsername ?? "Unknown User";
      email = storedEmail ?? "Unknown Email";
    });
  }

  Future<void> _fetchCounts() async {
    try {
      final data = await userCountsController.fetchUserCounts();
      setState(() {
        memberCount = data.members;
        instructorCount = data.instructors;
      });
    } catch (e) {
      print("Error fetching counts: $e");
    }
  }

  void _startSessionTimer() async {
    _sessionTimer = Timer.periodic(const Duration(minutes: 1), (timer) async {
      String? expiresAtStr = await storage.read(key: "expires_at");

      if (expiresAtStr == null) {
        _logout();
        return;
      }

      DateTime expiresAt = DateTime.parse(expiresAtStr);
      if (DateTime.now().isAfter(expiresAt)) {
        _logout();
      }
    });
  }

  @override
  void dispose() {
    _sessionTimer?.cancel();
    super.dispose();
  }

  void _showLogoutDialog() {
    showDialog(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text("Confirm Logout"),
            content: const Text("Are you sure you want to log out?"),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context), // Cancel logout
                child: const Text("Cancel"),
              ),
              TextButton(
                onPressed: _logout, // Proceed with logout
                child: const Text(
                  "Logout",
                  style: TextStyle(color: Colors.red),
                ),
              ),
            ],
          ),
    );
  }

  void _logout() async {
    await storage.deleteAll(); // Clear stored credentials
    Navigator.pushAndRemoveUntil(
      context,
      MaterialPageRoute(builder: (context) => const LoginScreen()),
      (route) => false,
    );
  }

  Future<bool> _isAuthorized(BuildContext context, String requiredRole) async {
    String? rolesJson = await storage.read(key: "roles");

    if (rolesJson != null) {
      List<String> roles;
      try {
        roles = List<String>.from(jsonDecode(rolesJson));
      } catch (e) {
        print("⚠️ Error decoding roles JSON: $e");
        roles = [];
      }

      return roles.contains(requiredRole);
    }

    return false;
  }

  void _navigateToProfile() {
    Navigator.push(
      context,
      MaterialPageRoute(builder: (context) => const ProfileScreen()),
    );
  }

  void _navigateToManageMembers() {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => const SuperAdminManageMembersScreen(),
      ),
    );
  }

  void _navigateToManageInstructors() {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => const SuperAdminManageInstructorsScreen(),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<bool>(
      future: _isAuthorized(context, "Super Admin"),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Scaffold(
            body: Center(child: CircularProgressIndicator()),
          );
        }

        if (snapshot.data == false) {
          return Scaffold(
            body: Center(
              child: AlertDialog(
                title: const Text("Unauthorized Access"),
                content: const Text(
                  "You do not have permission to access this feature.",
                ),
                actions: [
                  TextButton(
                    child: const Text("OK"),
                    onPressed: () {
                      Navigator.pushAndRemoveUntil(
                        context,
                        MaterialPageRoute(
                          builder: (context) => const LoginScreen(),
                        ),
                        (route) => false,
                      );
                    },
                  ),
                ],
              ),
            ),
          );
        }

        return Scaffold(
          appBar: AppBar(title: const Text("Super Admin Dashboard")),
          drawer: Drawer(
            child: ListView(
              children: [
                DrawerHeader(
                  decoration: const BoxDecoration(
                    color: Color.fromRGBO(255, 179, 0, 1),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      GestureDetector(
                        onTap: _navigateToProfile,
                        child: const CircleAvatar(
                          radius: 30,
                          backgroundColor: Colors.white,
                          child: Icon(
                            Icons.person,
                            size: 40,
                            color: Color.fromRGBO(255, 179, 0, 1),
                          ),
                        ),
                      ),
                      const SizedBox(height: 10),
                      Text(
                        username,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 18,
                        ),
                      ),
                      Text(
                        email,
                        style: const TextStyle(
                          color: Colors.white70,
                          fontSize: 14,
                        ),
                      ),
                    ],
                  ),
                ),
                ListTile(
                  leading: const Icon(Icons.people),
                  title: const Text("Manage Members"),
                  onTap: _navigateToManageMembers,
                ),
                ListTile(
                  leading: const Icon(Icons.person),
                  title: const Text("Manage Instructors"),
                  onTap: _navigateToManageInstructors,
                ),
                ListTile(
                  leading: const Icon(Icons.logout),
                  title: const Text("Logout"),
                  onTap: _showLogoutDialog, // Implement logout logic
                ),
              ],
            ),
          ),
          body: Padding(
            padding: const EdgeInsets.all(
              12.0,
            ), // Less padding to reduce overall spacing
            child: GridView.count(
              crossAxisCount: 4,
              crossAxisSpacing: 12, // Reduced spacing between cards
              mainAxisSpacing: 12,
              childAspectRatio: 3.5, // Adjusted for a more compact look
              children: [
                DashboardCard(
                  title: "Total Members",
                  value: "$memberCount",
                  icon: Icons.people,
                  onTap: _navigateToManageMembers,
                ),
                DashboardCard(
                  title: "Total Instructors",
                  value: "$instructorCount",
                  icon: Icons.person,
                  onTap: _navigateToManageInstructors,
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}

class DashboardCard extends StatelessWidget {
  final String title;
  final String value;
  final IconData icon;
  final VoidCallback onTap;

  const DashboardCard({
    super.key,
    required this.title,
    required this.value,
    required this.icon,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Card(
        elevation: 3,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(8),
          side: const BorderSide(
            color: Color.fromRGBO(255, 179, 0, 1),
            width: 1.5,
          ),
        ),
        child: SizedBox(
          height: 30, // Reduced height to make the card smaller
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(
                      title.toUpperCase(),
                      style: const TextStyle(
                        fontSize: 10, // Slightly smaller font
                        fontWeight: FontWeight.bold,
                        color: Colors.blueGrey,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      value,
                      style: const TextStyle(
                        fontSize: 18, // Reduced font size for better fit
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ],
                ),
                Icon(
                  icon,
                  size: 24, // Smaller icon
                  color: Colors.blueGrey.shade300,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
