import 'dart:convert';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:pharma/services/loginApi.dart';

// Define the Medicine class
class Medicine {
  final int id;
  final String name;
  final String mealTime;
  final Map<String, bool> timesToTake;
  final Map<String, bool> timesConsumed;
  final int totalCount; // Adding totalCount to represent stock availability

  Medicine({
    required this.id,
    required this.name,
    required this.mealTime,
    required this.timesToTake,
    required this.timesConsumed,
    required this.totalCount, // Add totalCount to constructor
  });

  factory Medicine.fromJson(Map<String, dynamic> json) {
    Map<String, bool> timesToTake = {
      'Morning': json['morning'] == 1,
      'Afternoon': json['afternoon'] == 1,
      'Evening': json['evening'] == 1,
      'Night': json['night'] == 1,
    };

    Map<String, bool> timesConsumed = {
      'Morning': false,
      'Afternoon': false,
      'Evening': false,
      'Night': false,
    };

    if (timesToTake.containsValue(true)) {
      return Medicine(
        id: json['id'] ?? 0,
        name: json['medicine_name'] ?? '',
        mealTime: json['timing'] ?? '',
        timesToTake: timesToTake,
        timesConsumed: timesConsumed,
        totalCount: json['total_count'] ??
            0, // Adding the total_count from API response
      );
    } else {
      throw Exception("Medicine has no valid times to take.");
    }
  }

  int countConsumedTimes() {
    return timesConsumed.values.where((isConsumed) => isConsumed).length;
  }

  bool get isOutOfStock =>
      totalCount == 0; // Check if the medicine is out of stock
}

// Define the PatientMedicineTracker widget
class PatientMedicineTracker extends StatefulWidget {
  @override
  _PatientMedicineTrackerState createState() => _PatientMedicineTrackerState();
}

class _PatientMedicineTrackerState extends State<PatientMedicineTracker> {
  List<Medicine> _medicines = [];
  bool _isLoading = true;
  bool _isSubmitting = false;
  int _totalConsumed = 0;
  DateTime _selectedDate = DateTime.now();
  String _errorMessage = '';
  bool _isOutOfStock = false; // Track if there are any out-of-stock medicines

  @override
  void initState() {
    super.initState();
    _fetchMedicines();
  }

