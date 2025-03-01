import 'package:flutter/material.dart';
import '../../../views/profile/profile_screen.dart'; // Adjust the import according to your project structure
import '../../../enums/user_roles.dart'; // Import the UserRole enum
import '../../views/front_desk/front_desk_creating_members.dart';

class AppSidebar extends StatelessWidget {
  final String username;
  final String email;
  final VoidCallback onLogout;
  final UserRole userRole; // Add the userRole parameter

  const AppSidebar({
    super.key,
    required this.username,
    required this.email,
    required this.onLogout,
    required this.userRole, // Require the userRole
  });

  void _navigateToProfile(BuildContext context) {
    Navigator.push(
      context,
      MaterialPageRoute(builder: (context) => const ProfileScreen()),
    );
  }

  void _navigateToCreateMember(BuildContext context) {
    Navigator.push(
      context,
      MaterialPageRoute(builder: (context) => const CreateMemberScreen()),
    );
  }

  @override
  Widget build(BuildContext context) {
    // Debug print to check the role in the sidebar
    print("Sidebar user role: $userRole");

    return Drawer(
      child: ListView(
        padding: EdgeInsets.zero,
        children: [
          DrawerHeader(
            decoration: const BoxDecoration(color: Color.fromRGBO(255, 179, 0, 1)),
            child: GestureDetector(
              onTap: () => _navigateToProfile(context),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const CircleAvatar(
                    radius: 30,
                    backgroundColor: Colors.white,
                    child: Icon(Icons.person, size: 40, color: Color.fromRGBO(255, 179, 0, 1)),
                  ),
                  const SizedBox(height: 10),
                  Text(username, style: const TextStyle(color: Colors.white, fontSize: 18)),
                  Text(email, style: const TextStyle(color: Colors.white70, fontSize: 14)),
                ],
              ),
            ),
          ),
          ListTile(
            leading: const Icon(Icons.dashboard),
            title: const Text("Dashboard"),
            onTap: () {},
          ),
          if (userRole == UserRole.superAdmin || userRole == UserRole.admin) ...[
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
          ],
          if (userRole == UserRole.frontDesk) ...[
            ListTile(
              leading: const Icon(Icons.people),
              title: const Text("Front Desk"),
              onTap: () {
                // Navigate to members screen
              },
            ),
            ListTile(
              leading: const Icon(Icons.person_add),
              title: const Text("Create Members"),
              onTap: () => _navigateToCreateMember(context),
            ),
          ],
          if (userRole == UserRole.instructor)
            ListTile(
              leading: const Icon(Icons.school),
              title: const Text("Courses"),
              onTap: () {
                // Navigate to courses screen
              },
            ),
          ListTile(
            leading: const Icon(Icons.logout),
            title: const Text("Logout"),
            onTap: onLogout,
          ),
        ],
      ),
    );
  }
}
