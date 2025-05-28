// lib/enums/permissions.dart
enum Permission {
  addMembers,
  viewMembers,
  editMembers,
  deleteMembers,
  viewInstructors,
  editInstructors,
  deleteInstructors,
  // Add more permissions as needed
}

// Map roles to their permissions
Map<String, List<Permission>> rolePermissions = {
  'Super Admin': [
    Permission.addMembers,
    Permission.viewMembers,
    Permission.editMembers,
    Permission.deleteMembers,
    Permission.viewInstructors,
    Permission.editInstructors,
    Permission.deleteInstructors,
  ],
  'Admin': [
    Permission.addMembers,
    Permission.viewMembers,
    Permission.editMembers,
    Permission.viewInstructors,
    Permission.editInstructors,
  ],
  'Front Desk': [
    Permission.addMembers,
    Permission.viewMembers,
    Permission.editMembers,
  ],
  'Instructor': [
    Permission.viewMembers,
  ],
  'Member': [],
};
