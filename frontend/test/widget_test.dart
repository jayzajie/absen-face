import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'package:frontend/src/app.dart';
import 'package:frontend/src/attendance_service.dart';
import 'package:frontend/src/screens/account_screen.dart';

void main() {
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
