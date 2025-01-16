import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:pharma/services/loginApi.dart';

Future<void> deletePrescription(
    {required BuildContext context, required int? id}) async {
  try {
    final response = await dio.delete(
      "$baseUrl/delete/$id",
      options: Options(
        headers: {
          'Authorization': 'Bearer $token', // Add token in headers
        },
      ),
    );

    if (response.statusCode == 200) {
      // Successfully deleted
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Prescription deleted successfully.")),
      );
    } else {
      // Handle non-200 status codes
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Failed to delete prescription.")),
      );
    }
  } catch (e) {
    // Handle errors

    print(e);
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text("Error: ${e.toString()}")),
    );
  }
}
