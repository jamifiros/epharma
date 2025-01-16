import 'dart:io';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:pharma/authentication/login.dart';
import 'package:pharma/presentation/medicinetracker.dart';
import 'package:pharma/services/deleetPrescriptionApi.dart';
import 'package:pharma/services/loginApi.dart';
import 'package:pharma/services/logoutApi.dart';
import 'package:pharma/services/uploadPrescriptionApi.dart';
import 'package:quickalert/models/quickalert_type.dart';
import 'package:quickalert/widgets/quickalert_dialog.dart';

class UploadPrescriptionScreen extends StatefulWidget {
  @override
  _UploadPrescriptionScreenState createState() =>
      _UploadPrescriptionScreenState();
}

class _UploadPrescriptionScreenState extends State<UploadPrescriptionScreen> {
  final ImagePicker _picker = ImagePicker();
  List<XFile> _images = [];
  late Future<List<dynamic>> _prescriptionsFuture;
  bool _isLoading = false; // Loading state for submit button

  @override
  void initState() {
    super.initState();
    _prescriptionsFuture = _loadPrescriptions();
  }

  Future<List<Map<String, dynamic>>> _loadPrescriptions() async {
    try {
      final prescriptions = await fetchPrescriptions();
      return prescriptions ?? [];
    } catch (e) {
      print("Error fetching prescriptions: $e");
      return [];
    }
  }

  void _refreshPrescriptions() {
    setState(() {
      _prescriptionsFuture = _loadPrescriptions();
    });
  }

  Future<void> _handleRefresh() async {
    await _loadPrescriptions();
    setState(() {});
  }

  Future<void> _takePicture() async {
    final XFile? photo = await _picker.pickImage(source: ImageSource.camera);
    if (photo != null) {
      setState(() {
        _images.add(photo);
      });
    }
  }

  Future<void> _pickImages() async {
    final List<XFile>? photos = await _picker.pickMultiImage();
    if (photos != null && photos.isNotEmpty) {
      setState(() {
        _images.addAll(photos);
      });
    }
  }

