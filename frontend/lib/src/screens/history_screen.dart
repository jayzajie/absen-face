import 'package:flutter/material.dart';

import '../app.dart';
import '../models.dart';
import '../widgets.dart';

class HistoryScreen extends StatelessWidget {
  const HistoryScreen({super.key, required this.controller});
  final AppController controller;

  @override
  Widget build(BuildContext context) => Scaffold(
    body: SafeArea(
      child: Column(
        children: [
          const StatusBar(),
          ScreenTitle(
            title: 'Riwayat Absensi',
            onBack: () => controller.go(AppPage.home),
          ),
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(14, 0, 14, 18),
              child: Column(
                children: [
                  Row(
                    children: [
                      Expanded(child: _filter('Mei 2024')),
                      const SizedBox(width: 8),
                      Expanded(child: _filter('Semua Status')),
                    ],
                  ),
                  const SizedBox(height: 18),
                  const Row(
                    children: [
                      Expanded(child: _StatCard('Total Hadir', '20', 'hari')),
                      SizedBox(width: 8),
                      Expanded(child: _StatCard('Tepat Waktu', '18', 'hari')),
                      SizedBox(width: 8),
                      Expanded(child: _StatCard('Terlambat', '2', 'hari')),
                    ],
                  ),
                  const SizedBox(height: 20),
                  ...attendanceDays.map(_day),
                ],
              ),
            ),
          ),
          AppBottomBar(controller: controller, selected: AppPage.history),
        ],
      ),
    ),
  );

  Widget _filter(String text) => Container(
    height: 42,
    padding: const EdgeInsets.symmetric(horizontal: 12),
    decoration: BoxDecoration(
      color: Colors.white,
      border: Border.all(color: border),
      borderRadius: BorderRadius.circular(7),
    ),
    child: Row(
      children: [
        Expanded(child: Text(text, style: const TextStyle(fontSize: 12))),
        const Icon(Icons.keyboard_arrow_down_rounded, size: 19, color: muted),
      ],
    ),
  );

  Widget _day(AttendanceDay day) => Padding(
    padding: const EdgeInsets.only(bottom: 20),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.only(left: 3, bottom: 10),
          child: Text(
            day.date,
            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600),
          ),
        ),
        Container(
          decoration: BoxDecoration(
            color: Colors.white,
            border: Border.all(color: border),
            borderRadius: BorderRadius.circular(7),
          ),
          child: Column(
            children: [
              for (var i = 0; i < day.entries.length; i++) ...[
                _entry(day.entries[i]),
                if (i != day.entries.length - 1)
                  const Divider(height: 1, color: border),
              ],
            ],
          ),
        ),
      ],
    ),
  );

  Widget _entry(AttendanceEntry entry) => SizedBox(
    height: 57,
    child: Padding(
      padding: const EdgeInsets.symmetric(horizontal: 11),
      child: Row(
        children: [
          Container(
            width: 31,
            height: 31,
            decoration: BoxDecoration(
              color: const Color(0xFFE7F0EB),
              borderRadius: BorderRadius.circular(5),
            ),
            child: const Icon(Icons.login_rounded, color: green, size: 19),
          ),
          const SizedBox(width: 11),
          Expanded(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  entry.kind,
                  style: const TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                const SizedBox(height: 3),
                Text(
                  entry.time,
                  style: const TextStyle(fontSize: 10, color: muted),
                ),
              ],
            ),
          ),
          Text(
            entry.status,
            style: TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.w600,
              color: entry.late ? const Color(0xFFE86822) : green,
            ),
          ),
          const SizedBox(width: 7),
          CircleAvatar(
            radius: 3,
            backgroundColor: entry.late ? const Color(0xFFE86822) : green,
          ),
          const SizedBox(width: 8),
          const Icon(Icons.chevron_right_rounded, size: 20, color: muted),
        ],
      ),
    ),
  );
}

class _StatCard extends StatelessWidget {
  const _StatCard(this.label, this.value, this.unit);
  final String label;
  final String value;
  final String unit;
  @override
  Widget build(BuildContext context) => Container(
    height: 82,
    padding: const EdgeInsets.all(10),
    decoration: BoxDecoration(
      color: Colors.white,
      border: Border.all(color: border),
      borderRadius: BorderRadius.circular(7),
    ),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 10, color: muted)),
        const SizedBox(height: 5),
        Text.rich(
          TextSpan(
            text: value,
            style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700),
            children: [
              TextSpan(
                text: ' hari',
                style: TextStyle(
                  fontSize: 10,
                  color: muted,
                  fontWeight: FontWeight.w400,
                ),
              ),
            ],
          ),
        ),
      ],
    ),
  );
}
