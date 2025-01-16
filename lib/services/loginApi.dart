import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:pharma/presentation/pharma.dart';
import 'package:pharma/services/uploadPrescriptionApi.dart';

final Dio dio = Dio();
const String baseUrl = "http://192.168.1.49:8000/api";
String? token;

Future<void> loginApi(
    String email, String password, BuildContext context) async {
  try {
    final response = await dio.post(
      "$baseUrl/login",
      data: {
        "email": email,
        "password": password,
      },
      options: Options(
        headers: {
          "Content-Type": "application/json",
        },
      ),
    );

    print("looooooog:$response");

    if (response.statusCode == 200) {
      token = response.data['token'];

      print("Login successful!");

      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (context) => UploadPrescriptionScreen(),
        ),
      );
    } else {
      print('Login failed');
    }
  } on DioError catch (e) {
    print("DioError: $e");
  } catch (e) {
    print("Error: $e");
  }
}

Future<List<Map<String, dynamic>>> fetchPrescriptions() async {
  try {
    final response = await dio.get(
      '$baseUrl/prescriptions/view', // Replace with your actual API endpoint
      options: Options(
        headers: {
          'Authorization': 'Bearer $token', // Add token if required
          'Content-Type': 'application/json',
        },
      ),
    );

    print("Response: $response");

    // Check if response data is a map and contains a prescriptions list
    if (response.statusCode == 200 || response.statusCode == 201) {
      final data = response.data;

      print("Response data: $data"); // Ensure response.data is parsed correctly

      if (data is Map<String, dynamic> && data['data'] is List) {
        // Cast the prescriptions list to a List<Map<String, dynamic>>
        return List<Map<String, dynamic>>.from(data['data']);
      } else {
        throw Exception(
            "Invalid response format: 'prescriptions' is not a List.");
      }
    } else {
      throw Exception(
          "Failed to fetch prescriptions. Status code: ${response.statusCode}");
    }
  } on DioError catch (e) {
    print("DioError: ${e.message}");
    throw Exception(
        "Error: ${e.response?.statusCode} - ${e.response?.data ?? e.message}");
  } catch (e) {
    print("Unexpected error: $e");
    throw Exception("An unexpected error occurred.");
  }
}
