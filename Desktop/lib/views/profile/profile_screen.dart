import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '/controller/profile/profile_controller.dart';

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
  TextEditingController _contactPersonController = TextEditingController();
  TextEditingController _contactNumberController = TextEditingController();
  // Remove the relationship controller as we'll use dropdown instead
  String? _selectedGender;
  String? _selectedRelationship;

  final List<String> _genders = ["Male", "Female", "Prefer not to say"];
  final List<String> _relationships = ["Parent", "Sibling", "Spouse", "Friend", "Relative", "Other"];
  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;
  bool _hasChanges = false;
  final FlutterSecureStorage _storage = const FlutterSecureStorage();
  final ProfileController _profileController = ProfileController();
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _loadUserData();
  }

  Future<void> _loadUserData() async {
    setState(() {
      _isLoading = true;
    });

    try {
      // Get profile data from secure storage
      final profileData = await _profileController.getUserProfileFromStorage();

      setState(() {
        _usernameController.text = profileData['username'] ?? "";
        _emailController.text = profileData['email'] ?? "";
        _firstNameController.text = profileData['firstname'] ?? "";
        _middleNameController.text = profileData['middlename'] ?? "";
        _lastNameController.text = profileData['lastname'] ?? "";
        _phoneNumberController.text = profileData['phone_number'] ?? "";
        _addressController.text = profileData['address'] ?? "";
        _dateOfBirthController.text = profileData['date_of_birth'] ?? "";
        _contactPersonController.text = profileData['emergency_contact_person'] ?? "";
        _contactNumberController.text = profileData['emergency_contact_number'] ?? "";
        
        // Ensure gender is valid
        _selectedGender = _genders.contains(profileData['gender']) 
                           ? profileData['gender'] 
                           : null;
                           
        // Ensure relationship is valid
        _selectedRelationship = _relationships.contains(profileData['emergency_relationship']) 
                               ? profileData['emergency_relationship'] 
                               : null;
      });
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error loading profile: ${e.toString()}'))
      );
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  Future<void> _saveUserData() async {
    setState(() {
      _isLoading = true;
    });

    try {
      await _profileController.updateProfileFromScreen(
        context,
        _usernameController,
        _emailController,
        _firstNameController,
        _middleNameController,
        _lastNameController,
        _phoneNumberController,
        _addressController,
        _dateOfBirthController,
        _selectedGender,
        _passwordController,
        _confirmPasswordController,
        _contactPersonController,
        _contactNumberController,
        _selectedRelationship, // Pass selected relationship instead of controller
      );

      setState(() {
        _hasChanges = false;
        // Clear password fields after successful update
        _passwordController.clear();
        _confirmPasswordController.clear();
      });
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error saving profile: ${e.toString()}'))
      );
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
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
            if (_isLoading)
              const Padding(
                padding: EdgeInsets.all(10.0),
                child: CircularProgressIndicator(
                  color: Colors.white,
                  strokeWidth: 2.0,
                ),
              )
            else
              IconButton(
                icon: const Icon(Icons.save),
                onPressed: _hasChanges ? _saveUserData : null,
              ),
          ],
        ),
        body: _isLoading
            ? const Center(child: CircularProgressIndicator())
            : SingleChildScrollView(
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
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color.fromRGBO(255, 179, 0, 1)),
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
                      "Emergency Contact",
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color.fromRGBO(255, 179, 0, 1)),
                    ),
                    const SizedBox(height: 10),
                    _buildProfileField("Contact Person", Icons.person, _contactPersonController),
                    _buildProfileField("Contact Number", Icons.phone, _contactNumberController),
                    _buildRelationshipDropdown(), // Add the relationship dropdown here
                    const SizedBox(height: 20),
                    const Text(
                      "Account Information",
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color.fromRGBO(255, 179, 0, 1)),
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

  // New method for relationship dropdown
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
                prefixIcon: Icon(Icons.family_restroom, color: Colors.grey),
                border: InputBorder.none,
              ),
              value: _selectedRelationship,
              items: _relationships.map((String value) {
                return DropdownMenuItem<String>(
                  value: value,
                  child: Text(value),
                );
              }).toList(),
              onChanged: (String? newValue) {
                setState(() {
                  _selectedRelationship = newValue;
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