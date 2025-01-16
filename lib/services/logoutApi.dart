import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:pharma/authentication/login.dart';
import 'package:pharma/services/loginApi.dart';

Future<void> logoutApi(context) async {
  if (token == null) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text("User not logged in!")),
    );
    return;
  }

  try {
    // Create Dio instance
    Dio dio = Dio();

    // Set headers with the token
    dio.options.headers['Authorization'] = 'Bearer $token';

    // Send the request
    Response response = await dio.post("$baseUrl/logout");

    if (response.statusCode == 200) {
      // Logout successful
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Logged out successfully.")),
      );

      // Navigate to login screen
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (context) => LoginPage()),
        (route) => false,
      );
    } else {
      // Handle non-successful response
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Logout failed. Please try again.")),
      );
    }
  } catch (e) {
    // Handle errors
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text("An error occurred: $e")),
    );
  }
}
