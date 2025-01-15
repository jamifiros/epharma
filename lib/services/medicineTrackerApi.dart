import 'dart:convert';
import 'package:dio/dio.dart';
import 'package:pharma/services/loginApi.dart';

Future<Map<String, dynamic>> submitMedicineData(
    Map<String, dynamic> data) async {
  try {
    final response = await dio.post(
      "$baseUrl/medicines/track",
      data: jsonEncode(data),
      options: Options(
        headers: {
          "Content-Type": "application/json",
        },
      ),
    );

    if (response.statusCode == 200 || response.statusCode == 201) {
      return {
        "success": true,
        "message": response.data["message"] ?? "Data submitted successfully"
      };
    } else {
      return {
        "success": false,
        "message": response.data["message"] ?? "Submission failed"
      };
    }
  } catch (e) {
    return {"success": false, "message": "Error submitting data: $e"};
  }
}
