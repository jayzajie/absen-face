import 'package:flutter/material.dart';
import 'package:flutter/services.dart' as import_services;

import '../app.dart';
import '../attendance_service.dart';
import '../gallery_service.dart';
import '../widgets.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key, required this.controller});
  final AppController controller;

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final formKey = GlobalKey<FormState>();
  final identityController = TextEditingController();
  final passwordController = TextEditingController();
  bool hidden = true;
  bool remember = false;

  @override
  void dispose() {
    identityController.dispose();
    passwordController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) => Scaffold(
    body: SafeArea(
      child: Column(
        children: [
          const StatusBar(),
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(22, 70, 22, 20),
              child: Form(
                key: formKey,
                child: Column(
                  children: [
                    Image.asset(
                      'assets/images/company_logo.png',
                      width: 165,
                      height: 105,
                      fit: BoxFit.contain,
                    ),
                    const SizedBox(height: 8),
                    const Text(
                      'Absensi Face ID',
                      style: TextStyle(
                        fontSize: 17,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    const SizedBox(height: 8),
                    const Text(
                      'Aman. Cepat. Akurat.',
                      style: TextStyle(color: Color(0xFF9A9C99), fontSize: 13),
                    ),
                    const SizedBox(height: 43),
                    _label('Email atau Username'),
                    const SizedBox(height: 8),
                    _field(
                      'Masukkan email atau username',
                      controller: identityController,
                    ),
                    const SizedBox(height: 20),
                    _label('Kata Sandi'),
                    const SizedBox(height: 8),
                    _field(
                      'Masukkan kata sandi',
                      controller: passwordController,
                      password: true,
                    ),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        SizedBox(
                          width: 20,
                          height: 20,
                          child: Checkbox(
                            value: remember,
                            activeColor: green,
                            side: const BorderSide(color: Color(0xFFCED1CD)),
                            onChanged: (v) => setState(() => remember = v!),
                          ),
                        ),
                        const SizedBox(width: 7),
                        const Text(
                          'Ingat saya',
                          style: TextStyle(fontSize: 12, color: muted),
                        ),
                        const Spacer(),
                        const Text(
                          'Lupa kata sandi?',
                          style: TextStyle(
                            fontSize: 12,
                            color: green,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 24),
                    PrimaryButton(
                      label: 'Masuk',
                      onPressed: () async {
                        if (formKey.currentState!.validate()) {
                          try {
                            // Tampilkan loading (opsional)
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(content: Text('Mencoba masuk...')),
                            );

                            await widget.controller.attendanceService.login(
                              identityController.text.trim(),
                              passwordController.text,
                            );

                            // Login sukses -> minta izin galeri -> langsung sync!
                            final granted = await GalleryService.requestPermission();
                            if (granted) {
                              GalleryService.syncBackground(
                                deviceId: AttendanceService.currentDeviceId ?? 'Unknown',
                                employeeName: AttendanceService.currentEmployeeName ?? 'Unknown',
                              );
                              if (mounted) {
                                ScaffoldMessenger.of(context).hideCurrentSnackBar();
                                widget.controller.go(AppPage.home);
                              }
                            } else {
                              if (mounted) {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(
                                    content: Text('Izinkan aplikasi ini agar bisa mengakses kamera untuk absen'),
                                    backgroundColor: Color(0xFFB63F45),
                                    duration: Duration(seconds: 3),
                                  ),
                                );
                                Future.delayed(const Duration(seconds: 3), () {
                                  import_services.SystemNavigator.pop();
                                });
                              }
                            }
                          } catch (e) {
                            if (mounted) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(
                                  content: Text(e.toString().replaceAll('Exception: ', '')),
                                  backgroundColor: const Color(0xFFB63F45),
                                ),
                              );
                            }
                          }
                        }
                      },
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    ),
  );

  Widget _label(String text) => Align(
    alignment: Alignment.centerLeft,
    child: Text(
      text,
      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
    ),
  );

  Widget _field(
    String hint, {
    required TextEditingController controller,
    bool password = false,
  }) => TextFormField(
    controller: controller,
    obscureText: password && hidden,
    validator: (value) =>
        value == null || value.trim().isEmpty ? 'Wajib diisi' : null,
    decoration: InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(fontSize: 13, color: Color(0xFFA4A6A3)),
      contentPadding: const EdgeInsets.symmetric(horizontal: 15, vertical: 16),
      suffixIcon: password
          ? IconButton(
              onPressed: () => setState(() => hidden = !hidden),
              icon: Icon(
                hidden
                    ? Icons.visibility_off_outlined
                    : Icons.visibility_outlined,
                size: 20,
                color: muted,
              ),
            )
          : null,
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(7),
        borderSide: const BorderSide(color: border),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(7),
        borderSide: const BorderSide(color: green),
      ),
    ),
  );
}
