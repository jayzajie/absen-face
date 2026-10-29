package com.example.frontend.data

import android.os.Build
import com.example.frontend.BuildConfig
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.MultipartBody
import okhttp3.OkHttpClient
import okhttp3.Request
import okhttp3.RequestBody.Companion.toRequestBody
import org.json.JSONObject
import java.io.IOException
import java.util.concurrent.TimeUnit

class AttendanceApi {
    private val client = OkHttpClient.Builder()
        .connectTimeout(20, TimeUnit.SECONDS)
        .readTimeout(30, TimeUnit.SECONDS)
        .writeTimeout(30, TimeUnit.SECONDS)
        .build()

    val deviceId: String = "${Build.MANUFACTURER} ${Build.MODEL}".take(100)

    suspend fun login(username: String, password: String): LoginResponse = withContext(Dispatchers.IO) {
        val base = BuildConfig.API_URL.trimEnd('/')
        val jsonBody = JSONObject().apply {
            put("username", username)
            put("password", password)
            put("device_id", deviceId)
        }.toString()

        val request = Request.Builder()
            .url("$base/mobile/login")
            .header("Accept", "application/json")
            .header("Content-Type", "application/json")
            .post(jsonBody.toRequestBody("application/json; charset=utf-8".toMediaType()))
            .build()

        client.newCall(request).execute().use { response ->
            val body = response.body?.string().orEmpty()
            if (!response.isSuccessful) {
                val errorMsg = try {
                    JSONObject(body).optString("message", "Username atau password salah.")
                } catch (_: Exception) {
                    "Username atau password salah."
                }
                throw IOException(errorMsg)
            }

            val json = JSONObject(body)
            val name = json.optString("name")
            val token = json.optString("token")
            val hasFace = json.optBoolean("has_face_registered", false)
            if (token.isBlank()) {
                throw IOException("Token login tidak diterima dari server.")
            }
            LoginResponse(name = name, token = token, hasFaceRegistered = hasFace)
        }
    }

    suspend fun registerFace(
        token: String,
        faceBytes: ByteArray,
        fileName: String
    ): Boolean = withContext(Dispatchers.IO) {
        val base = BuildConfig.API_URL.trimEnd('/')
        val imageMediaType = "image/jpeg".toMediaType()

        val multipartBody = MultipartBody.Builder()
            .setType(MultipartBody.FORM)
            .addFormDataPart(
                "face_photo",
                fileName,
                faceBytes.toRequestBody(imageMediaType)
            )
            .build()

        val request = Request.Builder()
            .url("$base/mobile/register-face")
            .header("Accept", "application/json")
            .header("Authorization", "Bearer $token")
            .post(multipartBody)
            .build()

        client.newCall(request).execute().use { response ->
            val body = response.body?.string().orEmpty()
            if (!response.isSuccessful) {
                val errorMsg = try {
                    JSONObject(body).optString("message", "Gagal mendaftarkan wajah ($response.code).")
                } catch (_: Exception) {
                    "Gagal mendaftarkan wajah ($response.code)."
                }
                throw IOException(errorMsg)
            }
            true
        }
    }

    suspend fun recordAttendanceVerified(
        token: String,
        type: String,
        selfieBytes: ByteArray,
        selfieFileName: String
    ): AttendanceResult = withContext(Dispatchers.IO) {
        val base = BuildConfig.API_URL.trimEnd('/')
        val imageMediaType = "image/jpeg".toMediaType()

        val multipartBody = MultipartBody.Builder()
            .setType(MultipartBody.FORM)
            .addFormDataPart("type", type)
            .addFormDataPart("device_id", deviceId)
            .addFormDataPart(
                "selfie",
                selfieFileName,
                selfieBytes.toRequestBody(imageMediaType)
            )
            .build()

        val request = Request.Builder()
            .url("$base/attendances/verify")
            .header("Accept", "application/json")
            .header("Authorization", "Bearer $token")
            .post(multipartBody)
            .build()

        client.newCall(request).execute().use { response ->
            val body = response.body?.string().orEmpty()
            if (!response.isSuccessful) {
                val errorMsg = try {
                    JSONObject(body).optString("message", "Gagal memverifikasi kehadiran ($response.code).")
                } catch (_: Exception) {
                    "Gagal memverifikasi kehadiran ($response.code)."
                }
                throw IOException(errorMsg)
            }

            val json = JSONObject(body)
            AttendanceResult(
                id = json.optLong("id"),
                employeeName = json.optString("employee_name"),
                type = json.optString("type"),
                occurredAt = json.optString("occurred_at"),
                status = json.optString("status"),
                isLate = json.optBoolean("is_late", false),
                diffMinutes = json.optInt("diff_minutes", 0),
                checkInTime = if (json.has("check_in_time") && !json.isNull("check_in_time")) json.optString("check_in_time") else null,
                faceMatchScore = if (json.has("face_match_score") && !json.isNull("face_match_score")) json.optDouble("face_match_score") else null,
                faceThreshold = if (json.has("face_threshold") && !json.isNull("face_threshold")) json.optDouble("face_threshold") else null
            )
        }
    }
}
