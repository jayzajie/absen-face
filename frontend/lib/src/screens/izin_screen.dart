import 'package:flutter/material.dart';
import '../app.dart';
import '../widgets.dart';

class IzinScreen extends StatelessWidget {
  const IzinScreen({super.key, required this.controller});
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
                    'Pengajuan Izin / Cuti',
                    style: TextStyle(fontSize: 22, fontWeight: FontWeight.w700),
                  ),
                  const SizedBox(height: 16),
                  SectionCard(
                    child: Column(
                      children: [
                        const Icon(Icons.beach_access_rounded, size: 48, color: green),
                        const SizedBox(height: 16),
                        const Text(
                          'Fitur Izin Belum Tersedia',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700),
                        ),
                        const SizedBox(height: 8),
                        const Text(
                          'Pengajuan izin/cuti akan segera hadir di pembaruan aplikasi berikutnya.',
                          textAlign: TextAlign.center,
                          style: TextStyle(fontSize: 13, color: muted),
                        ),
                        const SizedBox(height: 24),
                        PrimaryButton(
                          label: 'Kembali ke Beranda',
                          onPressed: () => controller.go(AppPage.home),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
          AppBottomBar(controller: controller, selected: AppPage.izin),
        ],
      ),
    ),
  );
}
