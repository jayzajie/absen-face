import 'dart:io';

import 'package:flutter/material.dart';
import 'package:path_provider/path_provider.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'attendance_service.dart';
import 'screens/account_screen.dart';
import 'screens/history_screen.dart';
import 'screens/home_screen.dart';
import 'screens/izin_screen.dart';
import 'screens/login_screen.dart';
import 'screens/scan_screen.dart';
import 'screens/success_screen.dart';

const green = Color(0xFF2F88BE);
const brandRed = Color(0xFFCF403B);
const ink = Color(0xFF17222B);
const muted = Color(0xFF687782);
const border = Color(0xFFDCE7ED);
const portraitAsset = 'assets/images/face_portrait.png';

enum AppPage { login, home, scan, history, izin, account, success }

class AppController extends ChangeNotifier {
  AppController({AttendanceService? service})
    : attendanceService = service ?? AttendanceService();

  AppPage page = AppPage.login;
  String pendingAttendance = 'masuk';
  Map<String, dynamic>? lastAttendanceData;
  final AttendanceService attendanceService;
  File? profilePhoto;

  Future<void> loadPreferences() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('profile_photo_id');
    final path = prefs.getString('profile_photo_path');
    if (path != null && await File(path).exists()) {
      profilePhoto = File(path);
    }
    notifyListeners();
  }

  Future<void> setProfilePhoto(String selectedPath) async {
    final directory = await getApplicationSupportDirectory();
    final saved = await File(
      selectedPath,
    ).copy('${directory.path}/profile_photo');
    await FileImage(saved).evict();
    profilePhoto = saved;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('profile_photo_path', saved.path);
    notifyListeners();
  }

  Future<void> removeProfilePhoto() async {
    final photo = profilePhoto;
    profilePhoto = null;
    if (photo != null && await photo.exists()) await photo.delete();
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('profile_photo_id');
    await prefs.remove('profile_photo_path');
    notifyListeners();
  }

  void go(AppPage next) {
    page = next;
    notifyListeners();
  }

  void startScan(String type) {
    pendingAttendance = type;
    go(AppPage.scan);
  }

  Future<void> submitAttendance({
    required List<int> selfieBytes,
    required String selfieName,
  }) async {
    final result = await attendanceService.record(
      pendingAttendance,
      selfieBytes: selfieBytes,
      selfieName: selfieName,
    );

    lastAttendanceData = result;
    go(AppPage.success);
  }
}

class AbsenKuApp extends StatefulWidget {
  const AbsenKuApp({super.key});

  @override
  State<AbsenKuApp> createState() => _AbsenKuAppState();
}

class _AbsenKuAppState extends State<AbsenKuApp> {
  final controller = AppController();

  @override
  void initState() {
    super.initState();
    controller.loadPreferences();
  }

  @override
  void dispose() {
    controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) => MaterialApp(
    debugShowCheckedModeBanner: false,
    title: 'GJP-Absensi',
    theme: ThemeData(
      useMaterial3: true,
      scaffoldBackgroundColor: const Color(0xFFF7FAFC),
      colorScheme: ColorScheme.fromSeed(seedColor: green),
      fontFamily: 'Arial',
      textTheme: const TextTheme(bodyMedium: TextStyle(color: ink)),
    ),
    home: AnimatedBuilder(
      animation: controller,
      builder: (context, child) => PhoneFrame(child: _screen()),
    ),
  );

  Widget _screen() => switch (controller.page) {
    AppPage.login => LoginScreen(controller: controller),
    AppPage.home => HomeScreen(controller: controller),
    AppPage.scan => ScanScreen(controller: controller),
    AppPage.history => HistoryScreen(controller: controller),
    AppPage.izin => IzinScreen(controller: controller),
    AppPage.account => AccountScreen(controller: controller),
    AppPage.success => SuccessScreen(controller: controller),
  };
}

class PhoneFrame extends StatelessWidget {
  const PhoneFrame({super.key, required this.child});
  final Widget child;

  @override
  Widget build(BuildContext context) => ColoredBox(
    color: const Color(0xFFEAF2F7),
    child: Center(
      child: ConstrainedBox(
        constraints: const BoxConstraints(maxWidth: 430),
        child: ColoredBox(color: const Color(0xFFF7FAFC), child: child),
      ),
    ),
  );
}
