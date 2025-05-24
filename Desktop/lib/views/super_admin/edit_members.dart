import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../model/super_admin/user_model.dart';
import '../../controller/super_admin/member_controller.dart';

class EditMemberScreen extends StatefulWidget {
  final User member;

  const EditMemberScreen({super.key, required this.member});

  @override
  State<EditMemberScreen> createState() => _EditMemberScreenState();
}

class _EditMemberScreenState extends State<EditMemberScreen> {
  final _formKey = GlobalKey<FormState>();
  late User _editedMember;
  bool _isLoading = false;
  final MemberController _memberController = MemberController(
    apiUrl: 'http://localhost/fitlogsync/Desktop/api/get_users.php',
  );

  @override
  void initState() {
    super.initState();
    // Create a copy of the member for editing
    _editedMember = User.fromJson(widget.member.toJson());
    
    // Set default values for fields that might be null
    _editedMember.middlename ??= '';
    _editedMember.status ??= 'Inactive';
    _editedMember.subscriptionStatus ??= 'Pending';
    _editedMember.medicalConditions ??= '';
    _editedMember.currentMedications ??= '';
    _editedMember.previousInjuries ??= '';
    _editedMember.contactPerson ??= '';
    _editedMember.contactNumber ??= '';
    _editedMember.relationship ??= '';
    _editedMember.rulesAndPolicy ??= false;
    _editedMember.liabilityWaiver ??= false;
    _editedMember.cancellationAndRefundPolicy ??= false;
  }

