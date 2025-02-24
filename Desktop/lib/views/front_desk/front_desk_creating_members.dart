import 'package:flutter/material.dart';
import '/controller/front_desk/create_member_controller.dart'; // Import the controller

class CreateMemberScreen extends StatefulWidget {
  const CreateMemberScreen({super.key});

  @override
  State<CreateMemberScreen> createState() => _CreateMemberScreenState();
}

class _CreateMemberScreenState extends State<CreateMemberScreen> {
  // Controllers for text fields
  final TextEditingController _usernameController = TextEditingController();
  final TextEditingController _firstnameController = TextEditingController();
  final TextEditingController _middlenameController = TextEditingController();
  final TextEditingController _lastnameController = TextEditingController();
  final TextEditingController _dateofbirthController = TextEditingController();
  final TextEditingController _addressController = TextEditingController();
  final TextEditingController _phonenumberController = TextEditingController();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  final TextEditingController _confirmPasswordController = TextEditingController();
  final TextEditingController _contactPersonController = TextEditingController();
  final TextEditingController _contactNumberController = TextEditingController();
  final TextEditingController _medicalConditionsController = TextEditingController();
  final TextEditingController _currentMedicationsController = TextEditingController();
  final TextEditingController _previousInjuriesController = TextEditingController();

  // State for dropdowns and checkboxes
  String? _gender;
  String? _relationship;
  String _q1 = "No";
  String _q2 = "No";
  String _q3 = "No";
  String _q4 = "No";
  String _q5 = "No";
  String _q6 = "No";
  String _q7 = "No";
  String _q8 = "No";
  String _q9 = "No";
  String _q10 = "No";
  bool _waiverRules = false;
  bool _waiverLiability = false;
  bool _waiverCancel = false;

  final List<String> _genders = ["Male", "Female", "Other"];
  final List<String> _relationships = ["Friend", "Family", "Other"];

  final MemberController _memberController = MemberController();

