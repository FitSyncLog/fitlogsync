import 'package:http/http.dart' as http;
import 'dart:convert';
import '../../model/super_admin/user_model.dart';

class MemberController {
  final String apiUrl;
  final String _baseApiUrl = 'http://localhost/fitlogsync/Desktop/api';

  MemberController({required this.apiUrl});

  Future<List<User>> fetchMembers() async {
    try {
      final response = await http.get(Uri.parse(apiUrl));

      if (response.statusCode == 200) {
        List jsonResponse = json.decode(response.body);
        return jsonResponse.map((user) => User.fromJson(user)).toList();
      } else {
        throw Exception('Failed to load members: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Failed to load members: $e');
    }
  }

  // Get member by ID
  Future<User> getMemberById(int userId) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseApiUrl/get_members_information.php?user_id=$userId'),
      );

      if (response.statusCode == 200) {
        final Map<String, dynamic> jsonResponse = json.decode(response.body);
        if (jsonResponse['success'] == true) {
          return User.fromJson(jsonResponse['data']);
        } else {
          throw Exception(jsonResponse['message'] ?? 'Failed to get member');
        }
      } else {
        throw Exception('Failed to get member details: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Failed to get member details: $e');
    }
  }

  // Update member details
  Future<bool> updateMember(User member) async {
    try {
      // Print the request body for debugging
      print('Update request body: ${jsonEncode(member.toJson())}');
      
      final response = await http.post(
        Uri.parse('$_baseApiUrl/update_member.php'),
        headers: <String, String>{
          'Content-Type': 'application/json; charset=UTF-8',
        },
        body: jsonEncode(member.toJson()),
      );

      // Print the response for debugging
      print('Update response: ${response.body}');

      if (response.statusCode == 200) {
        final Map<String, dynamic> result = json.decode(response.body);
        if (result['success'] == true) {
          return true;
        } else {
          throw Exception(result['message'] ?? 'Unknown error occurred');
        }
      } else {
        throw Exception('Failed to update member: ${response.statusCode}');
      }
    } catch (e) {
      print('Update member exception: $e');
      throw Exception('Failed to update member: $e');
    }
  }

  // Delete member
  Future<bool> deleteMember(String accountNumber) async {
    try {
      final response = await http.post(
        Uri.parse('$_baseApiUrl/delete_member.php'),
        headers: <String, String>{
          'Content-Type': 'application/json; charset=UTF-8',
        },
        body: jsonEncode(<String, String>{
          'account_number': accountNumber,
        }),
      );

      if (response.statusCode == 200) {
        final Map<String, dynamic> result = json.decode(response.body);
        return result['success'] == true;
      } else {
        throw Exception('Failed to delete member: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Failed to delete member: $e');
    }
  }
}