  Future<void> _submitPrescription() async {
    if (_images.isEmpty) {
      QuickAlert.show(
        context: context,
        type: QuickAlertType.warning,
        title: "No Images Selected",
        text: "Please select at least one image to upload.",
      );
      return;
    }

    setState(() {
      _isLoading = true; // Show loading indicator
    });

    try {
      await uploadPrescriptionApi(
        prescriptionImages: _images,
        context: context,
      );
      QuickAlert.show(
        context: context,
        type: QuickAlertType.success,
        title: "Success",
        text: "Order confirmed successfully! Medicine Will be Delivered Sooon",
      );
      _refreshPrescriptions();
      setState(() {
        _images.clear(); // Clear selected images after submission
      });
    } catch (e) {
      QuickAlert.show(
        context: context,
        type: QuickAlertType.error,
        title: "Upload Failed",
        text: "Failed to upload prescriptions. Please try again.",
      );
    } finally {
      setState(() {
        _isLoading = false; // Hide loading indicator
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: Text(
          "Upload Prescription",
          style: TextStyle(
            fontSize: 15,
            fontWeight: FontWeight.bold,
          ),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(
            onPressed: () {
              logoutApi(context);
            },
            icon: Icon(Icons.logout),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _handleRefresh,
        child: SingleChildScrollView(
          physics: AlwaysScrollableScrollPhysics(),
          child: Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(
                  child: Image.network(
                    'https://tse1.mm.bing.net/th?id=OIP.ZZ7OScEv3p3zAfEqTx6rdQHaE7&pid=Api&P=0&h=180',
                    height: 150,
                    fit: BoxFit.cover,
                  ),
                ),
                SizedBox(height: 20),
                InkWell(
                  onTap: () => Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => PatientMedicineTracker(),
                    ),
                  ),
                  child: Container(
                    height: 100,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(10),
                      color: Colors.grey.shade200,
                    ),
                    child: Center(
                      child: Text(
                        "V I E W   M E D I C I N E",
                        style: TextStyle(color: Colors.black45),
                      ),
                    ),
                  ),
                ),
                SizedBox(height: 20),
                Text(
                  "Prescriptions",
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                ),
                SizedBox(height: 10),
                Container(
                  height: 100,
                  child: FutureBuilder<List<dynamic>>(
                    future: _prescriptionsFuture,
                    builder: (context, snapshot) {
                      if (snapshot.connectionState == ConnectionState.waiting) {
                        return Center(child: CircularProgressIndicator());
                      } else if (snapshot.hasError) {
                        return Center(
                            child: Text(
                                "Error loading prescriptions: ${snapshot.error}"));
                      } else if (snapshot.data == null ||
                          snapshot.data!.isEmpty) {
                        return Center(
                            child: Text(
                                "No prescriptions available. Upload one to get started!"));
                      }

                      final prescriptions = snapshot.data!;
                      return ListView.builder(
                        scrollDirection: Axis.horizontal,
                        itemCount: prescriptions.length,
                        itemBuilder: (context, index) {
                          final prescription = prescriptions[index];
                          final imageUrl = prescription['image'];
                          return Padding(
                            padding: const EdgeInsets.all(4.0),
                            child: ClipRRect(
                              borderRadius: BorderRadius.circular(7),
                              child: Stack(
                                children: [
                                  Container(
                                    height: 100,
                                    width: 100,
                                    child: imageUrl != null
                                        ? Image.network(
                                            '$baseUrl$imageUrl'
                                                .replaceAll('api', ''),
                                            width: 50,
                                            height: 300,
                                            fit: BoxFit.cover,
                                          )
                                        : Icon(Icons.image, color: Colors.teal),
                                  ),
                                  Positioned(
                                    right: 0,
                                    top: 0,
                                    child: IconButton(
                                      onPressed: () {
                                        deletePrescription(
                                                context: context,
                                                id: prescription["id"])
                                            .then((_) {
                                          _refreshPrescriptions();
                                        });
                                      },
                                      icon: Icon(
                                        Icons.cancel,
                                        color: Colors.red,
                                      ),
                                    ),
                                  )
                                ],
                              ),
                            ),
                          );
                        },
                      );
                    },
                  ),
                ),
                SizedBox(height: 20),
                Text(
                  "Selected Prescriptions",
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                ),
                SizedBox(height: 10),
                _images.isNotEmpty
                    ? Container(
                        height: 100,
                        child: ListView.builder(
                          scrollDirection: Axis.horizontal,
                          itemCount: _images.length,
                          itemBuilder: (context, index) {
                            final image = _images[index];
                            return Padding(
                              padding: const EdgeInsets.all(4.0),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(7),
                                child: Stack(
                                  children: [
                                    Image.file(
                                      File(image.path),
                                      width: 100,
                                      height: 100,
                                      fit: BoxFit.cover,
                                    ),
                                    Positioned(
                                      right: 0,
                                      top: 0,
                                      child: IconButton(
                                        onPressed: () {
                                          setState(() {
                                            _images.removeAt(index);
                                          });
                                        },
                                        icon: Icon(
                                          Icons.cancel,
                                          color: Colors.red,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            );
                          },
                        ),
                      )
                    : Text("No prescriptions selected."),
                SizedBox(height: 20),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  children: [
                    ElevatedButton.icon(
                      onPressed: _takePicture,
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
                      onPressed: _pickImages,
                      icon: Icon(Icons.photo_library),
                      label: Text("Gallery"),
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
                SizedBox(height: 20),
                Center(
                  child: ElevatedButton(
                    onPressed: _isLoading ? null : _submitPrescription,
                    child: _isLoading
                        ? CircularProgressIndicator(
                            valueColor:
                                AlwaysStoppedAnimation<Color>(Colors.white),
                          )
                        : Text("S U B M I T"),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.teal,
                      foregroundColor: Colors.white,
                      padding:
                          EdgeInsets.symmetric(horizontal: 50, vertical: 15),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
