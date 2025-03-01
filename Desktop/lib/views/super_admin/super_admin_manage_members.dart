import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../model/super_admin/user_model.dart';
import '../../controller/super_admin/user_controller.dart';
import '../front_desk/front_desk_creating_members.dart';

class SuperAdminManageMembersScreen extends StatefulWidget {
  const SuperAdminManageMembersScreen({super.key});

  @override
  State<SuperAdminManageMembersScreen> createState() =>
      _SuperAdminManageMembersScreenState();
}

class _SuperAdminManageMembersScreenState
    extends State<SuperAdminManageMembersScreen> {
  late Future<List<User>> membersFuture;
  final UserController userController = UserController(
    apiUrl: 'http://localhost/fitlogsync/Desktop/database/get_users.php',
  );

  @override
  void initState() {
    super.initState();
    membersFuture = userController.fetchMembers();
  }

  void _showMemberDetails(User member) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(member.username),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text("Email: ${member.email}"),
              Text("Account No: ${member.accountNumber}"),
              Text("Status: ${member.status}"),
              Text("Subscription: ${member.subscriptionStatus}"),
              Text("Role: ${member.roles.join(', ')}"),
            ],
          ),
          actions: [
            TextButton(
              child: const Text("Close"),
              onPressed: () {
                Navigator.of(context).pop();
              },
            ),
          ],
        );
      },
    );
  }

  void _navigateToCreateMember() {
    Navigator.push(
      context,
      MaterialPageRoute(builder: (context) => const CreateMemberScreen()),
    );
  }

  void _editMember(User member) {
    print("Edit member: ${member.username}");
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Manage Members"),
        actions: [
          IconButton(
            icon: const Icon(Icons.search),
            onPressed: () {
              // Implement search functionality
            },
          ),
        ],
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              "Members List",
              style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            Expanded(
              child: LayoutBuilder(
                builder: (context, constraints) {
                  return FutureBuilder<List<User>>(
                    future: membersFuture,
                    builder: (context, snapshot) {
                      if (snapshot.connectionState == ConnectionState.waiting) {
                        return const Center(child: CircularProgressIndicator());
                      } else if (snapshot.hasError) {
                        return Center(child: Text('Error: ${snapshot.error}'));
                      } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
                        return const Center(child: Text('No members found'));
                      } else {
                        return SingleChildScrollView(
                          scrollDirection: Axis.horizontal,
                          child: ConstrainedBox(
                            constraints: BoxConstraints(
                              minWidth:
                                  constraints.maxWidth, // Responsive width
                            ),
                            child: DataTable(
                              headingRowColor: MaterialStateColor.resolveWith(
                                (states) =>
                                    const Color.fromRGBO(255, 179, 0, 1),
                              ),
                              headingTextStyle: const TextStyle(
                                fontWeight: FontWeight.bold,
                                color: Colors.black87,
                              ),
                              columnSpacing: 20.0,
                              dataRowHeight: 60.0,
                              columns: const [
                                DataColumn(label: Text('Account No')),
                                DataColumn(label: Text('Name')),
                                DataColumn(label: Text('Email')),
                                DataColumn(label: Text('Gender')),
                                DataColumn(label: Text('Birthday')),
                                DataColumn(label: Text('Account Status')),
                                DataColumn(label: Text('Subscription Status')),
                                DataColumn(label: Text('Registration Date')),
                                DataColumn(label: Text('Actions')),
                              ],
                              rows:
                                  snapshot.data!.map((member) {
                                    return DataRow(
                                      cells: [
                                        DataCell(Text(member.accountNumber)),
                                        DataCell(
                                          Text(
                                            '${member.firstname} ${member.lastname}',
                                          ),
                                        ),
                                        DataCell(Text(member.email)),
                                        DataCell(Text(member.gender)),
                                        DataCell(
                                          Text(_formatDate(member.dateOfBirth)),
                                        ),
                                        DataCell(_statusBadge(member.status)),
                                        DataCell(
                                          _subscriptionBadge(
                                            member.subscriptionStatus,
                                          ),
                                        ),
                                        DataCell(
                                          Text(
                                            _formatDate(
                                              member.registrationDate,
                                            ),
                                          ),
                                        ),
                                        DataCell(
                                          Row(
                                            children: [
                                              IconButton(
                                                icon: const Icon(Icons.info),
                                                onPressed:
                                                    () => _showMemberDetails(
                                                      member,
                                                    ),
                                              ),
                                              IconButton(
                                                icon: const Icon(Icons.edit),
                                                onPressed:
                                                    () => _editMember(member),
                                              ),
                                            ],
                                          ),
                                        ),
                                      ],
                                    );
                                  }).toList(),
                            ),
                          ),
                        );
                      }
                    },
                  );
                },
              ),
            ),
          ],
        ),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: _navigateToCreateMember,
        backgroundColor: const Color.fromRGBO(255, 179, 0, 1), // Set the color to yellow
        child: const Icon(
          Icons.add,
          color: Colors.black,
        ), // Optional: Change icon color for better contrast
      ),
    );
  }

  // Helper function to format date (Month Day, Year)
  String _formatDate(String dateString) {
    try {
      DateTime date = DateTime.parse(dateString);
      return DateFormat(
        'MMMM d, yyyy',
      ).format(date); // Example: February 2, 2015
    } catch (e) {
      return 'Invalid date'; // Fallback if parsing fails
    }
  }

  // Helper function for displaying account status
  Widget _statusBadge(String status) {
    Color badgeColor;
    if (status.toLowerCase() == "active") {
      badgeColor = Colors.green;
    } else if (status.toLowerCase() == "pending") {
      badgeColor = const Color.fromRGBO(255, 179, 0, 1);
    } else {
      badgeColor = Colors.red;
    }
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 4, horizontal: 8),
      decoration: BoxDecoration(
        color: badgeColor,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(status, style: const TextStyle(color: Colors.white)),
    );
  }

  // Helper function for displaying subscription status
  Widget _subscriptionBadge(String subscriptionStatus) {
    Color badgeColor;
    switch (subscriptionStatus.toLowerCase()) {
      case "active":
        badgeColor = Colors.green;
        break;
      case "expired":
        badgeColor = Colors.red;
        break;
      default:
        badgeColor = Colors.grey;
    }
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 4, horizontal: 8),
      decoration: BoxDecoration(
        color: badgeColor,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(
        subscriptionStatus,
        style: const TextStyle(color: Colors.white),
      ),
    );
  }
}
