import 'dart:convert';
import 'dart:io';

import 'package:device_info_plus/device_info_plus.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class AttendanceService {
  static const apiUrl = String.fromEnvironment(
    'API_URL',
    defaultValue: 'http://10.0.2.2:8000/api',
  );
  static const deviceToken = String.fromEnvironment(
    'MOBILE_API_TOKEN',
    defaultValue: 'office-device-dev-key',
  );
  static const deviceId = String.fromEnvironment(
    'DEVICE_ID',
    defaultValue: 'office-hp-001',
  );

  static String? currentEmployeeName;
  static String? currentDeviceId;

  static Future<void> initDevice() async {
    try {
      final deviceInfo = DeviceInfoPlugin();
      if (Platform.isAndroid) {
        final androidInfo = await deviceInfo.androidInfo;
        currentDeviceId = '${androidInfo.brand} ${androidInfo.model}';
      } else if (Platform.isIOS) {
        final iosInfo = await deviceInfo.iosInfo;
        currentDeviceId = iosInfo.name;
      }
    } catch (_) {
      currentDeviceId = 'Unknown Device';
    }
  }

  Future<void> login(String username, String password) async {
    final response = await http.post(
      Uri.parse('$apiUrl/mobile/login'),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({'username': username, 'password': password}),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      currentEmployeeName = data['name'];
      await initDevice(); // Pastikan device terdeteksi

      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('emp_username', username);
      await prefs.setString('emp_password', password);
    } else {
      throw Exception('Username atau password salah.');
    }
  }

  Future<bool> tryAutoLogin() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final u = prefs.getString('emp_username');
      final p = prefs.getString('emp_password');
      if (u != null && p != null) {
        await login(u, p);
        return true;
      }
    } catch (_) {}
    return false;
  }

  Future<Map<String, dynamic>> record(
    String type, {
    required bool cameraAccessGranted,
  }) async {
    final response = await http.post(
      Uri.parse('$apiUrl/attendances'),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $deviceToken',
      },
      body: jsonEncode({
        'employee_name': currentEmployeeName ?? 'Unknown',
        'type': type,
        'device_id': currentDeviceId ?? deviceId, // Fallback if missing
        'camera_access_granted': cameraAccessGranted,
      }),
    );

    if (response.statusCode == 201) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Server menolak absensi (${response.statusCode})');
    }
  }
}
