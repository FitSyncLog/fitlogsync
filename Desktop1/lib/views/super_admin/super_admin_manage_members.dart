import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../model/super_admin/user_model.dart';
import '../../controller/super_admin/user_controller.dart';
import '../../controller/super_admin/member_controller.dart';
import '../front_desk/front_desk_creating_members.dart';
import 'edit_members.dart';
import '../../enums/permissions.dart';

class SuperAdminManageMembersScreen extends StatefulWidget {
  const SuperAdminManageMembersScreen({super.key});

  @override
  State<SuperAdminManageMembersScreen> createState() =>
      _SuperAdminManageMembersScreenState();
}

class _SuperAdminManageMembersScreenState
    extends State<SuperAdminManageMembersScreen> {
  late Future<List<User>> membersFuture = Future.value([]);
  final UserController userController = UserController(
    apiUrl: 'http://localhost/fitlogsync/Desktop/api/get_users.php',
  );

  final MemberController memberController = MemberController(
    apiUrl: 'http://localhost/fitlogsync/Desktop/api/get_users.php',
  );

  // Map to store permission states
  Map<Permission, bool> permissionsMap = {};

  // For searching
  final TextEditingController _searchController = TextEditingController();
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    _loadPermissionsAndData();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadPermissionsAndData() async {
    // Preload permissions first
    await userController.preloadPermissions();

    // Then load member data
    _refreshMemberList();

    // Refresh UI after permissions are loaded
    if (mounted) setState(() {});
  }

  Future<void> _refreshMemberList() async {
    setState(() {
      membersFuture = userController.fetchMembers();
    });
  }

  void _showMemberDetails(User member) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          contentPadding: EdgeInsets.zero, // Remove default padding
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(10),
          ),
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
                  _buildInfoTile(
                    Icons.person,
                    "Middle Name",
                    member.middlename,
                  ),
                  _buildInfoTile(Icons.person, "Last Name", member.lastname),
                  _buildInfoTile(
                    Icons.calendar_today,
                    "Date of Birth",
                    _formatDate(member.dateOfBirth),
                  ),
                  _buildInfoTile(Icons.transgender, "Gender", member.gender),
                  _buildInfoTile(
                    Icons.phone,
                    "Phone Number",
                    member.phoneNumber,
                  ),
                  _buildInfoTile(Icons.location_on, "Address", member.address),
                ]),

                // Account Information Section
                _buildSection("Account Information", [
                  _buildInfoTile(Icons.email, "Email", member.email),
                  _buildInfoTile(
                    Icons.confirmation_number,
                    "Account Number",
                    _formatAccountNumber(member.accountNumber),
                  ),
                  _buildInfoTile(
                    Icons.check_circle,
                    "Account Status",
                    member.status,
                  ),
                  _buildInfoTile(
                    Icons.date_range,
                    "Registration Date",
                    _formatDate(member.registrationDate),
                  ),
                  _buildInfoTile(
                    Icons.subscriptions,
                    "Subscription Status",
                    member.subscriptionStatus,
                  ),
                  _buildInfoTile(
                    Icons.app_registration,
                    "Registered By",
                    member.enrolledBy,
                  ),
                ]),

                // Contact of Emergency Section
                _buildSection("Contact of Emergency", [
                  _buildInfoTile(
                    Icons.person,
                    "Contact Person",
                    member.contactPerson,
                  ),
                  _buildInfoTile(
                    Icons.phone,
                    "Contact Number",
                    member.contactNumber,
                  ),
                  _buildInfoTile(
                    Icons.people,
                    "Relationship",
                    member.relationship,
                  ),
                ]),

                // Medical Background Section
                _buildSection("Medical Background", [
                  _buildInfoTile(
                    Icons.healing,
                    "Medical Conditions",
                    member.medicalConditions,
                  ),
                  _buildInfoTile(
                    Icons.local_hospital,
                    "Current Medications",
                    member.currentMedications,
                  ),
                  _buildInfoTile(
                    Icons.accessibility,
                    "Previous Injuries",
                    member.previousInjuries,
                  ),
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
                  _buildWaiverTile(
                    "Agree to the Rules and Policy",
                    member.rulesAndPolicy,
                  ),
                  _buildWaiverTile(
                    "Agree to the Liability Waiver",
                    member.liabilityWaiver,
                  ),
                  _buildWaiverTile(
                    "Agree to the Cancellation Policy",
                    member.cancellationAndRefundPolicy,
                  ),
                ]),
              ],
            ),
          ),
          actions: [
            // Add edit button if user has permission
            if (userController.hasPermissionSync(Permission.editMembers))
              TextButton(
                child: const Text("Edit Member"),
                onPressed: () {
                  Navigator.of(context).pop();
                  _navigateToEditMember(member);
                },
              ),
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

  // Navigate to edit member screen
  void _navigateToEditMember(User member) async {
    final result = await Navigator.push(
      context,
      MaterialPageRoute(builder: (context) => EditMemberScreen(member: member)),
    );

    // Refresh the member list when returning from edit screen if there's a result
    if (result != null) {
      _refreshMemberList();
    }
  }

  // Delete member confirmation dialog
  void _showDeleteConfirmationDialog(User member) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text("Confirm Deletion"),
          content: Text(
            "Are you sure you want to delete ${member.firstname} ${member.lastname}? This action cannot be undone.",
          ),
          actions: [
            TextButton(
              child: const Text("Cancel"),
              onPressed: () {
                Navigator.of(context).pop();
              },
            ),
            TextButton(
              child: const Text("Delete", style: TextStyle(color: Colors.red)),
              onPressed: () async {
                Navigator.of(context).pop();
                await _deleteMember(member);
              },
            ),
          ],
        );
      },
    );
  }

  // Delete member function
  Future<void> _deleteMember(User member) async {
    try {
      // Show loading indicator
      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (BuildContext context) {
          return const Center(child: CircularProgressIndicator());
        },
      );

      final success = await memberController.deleteMember(member.accountNumber);

      // Close loading indicator
      if (mounted) Navigator.of(context).pop();

      if (success) {
        // Show success message
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Member deleted successfully'),
              backgroundColor: Colors.green,
            ),
          );
        }

        // Refresh the list
        _refreshMemberList();
      } else {
        // Show error message
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Failed to delete member'),
              backgroundColor: Colors.red,
            ),
          );
        }
      }
    } catch (e) {
      // Close loading indicator
      if (mounted) Navigator.of(context).pop();

      // Show error message
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e'), backgroundColor: Colors.red),
        );
      }
    }
  }

  // Helper method to format date
  String _formatDate(String dateString) {
    try {
      DateTime date = DateTime.parse(dateString);
      return DateFormat('MMMM d, yyyy').format(date);
    } catch (e) {
      return 'Invalid date';
    }
  }

  // Helper method to format account number
  String _formatAccountNumber(String accountNumber) {
    // Format the account number as 2025-0002-8379-0001
    if (accountNumber.length != 16) return accountNumber;

    return accountNumber
        .replaceAllMapped(RegExp(r'.{4}'), (match) => '${match.group(0)}-')
        .substring(0, 19); // Remove trailing dash
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
          BoxShadow(color: Colors.black12, offset: Offset(0, 2), blurRadius: 6),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: Colors.orange,
            ),
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
          Expanded(child: Text(value)),
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

  void _navigateToCreateMember() async {
    final result = await Navigator.push(
      context,
      MaterialPageRoute(builder: (context) => const CreateMemberScreen()),
    );

    // Refresh the list when returning from create screen
    if (result != null) {
      _refreshMemberList();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Manage Members"),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _refreshMemberList,
          ),
        ],
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 16),
            // Search field
            TextField(
              controller: _searchController,
              decoration: InputDecoration(
                hintText: 'Search members...',
                prefixIcon: const Icon(Icons.search),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(8.0),
                ),
                suffixIcon:
                    _searchController.text.isNotEmpty
                        ? IconButton(
                          icon: const Icon(Icons.clear),
                          onPressed: () {
                            setState(() {
                              _searchController.clear();
                              _searchQuery = '';
                            });
                          },
                        )
                        : null,
              ),
              onChanged: (value) {
                setState(() {
                  _searchQuery = value.toLowerCase();
                });
              },
            ),
            const SizedBox(height: 16),
            Expanded(
              child: FutureBuilder<List<User>>(
                future: membersFuture,
                builder: (context, snapshot) {
                  if (snapshot.connectionState == ConnectionState.waiting) {
                    return const Center(child: CircularProgressIndicator());
                  } else if (snapshot.hasError) {
                    return Center(child: Text('Error: ${snapshot.error}'));
                  } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
                    return const Center(child: Text('No members found'));
                  } else {
                    // Filter members based on search query
                    final filteredMembers =
                        snapshot.data!.where((member) {
                          final fullName =
                              '${member.firstname} ${member.lastname}'
                                  .toLowerCase();
                          final email = member.email.toLowerCase();
                          final accountNumber =
                              member.accountNumber.toLowerCase();

                          return _searchQuery.isEmpty ||
                              fullName.contains(_searchQuery) ||
                              email.contains(_searchQuery) ||
                              accountNumber.contains(_searchQuery);
                        }).toList();

                    if (filteredMembers.isEmpty) {
                      return const Center(
                        child: Text('No members match your search'),
                      );
                    }

                    return SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: ConstrainedBox(
                        constraints: BoxConstraints(
                          minWidth: MediaQuery.of(context).size.width,
                        ),
                        child: DataTable(
                          headingRowColor: MaterialStateColor.resolveWith(
                            (states) => const Color.fromRGBO(255, 179, 0, 1),
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
                              filteredMembers.map((member) {
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
                                        _formatDate(member.registrationDate),
                                      ),
                                    ),
                                    DataCell(
                                      Row(
                                        children: [
                                          IconButton(
                                            icon: const Icon(Icons.info),
                                            tooltip: 'View Details',
                                            onPressed:
                                                () =>
                                                    _showMemberDetails(member),
                                          ),
                                          if (userController.hasPermissionSync(
                                            Permission.editMembers,
                                          ))
                                            IconButton(
                                              icon: const Icon(Icons.edit),
                                              tooltip: 'Edit Member',
                                              onPressed:
                                                  () => _navigateToEditMember(
                                                    member,
                                                  ),
                                            ),
                                          if (userController.hasPermissionSync(
                                            Permission.deleteMembers,
                                          ))
                                            IconButton(
                                              icon: const Icon(
                                                Icons.delete,
                                                color: Colors.red,
                                              ),
                                              tooltip: 'Delete Member',
                                              onPressed:
                                                  () =>
                                                      _showDeleteConfirmationDialog(
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
              ),
            ),
          ],
        ),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: _navigateToCreateMember,
        backgroundColor: const Color.fromRGBO(255, 179, 0, 1),
        child: const Icon(Icons.add, color: Colors.black),
      ),
    );
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
  Widget _subscriptionBadge(String? subscriptionStatus) {
    Color badgeColor;
    String displayStatus = subscriptionStatus ?? 'No Subscription';

    switch (displayStatus.toLowerCase()) {
      case "active":
        badgeColor = Colors.green;
        break;
      case "expired":
        badgeColor = Colors.red;
        break;
      case "no subscription":
        badgeColor = Colors.grey;
        break;
      default:
        badgeColor = Colors.orange; // For 'Pending' or other statuses
    }

    return Container(
      padding: const EdgeInsets.symmetric(vertical: 4, horizontal: 8),
      decoration: BoxDecoration(
        color: badgeColor,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(displayStatus, style: const TextStyle(color: Colors.white)),
    );
  }
}
