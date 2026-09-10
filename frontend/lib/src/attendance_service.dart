import 'dart:convert';
import 'dart:io';

import 'package:device_info_plus/device_info_plus.dart';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class AttendanceService {
  static const configuredApiUrl = String.fromEnvironment(
    'API_URL',
    defaultValue: '',
  );
  static String get apiUrl => configuredApiUrl.isNotEmpty
      ? configuredApiUrl
      : !kIsWeb && Platform.isAndroid
      ? 'http://10.0.2.2:8000/api'
      : 'http://localhost:8000/api';
  static const deviceId = String.fromEnvironment(
    'DEVICE_ID',
    defaultValue: 'office-hp-001',
  );

  static String? currentEmployeeName;
  static String? currentDeviceId;
  static String? currentEmployeeToken;

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

  Future<void> login(
    String username,
    String password, {
    bool rememberUsername = false,
  }) async {
    await initDevice();
    final response = await http
        .post(
          Uri.parse('$apiUrl/mobile/login'),
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          body: jsonEncode({
            'username': username,
            'password': password,
            'device_id': currentDeviceId ?? deviceId,
          }),
        )
        .timeout(
          const Duration(seconds: 15),
          onTimeout: () => throw Exception('Server tidak merespons.'),
        );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      currentEmployeeName = data['name'];
      currentEmployeeToken = data['token'];
      if (currentEmployeeToken == null) {
        throw Exception('Token login tidak diterima dari server.');
      }

      final prefs = await SharedPreferences.getInstance();
      await prefs.remove('emp_password');
      if (rememberUsername) {
        await prefs.setString('emp_username', username);
      } else {
        await prefs.remove('emp_username');
      }
    } else {
      throw Exception('Username atau password salah.');
    }
  }

  Future<String?> rememberedUsername() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('emp_password');
    return prefs.getString('emp_username');
  }

  Future<Map<String, dynamic>> record(
    String type, {
    required List<int> selfieBytes,
    required String selfieName,
  }) async {
    if (currentEmployeeToken == null) {
      throw Exception('Sesi berakhir. Silakan login kembali.');
    }
    final request =
        http.MultipartRequest('POST', Uri.parse('$apiUrl/attendances/verify'))
          ..headers['Accept'] = 'application/json'
          ..headers['Authorization'] = 'Bearer $currentEmployeeToken'
          ..fields['type'] = type
          ..fields['device_id'] = currentDeviceId ?? deviceId
          ..files.add(
            http.MultipartFile.fromBytes(
              'selfie',
              selfieBytes,
              filename: selfieName,
            ),
          );

    final streamed = await request.send().timeout(
      const Duration(seconds: 40),
      onTimeout: () => throw Exception('Server tidak merespons.'),
    );
    final response = await http.Response.fromStream(streamed);
    final data = jsonDecode(response.body) as Map<String, dynamic>;

    if (response.statusCode == 201) {
      return data;
    } else {
      throw Exception(
        data['message'] ?? 'Server menolak absensi (${response.statusCode})',
      );
    }
  }
}
