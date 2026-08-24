import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../app.dart';
import '../attendance_service.dart';
import '../widgets.dart';

class AccountScreen extends StatelessWidget {
  const AccountScreen({super.key, required this.controller});
  final AppController controller;

  @override
  Widget build(BuildContext context) => Scaffold(
    body: SafeArea(
      child: Column(
        children: [
          const StatusBar(),
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Akun Saya',
                    style: TextStyle(fontSize: 22, fontWeight: FontWeight.w700),
                  ),
                  const SizedBox(height: 16),
                  SectionCard(
                    child: Column(
                      children: [
                        Container(
                          width: 80,
                          height: 80,
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
                        const SizedBox(height: 16),
                        Text(
                          AttendanceService.currentEmployeeName ?? 'Unknown Employee',
                          style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'ID Perangkat: ${AttendanceService.currentDeviceId ?? '-'}',
                          style: const TextStyle(fontSize: 13, color: muted),
                        ),
                        const SizedBox(height: 24),
                        OutlinedButton.icon(
                          onPressed: () async {
                            final prefs = await SharedPreferences.getInstance();
                            await prefs.clear();
                            AttendanceService.currentEmployeeName = null;
                            AttendanceService.currentDeviceId = null;
                            controller.go(AppPage.login);
                          },
                          icon: const Icon(Icons.logout_rounded, size: 18, color: Color(0xFFB63F45)),
                          label: const Text(
                            'Keluar',
                            style: TextStyle(color: Color(0xFFB63F45)),
                          ),
                          style: OutlinedButton.styleFrom(
                            side: const BorderSide(color: Color(0xFFB63F45)),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(7),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
          AppBottomBar(controller: controller, selected: AppPage.account),
        ],
      ),
    ),
  );
}
