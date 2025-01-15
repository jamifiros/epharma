import 'dart:io';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:pharma/authentication/login.dart';
import 'package:pharma/services/registerApi.dart';

class RegisterPage extends StatefulWidget {
  @override
  _RegisterPageState createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final ImagePicker _picker = ImagePicker();
  XFile? _idProof;
  final TextEditingController _nameController = TextEditingController();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  final TextEditingController _phoneController = TextEditingController();
  final TextEditingController _guardianNameController = TextEditingController();
  final TextEditingController _guardianEmailController =
      TextEditingController();
  final TextEditingController _placeController = TextEditingController();
  final TextEditingController _districtController = TextEditingController();
  Future<void> _uploadIdProof() async {
    final XFile? idProof = await _picker.pickImage(source: ImageSource.gallery);
    if (idProof != null) {
      setState(() {
        _idProof = idProof;
      });
    }
  }

  Future<void> _takeIdProofPicture() async {
    final XFile? idProof = await _picker.pickImage(source: ImageSource.camera);
    if (idProof != null) {
      setState(() {
        _idProof = idProof;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back, color: Colors.black),
          onPressed: () {
            Navigator.pop(context);
          },
        ),
        title: Text(
          "Register",
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: Colors.black,
          ),
        ),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                "Create an Account",
                style: TextStyle(
                  fontSize: 24,
                  fontWeight: FontWeight.bold,
                ),
              ),
              SizedBox(height: 8),
              Text(
                "Please fill in your details to create an account.",
                style: TextStyle(fontSize: 14, color: Colors.grey[600]),
              ),
              SizedBox(height: 20),
              TextField(
                controller: _nameController,
                decoration: InputDecoration(
                  labelText: "Full Name",
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
              SizedBox(height: 16),
              TextField(
                controller: _emailController,
                decoration: InputDecoration(
                  labelText: "Email Address",
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
              SizedBox(height: 16),
              TextField(
                controller: _passwordController,
                decoration: InputDecoration(
                  labelText: "Password",
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
              SizedBox(height: 16),
              TextField(
                controller: _phoneController,
                decoration: InputDecoration(
                  labelText: "Phone Number",
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
              SizedBox(height: 16),
              TextField(
                controller: _guardianNameController,
                decoration: InputDecoration(
                  labelText: "Guardian Name",
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
              SizedBox(height: 16),
              TextField(
                controller: _guardianEmailController,
                decoration: InputDecoration(
                  labelText: "Guardian email ",
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
              SizedBox(height: 16),
              TextField(
                controller: _placeController,
                decoration: InputDecoration(
                  labelText: "Place",
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
              SizedBox(height: 16),
              TextField(
                controller: _districtController,
                decoration: InputDecoration(
                  labelText: "district",
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
              SizedBox(height: 16),
              Center(
                child: GestureDetector(
                  onTap: () {},
                  child: Column(
                    children: [
                      _idProof != null
                          ? Image.file(
                              File(_idProof!.path),
                              height: 150,
                            )
                          : SizedBox(),
                      SizedBox(height: 10),
                      Text(
                        _idProof != null
                            ? "ID Proof Uploaded"
                            : "Upload ID Proof",
                        style: TextStyle(
                          color: Colors.grey[800],
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              SizedBox(height: 10),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  ElevatedButton.icon(
                    onPressed: _takeIdProofPicture,
                    icon: Icon(Icons.camera_alt),
                    label: Text("Take Picture"),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.teal,
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                  ),
                  ElevatedButton.icon(
                    onPressed: _uploadIdProof,
                    icon: Icon(Icons.photo_library),
                    label: Text("Upload"),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.teal,
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                  ),
                ],
              ),
              SizedBox(height: 15),
              Center(
                child: ElevatedButton(
                  onPressed: () {
                    print("sdsddsd");
                    registerApi(
                        context: context,
                        email: _emailController.text,
                        name: _nameController.text,
                        password: _passwordController.text,
                        phone: _phoneController.text,
                        district: _districtController.text,
                        guardianemail: _guardianEmailController.text,
                        guardianname: _guardianNameController.text,
                        place: _placeController.text,
                        idProof: _idProof);
                  },
                  child: Text("Register"),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.teal,
                    foregroundColor: Colors.white,
                    padding: EdgeInsets.symmetric(horizontal: 50, vertical: 15),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                  ),
                ),
              ),
              SizedBox(height: 10),
              Center(
                child: TextButton(
                  onPressed: () {
                    Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => LoginPage(),
                        ));
                  },
                  child: Text(
                    "Already have an account!! Login",
                    style: TextStyle(color: Colors.teal),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
