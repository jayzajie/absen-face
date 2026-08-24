import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';

import 'package:frontend/src/app.dart';

void main() {
  testWidgets('login opens attendance dashboard', (tester) async {
    await tester.binding.setSurfaceSize(const Size(390, 844));
    await tester.pumpWidget(const AbsenKuApp());
    expect(tester.takeException(), isNull);

    expect(find.text('Absensi Face ID'), findsOneWidget);
    await tester.enterText(find.byType(TextFormField).first, 'dimas');
    await tester.enterText(find.byType(TextFormField).last, 'password');
    await tester.tap(find.text('Masuk'));
    await tester.pump();
    expect(tester.takeException(), isNull);

    expect(find.text('Dimas Pratama'), findsOneWidget);
    expect(find.text('Ringkasan Hari Ini'), findsOneWidget);

    await tester.tap(find.text('Riwayat'));
    await tester.pump();
    expect(tester.takeException(), isNull);
    expect(find.text('Riwayat Absensi'), findsOneWidget);
    expect(find.text('Senin, 20 Mei 2024'), findsOneWidget);
  });
}
