import 'dart:convert';
import 'package:http/http.dart' as http;
import '../../model/super_admin/user_model.dart';

class UserController {
  final String apiUrl;

  UserController({required this.apiUrl});

  Future<List<User>> fetchMembers() async {
    final response = await http.get(Uri.parse(apiUrl));

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        final List<dynamic> usersJson = data['users'];
        return usersJson.map((json) => User.fromJson(json)).toList();
      }
    } else {
      throw Exception('Failed to load members');
    }
    return [];
  }

  Future<List<User>> fetchInstructors() async {
    final response = await http.get(Uri.parse(apiUrl));

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        final List<dynamic> usersJson = data['users'];
        return usersJson.map((json) => User.fromJson(json)).toList();
      }
    } else {
      throw Exception('Failed to load members');
    }
    return [];
  }
}
