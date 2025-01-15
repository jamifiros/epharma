import 'package:flutter/material.dart';
import 'package:pharma/presentation/editprofile.dart';

class ProfileScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back, color: Colors.black),
          onPressed: () {
            Navigator.pop(context);
          },
        ),
        title: Text(
          "Profile",
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: Colors.black,
          ),
        ),
        actions: [
          IconButton(
            icon: Icon(Icons.edit, color: Colors.black),
            onPressed: () {
              Navigator.pushReplacement(
                  context,
                  MaterialPageRoute(
                    builder: (context) => EditProfileScreen(),
                  ));
            },
          ),
        ],
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            SizedBox(height: 20),
            CircleAvatar(
              radius: 50,
              //   backgroundImage: NetworkImage(
              //       'https://via.placeholder.com/150'), // Placeholder profile picture
            ),
            SizedBox(height: 15),
            Text(
              "John Doe",
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
              ),
            ),
            SizedBox(height: 5),
            Text(
              "johndoe@example.com",
              style: TextStyle(fontSize: 14, color: Colors.grey[600]),
            ),
            SizedBox(height: 20),
            ListTile(
              leading: Icon(Icons.phone, color: Colors.teal),
              title: Text("+1 234 567 890"),
            ),
            Divider(),
            ListTile(
              leading: Icon(Icons.location_on, color: Colors.teal),
              title: Text("123 Main Street, City, Country"),
            ),
            Divider(),
            ListTile(
              leading: Icon(Icons.calendar_today, color: Colors.teal),
              title: Text("Date of Birth: Jan 1, 1990"),
            ),
            Divider(),
            SizedBox(height: 30),
            Center(
              child: ElevatedButton(
                onPressed: () {},
                child: Text("Logout"),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.teal,
                  foregroundColor: Colors.white,
                  padding: EdgeInsets.symmetric(horizontal: 50, vertical: 15),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
