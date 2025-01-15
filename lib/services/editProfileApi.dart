import 'dart:convert';
import 'package:dio/dio.dart';
import 'package:pharma/services/loginApi.dart';

Future<Map<String, dynamic>> editProfile(
    String userId, Map<String, dynamic> data) async {
  try {
    final Dio dio = Dio();

    final response = await dio.put(
      "$baseUrl/profile/",
      data: jsonEncode(data),
      options: Options(
        headers: {
          "Content-Type": "application/json",
        },
      ),
    );

    if (response.statusCode == 200 || response.statusCode == 201) {
      return {"success": true, "message": "Profile updated successfully"};
    } else {
      return {
        "success": false,
        "message": response.data["message"] ?? "Profile update failed"
      };
    }
  } catch (e) {
    return {"success": false, "message": "Error updating profile: $e"};
  }
}
