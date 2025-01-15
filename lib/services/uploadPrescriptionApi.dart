import 'dart:io';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:pharma/services/loginApi.dart';

Future<void> uploadPrescriptionApi({
  required List<XFile> prescriptionImages,
  required BuildContext context, // Context for navigation or alerts
}) async {
  try {
    // Validate if images are provided
    if (prescriptionImages.isEmpty) {
      throw Exception("Please select at least one prescription image.");
    }

    // Prepare form data
    FormData formData = FormData.fromMap({
      "image": [
        for (XFile image in prescriptionImages)
          await MultipartFile.fromFile(
            image.path,
            filename: image.path.split('/').last, // Extract file name
          ),
      ],
    });

    // Debugging logs
    print("Uploading prescription data: $formData");

    final Response response = await dio.post(
      "$baseUrl/prescriptions",
      data: formData,
      options: Options(
        headers: {
          "Content-Type": "multipart/form-data",
          "Authorization": "Bearer $token", // Include token in headers
        },
      ),
    );

    // Handle success response
    if (response.statusCode == 201 || response.statusCode == 200) {
      print("Prescription upload successful: ${response.data}");
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Prescription uploaded successfully!")),
      );
    } else {
      print("Prescription upload failed: ${response.data}");
    }
  } catch (e) {
    print("Unexpected error: $e");
  }
}
