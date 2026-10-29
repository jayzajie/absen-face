package com.example.frontend.data

enum class AppPage {
    LOGIN, HOME, SCAN, HISTORY, IZIN, ACCOUNT, SUCCESS
}

data class LoginResponse(
    val name: String,
    val token: String
)

data class AttendanceResult(
    val id: Long? = null,
    val employeeName: String,
    val type: String,
    val occurredAt: String,
    val status: String,
    val isLate: Boolean = false,
    val diffMinutes: Int = 0,
    val checkInTime: String? = null,
    val faceMatchScore: Double? = null,
    val faceThreshold: Double? = null
)

data class AttendanceRecord(
    val date: String,
    val time: String,
    val type: String,
    val status: String
)
