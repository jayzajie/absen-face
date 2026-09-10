package com.example.frontend

import android.os.Build
import org.json.JSONObject
import java.io.IOException
import java.net.HttpURLConnection
import java.net.URL

class AttendanceApi {
    val deviceName: String = "${Build.MANUFACTURER} ${Build.MODEL}".take(100)

    fun login(username: String, password: String): String {
        val data = post("mobile/login", JSONObject()
            .put("username", username)
            .put("password", password)
            .put("device_id", deviceName), false)
        return data.getString("name").also {
            if (it.isBlank()) throw IOException("Respons login tidak valid.")
        }
    }

    fun record(employee: String, type: String, cameraActive: Boolean): JSONObject {
        require(type == "masuk" || type == "pulang")
        return post("attendances", JSONObject()
            .put("employee_name", employee)
            .put("type", type)
            .put("device_id", deviceName)
            .put("camera_access_granted", cameraActive), true).also {
            it.getString("type")
            it.getString("occurred_at")
        }
    }

    private fun post(path: String, body: JSONObject, authorized: Boolean): JSONObject {
        val base = BuildConfig.API_URL.trimEnd('/')
        if (base.isEmpty() || (!BuildConfig.DEBUG && !base.startsWith("https://"))) {
            throw IOException("Alamat API belum dikonfigurasi dengan HTTPS.")
        }
        if (authorized && BuildConfig.MOBILE_API_TOKEN.isBlank()) {
            throw IOException("Token perangkat belum dikonfigurasi.")
        }
        val connection = URL("$base/$path").openConnection() as HttpURLConnection
        try {
            connection.requestMethod = "POST"
            connection.instanceFollowRedirects = false
            connection.connectTimeout = 15_000
            connection.readTimeout = 20_000
            connection.doOutput = true
            connection.setRequestProperty("Accept", "application/json")
            connection.setRequestProperty("Content-Type", "application/json; charset=utf-8")
            if (authorized) connection.setRequestProperty("Authorization", "Bearer ${BuildConfig.MOBILE_API_TOKEN}")
            connection.outputStream.use { it.write(body.toString().toByteArray(Charsets.UTF_8)) }
            val code = connection.responseCode
            if (code !in 200..299) {
                throw IOException(when (code) {
                    401, 403 -> if (authorized) "Token perangkat ditolak server." else "Username atau kata sandi salah."
                    422 -> "Data tidak diterima server. Periksa isian Anda."
                    else -> "Server tidak dapat memproses permintaan ($code)."
                })
            }
            return connection.inputStream.bufferedReader().use { JSONObject(it.readText()) }
        } finally {
            connection.disconnect()
        }
    }
}
