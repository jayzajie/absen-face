import 'package:flutter/material.dart';

import 'app.dart';

class StatusBar extends StatelessWidget {
  const StatusBar({super.key});

  @override
  Widget build(BuildContext context) => const SizedBox(
    height: 36,
    child: Padding(
      padding: EdgeInsets.symmetric(horizontal: 22),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            '9:41',
            style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700),
          ),
          Row(
            children: [
              Icon(Icons.signal_cellular_alt, size: 15),
              SizedBox(width: 4),
              Icon(Icons.wifi, size: 15),
              SizedBox(width: 4),
              Icon(Icons.battery_full, size: 17),
            ],
          ),
        ],
      ),
    ),
  );
}

class BrandMark extends StatelessWidget {
  const BrandMark({super.key, this.size = 60});
  final double size;

  @override
  Widget build(BuildContext context) =>
      CustomPaint(size: Size.square(size), painter: _BrandPainter());
}

class _BrandPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final p = Paint()
      ..color = green
      ..style = PaintingStyle.stroke
      ..strokeWidth = size.width * .055
      ..strokeCap = StrokeCap.round
      ..strokeJoin = StrokeJoin.round;
    final w = size.width, h = size.height;
    canvas.drawPath(
      Path()
        ..moveTo(w * .5, h * .06)
        ..lineTo(w * .88, h * .23)
        ..lineTo(w * .84, h * .67)
        ..quadraticBezierTo(w * .7, h * .88, w * .5, h * .96)
        ..quadraticBezierTo(w * .3, h * .88, w * .16, h * .67)
        ..lineTo(w * .12, h * .23)
        ..close(),
      p,
    );
    canvas.drawCircle(
      Offset(w * .38, h * .43),
      w * .025,
      p..style = PaintingStyle.fill,
    );
    canvas.drawCircle(Offset(w * .62, h * .43), w * .025, p);
    p.style = PaintingStyle.stroke;
    canvas.drawPath(
      Path()
        ..moveTo(w * .34, h * .61)
        ..quadraticBezierTo(w * .5, h * .75, w * .66, h * .61),
      p,
    );
    canvas.drawPath(
      Path()
        ..moveTo(w * .27, h * .31)
        ..lineTo(w * .27, h * .43)
        ..moveTo(w * .73, h * .31)
        ..lineTo(w * .73, h * .43)
        ..moveTo(w * .42, h * .53)
        ..quadraticBezierTo(w * .5, h * .58, w * .52, h * .5),
      p,
    );
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}

class PrimaryButton extends StatelessWidget {
  const PrimaryButton({
    super.key,
    required this.label,
    required this.onPressed,
    this.icon,
  });
  final String label;
  final VoidCallback onPressed;
  final IconData? icon;

  @override
  Widget build(BuildContext context) => SizedBox(
    width: double.infinity,
    height: 52,
    child: FilledButton.icon(
      onPressed: onPressed,
      icon: icon == null ? const SizedBox.shrink() : Icon(icon, size: 21),
      label: Text(
        label,
        style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700),
      ),
      style: FilledButton.styleFrom(
        backgroundColor: green,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(7)),
      ),
    ),
  );
}

class AppBottomBar extends StatelessWidget {
  const AppBottomBar({
    super.key,
    required this.controller,
    required this.selected,
  });
  final AppController controller;
  final AppPage selected;

  @override
  Widget build(BuildContext context) => Container(
    height: 72,
    decoration: const BoxDecoration(
      color: Colors.white,
      border: Border(top: BorderSide(color: border)),
    ),
    child: Row(
      children: [
        _item(Icons.home_rounded, 'Beranda', AppPage.home),
        _item(Icons.history_rounded, 'Riwayat', AppPage.history),
        _item(Icons.calendar_month_outlined, 'Izin', AppPage.izin),
        _item(Icons.person_outline_rounded, 'Akun', AppPage.account),
      ],
    ),
  );

  Widget _item(IconData icon, String label, AppPage page) {
    final active = selected == page;
    return Expanded(
      child: InkWell(
        onTap: () => controller.go(page),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              icon,
              color: active ? green : const Color(0xFF929592),
              size: 23,
            ),
            const SizedBox(height: 4),
            Text(
              label,
              style: TextStyle(
                fontSize: 10,
                fontWeight: active ? FontWeight.w700 : FontWeight.w400,
                color: active ? green : muted,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class SectionCard extends StatelessWidget {
  const SectionCard({
    super.key,
    required this.child,
    this.padding = const EdgeInsets.all(16),
  });
  final Widget child;
  final EdgeInsets padding;
  @override
  Widget build(BuildContext context) => Container(
    width: double.infinity,
    padding: padding,
    decoration: BoxDecoration(
      color: Colors.white,
      border: Border.all(color: border),
      borderRadius: BorderRadius.circular(8),
    ),
    child: child,
  );
}

class ScreenTitle extends StatelessWidget {
  const ScreenTitle({super.key, required this.title, required this.onBack});
  final String title;
  final VoidCallback onBack;
  @override
  Widget build(BuildContext context) => SizedBox(
    height: 58,
    child: Stack(
      alignment: Alignment.center,
      children: [
        Align(
          alignment: Alignment.centerLeft,
          child: IconButton(
            onPressed: onBack,
            icon: const Icon(Icons.arrow_back, size: 22),
          ),
        ),
        Text(
          title,
          style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 16),
        ),
      ],
    ),
  );
}
