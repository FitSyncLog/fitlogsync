import 'dart:convert';
import 'package:http/http.dart' as http;
import '/model/super_admin/user_counts_model.dart';

class UserCountsController {
  Future<UserCounts> fetchUserCounts() async {
    try {
      final response = await http.get(Uri.parse('http://localhost/fitlogsync/Desktop/database/get_user_counts.php'));

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = jsonDecode(response.body);
        if (data["success"] == true) {
          return UserCounts.fromJson(data);
        } else {
          throw Exception("Failed to fetch user counts: ${data['message']}");
        }
      } else {
        throw Exception("Server error: ${response.statusCode}");
      }
    } catch (e) {
      throw Exception("Error fetching user counts: $e");
    }
  }
}
