import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:pharma/presentation/pharma.dart';
import 'package:pharma/services/uploadPrescriptionApi.dart';

final Dio dio = Dio();
const String baseUrl = "http://192.168.86.249:8000/api";
String? token;

Future<void> loginApi(email, password, context) async {
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
    print(response.data);
    if (response.statusCode == 200) {
      token = response.data['token'];

      print("successss");
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (context) => UploadPrescriptionScreen(),
        ),
      );
    } else {
      print('failed');
    }
  } on DioError catch (e) {
    print(e);
  } catch (e) {
    print(e);
  }
}