  Future<void> _saveMember() async {
    if (_formKey.currentState!.validate()) {
      _formKey.currentState!.save();
      
      setState(() {
        _isLoading = true;
      });

      try {
        bool success = await _memberController.updateMember(_editedMember);
        if (success) {
          if (!mounted) return;
          Navigator.pop(context, _editedMember);
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Member updated successfully'),
              backgroundColor: Colors.green,
            ),
          );
        } else {
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Failed to update member'),
              backgroundColor: Colors.red,
            ),
          );
        }
      } catch (e) {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error: $e'),
            backgroundColor: Colors.red,
          ),
        );
      } finally {
        if (mounted) {
          setState(() {
            _isLoading = false;
          });
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Edit Member: ${widget.member.firstname} ${widget.member.lastname}'),
      ),
      body: _isLoading
        ? const Center(child: CircularProgressIndicator())
        : SingleChildScrollView(
            padding: const EdgeInsets.all(16.0),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildPersonalInfoSection(),
                  const SizedBox(height: 20),
                  _buildAccountInfoSection(),
                  const SizedBox(height: 20),
                  _buildEmergencyContactSection(),
                  const SizedBox(height: 20),
                  _buildMedicalBackgroundSection(),
                  const SizedBox(height: 20),
                  _buildWaiversSection(),
                  const SizedBox(height: 40),
                  _buildSubmitButton(),
                ],
              ),
            ),
          ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          title,
          style: const TextStyle(
            fontSize: 18, 
            fontWeight: FontWeight.bold,
            color: Color.fromRGBO(255, 179, 0, 1),
          ),
        ),
        const Divider(),
        const SizedBox(height: 10),
      ],
    );
  }

  Widget _buildPersonalInfoSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildSectionTitle('Personal Information'),
        Row(
          children: [
            Expanded(
              child: TextFormField(
                initialValue: _editedMember.firstname,
                decoration: const InputDecoration(
                  labelText: 'First Name',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter first name';
                  }
                  return null;
                },
                onSaved: (value) {
                  _editedMember.firstname = value ?? '';
                },
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: TextFormField(
                initialValue: _editedMember.middlename,
                decoration: const InputDecoration(
                  labelText: 'Middle Name',
                  border: OutlineInputBorder(),
                ),
                onSaved: (value) {
                  _editedMember.middlename = value ?? '';
                },
              ),
            ),
          ],
        ),
        const SizedBox(height: 16),
        Row(
          children: [
            Expanded(
              child: TextFormField(
                initialValue: _editedMember.lastname,
                decoration: const InputDecoration(
                  labelText: 'Last Name',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter last name';
                  }
                  return null;
                },
                onSaved: (value) {
                  _editedMember.lastname = value ?? '';
                },
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: DropdownButtonFormField<String>(
                value: _editedMember.gender,
                decoration: const InputDecoration(
                  labelText: 'Gender',
                  border: OutlineInputBorder(),
                ),
                items: const [
                  DropdownMenuItem(value: 'Male', child: Text('Male')),
                  DropdownMenuItem(value: 'Female', child: Text('Female')),
                  DropdownMenuItem(value: 'Other', child: Text('Other')),
                ],
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please select gender';
                  }
                  return null;
                },
                onChanged: (newValue) {
                  if (newValue != null) {
                    setState(() {
                      _editedMember.gender = newValue;
                    });
                  }
                },
                onSaved: (value) {
                  _editedMember.gender = value ?? '';
                },
              ),
            ),
          ],
        ),
        const SizedBox(height: 16),
        TextFormField(
          initialValue: _editedMember.dateOfBirth,
          decoration: InputDecoration(
            labelText: 'Date of Birth',
            border: const OutlineInputBorder(),
            suffixIcon: IconButton(
              icon: const Icon(Icons.calendar_today),
              onPressed: () async {
                final DateTime? picked = await showDatePicker(
                  context: context,
                  initialDate: DateTime.tryParse(_editedMember.dateOfBirth) ?? DateTime.now(),
                  firstDate: DateTime(1900),
                  lastDate: DateTime.now(),
                );
                if (picked != null) {
                  setState(() {
                    _editedMember.dateOfBirth = DateFormat('yyyy-MM-dd').format(picked);
                  });
                }
              },
            ),
          ),
          validator: (value) {
            if (value == null || value.isEmpty) {
              return 'Please enter date of birth';
            }
            return null;
          },
          onSaved: (value) {
            _editedMember.dateOfBirth = value ?? '';
          },
        ),
        const SizedBox(height: 16),
        TextFormField(
          initialValue: _editedMember.phoneNumber,
          decoration: const InputDecoration(
            labelText: 'Phone Number',
            border: OutlineInputBorder(),
          ),
          validator: (value) {
            if (value == null || value.isEmpty) {
              return 'Please enter phone number';
            }
            return null;
          },
          onSaved: (value) {
            _editedMember.phoneNumber = value ?? '';
          },
        ),
        const SizedBox(height: 16),
        TextFormField(
          initialValue: _editedMember.address,
          decoration: const InputDecoration(
            labelText: 'Address',
            border: OutlineInputBorder(),
          ),
          maxLines: 2,
          validator: (value) {
            if (value == null || value.isEmpty) {
              return 'Please enter address';
            }
            return null;
          },
          onSaved: (value) {
            _editedMember.address = value ?? '';
          },
        ),
      ],
    );
  }

  Widget _buildAccountInfoSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildSectionTitle('Account Information'),
        TextFormField(
          initialValue: _editedMember.email,
          decoration: const InputDecoration(
            labelText: 'Email',
            border: OutlineInputBorder(),
          ),
          validator: (value) {
            if (value == null || value.isEmpty) {
              return 'Please enter email';
            }
            if (!value.contains('@')) {
              return 'Please enter a valid email';
            }
            return null;
          },
          onSaved: (value) {
            _editedMember.email = value ?? '';
          },
        ),
        const SizedBox(height: 16),
        Row(
          children: [
            Expanded(
              child: DropdownButtonFormField<String>(
                value: _editedMember.status,
                decoration: const InputDecoration(
                  labelText: 'Account Status',
                  border: OutlineInputBorder(),
                ),
                items: const [
                  DropdownMenuItem(value: 'Active', child: Text('Active')),
                  DropdownMenuItem(value: 'Pending', child: Text('Pending')),
                  DropdownMenuItem(value: 'Inactive', child: Text('Inactive')),
                  DropdownMenuItem(value: 'Banned', child: Text('Banned')),
                ],
                onChanged: (newValue) {
                  if (newValue != null) {
                    setState(() {
                      _editedMember.status = newValue;
                    });
                  }
                },
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please select account status';
                  }
                  return null;
                },
                onSaved: (value) {
                  _editedMember.status = value ?? 'Inactive';
                },
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: DropdownButtonFormField<String>(
                value: _editedMember.subscriptionStatus,
                decoration: const InputDecoration(
                  labelText: 'Subscription Status',
                  border: OutlineInputBorder(),
                ),
                items: const [
                  DropdownMenuItem(value: 'Active', child: Text('Active')),
                  DropdownMenuItem(value: 'Expired', child: Text('Expired')),
                  DropdownMenuItem(value: 'Pending', child: Text('Pending')),
                  DropdownMenuItem(value: 'No Subscription', child: Text('No Subscription')),
                ],
                onChanged: (newValue) {
                  if (newValue != null) {
                    setState(() {
                      _editedMember.subscriptionStatus = newValue;
                    });
                  }
                },
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please select subscription status';
                  }
                  return null;
                },
                onSaved: (value) {
                  _editedMember.subscriptionStatus = value ?? 'Pending';
                },
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildEmergencyContactSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildSectionTitle('Emergency Contact'),
        TextFormField(
          initialValue: _editedMember.contactPerson,
          decoration: const InputDecoration(
            labelText: 'Contact Person',
            border: OutlineInputBorder(),
          ),
          validator: (value) {
            if (value == null || value.isEmpty) {
              return 'Please enter contact person';
            }
            return null;
          },
          onSaved: (value) {
            _editedMember.contactPerson = value ?? '';
          },
        ),
        const SizedBox(height: 16),
        Row(
          children: [
            Expanded(
              child: TextFormField(
                initialValue: _editedMember.contactNumber,
                decoration: const InputDecoration(
                  labelText: 'Contact Number',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter contact number';
                  }
                  return null;
                },
                onSaved: (value) {
                  _editedMember.contactNumber = value ?? '';
                },
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: TextFormField(
                initialValue: _editedMember.relationship,
                decoration: const InputDecoration(
                  labelText: 'Relationship',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter relationship';
                  }
                  return null;
                },
                onSaved: (value) {
                  _editedMember.relationship = value ?? '';
                },
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildMedicalBackgroundSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildSectionTitle('Medical Background'),
        TextFormField(
          initialValue: _editedMember.medicalConditions,
          decoration: const InputDecoration(
            labelText: 'Medical Conditions',
            border: OutlineInputBorder(),
          ),
          maxLines: 2,
          onSaved: (value) {
            _editedMember.medicalConditions = value ?? '';
          },
        ),
        const SizedBox(height: 16),
        TextFormField(
          initialValue: _editedMember.currentMedications,
          decoration: const InputDecoration(
            labelText: 'Current Medications',
            border: OutlineInputBorder(),
          ),
          maxLines: 2,
          onSaved: (value) {
            _editedMember.currentMedications = value ?? '';
          },
        ),
        const SizedBox(height: 16),
        TextFormField(
          initialValue: _editedMember.previousInjuries,
          decoration: const InputDecoration(
            labelText: 'Previous Injuries',
            border: OutlineInputBorder(),
          ),
          maxLines: 2,
          onSaved: (value) {
            _editedMember.previousInjuries = value ?? '';
          },
        ),
      ],
    );
  }

  Widget _buildWaiversSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildSectionTitle('Waivers'),
        CheckboxListTile(
          title: const Text('Rules and Policy'),
          value: _editedMember.rulesAndPolicy,
          onChanged: (bool? value) {
            setState(() {
              _editedMember.rulesAndPolicy = value ?? false;
            });
          },
          controlAffinity: ListTileControlAffinity.leading,
        ),
        CheckboxListTile(
          title: const Text('Liability Waiver'),
          value: _editedMember.liabilityWaiver,
          onChanged: (bool? value) {
            setState(() {
              _editedMember.liabilityWaiver = value ?? false;
            });
          },
          controlAffinity: ListTileControlAffinity.leading,
        ),
        CheckboxListTile(
          title: const Text('Cancellation and Refund Policy'),
          value: _editedMember.cancellationAndRefundPolicy,
          onChanged: (bool? value) {
            setState(() {
              _editedMember.cancellationAndRefundPolicy = value ?? false;
            });
          },
          controlAffinity: ListTileControlAffinity.leading,
        ),
      ],
    );
  }

  Widget _buildSubmitButton() {
    return Center(
      child: ElevatedButton(
        onPressed: _saveMember,
        style: ElevatedButton.styleFrom(
          backgroundColor: const Color.fromRGBO(255, 179, 0, 1),
          foregroundColor: Colors.black,
          padding: const EdgeInsets.symmetric(horizontal: 50, vertical: 15),
        ),
        child: const Text(
          'Save Changes',
          style: TextStyle(fontSize: 16),
        ),
      ),
    );
  }
}