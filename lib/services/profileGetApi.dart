import 'package:dio/dio.dart';
import 'package:pharma/services/loginApi.dart';

Future<Map<String, dynamic>> getProfile(String userId) async {
  try {
    final response = await dio.get(
      "$baseUrl/profile/$userId",
      options: Options(
        headers: {
          "Content-Type": "application/json",
        },
      ),
    );

    if (response.statusCode == 200) {
      return {"success": true, "data": response.data};
    } else {
      return {"success": false, "message": "Failed to fetch profile"};
    }
  } catch (e) {
    return {"success": false, "message": "Error fetching profile: $e"};
  }
}
