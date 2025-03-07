import 'package:http/http.dart' as http;
import 'dart:convert';
import '../../model/super_admin/user_model.dart';

class MemberController {
  final String apiUrl;

  MemberController({required this.apiUrl});

  Future<List<User>> fetchMembers() async {
    final response = await http.get(Uri.parse(apiUrl));

    if (response.statusCode == 200) {
      List jsonResponse = json.decode(response.body);
      return jsonResponse.map((user) => User.fromJson(user)).toList();
    } else {
      throw Exception('Failed to load members');
    }
  }

  // Add a method to update member details
  Future<bool> updateMember(User member) async {
    final response = await http.post(
      Uri.parse('$apiUrl/update'),
      headers: <String, String>{
        'Content-Type': 'application/json; charset=UTF-8',
      },
      body: jsonEncode(<String, dynamic>{
        'username': member.username,
        'email': member.email,
        'account_number': member.accountNumber,
        'status': member.status,
        'subscription_status': member.subscriptionStatus,
        'roles': member.roles,
        'medical_conditions': member.medicalConditions,
        'current_medications': member.currentMedications,
        'previous_injuries': member.previousInjuries,
        'contact_person': member.contactPerson,
        'contact_number': member.contactNumber,
        'relationship': member.relationship,
        'rules_and_policy': member.rulesAndPolicy ? 1 : 0,
        'liability_waiver': member.liabilityWaiver ? 1 : 0,
        'cancellation_and_refund_policy': member.cancellationAndRefundPolicy ? 1 : 0,
      }),
    );

    return response.statusCode == 200;
  }
}
