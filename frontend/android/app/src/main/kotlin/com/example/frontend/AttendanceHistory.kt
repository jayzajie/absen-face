package com.example.frontend

import android.content.Context
import org.json.JSONArray
import org.json.JSONException
import org.json.JSONObject
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

class AttendanceHistory(context: Context) {
    private val preferences = context.getSharedPreferences("attendance_history", Context.MODE_PRIVATE)

    fun entries(username: String): List<JSONObject> = try {
        val array = JSONArray(preferences.getString(username, "[]"))
        (0 until array.length()).map { array.getJSONObject(it) }
    } catch (_: JSONException) {
        emptyList()
    }

    fun add(username: String, entry: JSONObject) {
        val entries = listOf(entry) + entries(username)
        preferences.edit().putString(username, JSONArray(entries.take(100)).toString()).apply()
    }

    companion object {
        fun date(raw: String): Date? = try {
            SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ssXXX", Locale.ROOT)
                .parse(raw.replace(Regex("\\.\\d+"), ""))
        } catch (_: java.text.ParseException) {
            null
        }

        fun format(raw: String): String = date(raw)?.let {
            SimpleDateFormat("dd MMM yyyy, HH:mm z", Locale.forLanguageTag("id-ID")).format(it)
        } ?: raw

        fun today(): String = SimpleDateFormat("EEEE, d MMMM yyyy", Locale.forLanguageTag("id-ID")).format(Date())

        fun isToday(raw: String): Boolean {
            val day = SimpleDateFormat("yyyy-MM-dd", Locale.ROOT)
            return date(raw)?.let { day.format(it) == day.format(Date()) } ?: false
        }
    }
}
