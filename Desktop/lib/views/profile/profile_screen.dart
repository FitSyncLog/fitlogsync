import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  TextEditingController _usernameController = TextEditingController();
  TextEditingController _emailController = TextEditingController();
  TextEditingController _firstNameController = TextEditingController();
  TextEditingController _middleNameController = TextEditingController();
  TextEditingController _lastNameController = TextEditingController();
  TextEditingController _phoneNumberController = TextEditingController();
  TextEditingController _addressController = TextEditingController();
  TextEditingController _passwordController = TextEditingController();
  TextEditingController _confirmPasswordController = TextEditingController();
  TextEditingController _dateOfBirthController = TextEditingController();
  String? _selectedGender;

  final List<String> _genders = ["Male", "Female", "Prefer not to say"];
  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;
  bool _hasChanges = false;
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  @override
  void initState() {
    super.initState();
    _loadUserData();
  }

  Future<void> _loadUserData() async {
    String? username = await _storage.read(key: "username");
    String? email = await _storage.read(key: "email");
    String? firstName = await _storage.read(key: "firstname");
    String? middleName = await _storage.read(key: "middlename");
    String? lastName = await _storage.read(key: "lastname");
    String? phoneNumber = await _storage.read(key: "phone_number");
    String? address = await _storage.read(key: "address");
    String? dateOfBirth = await _storage.read(key: "date_of_birth");
    String? gender = await _storage.read(key: "gender");

    setState(() {
      _usernameController.text = username ?? "Unknown";
      _emailController.text = email ?? "Unknown";
      _firstNameController.text = firstName ?? "Unknown";
      _middleNameController.text = middleName ?? "Unknown";
      _lastNameController.text = lastName ?? "Unknown";
      _phoneNumberController.text = phoneNumber ?? "Unknown";
      _addressController.text = address ?? "Unknown";
      _dateOfBirthController.text = dateOfBirth ?? "";

      // Ensure gender is valid
      _selectedGender = _genders.contains(gender) ? gender : null;
    });
  }

  Future<void> _saveUserData() async {
    await _storage.write(key: "username", value: _usernameController.text);
    await _storage.write(key: "email", value: _emailController.text);
    await _storage.write(key: "firstname", value: _firstNameController.text);
    await _storage.write(key: "middlename", value: _middleNameController.text);
    await _storage.write(key: "lastname", value: _lastNameController.text);
    await _storage.write(key: "date_of_birth", value: _dateOfBirthController.text);
    await _storage.write(key: "gender", value: _selectedGender ?? "Unknown");
    await _storage.write(key: "phone_number", value: _phoneNumberController.text);
    await _storage.write(key: "address", value: _addressController.text);
    // Optionally, update the user data on the server
    setState(() {
      _hasChanges = false;
    });
  }

  void _togglePasswordVisibility(bool isConfirmPassword) {
    setState(() {
      if (isConfirmPassword) {
        _obscureConfirmPassword = !_obscureConfirmPassword;
      } else {
        _obscurePassword = !_obscurePassword;
      }
      _hasChanges = true;
    });
  }

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: _onWillPop,
      child: Scaffold(
        appBar: AppBar(
          title: const Text("Profile"),
          actions: [
            IconButton(
              icon: const Icon(Icons.save),
              onPressed: _saveUserData,
            ),
          ],
        ),
        body: SingleChildScrollView(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Card(
                elevation: 4,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    children: [
                      const CircleAvatar(
                        radius: 50,
                        backgroundColor: Color.fromRGBO(255, 179, 0, 1),
                        child: Icon(Icons.person, size: 60, color: Colors.white),
                      ),
                      const SizedBox(height: 16),
                      SizedBox(
                        width: 200, // Adjust the width to your preference
                        child: TextFormField(
                          controller: _usernameController,
                          textAlign: TextAlign.center,
                          style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                          decoration: const InputDecoration(
                            border: UnderlineInputBorder(),
                            hintText: "Edit Username",
                          ),
                          onChanged: (text) {
                            setState(() {
                              _hasChanges = true;
                            });
                          },
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 20),
              const Text(
                "Personal Information",
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 10),
              Row(
                children: [
                  Expanded(
                    flex: 4,
                    child: _buildProfileField("First Name", Icons.person, _firstNameController),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    flex: 3,
                    child: _buildProfileField("Middle Name", Icons.person, _middleNameController),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    flex: 4,
                    child: _buildProfileField("Last Name", Icons.person, _lastNameController),
                  ),
                ],
              ),
              const SizedBox(height: 10),
              Row(
                children: [
                  Expanded(
                    flex: 4,
                    child: _buildDateField("Date of Birth", Icons.calendar_today, _dateOfBirthController),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    flex: 3,
                    child: _buildGenderDropdown(),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    flex: 4,
                    child: _buildProfileField("Phone Number", Icons.phone, _phoneNumberController),
                  ),
                ],
              ),
              _buildProfileField("Address", Icons.location_on, _addressController),
              const SizedBox(height: 20),
              const Text(
                "Account Information",
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 10),
              _buildProfileField("Email", Icons.email, _emailController),
              _buildProfileFieldWithVisibility(
                "Password",
                Icons.lock,
                _passwordController,
                _obscurePassword,
                isConfirmPassword: false,
              ),
              _buildProfileFieldWithVisibility(
                "Confirm Password",
                Icons.lock,
                _confirmPasswordController,
                _obscureConfirmPassword,
                isConfirmPassword: true,
              ),
            ],
          ),
        ),
      ),
    );
  }

  Future<bool> _onWillPop() async {
    if (_hasChanges) {
      return await showDialog(
        context: context,
        builder: (context) => AlertDialog(
          title: const Text("Unsaved Changes"),
          content: const Text("Do you want to save them before exiting?"),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context).pop(false);
                _saveUserData();
              },
              child: const Text("Yes"),
            ),
            TextButton(
              onPressed: () {
                Navigator.of(context).pop(true);
              },
              child: const Text("No"),
            ),
          ],
        ),
      );
    }
    return true;
  }

  Widget _buildProfileField(String label, IconData icon, TextEditingController controller, {bool obscureText = false}) {
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
            onChanged: (text) {
              setState(() {
                _hasChanges = true;
              });
            },
            decoration: InputDecoration(
              labelText: label,
              prefixIcon: Icon(icon, color: Colors.grey),
              border: InputBorder.none,
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildProfileFieldWithVisibility(String label, IconData icon, TextEditingController controller, bool obscureText, {required bool isConfirmPassword}) {
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
            onChanged: (text) {
              setState(() {
                _hasChanges = true;
              });
            },
            decoration: InputDecoration(
              labelText: label,
              prefixIcon: Icon(icon, color: Colors.grey),
              suffixIcon: IconButton(
                icon: Icon(
                  obscureText ? Icons.visibility_off : Icons.visibility,
                  color: Colors.grey,
                ),
                onPressed: () => _togglePasswordVisibility(isConfirmPassword),
              ),
              border: InputBorder.none,
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildDateField(String label, IconData icon, TextEditingController controller) {
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
                setState(() {
                  _hasChanges = true;
                });
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
              value: _selectedGender,
              items: _genders.map((String value) {
                return DropdownMenuItem<String>(
                  value: value,
                  child: Text(value),
                );
              }).toList(),
              onChanged: (String? newValue) {
                setState(() {
                  _selectedGender = newValue;
                  _hasChanges = true;
                });
              },
              isExpanded: true,
            ),
          ),
        ),
      ),
    );
  }
}
