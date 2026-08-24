import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../app.dart';
import '../widgets.dart';

class SuccessScreen extends StatelessWidget {
  const SuccessScreen({super.key, required this.controller});
  final AppController controller;

  @override
  Widget build(BuildContext context) {
    final data = controller.lastAttendanceData;
    if (data == null) {
      return Scaffold(
        body: Center(
          child: PrimaryButton(
            label: 'Kembali',
            onPressed: () => controller.go(AppPage.home),
          ),
        ),
      );
    }

    final type = data['type'] as String;
    final isMasuk = type == 'masuk';
    final rawDate = DateTime.parse(data['occurred_at']).toLocal();
    final dayStr = DateFormat('EEEE, d MMM yyyy', 'id_ID').format(rawDate);
    final timeStr = DateFormat('HH:mm').format(rawDate);

    final isLate = data['is_late'] as bool? ?? false;
    final diffMins = (data['diff_minutes'] as num?)?.toInt().abs() ?? 0;
    
    final checkInTime = data['check_in_time'] as String?;

    return Scaffold(
      backgroundColor: green,
      body: SafeArea(
        child: Column(
          children: [
            const Padding(
              padding: EdgeInsets.symmetric(horizontal: 22, vertical: 10),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    '9:41',
                    style: TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w700,
                      color: Colors.white,
                    ),
                  ),
                  Row(
                    children: [
                      Icon(Icons.signal_cellular_alt, size: 15, color: Colors.white),
                      SizedBox(width: 4),
                      Icon(Icons.wifi, size: 15, color: Colors.white),
                      SizedBox(width: 4),
                      Icon(Icons.battery_full, size: 17, color: Colors.white),
                    ],
                  ),
                ],
              ),
            ),
            Expanded(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 24),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Container(
                      width: 80,
                      height: 80,
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(
                        Icons.check_rounded,
                        size: 50,
                        color: green,
                      ),
                    ),
                    const SizedBox(height: 24),
                    Text(
                      isMasuk ? 'Absen Masuk Berhasil!' : 'Absen Pulang Berhasil!',
                      style: const TextStyle(
                        fontSize: 22,
                        fontWeight: FontWeight.w800,
                        color: Colors.white,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      '$dayStr jam $timeStr WIB',
                      style: TextStyle(
                        fontSize: 14,
                        color: Colors.white.withValues(alpha: .8),
                      ),
                    ),
                    const SizedBox(height: 32),
                    Container(
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Column(
                        children: [
                          if (isMasuk) ...[
                            _infoRow('Status', isLate ? 'Terlambat' : 'Tepat Waktu', isLate ? const Color(0xFFB63F45) : green),
                            const Divider(height: 24, color: border),
                            _infoRow('Selisih Waktu', isLate ? 'Telat $diffMins menit' : 'Lebih cepat $diffMins menit', ink),
                            const Divider(height: 24, color: border),
                            _infoRow('Target Jam', '09:00 WIB', muted),
                          ] else ...[
                            _infoRow('Absen Masuk', checkInTime ?? '--:--', ink),
                            const Divider(height: 24, color: border),
                            _infoRow('Absen Keluar', timeStr, green),
                          ],
                        ],
                      ),
                    ),
                    const SizedBox(height: 48),
                    PrimaryButton(
                      label: 'Selesai',
                      onPressed: () => controller.go(AppPage.home),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _infoRow(String label, String value, Color valueColor) => Row(
    mainAxisAlignment: MainAxisAlignment.spaceBetween,
    children: [
      Text(label, style: const TextStyle(fontSize: 14, color: muted)),
      Text(
        value,
        style: TextStyle(
          fontSize: 15,
          fontWeight: FontWeight.w700,
          color: valueColor,
        ),
      ),
    ],
  );
}
