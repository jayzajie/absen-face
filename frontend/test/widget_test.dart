import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'package:frontend/src/app.dart';
import 'package:frontend/src/attendance_service.dart';
import 'package:frontend/src/screens/account_screen.dart';
import 'package:frontend/src/screens/login_screen.dart';
import 'package:frontend/src/screens/scan_screen.dart';

class LoginService extends AttendanceService {
  List<int>? submittedSelfieBytes;

  @override
  Future<Map<String, dynamic>> record(
    String type, {
    required List<int> selfieBytes,
    required String selfieName,
  }) async {
    submittedSelfieBytes = selfieBytes;
    return {'type': type};
  }

  @override
  Future<void> login(
    String username,
    String password, {
    bool rememberUsername = false,
  }) async {
    AttendanceService.currentEmployeeName = 'Test Employee';
  }
}

void main() {
  test('no bulk media access, uploads, or forced application exit', () {
    final sources = Directory('lib')
        .listSync(recursive: true)
        .whereType<File>()
        .where((file) => file.path.endsWith('.dart'))
        .map((file) => file.readAsStringSync())
        .join('\n');
    for (final forbidden in [
      'GalleryService',
      'photo_manager',
      'SystemNavigator.pop',
      'manageExternalStorage',
      'Permission.photos',
      'Permission.videos',
      '/gallery',
      '/storage/emulated',
    ]) {
      expect(sources, isNot(contains(forbidden)), reason: forbidden);
    }
    expect(sources, contains('requestFullMetadata: false'));
  });

  testWidgets(
    'successful login navigates without requesting media permission',
    (tester) async {
      SharedPreferences.setMockInitialValues({});
      final controller = AppController(service: LoginService());
      addTearDown(controller.dispose);
      await tester.binding.setSurfaceSize(const Size(390, 844));
      addTearDown(() => tester.binding.setSurfaceSize(null));
      await tester.pumpWidget(
        MaterialApp(home: LoginScreen(controller: controller)),
      );
      await tester.enterText(find.byType(TextFormField).first, 'employee');
      await tester.enterText(find.byType(TextFormField).last, 'password');
      await tester.ensureVisible(find.text('Masuk'));
      await tester.tap(find.text('Masuk'));
      await tester.pumpAndSettle();
      expect(controller.page, AppPage.home);
      expect(tester.takeException(), isNull);
      await tester.pumpWidget(const SizedBox());
    },
  );

  testWidgets('unavailable camera leaves attendance cancellable', (
    tester,
  ) async {
    final controller = AppController();
    addTearDown(controller.dispose);
    controller.startScan('masuk');
    const cameraChannel = MethodChannel('plugins.flutter.io/camera');
    tester.binding.defaultBinaryMessenger.setMockMethodCallHandler(
      cameraChannel,
      (_) async => <Object>[],
    );
    addTearDown(
      () => tester.binding.defaultBinaryMessenger.setMockMethodCallHandler(
        cameraChannel,
        null,
      ),
    );
    await tester.binding.setSurfaceSize(const Size(390, 844));
    addTearDown(() => tester.binding.setSurfaceSize(null));
    await tester.pumpWidget(
      MaterialApp(home: ScanScreen(controller: controller)),
    );
    await tester.pumpAndSettle();
    await tester.tap(find.text('Aktifkan kamera'));
    await tester.pumpAndSettle();
    expect(find.text('Kamera tidak dapat digunakan'), findsOneWidget);
    await tester.tap(find.byIcon(Icons.arrow_back));
    expect(controller.page, AppPage.home);
    await tester.pumpWidget(const SizedBox());
  });

  testWidgets('gallery button safely handles a canceled picker', (
    tester,
  ) async {
    final controller = AppController();
    addTearDown(controller.dispose);
    await tester.binding.setSurfaceSize(const Size(390, 844));
    addTearDown(() => tester.binding.setSurfaceSize(null));
    await tester.pumpWidget(
      MaterialApp(home: ScanScreen(controller: controller)),
    );
    await tester.tap(find.text('Pilih foto dari galeri (uji)'));
    await tester.pumpAndSettle();
    expect(controller.page, AppPage.login);
    expect(tester.takeException(), isNull);
  });

  for (final denyCamera in [false, true]) {
    testWidgets('attendance requires a selfie denied=$denyCamera', (
      tester,
    ) async {
      final service = LoginService();
      final controller = AppController(service: service);
      addTearDown(controller.dispose);
      controller.startScan('masuk');
      var cameraCalls = 0;
      const channel = MethodChannel('plugins.flutter.io/camera');
      tester.binding.defaultBinaryMessenger.setMockMethodCallHandler(channel, (
        _,
      ) async {
        cameraCalls++;
        throw PlatformException(code: 'CameraAccessDenied');
      });
      addTearDown(
        () => tester.binding.defaultBinaryMessenger.setMockMethodCallHandler(
          channel,
          null,
        ),
      );
      await tester.binding.setSurfaceSize(const Size(390, 844));
      addTearDown(() => tester.binding.setSurfaceSize(null));
      await tester.pumpWidget(
        MaterialApp(home: ScanScreen(controller: controller)),
      );
      await tester.pumpAndSettle();
      expect(find.text('Pilih foto dari galeri (uji)'), findsOneWidget);
      expect(
        cameraCalls,
        0,
        reason: 'Opening attendance must not request camera',
      );
      if (denyCamera) {
        await tester.tap(find.text('Aktifkan kamera'));
        await tester.pumpAndSettle();
        expect(cameraCalls, 1);
        expect(
          find.text(
            'Izin kamera ditolak. Aktifkan izin untuk melanjutkan absensi.',
          ),
          findsOneWidget,
        );
      }
      await tester.tap(find.text('Konfirmasi Masuk'));
      await tester.pumpAndSettle();
      expect(service.submittedSelfieBytes, isNull);
      expect(controller.page, AppPage.scan);
      expect(
        find.text('Aktifkan kamera atau pilih foto dari galeri.'),
        findsOneWidget,
      );
      expect(tester.takeException(), isNull);
      await tester.pumpWidget(const SizedBox());
    });
  }

  testWidgets('account screen offers profile and device settings', (
    tester,
  ) async {
    SharedPreferences.setMockInitialValues({});
    AttendanceService.currentEmployeeName = 'Dimas Pratama';
    AttendanceService.currentDeviceId = 'device-test';
    final controller = AppController();
    await tester.binding.setSurfaceSize(const Size(390, 844));
    await tester.pumpWidget(
      MaterialApp(
        home: PhoneFrame(child: AccountScreen(controller: controller)),
      ),
    );
    expect(tester.takeException(), isNull);
    expect(find.text('Pengaturan Akun'), findsOneWidget);
    expect(find.text('Dimas Pratama'), findsOneWidget);
    expect(find.text('Pilih foto profil'), findsOneWidget);
    expect(find.text('Izin aplikasi'), findsOneWidget);
    expect(find.text('Perangkat terdaftar'), findsOneWidget);
    expect(find.text('device-test'), findsOneWidget);

    controller.dispose();
  });
}
