import 'package:flutter/material.dart';
import '../../model/super_admin/user_model.dart';
import '../../controller/super_admin/user_controller.dart'; // Import the UserController

class SuperAdminManageInstructorsScreen extends StatefulWidget {
  const SuperAdminManageInstructorsScreen({super.key});

  @override
  State<SuperAdminManageInstructorsScreen> createState() =>
      _SuperAdminManageInstructorsScreenState();
}

class _SuperAdminManageInstructorsScreenState
    extends State<SuperAdminManageInstructorsScreen> {
  late Future<List<User>> instructorsFuture;
  final UserController userController = UserController(
    apiUrl:
        'http://localhost/fitlogsync/Desktop/api/get_instructors.php',
  );

  @override
  void initState() {
    super.initState();
    instructorsFuture = userController.fetchInstructors();
  }

  void _showInstructorDetails(User instructor) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(instructor.username),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text("Email: ${instructor.email}"),
              Text("Role: ${instructor.roles.join(', ')}"),
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

  void _editInstructor(User instructor) {
    print("Edit instructor: ${instructor.username}");
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Manage Instructors"),
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
              "Instructors List",
              style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            Expanded(
              child: FutureBuilder<List<User>>(
                future: instructorsFuture,
                builder: (context, snapshot) {
                  if (snapshot.connectionState == ConnectionState.waiting) {
                    return const Center(child: CircularProgressIndicator());
                  } else if (snapshot.hasError) {
                    return Center(child: Text('Error: ${snapshot.error}'));
                  } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
                    return const Center(child: Text('No instructors found'));
                  } else {
                    return LayoutBuilder(
                      builder: (context, constraints) {
                        return SizedBox(
                          width: double.infinity, // Expand table to full width
                          child: DataTable(
                            columnSpacing: constraints.maxWidth * 0.1,
                            headingRowColor: MaterialStateColor.resolveWith(
                              (states) => const Color.fromRGBO(255, 179, 0, 1),
                            ),
                            headingTextStyle: const TextStyle(
                              fontWeight: FontWeight.bold,
                              color: Colors.black87,
                            ),
                            columns: const [
                              DataColumn(label: Expanded(child: Text('Name'))),
                              DataColumn(label: Expanded(child: Text('Email'))),
                              DataColumn(label: Expanded(child: Text('Role'))),
                              DataColumn(label: Text('Actions')),
                            ],
                            rows: snapshot.data!.map((instructor) {
                              return DataRow(
                                cells: [
                                  DataCell(
                                    Text(
                                      '${instructor.firstname} ${instructor.lastname}',
                                    ),
                                  ),
                                  DataCell(Text(instructor.email)),
                                  DataCell(Text(instructor.roles.join(', '))),
                                  DataCell(
                                    Row(
                                      children: [
                                        IconButton(
                                          icon: const Icon(Icons.info),
                                          onPressed: () =>
                                              _showInstructorDetails(
                                            instructor,
                                          ),
                                        ),
                                        IconButton(
                                          icon: const Icon(Icons.edit),
                                          onPressed: () =>
                                              _editInstructor(instructor),
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              );
                            }).toList(),
                          ),
                        );
                      },
                    );
                  }
                },
              ),
            ),
          ],
        ),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {},
        child: const Icon(Icons.add),
      ),
    );
  }
}
