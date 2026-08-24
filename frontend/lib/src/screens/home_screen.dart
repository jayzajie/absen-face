import 'package:flutter/material.dart';

import '../app.dart';
import '../attendance_service.dart';
import '../widgets.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key, required this.controller});
  final AppController controller;

  @override
  Widget build(BuildContext context) => Scaffold(
    body: SafeArea(
      child: Column(
        children: [
          const StatusBar(),
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(16, 14, 16, 14),
              child: Column(
                children: [
                  _header(),
                  const SizedBox(height: 18),
                  _summary(),
                  const SizedBox(height: 14),
                  Row(
                    children: [
                      Expanded(
                        child: _attendanceButton(
                          'Absen Masuk',
                          '08.30 WIB',
                          true,
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: _attendanceButton('Absen Pulang', '', false),
                      ),
                    ],
                  ),
                  const SizedBox(height: 14),
                  _location(),
                  const SizedBox(height: 14),
                  _schedule(),
                ],
              ),
            ),
          ),
          AppBottomBar(controller: controller, selected: AppPage.home),
        ],
      ),
    ),
  );

  Widget _header() => Row(
    children: [
      Expanded(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Selamat pagi,', style: TextStyle(fontSize: 14, color: muted)),
            const SizedBox(height: 3),
            Text(
              AttendanceService.currentEmployeeName ?? 'Karyawan',
              style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w700),
            ),
            const SizedBox(height: 2),
            const Text(
              'Staff',
              style: TextStyle(fontSize: 13, color: muted),
            ),
          ],
        ),
      ),
      const Icon(Icons.notifications_none_rounded, size: 23),
      const SizedBox(width: 16),
      Container(
        width: 50,
        height: 50,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          border: Border.all(color: border),
          image: const DecorationImage(
            image: AssetImage(portraitAsset),
            fit: BoxFit.cover,
            alignment: Alignment(0, -.6),
          ),
        ),
      ),
    ],
  );

  Widget _summary() => SectionCard(
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Row(
          children: [
            Expanded(
              child: Text(
                'Ringkasan Hari Ini',
                style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
              ),
            ),
            Icon(Icons.calendar_today_outlined, size: 19, color: muted),
          ],
        ),
        const SizedBox(height: 5),
        const Text(
          'Senin, 20 Mei 2024',
          style: TextStyle(fontSize: 11, color: muted),
        ),
        const Padding(
          padding: EdgeInsets.symmetric(vertical: 14),
          child: Divider(height: 1, color: border),
        ),
        Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Jam Kerja',
                    style: TextStyle(fontSize: 11, color: muted),
                  ),
                  SizedBox(height: 8),
                  Text.rich(
                    TextSpan(
                      text: '08:00',
                      style: TextStyle(
                        fontSize: 23,
                        fontWeight: FontWeight.w700,
                      ),
                      children: [
                        TextSpan(
                          text: ' jam',
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w400,
                            color: muted,
                          ),
                        ),
                      ],
                    ),
                  ),
                  SizedBox(height: 8),
                  Text(
                    'Target 08:00 jam',
                    style: TextStyle(fontSize: 10, color: muted),
                  ),
                ],
              ),
            ),
            Container(width: 1, height: 72, color: border),
            const SizedBox(width: 17),
            const Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Status Kehadiran',
                    style: TextStyle(fontSize: 11, color: muted),
                  ),
                  SizedBox(height: 8),
                  Text(
                    'Hadir',
                    style: TextStyle(
                      fontSize: 20,
                      color: green,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  SizedBox(height: 8),
                  Row(
                    children: [
                      Text(
                        'Tepat waktu',
                        style: TextStyle(
                          fontSize: 10,
                          color: green,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      SizedBox(width: 6),
                      CircleAvatar(radius: 3, backgroundColor: green),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ],
    ),
  );

  Widget _attendanceButton(String title, String time, bool active) => SizedBox(
    height: 76,
    child: active
        ? FilledButton(
            onPressed: () => controller.startScan('masuk'),
            style: FilledButton.styleFrom(
              backgroundColor: green,
              padding: const EdgeInsets.symmetric(horizontal: 14),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(7),
              ),
            ),
            child: _attendanceContent(title, time, Colors.white),
          )
        : OutlinedButton(
            onPressed: () => controller.startScan('pulang'),
            style: OutlinedButton.styleFrom(
              padding: const EdgeInsets.symmetric(horizontal: 14),
              side: const BorderSide(color: border),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(7),
              ),
            ),
            child: _attendanceContent(title, time, ink),
          ),
  );

  Widget _attendanceContent(String title, String time, Color color) => Row(
    children: [
      Icon(Icons.login_rounded, color: color, size: 26),
      const SizedBox(width: 11),
      Expanded(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              title,
              style: TextStyle(
                color: color,
                fontSize: 13,
                fontWeight: FontWeight.w700,
              ),
            ),
            if (time.isNotEmpty) ...[
              const SizedBox(height: 5),
              Text(
                time,
                style: TextStyle(
                  color: color.withValues(alpha: .7),
                  fontSize: 10,
                ),
              ),
            ],
          ],
        ),
      ),
    ],
  );

  Widget _location() => SectionCard(
    child: Row(
      children: [
        const Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Lokasi Kantor',
                style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13),
              ),
              SizedBox(height: 8),
              Text(
                'Kantor Pusat',
                style: TextStyle(fontWeight: FontWeight.w700, fontSize: 12),
              ),
              SizedBox(height: 3),
              Text(
                'Jl. Jend. Sudirman Kav. 52-53,\nJakarta Selatan',
                style: TextStyle(fontSize: 10, color: muted, height: 1.45),
              ),
            ],
          ),
        ),
        Container(
          width: 96,
          height: 76,
          decoration: BoxDecoration(
            color: const Color(0xFFF1F2ED),
            borderRadius: BorderRadius.circular(7),
          ),
          child: CustomPaint(painter: _MapPainter()),
        ),
      ],
    ),
  );

  Widget _schedule() => const SectionCard(
    child: Row(
      children: [
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Jadwal Kerja',
                style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13),
              ),
              SizedBox(height: 8),
              Text('Senin – Jumat', style: TextStyle(fontSize: 11)),
              SizedBox(height: 3),
              Text(
                '09:00 – 17:00 WIB',
                style: TextStyle(fontSize: 11, color: muted),
              ),
            ],
          ),
        ),
        Icon(Icons.schedule_rounded, color: muted, size: 30),
      ],
    ),
  );
}

class _MapPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size s) {
    final road = Paint()
      ..color = Colors.white
      ..strokeWidth = 7
      ..strokeCap = StrokeCap.round;
    canvas.drawLine(
      Offset(5, s.height * .2),
      Offset(s.width * .85, s.height),
      road,
    );
    canvas.drawLine(
      Offset(s.width * .2, s.height),
      Offset(s.width * .95, 4),
      road,
    );
    canvas.drawLine(
      Offset(0, s.height * .65),
      Offset(s.width, s.height * .3),
      road,
    );
    final pin = Paint()..color = const Color(0xFF1675C1);
    canvas.drawCircle(Offset(s.width * .55, s.height * .48), 9, pin);
    canvas.drawCircle(
      Offset(s.width * .55, s.height * .48),
      3,
      Paint()..color = Colors.white,
    );
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