  Future<void> _createMember() async {
    bool success = await _memberController.createMember(
      username: _usernameController.text,
      firstname: _firstnameController.text,
      middlename: _middlenameController.text,
      lastname: _lastnameController.text,
      dateofbirth: _dateofbirthController.text,
      gender: _gender ?? "",
      address: _addressController.text,
      phonenumber: _phonenumberController.text,
      email: _emailController.text,
      password: _passwordController.text,
      confirmPassword: _confirmPasswordController.text,
      contactPerson: _contactPersonController.text,
      contactNumber: _contactNumberController.text,
      relationship: _relationship ?? "",
      medicalConditions: _medicalConditionsController.text,
      currentMedications: _currentMedicationsController.text,
      previousInjuries: _previousInjuriesController.text,
      q1: _q1,
      q2: _q2,
      q3: _q3,
      q4: _q4,
      q5: _q5,
      q6: _q6,
      q7: _q7,
      q8: _q8,
      q9: _q9,
      q10: _q10,
      waiverRules: _waiverRules,
      waiverLiability: _waiverLiability,
      waiverCancel: _waiverCancel,
    );

    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Row(
            children: [
              Icon(Icons.check, color: Colors.green),
              SizedBox(width: 8),
              Text('Member created successfully'),
            ],
          ),
        ),
      );
      // Reset the form fields
      _resetForm();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to create member')),
      );
    }
  }

  void _resetForm() {
    _usernameController.clear();
    _firstnameController.clear();
    _middlenameController.clear();
    _lastnameController.clear();
    _dateofbirthController.clear();
    _addressController.clear();
    _phonenumberController.clear();
    _emailController.clear();
    _passwordController.clear();
    _confirmPasswordController.clear();
    _contactPersonController.clear();
    _contactNumberController.clear();
    _medicalConditionsController.clear();
    _currentMedicationsController.clear();
    _previousInjuriesController.clear();
    setState(() {
      _gender = null;
      _relationship = null;
      _q1 = "No";
      _q2 = "No";
      _q3 = "No";
      _q4 = "No";
      _q5 = "No";
      _q6 = "No";
      _q7 = "No";
      _q8 = "No";
      _q9 = "No";
      _q10 = "No";
      _waiverRules = false;
      _waiverLiability = false;
      _waiverCancel = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Create Member")),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              "Personal Information",
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),
            Row(
              children: [
                Expanded(
                  child: _buildProfileField(
                    "First Name",
                    Icons.person,
                    _firstnameController,
                    placeholder: "Enter your first name",
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: _buildProfileField(
                    "Middle Name",
                    Icons.person,
                    _middlenameController,
                    placeholder: "Enter your middle name",
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: _buildProfileField(
                    "Last Name",
                    Icons.person,
                    _lastnameController,
                    placeholder: "Enter your last name",
                  ),
                ),
              ],
            ),
            const SizedBox(height: 10),
            Row(
              children: [
                Expanded(
                  child: _buildDateField(
                    "Date of Birth",
                    Icons.calendar_today,
                    _dateofbirthController,
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: _buildGenderDropdown(),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: _buildProfileField(
                    "Phone Number",
                    Icons.phone,
                    _phonenumberController,
                    placeholder: "Enter your phone number",
                  ),
                ),
              ],
            ),
            const SizedBox(height: 10),
            _buildProfileField(
              "Address",
              Icons.location_on,
              _addressController,
              placeholder: "Enter your address",
            ),
            const SizedBox(height: 20),
            const Text(
              "Account Information",
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),
            _buildProfileField(
              "Username",
              Icons.account_circle,
              _usernameController,
              placeholder: "Enter your username",
            ),
            const SizedBox(height: 10),
            _buildProfileField(
              "Email",
              Icons.email,
              _emailController,
              placeholder: "Enter your email",
            ),
            const SizedBox(height: 10),
            _buildProfileFieldWithVisibility(
              "Password",
              Icons.lock,
              _passwordController,
              isConfirmPassword: false,
            ),
            const SizedBox(height: 10),
            _buildProfileFieldWithVisibility(
              "Confirm Password",
              Icons.lock,
              _confirmPasswordController,
              isConfirmPassword: true,
            ),
            const SizedBox(height: 20),
            const Text(
              "Contact of Emergency",
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),
            Row(
              children: [
                Expanded(
                  child: _buildProfileField(
                    "Contact Person",
                    Icons.person,
                    _contactPersonController,
                    placeholder: "Enter contact person",
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: _buildProfileField(
                    "Contact Number",
                    Icons.phone,
                    _contactNumberController,
                    placeholder: "Enter contact number",
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: _buildRelationshipDropdown(),
                ),
              ],
            ),
            const SizedBox(height: 20),
            const Text(
              "Medical Background",
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),
            _buildProfileField(
              "Medical Conditions",
              Icons.medical_services,
              _medicalConditionsController,
              placeholder: "Enter any medical conditions",
            ),
            const SizedBox(height: 10),
            _buildProfileField(
              "Current Medications",
              Icons.healing,
              _currentMedicationsController,
              placeholder: "Enter current medications",
            ),
            const SizedBox(height: 10),
            _buildProfileField(
              "Previous Injuries",
              Icons.sick, // Corrected icon
              _previousInjuriesController,
              placeholder: "Enter previous injuries",
            ),
            const SizedBox(height: 20),
            const Text(
              "Physical Activity Readiness Questions (PAR-Q)",
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),
            _buildRadioButton(
              "Q1: Heart condition diagnosed by a doctor?",
              _q1,
              (value) {
                setState(() {
                  _q1 = value!;
                });
              },
            ),
            _buildRadioButton(
              "Q2: Chest pain during physical activity?",
              _q2,
              (value) {
                setState(() {
                  _q2 = value!;
                });
              },
            ),
            _buildRadioButton(
              "Q3: Chest pain in the past month when not physically active?",
              _q3,
              (value) {
                setState(() {
                  _q3 = value!;
                });
              },
            ),
            _buildRadioButton(
              "Q4: Lose balance because of dizziness or lose consciousness?",
              _q4,
              (value) {
                setState(() {
                  _q4 = value!;
                });
              },
            ),
            _buildRadioButton(
              "Q5: Bone or joint problem worsened by physical activity?",
              _q5,
              (value) {
                setState(() {
                  _q5 = value!;
                });
              },
            ),
            _buildRadioButton(
              "Q6: Doctor prescribing medication for blood pressure or heart condition?",
              _q6,
              (value) {
                setState(() {
                  _q6 = value!;
                });
              },
            ),
            _buildRadioButton(
              "Q7: Chronic medical conditions affecting ability to exercise safely?",
              _q7,
              (value) {
                setState(() {
                  _q7 = value!;
                });
              },
            ),
            _buildRadioButton(
              "Q8: Pregnant or given birth in the last 6 months?",
              _q8,
              (value) {
                setState(() {
                  _q8 = value!;
                });
              },
            ),
            _buildRadioButton(
              "Q9: Recent injuries or surgeries limiting physical activity?",
              _q9,
              (value) {
                setState(() {
                  _q9 = value!;
                });
              },
            ),
            _buildRadioButton(
              "Q10: Any other reason not to do physical activity?",
              _q10,
              (value) {
                setState(() {
                  _q10 = value!;
                });
              },
            ),
            const SizedBox(height: 20),
            const Text(
              "Waiver and Agreements",
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),
            _buildCheckbox("I agree to the Rules and Policy", _waiverRules),
            _buildCheckbox("I agree to the Liability Waiver", _waiverLiability),
            _buildCheckbox(
              "I agree to the Cancellation and Refund Policy",
              _waiverCancel,
            ),
            const SizedBox(height: 20),
            ElevatedButton(
              onPressed: _createMember,
              child: const Text('Create Member'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildProfileField(
    String label,
    IconData icon,
    TextEditingController controller, {
    String? placeholder,
    bool obscureText = false,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Card(
        elevation: 2,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16.0),
          child: TextFormField(
            controller: controller,
            obscureText: obscureText,
            decoration: InputDecoration(
              labelText: label,
              hintText: placeholder,
              prefixIcon: Icon(icon, color: Colors.grey),
              border: InputBorder.none,
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildProfileFieldWithVisibility(
    String label,
    IconData icon,
    TextEditingController controller, {
    required bool isConfirmPassword,
    bool obscureText = true,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Card(
        elevation: 2,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16.0),
          child: TextFormField(
            controller: controller,
            obscureText: obscureText,
            decoration: InputDecoration(
              labelText: label,
              prefixIcon: Icon(icon, color: Colors.grey),
              suffixIcon: IconButton(
                icon: Icon(
                  obscureText ? Icons.visibility_off : Icons.visibility,
                  color: Colors.grey,
                ),
                onPressed: () {
                  setState(() {
                    obscureText = !obscureText;
                  });
                },
              ),
              border: InputBorder.none,
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildDateField(
    String label,
    IconData icon,
    TextEditingController controller,
  ) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Card(
        elevation: 2,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16.0),
          child: TextFormField(
            controller: controller,
            decoration: InputDecoration(
              labelText: label,
              prefixIcon: Icon(icon, color: Colors.grey),
              border: InputBorder.none,
            ),
            readOnly: true,
            onTap: () async {
              DateTime? pickedDate = await showDatePicker(
                context: context,
                initialDate: DateTime.now(),
                firstDate: DateTime(1900),
                lastDate: DateTime(2101),
              );
              if (pickedDate != null) {
                controller.text = pickedDate.toLocal().toString().split(' ')[0];
              }
            },
          ),
        ),
      ),
    );
  }

  Widget _buildGenderDropdown() {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Card(
        elevation: 2,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16.0),
          child: DropdownButtonHideUnderline(
            child: DropdownButtonFormField<String>(
              decoration: InputDecoration(
                labelText: "Gender",
                prefixIcon: Icon(Icons.transgender, color: Colors.grey),
                border: InputBorder.none,
              ),
              value: _gender,
              items: _genders.map((String value) {
                return DropdownMenuItem<String>(
                  value: value,
                  child: Text(value),
                );
              }).toList(),
              onChanged: (String? newValue) {
                setState(() {
                  _gender = newValue;
                });
              },
              isExpanded: true,
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildRelationshipDropdown() {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Card(
        elevation: 2,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16.0),
          child: DropdownButtonHideUnderline(
            child: DropdownButtonFormField<String>(
              decoration: InputDecoration(
                labelText: "Relationship",
                prefixIcon: Icon(Icons.people, color: Colors.grey),
                border: InputBorder.none,
              ),
              value: _relationship,
              items: _relationships.map((String value) {
                return DropdownMenuItem<String>(
                  value: value,
                  child: Text(value),
                );
              }).toList(),
              onChanged: (String? newValue) {
                setState(() {
                  _relationship = newValue;
                });
              },
              isExpanded: true,
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildRadioButton(String label, String value, Function(String?) onChanged) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label),
        Row(
          children: [
            Radio<String>(
              value: "Yes",
              groupValue: value,
              onChanged: onChanged,
            ),
            const Text("Yes"),
            Radio<String>(
              value: "No",
              groupValue: value,
              onChanged: onChanged,
            ),
            const Text("No"),
          ],
        ),
        const SizedBox(height: 10),
      ],
    );
  }

  Widget _buildCheckbox(String label, bool value) {
    return Row(
      children: [
        Checkbox(
          value: value,
          onChanged: (bool? newValue) {
            setState(() {
              if (label.contains("Rules")) {
                _waiverRules = newValue!;
              } else if (label.contains("Liability")) {
                _waiverLiability = newValue!;
              } else if (label.contains("Cancel")) {
                _waiverCancel = newValue!;
              }
            });
          },
        ),
        Text(label),
      ],
    );
  }
}
