import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
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
        contentPadding: EdgeInsets.zero, // Remove default padding
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        title: Container(
          padding: const EdgeInsets.all(16.0),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.only(
              topLeft: Radius.circular(10),
              topRight: Radius.circular(10),
            ),
            color: Colors.grey[200],
          ),
          child: Column(
            children: [
              CircleAvatar(
                radius: 25,
                backgroundColor: Color.fromRGBO(255, 179, 0, 1),
                child: Icon(Icons.person, size: 25, color: Colors.white),
              ),
              SizedBox(height: 10),
              Text(
                member.username,
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                textAlign: TextAlign.center,
              ),
            ],
          ),
        ),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Personal Information Section
              _buildSection("Personal Information", [
                _buildInfoTile(Icons.person, "First Name", member.firstname),
                _buildInfoTile(Icons.person, "Middle Name", member.middlename),
                _buildInfoTile(Icons.person, "Last Name", member.lastname),
                _buildInfoTile(Icons.calendar_today, "Date of Birth", _formatDate(member.dateOfBirth)),
                _buildInfoTile(Icons.transgender, "Gender", member.gender),
                _buildInfoTile(Icons.phone, "Phone Number", member.phoneNumber),
                _buildInfoTile(Icons.location_on, "Address", member.address),
              ]),

              // Account Information Section
              _buildSection("Account Information", [
                _buildInfoTile(Icons.email, "Email", member.email),
                _buildInfoTile(Icons.confirmation_number, "Account Number", _formatAccountNumber(member.accountNumber)),
                _buildInfoTile(Icons.check_circle, "Account Status", member.status),
                _buildInfoTile(Icons.date_range, "Registration Date", _ProperDate(member.registrationDate)),
                _buildInfoTile(Icons.subscriptions, "Subscription Status", member.subscriptionStatus),
                _buildInfoTile(Icons.app_registration, "Registered By", member.enrolledBy),
              ]),

              // Contact of Emergency Section
              _buildSection("Contact of Emergency", [
                _buildInfoTile(Icons.person, "Contact Person", member.contactPerson),
                _buildInfoTile(Icons.phone, "Contact Number", member.contactNumber),
                _buildInfoTile(Icons.people, "Relationship", member.relationship),
              ]),

              // Medical Background Section
              _buildSection("Medical Background", [
                _buildInfoTile(Icons.healing, "Medical Conditions", member.medicalConditions),
                _buildInfoTile(Icons.local_hospital, "Current Medications", member.currentMedications),
                _buildInfoTile(Icons.accessible, "Previous Injuries", member.previousInjuries),
              ]),

              // PAR-Q Questions Section
              _buildSection("Physical Activity Readiness Questions (PAR-Q)", [
                _buildParQTile(
                  "Q1: Has your doctor ever said that you have a heart condition and that you should only do physical activity recommended by a doctor?",
                  member.q1,
                ),
                _buildParQTile(
                  "Q2: Do you feel pain in your chest when you perform physical activity?",
                  member.q2,
                ),
                _buildParQTile(
                  "Q3: In the past month, have you had chest pain when you were not doing physical activity?",
                  member.q3,
                ),
                _buildParQTile(
                  "Q4: Do you lose your balance because of dizziness or do you ever lose consciousness?",
                  member.q4,
                ),
                _buildParQTile(
                  "Q5: Do you have a bone or joint problem that could be worsened by a change in your physical activity?",
                  member.q5,
                ),
                _buildParQTile(
                  "Q6: Is your doctor currently prescribing any medication for your blood pressure or heart condition?",
                  member.q6,
                ),
                _buildParQTile(
                  "Q7: Do you have any chronic medical conditions that may affect your ability to exercise safely?",
                  member.q7,
                ),
                _buildParQTile(
                  "Q8: Are you pregnant or have you given birth in the last 6 months?",
                  member.q8,
                ),
                _buildParQTile(
                  "Q9: Do you have any recent injuries or surgeries that may limit your physical activity?",
                  member.q9,
                ),
                _buildParQTile(
                  "Q10: Do you know of any other reason why you should not do physical activity?",
                  member.q10,
                ),
              ]),

              // Waivers Section
              _buildSection("Waivers", [
                _buildWaiverTile("Agree to the Rules and Policy", member.rulesAndPolicy),
                _buildWaiverTile("Agree to the Liability Waiver", member.liabilityWaiver),
                _buildWaiverTile("Agree to the Cancellation Policy", member.cancellationAndRefundPolicy),
              ]),
            ],
          ),
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

// Helper method to format date
String _ProperDate(String dateString) {
  DateTime date = DateTime.parse(dateString);
  return DateFormat('MMMM d, yyyy').format(date);
}

// Helper method to format account number
String _formatAccountNumber(String accountNumber) {
  // Format the account number as 2025-0002-8379-0001
  return accountNumber.replaceAllMapped(RegExp(r'\d{4}'), (match) => '${match.group(0)}-');
}

// Helper method to build a section with a title and content
Widget _buildSection(String title, List<Widget> children) {
  return Container(
    padding: const EdgeInsets.all(16.0),
    margin: const EdgeInsets.symmetric(vertical: 8.0, horizontal: 16.0),
    decoration: BoxDecoration(
      color: Colors.white,
      borderRadius: BorderRadius.circular(8),
      boxShadow: [
        BoxShadow(
          color: Colors.black12,
          offset: Offset(0, 2),
          blurRadius: 6,
        ),
      ],
    ),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          title,
          style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.orange),
          textAlign: TextAlign.center,
        ),
        const SizedBox(height: 8),
        ...children,
      ],
    ),
  );
}

// Helper method to build an information tile
Widget _buildInfoTile(IconData icon, String label, String value) {
  return Padding(
    padding: const EdgeInsets.symmetric(vertical: 4.0),
    child: Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, color: Colors.grey),
        SizedBox(width: 8),
        Expanded(
          child: Text(label, style: TextStyle(fontWeight: FontWeight.bold)),
        ),
        SizedBox(width: 8),
        Expanded(
          child: Text(value),
        ),
      ],
    ),
  );
}

// Helper method to build a PAR-Q tile
Widget _buildParQTile(String question, String answer) {
  return Column(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      Text(question, style: TextStyle(fontWeight: FontWeight.bold)),
      SizedBox(height: 4),
      Text("Answer: $answer"),
      Divider(),
    ],
  );
}

// Helper method to build a waiver tile with checkboxes
Widget _buildWaiverTile(String label, bool isChecked) {
  return Padding(
    padding: const EdgeInsets.symmetric(vertical: 4.0),
    child: Row(
      children: [
        Checkbox(
          value: isChecked,
          onChanged: (bool? value) {}, // Disabled for display purposes
        ),
        SizedBox(width: 8),
        Expanded(
          child: Text(label, style: TextStyle(fontWeight: FontWeight.bold)),
        ),
      ],
    ),
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
                                                icon: const Icon(
                                                  Icons.touch_app,
                                                ),
                                                onPressed:
                                                    () => _showMemberDetails(
                                                      member,
                                                    ),
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
        backgroundColor: const Color.fromRGBO(
          255,
          179,
          0,
          1,
        ), // Set the color to yellow
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
