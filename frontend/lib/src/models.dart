class AttendanceEntry {
  const AttendanceEntry(this.kind, this.time, this.status, {this.late = false});
  final String kind;
  final String time;
  final String status;
  final bool late;
}

class AttendanceDay {
  const AttendanceDay(this.date, this.entries);
  final String date;
  final List<AttendanceEntry> entries;
}

const attendanceDays = [
  AttendanceDay('Senin, 20 Mei 2024', [
    AttendanceEntry('Masuk', '08:30 WIB', 'Tepat waktu'),
    AttendanceEntry('Pulang', '17:32 WIB', 'Tepat waktu'),
  ]),
  AttendanceDay('Jumat, 17 Mei 2024', [
    AttendanceEntry('Masuk', '09:12 WIB', 'Terlambat', late: true),
    AttendanceEntry('Pulang', '17:45 WIB', 'Tepat waktu'),
  ]),
  AttendanceDay('Kamis, 16 Mei 2024', [
    AttendanceEntry('Masuk', '08:58 WIB', 'Tepat waktu'),
    AttendanceEntry('Pulang', '17:30 WIB', 'Tepat waktu'),
  ]),
];
