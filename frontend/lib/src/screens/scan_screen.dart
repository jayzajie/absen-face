import 'package:camera/camera.dart';
import 'package:flutter/material.dart';

import '../app.dart';
import '../widgets.dart';

class ScanScreen extends StatefulWidget {
  const ScanScreen({super.key, required this.controller});
  final AppController controller;

  @override
  State<ScanScreen> createState() => _ScanScreenState();
}

class _ScanScreenState extends State<ScanScreen> {
  CameraController? camera;
  String? cameraError;
  bool sending = false;
  bool startingCamera = false;

  bool get cameraReady => camera?.value.isInitialized ?? false;

  Future<void> _startCamera() async {
    setState(() {
      startingCamera = true;
      cameraError = null;
    });
    CameraController? pendingCamera;
    try {
      final cameras = await availableCameras();
      if (cameras.isEmpty) {
        throw CameraException('noCamera', 'Kamera tidak ditemukan');
      }
      final selected = cameras.firstWhere(
        (item) => item.lensDirection == CameraLensDirection.front,
        orElse: () => cameras.first,
      );
      final controller = CameraController(
        selected,
        ResolutionPreset.high,
        enableAudio: false,
      );
      pendingCamera = controller;
      await controller.initialize();
      if (!mounted) {
        await controller.dispose();
        return;
      }
      camera = controller;
      setState(() {});
    } on CameraException catch (error) {
      await pendingCamera?.dispose();
      if (mounted) setState(() => cameraError = _cameraMessage(error.code));
    } catch (_) {
      await pendingCamera?.dispose();
      if (mounted) setState(() => cameraError = 'Kamera tidak dapat digunakan');
    } finally {
      if (mounted) setState(() => startingCamera = false);
    }
  }

  String _cameraMessage(String code) => switch (code) {
    'CameraAccessDenied' || 'CameraAccessDeniedWithoutPrompt' =>
      'Izin kamera ditolak. Anda tetap dapat konfirmasi absensi.',
    _ => 'Kamera tidak dapat digunakan',
  };

  Future<void> _confirm() async {
    setState(() => sending = true);
    try {
      await widget.controller.submitAttendance(
        cameraAccessGranted: cameraReady,
      );
    } catch (error) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal menghubungi server: $error')),
        );
        setState(() => sending = false);
      }
    }
  }

  @override
  void dispose() {
    camera?.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) => Scaffold(
    body: SafeArea(
      child: Column(
        children: [
          const StatusBar(),
          ScreenTitle(
            title: 'Konfirmasi Absensi',
            onBack: () => widget.controller.go(AppPage.home),
          ),
          Expanded(
            child: Stack(
              children: [
                Positioned.fill(child: _cameraPreview()),
                const Positioned(
                  top: 14,
                  left: 0,
                  right: 0,
                  child: Text(
                    'Pratinjau kamera • tidak merekam foto atau video',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 12,
                      shadows: [Shadow(color: Colors.black54, blurRadius: 4)],
                    ),
                  ),
                ),
                if (cameraReady)
                  Positioned(
                    top: 50,
                    left: 0,
                    right: 0,
                    child: Center(
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 12,
                          vertical: 7,
                        ),
                        decoration: BoxDecoration(
                          color: green,
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: const Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                              Icons.videocam_rounded,
                              color: Colors.white,
                              size: 15,
                            ),
                            SizedBox(width: 7),
                            Text(
                              'Kamera aktif',
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 11,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                Positioned(
                  top: 64,
                  left: 20,
                  right: 20,
                  bottom: 20,
                  child: IgnorePointer(
                    child: CustomPaint(painter: _FaceFramePainter()),
                  ),
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.fromLTRB(18, 18, 18, 22),
            decoration: const BoxDecoration(
              color: Color(0xFFF7FAFC),
              borderRadius: BorderRadius.vertical(top: Radius.circular(14)),
            ),
            child: Column(
              children: [
                Row(
                  children: [
                    Icon(
                      cameraError == null
                          ? Icons.camera_alt_outlined
                          : Icons.error_outline,
                      size: 20,
                      color: cameraError == null ? green : Colors.red,
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Text(
                        cameraError ??
                            (cameraReady
                                ? 'Kamera aktif. Konfirmasi untuk mencatat kehadiran.'
                                : 'Pratinjau opsional. Absensi dapat dilanjutkan tanpa kamera.'),
                        style: TextStyle(
                          fontSize: 12,
                          color: cameraError == null ? muted : Colors.red,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 15),
                const Row(
                  children: [
                    Icon(
                      Icons.lightbulb_outline_rounded,
                      size: 19,
                      color: muted,
                    ),
                    SizedBox(width: 10),
                    Expanded(
                      child: Text(
                        'Tidak ada pencocokan atau verifikasi wajah',
                        style: TextStyle(fontSize: 11, color: muted),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 20),
                PrimaryButton(
                  label: sending
                      ? 'Mengirim...'
                      : 'Konfirmasi ${widget.controller.pendingAttendance == 'masuk' ? 'Masuk' : 'Pulang'}',
                  onPressed: !sending ? _confirm : () {},
                ),
              ],
            ),
          ),
        ],
      ),
    ),
  );

  Widget _cameraPreview() {
    if (!cameraReady) {
      return Container(
        color: const Color(0xFF202522),
        child: Center(
          child: startingCamera
              ? const CircularProgressIndicator(color: Colors.white)
              : FilledButton.icon(
                  onPressed: _startCamera,
                  icon: const Icon(Icons.videocam_outlined),
                  label: const Text('Aktifkan pratinjau (opsional)'),
                ),
        ),
      );
    }
    final size = camera!.value.previewSize!;
    return ClipRect(
      child: FittedBox(
        fit: BoxFit.cover,
        child: SizedBox(
          width: size.height,
          height: size.width,
          child: CameraPreview(camera!),
        ),
      ),
    );
  }
}

class _FaceFramePainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final p = Paint()
      ..color = Colors.white
      ..strokeWidth = 4
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round;
    const length = 43.0, radius = 18.0;
    final r = RRect.fromRectAndRadius(
      Offset.zero & size,
      const Radius.circular(radius),
    );
    final path = Path()
      ..moveTo(r.left, r.top + length)
      ..lineTo(r.left, r.top + radius)
      ..quadraticBezierTo(r.left, r.top, r.left + radius, r.top)
      ..lineTo(r.left + length, r.top)
      ..moveTo(r.right - length, r.top)
      ..lineTo(r.right - radius, r.top)
      ..quadraticBezierTo(r.right, r.top, r.right, r.top + radius)
      ..lineTo(r.right, r.top + length)
      ..moveTo(r.right, r.bottom - length)
      ..lineTo(r.right, r.bottom - radius)
      ..quadraticBezierTo(r.right, r.bottom, r.right - radius, r.bottom)
      ..lineTo(r.right - length, r.bottom)
      ..moveTo(r.left + length, r.bottom)
      ..lineTo(r.left + radius, r.bottom)
      ..quadraticBezierTo(r.left, r.bottom, r.left, r.bottom - radius)
      ..lineTo(r.left, r.bottom - length);
    canvas.drawPath(path, p);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
