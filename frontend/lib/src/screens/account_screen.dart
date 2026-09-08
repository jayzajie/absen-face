import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../app.dart';
import '../attendance_service.dart';
import '../widgets.dart';

class AccountScreen extends StatefulWidget {
  const AccountScreen({super.key, required this.controller});
  final AppController controller;

  @override
  State<AccountScreen> createState() => _AccountScreenState();
}

class _AccountScreenState extends State<AccountScreen> {
  Future<void> _choosePhoto() async {
    try {
      final photo = await ImagePicker().pickImage(
        source: ImageSource.gallery,
        requestFullMetadata: false,
      );
      if (photo == null) return;
      await widget.controller.setProfilePhoto(photo.path);
      if (mounted) setState(() {});
    } catch (_) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Foto tidak dapat dipilih. Coba lagi.')),
        );
      }
    }
  }

  Future<void> _logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();
    AttendanceService.currentEmployeeName = null;
    AttendanceService.currentDeviceId = null;
    await widget.controller.removeProfilePhoto();
    widget.controller.go(AppPage.login);
  }

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
                    'Pengaturan Akun',
                    style: TextStyle(fontSize: 22, fontWeight: FontWeight.w700),
                  ),
                  const SizedBox(height: 16),
                  SectionCard(
                    child: Column(
                      children: [
                        Stack(
                          clipBehavior: Clip.none,
                          children: [
                            ProfileAvatar(
                              file: widget.controller.profilePhoto,
                              size: 88,
                            ),
                            Positioned(
                              right: -5,
                              bottom: -3,
                              child: Material(
                                color: green,
                                shape: const CircleBorder(),
                                child: IconButton(
                                  onPressed: _choosePhoto,
                                  tooltip: 'Ganti foto profil',
                                  constraints: const BoxConstraints.tightFor(
                                    width: 36,
                                    height: 36,
                                  ),
                                  padding: EdgeInsets.zero,
                                  icon: const Icon(
                                    Icons.camera_alt_rounded,
                                    size: 18,
                                    color: Colors.white,
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
                        Text(
                          AttendanceService.currentEmployeeName ?? 'Karyawan',
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'Staff',
                          style: TextStyle(fontSize: 13, color: muted),
                        ),
                        const SizedBox(height: 12),
                        TextButton.icon(
                          onPressed: _choosePhoto,
                          icon: const Icon(Icons.edit_rounded, size: 17),
                          label: Text(
                            widget.controller.profilePhoto == null
                                ? 'Pilih foto profil'
                                : 'Ganti foto profil',
                          ),
                        ),
                        if (widget.controller.profilePhoto != null)
                          TextButton(
                            onPressed: () async {
                              await widget.controller.removeProfilePhoto();
                              if (mounted) setState(() {});
                            },
                            child: const Text('Hapus foto profil'),
                          ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 14),
                  const Text(
                    'Preferensi',
                    style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700),
                  ),
                  const SizedBox(height: 8),
                  SectionCard(
                    padding: EdgeInsets.zero,
                    child: Column(
                      children: [
                        ListTile(
                          onTap: openAppSettings,
                          leading: const Icon(
                            Icons.admin_panel_settings_outlined,
                            color: green,
                          ),
                          title: const Text('Izin aplikasi'),
                          subtitle: const Text('Kelola akses kamera'),
                          trailing: const Icon(Icons.chevron_right_rounded),
                        ),
                        const Divider(height: 1, color: border),
                        ListTile(
                          leading: const Icon(
                            Icons.phonelink_lock_outlined,
                            color: green,
                          ),
                          title: const Text('Perangkat terdaftar'),
                          subtitle: Text(
                            AttendanceService.currentDeviceId ?? '-',
                          ),
                        ),
                        const Divider(height: 1, color: border),
                        const ListTile(
                          leading: Icon(
                            Icons.privacy_tip_outlined,
                            color: green,
                          ),
                          title: Text('Privasi & keamanan'),
                          subtitle: Text(
                            'Kamera hanya pratinjau, tanpa verifikasi wajah. Foto profil pilihan Anda disimpan lokal.',
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),
                  SizedBox(
                    width: double.infinity,
                    child: OutlinedButton.icon(
                      onPressed: _logout,
                      icon: const Icon(
                        Icons.logout_rounded,
                        size: 18,
                        color: brandRed,
                      ),
                      label: const Text(
                        'Keluar',
                        style: TextStyle(color: brandRed),
                      ),
                      style: OutlinedButton.styleFrom(
                        side: const BorderSide(color: brandRed),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          AppBottomBar(
            controller: widget.controller,
            selected: AppPage.account,
          ),
        ],
      ),
    ),
  );
}
