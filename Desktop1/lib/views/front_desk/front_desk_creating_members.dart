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

  // Visibility state for password fields
  bool _isPasswordVisible = false;
  bool _isConfirmPasswordVisible = false;

  // Validation state
  Map<String, String?> _errors = {};

  final List<String> _genders = ["Male", "Female", "Other"];
  final List<String> _relationships = ["Parent", "Sibling", "Spouse", "Friend", "Relative", "Other"];

  final MemberController _memberController = MemberController();

  Future<void> _createMember() async {
    // Clear previous errors
    setState(() {
      _errors.clear();
    });

    // Validate required fields
    if (!_validateRequiredFields()) {
      setState(() {
        _errors['required'] = "All fields are required except middle name.";
      });
      return;
    }

    // Validate email format
    if (!_validateEmail(_emailController.text)) {
      setState(() {
        _errors['email'] = "Invalid email address.";
      });
      return;
    }

    // Validate phone number format
    if (!_validatePhoneNumber(_phonenumberController.text)) {
      setState(() {
        _errors['phone'] = "Phone number must be 11 digits and start with '09'.";
      });
      return;
    }

    // Validate password match
    if (_passwordController.text != _confirmPasswordController.text) {
      setState(() {
        _errors['password'] = "Passwords do not match.";
      });
      return;
    }

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

  bool _validateRequiredFields() {
    bool isValid = true;
    if (_usernameController.text.isEmpty) {
      _errors['username'] = "Username is required.";
      isValid = false;
    }
    if (_firstnameController.text.isEmpty) {
      _errors['firstname'] = "First name is required.";
      isValid = false;
    }
    if (_lastnameController.text.isEmpty) {
      _errors['lastname'] = "Last name is required.";
      isValid = false;
    }
    if (_dateofbirthController.text.isEmpty) {
      _errors['dateofbirth'] = "Date of birth is required.";
      isValid = false;
    }
    if (_gender == null) {
      _errors['gender'] = "Gender is required.";
      isValid = false;
    }
    if (_addressController.text.isEmpty) {
      _errors['address'] = "Address is required.";
      isValid = false;
    }
    if (_phonenumberController.text.isEmpty) {
      _errors['phone'] = "Phone number is required.";
      isValid = false;
    }
    if (_emailController.text.isEmpty) {
      _errors['email'] = "Email is required.";
      isValid = false;
    }
    if (_passwordController.text.isEmpty) {
      _errors['password'] = "Password is required.";
      isValid = false;
    }
    if (_confirmPasswordController.text.isEmpty) {
      _errors['confirmPassword'] = "Confirm password is required.";
      isValid = false;
    }
    if (_contactPersonController.text.isEmpty) {
      _errors['contactPerson'] = "Contact person is required.";
      isValid = false;
    }
    if (_contactNumberController.text.isEmpty) {
      _errors['contactNumber'] = "Contact number is required.";
      isValid = false;
    }
    if (_relationship == null) {
      _errors['relationship'] = "Relationship is required.";
      isValid = false;
    }
    if (!_waiverRules) {
      _errors['waiverRules'] = "You must agree to the Rules and Policy.";
      isValid = false;
    }
    if (!_waiverLiability) {
      _errors['waiverLiability'] = "You must agree to the Liability Waiver.";
      isValid = false;
    }
    if (!_waiverCancel) {
      _errors['waiverCancel'] = "You must agree to the Cancellation and Refund Policy.";
      isValid = false;
    }
    return isValid;
  }

  bool _validateEmail(String email) {
    final emailRegExp = RegExp(r'^[^@]+@[^@]+\.[^@]+');
    return emailRegExp.hasMatch(email);
  }

  bool _validatePhoneNumber(String phoneNumber) {
    final phoneRegExp = RegExp(r'^09\d{9}$');
    return phoneRegExp.hasMatch(phoneNumber);
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
      _isPasswordVisible = false;
      _isConfirmPasswordVisible = false;
      _errors.clear();
    });
  }

  Future<bool> _onWillPop() async {
    bool hasChanges = _usernameController.text.isNotEmpty ||
        _firstnameController.text.isNotEmpty ||
        _middlenameController.text.isNotEmpty ||
        _lastnameController.text.isNotEmpty ||
        _dateofbirthController.text.isNotEmpty ||
        _addressController.text.isNotEmpty ||
        _phonenumberController.text.isNotEmpty ||
        _emailController.text.isNotEmpty ||
        _passwordController.text.isNotEmpty ||
        _confirmPasswordController.text.isNotEmpty ||
        _contactPersonController.text.isNotEmpty ||
        _contactNumberController.text.isNotEmpty ||
        _medicalConditionsController.text.isNotEmpty ||
        _currentMedicationsController.text.isNotEmpty ||
        _previousInjuriesController.text.isNotEmpty ||
        _gender != null ||
        _relationship != null ||
        _waiverRules ||
        _waiverLiability ||
        _waiverCancel;

    if (hasChanges) {
      bool? saveChanges = await showDialog<bool>(
        context: context,
        builder: (context) => AlertDialog(
          title: Text('Save Changes?'),
          content: Text('Do you want to save the changes?'),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(false),
              child: Text('No'),
            ),
            TextButton(
              onPressed: () async {
                await _createMember();
                Navigator.of(context).pop(true);
              },
              child: Text('Yes'),
            ),
          ],
        ),
      );
      return saveChanges ?? false;
    }
    return true;
  }

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: _onWillPop,
      child: Scaffold(
        appBar: AppBar(title: const Text("Create Member")),
        body: SingleChildScrollView(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                "Personal Information",
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color.fromRGBO(255, 179, 0, 1)),
              ),
              const SizedBox(height: 10),
              Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildProfileField(
                          "First Name",
                          Icons.person,
                          _firstnameController,
                          placeholder: "Enter your first name",
                        ),
                        if (_errors['firstname'] != null)
                          Text(
                            _errors['firstname']!,
                            style: TextStyle(color: Colors.red),
                          ),
                      ],
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
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildProfileField(
                          "Last Name",
                          Icons.person,
                          _lastnameController,
                          placeholder: "Enter your last name",
                        ),
                        if (_errors['lastname'] != null)
                          Text(
                            _errors['lastname']!,
                            style: TextStyle(color: Colors.red),
                          ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 10),
              Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildDateField(
                          "Date of Birth",
                          Icons.calendar_today,
                          _dateofbirthController,
                        ),
                        if (_errors['dateofbirth'] != null)
                          Text(
                            _errors['dateofbirth']!,
                            style: TextStyle(color: Colors.red),
                          ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildGenderDropdown(),
                        if (_errors['gender'] != null)
                          Text(
                            _errors['gender']!,
                            style: TextStyle(color: Colors.red),
                          ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildProfileField(
                          "Phone Number",
                          Icons.phone,
                          _phonenumberController,
                          placeholder: "Enter your phone number",
                        ),
                        if (_errors['phone'] != null)
                          Text(
                            _errors['phone']!,
                            style: TextStyle(color: Colors.red),
                          ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 10),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildProfileField(
                    "Address",
                    Icons.location_on,
                    _addressController,
                    placeholder: "Enter your address",
                  ),
                  if (_errors['address'] != null)
                    Text(
                      _errors['address']!,
                      style: TextStyle(color: Colors.red),
                    ),
                ],
              ),
              const SizedBox(height: 20),
              const Text(
                "Account Information",
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color.fromRGBO(255, 179, 0, 1)),
              ),
              const SizedBox(height: 10),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildProfileField(
                    "Username",
                    Icons.account_circle,
                    _usernameController,
                    placeholder: "Enter your username",
                  ),
                  if (_errors['username'] != null)
                    Text(
                      _errors['username']!,
                      style: TextStyle(color: Colors.red),
                    ),
                ],
              ),
              const SizedBox(height: 10),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildProfileField(
                    "Email",
                    Icons.email,
                    _emailController,
                    placeholder: "Enter your email",
                  ),
                  if (_errors['email'] != null)
                    Text(
                      _errors['email']!,
                      style: TextStyle(color: Colors.red),
                    ),
                ],
              ),
              const SizedBox(height: 10),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildProfileFieldWithVisibility(
                    "Password",
                    Icons.lock,
                    _passwordController,
                    isConfirmPassword: false,
                    obscureText: !_isPasswordVisible,
                    onVisibilityToggle: () {
                      setState(() {
                        _isPasswordVisible = !_isPasswordVisible;
                      });
                    },
                  ),
                  if (_errors['password'] != null)
                    Text(
                      _errors['password']!,
                      style: TextStyle(color: Colors.red),
                    ),
                ],
              ),
              const SizedBox(height: 10),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildProfileFieldWithVisibility(
                    "Confirm Password",
                    Icons.lock,
                    _confirmPasswordController,
                    isConfirmPassword: true,
                    obscureText: !_isConfirmPasswordVisible,
                    onVisibilityToggle: () {
                      setState(() {
                        _isConfirmPasswordVisible = !_isConfirmPasswordVisible;
                      });
                    },
                  ),
                  if (_errors['confirmPassword'] != null)
                    Text(
                      _errors['confirmPassword']!,
                      style: TextStyle(color: Colors.red),
                    ),
                ],
              ),
              const SizedBox(height: 20),
              const Text(
                "Contact of Emergency",
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color.fromRGBO(255, 179, 0, 1)),
              ),
              const SizedBox(height: 10),
              Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildProfileField(
                          "Contact Person",
                          Icons.person,
                          _contactPersonController,
                          placeholder: "Enter contact person",
                        ),
                        if (_errors['contactPerson'] != null)
                          Text(
                            _errors['contactPerson']!,
                            style: TextStyle(color: Colors.red),
                          ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildProfileField(
                          "Contact Number",
                          Icons.phone,
                          _contactNumberController,
                          placeholder: "Enter contact number",
                        ),
                        if (_errors['contactNumber'] != null)
                          Text(
                            _errors['contactNumber']!,
                            style: TextStyle(color: Colors.red),
                          ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildRelationshipDropdown(),
                        if (_errors['relationship'] != null)
                          Text(
                            _errors['relationship']!,
                            style: TextStyle(color: Colors.red),
                          ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 20),
              const Text(
                "Medical Background",
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color.fromRGBO(255, 179, 0, 1)),
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
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color.fromRGBO(255, 179, 0, 1)),
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
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color.fromRGBO(255, 179, 0, 1)),
              ),
              const SizedBox(height: 10),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildCheckbox("I agree to the Rules and Policy", _waiverRules),
                  if (_errors['waiverRules'] != null)
                    Text(
                      _errors['waiverRules']!,
                      style: TextStyle(color: Colors.red),
                    ),
                ],
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildCheckbox("I agree to the Liability Waiver", _waiverLiability),
                  if (_errors['waiverLiability'] != null)
                    Text(
                      _errors['waiverLiability']!,
                      style: TextStyle(color: Colors.red),
                    ),
                ],
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildCheckbox(
                    "I agree to the Cancellation and Refund Policy",
                    _waiverCancel,
                  ),
                  if (_errors['waiverCancel'] != null)
                    Text(
                      _errors['waiverCancel']!,
                      style: TextStyle(color: Colors.red),
                    ),
                ],
              ),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: _createMember,
                child: const Text('Create Member'),
              ),
            ],
          ),
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
    required bool obscureText,
    required VoidCallback onVisibilityToggle,
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
                onPressed: onVisibilityToggle,
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
