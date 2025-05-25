import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../enums/permissions.dart';
import '../../model/super_admin/user_model.dart';

class UserController {
  final String apiUrl;
  final FlutterSecureStorage storage = const FlutterSecureStorage();
  Map<String, bool> _cachedPermissions = {};

  UserController({required this.apiUrl});

  Future<List<User>> fetchMembers() async {
    // First check permissions
    bool hasViewPermission = await hasPermission(Permission.viewMembers);
    if (!hasViewPermission) {
      throw Exception('Permission denied');
    }
    
    final response = await http.get(Uri.parse(apiUrl));
    if (response.statusCode == 200) {
      final List<dynamic> data = jsonDecode(response.body)['users'];
      return data.map((json) => User.fromJson(json)).toList();
    } else {
      throw Exception('Failed to load members');
    }
  }

  Future<List<User>> fetchInstructors() async {
    bool hasViewPermission = await hasPermission(Permission.viewInstructors);
    if (!hasViewPermission) {
      throw Exception('Permission denied');
    }
    
    final response = await http.get(Uri.parse(apiUrl));
    if (response.statusCode == 200) {
      final List<dynamic> data = jsonDecode(response.body)['users'];
      return data.map((json) => User.fromJson(json)).toList();
    } else {
      throw Exception('Failed to load instructors');
    }
  }

  Future<void> editMember(User member) async {
    bool hasEditPermission = await hasPermission(Permission.editMembers);
    if (!hasEditPermission) {
      throw Exception('Permission denied');
    }
    // Implement edit member logic
  }

  Future<void> deleteMember(int memberId) async {
    bool hasDeletePermission = await hasPermission(Permission.deleteMembers);
    if (!hasDeletePermission) {
      throw Exception('Permission denied');
    }
    // Implement delete member logic
  }

  Future<bool> hasPermission(Permission permission) async {
    // Check if we've already cached this permission
    String permKey = permission.toString();
    if (_cachedPermissions.containsKey(permKey)) {
      return _cachedPermissions[permKey]!;
    }
    
    String? rolesJson = await storage.read(key: "roles");
    if (rolesJson != null) {
      List<String> roles = List<String>.from(jsonDecode(rolesJson));
      for (String role in roles) {
        if (rolePermissions[role]?.contains(permission) == true) {
          _cachedPermissions[permKey] = true;
          return true;
        }
      }
    }
    
    _cachedPermissions[permKey] = false;
    return false;
  }
  
  // This synchronous version can be used in build methods
  bool hasPermissionSync(Permission permission) {
    String permKey = permission.toString();
    return _cachedPermissions[permKey] ?? false;
  }
  
  // Method to preload permissions for better UX
  Future<void> preloadPermissions() async {
    // Load all permissions that might be needed
    for (var permission in Permission.values) {
      await hasPermission(permission);
    }
  }
}