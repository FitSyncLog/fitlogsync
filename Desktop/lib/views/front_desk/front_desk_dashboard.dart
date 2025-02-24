import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'dart:convert';
import '../login_screen.dart';
import '../profile_screen.dart';
import '../front_desk/front_desk_creating_members.dart';

class FrontDeskDashboardScreen extends StatefulWidget {
  const FrontDeskDashboardScreen({super.key});

  @override
  State<FrontDeskDashboardScreen> createState() =>
      _FrontDeskDashboardScreenState();
}

class _FrontDeskDashboardScreenState extends State<FrontDeskDashboardScreen> {
  final FlutterSecureStorage storage = const FlutterSecureStorage();
  bool _isLoggingOut = false;
  String username = "Loading...";
  String email = "Loading...";

  @override
  void initState() {
    super.initState();
    _loadUserData();
  }

  Future<void> _loadUserData() async {
    String? storedUsername = await storage.read(key: "username");
    String? storedEmail = await storage.read(key: "email");

    setState(() {
      username = storedUsername ?? "Unknown User";
      email = storedEmail ?? "Unknown Email";
    });
  }

  Future<void> _logout(BuildContext context) async {
    setState(() {
      _isLoggingOut = true;
    });

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return AlertDialog(
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const CircularProgressIndicator(),
              const SizedBox(height: 15),
              const Text("Logging out..."),
            ],
          ),
        );
      },
    );

    await Future.delayed(Duration(seconds: 2));

    await storage.deleteAll();

    if (!mounted) return;

    Navigator.of(context).pop();
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

  void _navigateToCreateMember() {
    Navigator.push(
      context,
      MaterialPageRoute(builder: (context) => const CreateMemberScreen()),
    );
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<bool>(
      future: _isAuthorized(context, "Front Desk"),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Center(child: CircularProgressIndicator());
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
          appBar: AppBar(
            title: const Text("Font Desk Dashboard"),
            actions: [
              IconButton(
                icon: const Icon(Icons.notifications),
                onPressed: () {},
              ),
              IconButton(icon: const Icon(Icons.settings), onPressed: () {}),
            ],
          ),
          drawer: Drawer(
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                DrawerHeader(
                  decoration: const BoxDecoration(
                    color: Color.fromRGBO(255, 179, 0, 1),
                  ),
                  child: GestureDetector(
                    onTap: _navigateToProfile,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const CircleAvatar(
                          radius: 30,
                          backgroundColor: Colors.white,
                          child: Icon(
                            Icons.person,
                            size: 40,
                            color: Color.fromRGBO(255, 179, 0, 1),
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
                ),
                ListTile(
                  leading: const Icon(Icons.dashboard),
                  title: const Text("Dashboard"),
                  onTap: () {},
                ),
                ListTile(
                  leading: const Icon(Icons.people),
                  title: const Text("Members"),
                  onTap: () => _navigateToCreateMember(),
                ),
                ListTile(
                  leading: const Icon(Icons.analytics),
                  title: const Text("Analytics"),
                  onTap: () {},
                ),
                ListTile(
                  leading: const Icon(Icons.settings),
                  title: const Text("Settings"),
                  onTap: () {},
                ),
                ListTile(
                  leading: const Icon(Icons.logout),
                  title: const Text("Logout"),
                  onTap: () => _logout(context),
                ),
              ],
            ),
          ),
          body: Padding(
            padding: const EdgeInsets.all(16.0),
            child: GridView.count(
              crossAxisCount: 2,
              crossAxisSpacing: 16,
              mainAxisSpacing: 16,
              children: const [
                DashboardCard(
                  title: "Users",
                  value: "1,234",
                  icon: Icons.people,
                ),
                DashboardCard(
                  title: "Revenue",
                  value: "\$12,345",
                  icon: Icons.monetization_on,
                ),
                DashboardCard(
                  title: "Orders",
                  value: "567",
                  icon: Icons.shopping_cart,
                ),
                DashboardCard(
                  title: "Feedback",
                  value: "89",
                  icon: Icons.feedback,
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

  const DashboardCard({
    super.key,
    required this.title,
    required this.value,
    required this.icon,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 4,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 40, color: const Color.fromRGBO(255, 179, 0, 1)),
            const SizedBox(height: 10),
            Text(
              title,
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 5),
            Text(
              value,
              style: const TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.w600,
                color: Color.fromRGBO(255, 179, 0, 1),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