  Future<void> _fetchMedicines() async {
    try {
      Dio dio = Dio();
      dio.options.headers = {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      };

      print("Request URL: $baseUrl/medicines");

      final response = await dio.get("$baseUrl/medicines");

      print("Response Status: ${response.statusCode}");
      print("Raw Response: ${response.data}");

      if (response.statusCode == 200) {
        List data = response.data['data'] ?? [];
        List<String> lowStockMessages =
            List<String>.from(response.data['low_stock_messages'] ?? []);

        // Show alert if there are low-stock messages
        if (lowStockMessages.isNotEmpty) {
          showDialog(
            context: context,
            builder: (context) {
              return AlertDialog(
                title: Text("Low Stock Alert"),
                content: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: lowStockMessages.map((message) {
                    return Text(
                      message,
                      style: TextStyle(color: Colors.red),
                    );
                  }).toList(),
                ),
                actions: [
                  TextButton(
                    onPressed: () {
                      Navigator.of(context).pop();
                    },
                    child: Text("OK"),
                  ),
                ],
              );
            },
          );
        }

        if (data.isEmpty) {
          throw Exception(response.data['message'] ?? "No medicines found.");
        }

        List<Medicine> medicinesList = [];
        bool hasOutOfStock = false;

        for (var prescription in data) {
          var medicines = prescription['medicines'];
          for (var med in medicines) {
            try {
              Medicine medicine = Medicine.fromJson(med);
              medicinesList.add(medicine);

              // Check if any medicine is out of stock
              if (medicine.isOutOfStock) {
                hasOutOfStock = true;
              }
            } catch (e) {
              print("Error parsing medicine: $e");
            }
          }
        }

        setState(() {
          _medicines = medicinesList;
          _isLoading = false;
          _errorMessage = '';
          _isOutOfStock = hasOutOfStock;
          _updateTotalConsumed();
        });
      } else {
        throw Exception(response.data['message'] ??
            "Failed to load medicines. Status code: ${response.statusCode}");
      }
    } catch (e) {
      setState(() {
        _isLoading = false;
        _errorMessage = e.toString();
        _medicines = [];
      });
      print("Error fetching medicines: $e");
    }
  }

  void _updateTotalConsumed() {
    int consumedCount = 0;
    for (var medicine in _medicines) {
      consumedCount += medicine.countConsumedTimes();
    }
    setState(() {
      _totalConsumed = consumedCount;
    });
  }

  Future<void> _selectDate(BuildContext context) async {
    final DateTime? pickedDate = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: DateTime(2000),
      lastDate: DateTime(2101),
    );

    if (pickedDate != null && pickedDate != _selectedDate) {
      setState(() {
        _selectedDate = pickedDate;
      });
    }
  }

  Future<void> _submitConsumptionStatus() async {
    setState(() {
      _isSubmitting = true;
    });

    try {
      Dio dio = Dio();
      dio.options.headers = {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      };

      final requestData = _medicines.map((medicine) {
        int consumedCount = medicine.countConsumedTimes();
        return {
          'medicine_id': medicine.id,
          'quantity': consumedCount,
        };
      }).toList();

      final response = await dio
          .post("$baseUrl/medicine-intake", data: {'medicines': requestData});

      if (response.statusCode == 200 || response.statusCode == 201) {
        ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text("Data submitted successfully.")));
      } else {
        throw Exception("Failed to submit consumption data.");
      }
    } catch (e) {
      print("Error submitting consumption status: $e");
      ScaffoldMessenger.of(context)
          .showSnackBar(SnackBar(content: Text("Error submitting data.")));
    } finally {
      setState(() {
        _isSubmitting = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.teal,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () {
            Navigator.pop(context);
          },
        ),
        title: Text(
          "Medicine Tracker",
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: Colors.white,
          ),
        ),
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator())
          : _errorMessage.isNotEmpty
              ? Center(
                  child: Text(
                    _errorMessage,
                    style: TextStyle(fontSize: 18, color: Colors.red),
                  ),
                )
              : Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        "Selected Date: ${_selectedDate.toLocal()}"
                            .split(' ')[0],
                        style: TextStyle(
                            fontSize: 18, fontWeight: FontWeight.bold),
                      ),
                      SizedBox(height: 10),
                      ElevatedButton(
                        onPressed: () => _selectDate(context),
                        child: Text("Select Date"),
                      ),
                      SizedBox(height: 10),
                      Text(
                        "Medicines List",
                        style: TextStyle(
                            fontSize: 18, fontWeight: FontWeight.bold),
                      ),
                      SizedBox(height: 10),
                      // Display out-of-stock message if any medicine is out of stock
                      if (_isOutOfStock)
                        Padding(
                          padding: const EdgeInsets.only(top: 8.0),
                          child: Text(
                            "Some medicines are out of stock.",
                            style: TextStyle(fontSize: 16, color: Colors.red),
                          ),
                        ),
                      Expanded(
                        child: ListView.builder(
                          itemCount: _medicines.length,
                          itemBuilder: (context, index) {
                            final medicine = _medicines[index];

                            return Card(
                              margin: EdgeInsets.symmetric(vertical: 8),
                              child: Padding(
                                padding: const EdgeInsets.all(16.0),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      "${medicine.name} (${medicine.mealTime})",
                                      style: TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                        color: medicine.isOutOfStock
                                            ? Colors.grey
                                            : Colors.black,
                                      ),
                                    ),
                                    ...medicine.timesToTake.keys
                                        .where((time) =>
                                            medicine.timesToTake[time]! &&
                                            !medicine.isOutOfStock)
                                        .map((time) {
                                      return Row(
                                        children: [
                                          Checkbox(
                                            value: medicine.timesToTake[time]!,
                                            onChanged:
                                                null, // Disable check for "timesToTake" as it should be fixed
                                          ),
                                          Text(time),
                                          Spacer(),
                                          Checkbox(
                                            value:
                                                medicine.timesConsumed[time]!,

                                            // Disable interaction if out of stock
                                            onChanged: medicine.isOutOfStock
                                                ? null
                                                : (bool? value) {
                                                    setState(() {
                                                      medicine.timesConsumed[
                                                          time] = value!;
                                                    });
                                                    _updateTotalConsumed();
                                                  },
                                          ),
                                          Text("Consumed"),
                                        ],
                                      );
                                    }).toList(),
                                  ],
                                ),
                              ),
                            );
                          },
                        ),
                      ),
                      Center(
                        child: ElevatedButton(
                          style: ElevatedButton.styleFrom(
                              padding: EdgeInsets.symmetric(
                                  horizontal: 50, vertical: 15),
                              backgroundColor: Colors.teal),
                          onPressed:
                              _isSubmitting ? null : _submitConsumptionStatus,
                          child: _isSubmitting
                              ? CircularProgressIndicator(
                                  valueColor: AlwaysStoppedAnimation<Color>(
                                      Colors.white),
                                )
                              : Text("Submit"),
                        ),
                      ),
                    ],
                  ),
                ),
    );
  }
}
