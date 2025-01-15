import 'dart:io';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:pharma/services/loginApi.dart';

Future<void> registerApi({
  required String name,
  required String email,
  required String password,
  required String phone,
  required String guardianname,
  required String guardianemail,
  required String place,
  required String district,
  context,
  XFile? idProof, // XFile used for optional file input
}) async {
  try {
    // Validate input fields
    if (name.isEmpty ||
        email.isEmpty ||
        password.isEmpty ||
        phone.isEmpty ||
        guardianname.isEmpty ||
        guardianemail.isEmpty ||
        place.isEmpty ||
        district.isEmpty) {
      throw Exception("All fields are required.");
    }

    // Prepare form data
    FormData formData = FormData.fromMap({
      "name": name,
      "email": email,
      "password": password,
      "mobile_no": phone,
      "guardian_name": guardianname,
      "guardian_email": guardianemail,
      "place": place,
      "district": district,
      if (idProof != null)
        "idproof": await MultipartFile.fromFile(
          idProof.path,
          filename: idProof.path.split('/').last, // Extract file name
        ),
    });

    // Debugging logs
    debugPrint("Sending registration data: $formData");

    // Make the API request
    final Dio dio = Dio(); // Use a local Dio instance
    final Response response = await dio.post(
      "$baseUrl/register",
      data: formData,
      options: Options(
        headers: {
          "Content-Type": "multipart/form-data",
        },
      ),
    );

    // Handle success response
    if (response.statusCode == 201 || response.statusCode == 200) {
      print("Registration successful: ${response.data}");
      Navigator.pop(context);
    } else {
      debugPrint("Registration failed: ${response.data}");
    }
  } on DioError catch (e) {
  } catch (e) {}
}
