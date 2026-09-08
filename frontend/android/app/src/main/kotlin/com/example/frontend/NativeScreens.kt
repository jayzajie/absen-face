package com.example.frontend

import android.content.Intent
import android.net.Uri
import android.provider.Settings
import android.view.View
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView

class NativeScreens(private val activity: MainActivity) {
    fun bind(page: MainActivity.Page, view: View) {
        view.findViewById<TextView>(R.id.employee_name)?.text = activity.employee
        when (page) {
            MainActivity.Page.HOME -> home(view)
            MainActivity.Page.HISTORY -> history(view)
            MainActivity.Page.ACCOUNT -> account(view)
            MainActivity.Page.SUCCESS -> success(view)
            else -> Unit
        }
    }

    private fun home(view: View) {
        view.findViewById<TextView>(R.id.today_date).text = AttendanceHistory.today()
        val today = activity.history.entries(activity.username).filter { AttendanceHistory.isToday(it.optString("occurred_at")) }
        if (today.isNotEmpty()) view.findViewById<TextView>(R.id.summary_text).text = today.joinToString("\n") {
            "${kind(it.optString("type"))} · ${AttendanceHistory.format(it.optString("occurred_at"))}"
        }
        view.findViewById<View>(R.id.check_in).setOnClickListener { activity.scan("masuk") }
        view.findViewById<View>(R.id.check_out).setOnClickListener { activity.scan("pulang") }
    }

    private fun history(view: View) {
        val entries = activity.history.entries(activity.username)
        view.findViewById<View>(R.id.history_empty).visibility = if (entries.isEmpty()) View.VISIBLE else View.GONE
        val list = view.findViewById<LinearLayout>(R.id.history_entries)
        entries.forEach { item ->
            val row = activity.layoutInflater.inflate(R.layout.item_attendance, list, false)
            row.findViewById<TextView>(R.id.row_kind).text = kind(item.optString("type"))
            row.findViewById<TextView>(R.id.row_time).text = AttendanceHistory.format(item.optString("occurred_at"))
            row.findViewById<TextView>(R.id.row_status).text = item.optString("status")
            list.addView(row)
        }
    }

    private fun account(view: View) {
        view.findViewById<TextView>(R.id.device_name).text = activity.api.deviceName
        val photo = activity.photos.load(activity.username)
        if (photo != null) view.findViewById<ImageView>(R.id.profile_photo).setImageBitmap(photo)
        view.findViewById<View>(R.id.remove_photo).isEnabled = photo != null
        view.findViewById<View>(R.id.choose_photo).setOnClickListener { activity.choosePhoto() }
        view.findViewById<View>(R.id.remove_photo).setOnClickListener {
            activity.photos.remove(activity.username)
            activity.show(MainActivity.Page.ACCOUNT)
        }
        view.findViewById<View>(R.id.logout).setOnClickListener { activity.logout() }
        view.findViewById<View>(R.id.app_settings).setOnClickListener {
            activity.startActivity(Intent(Settings.ACTION_APPLICATION_DETAILS_SETTINGS, Uri.parse("package:${activity.packageName}")))
        }
    }

    private fun success(view: View) {
        val item = activity.lastAttendance ?: return
        view.findViewById<TextView>(R.id.success_title).setText(
            if (item.optString("type") == "masuk") R.string.success_check_in else R.string.success_check_out)
        view.findViewById<TextView>(R.id.success_detail).text = listOf(
            activity.employee, AttendanceHistory.format(item.optString("occurred_at")), item.optString("status")
        ).joinToString("\n")
    }

    private fun kind(type: String): String = activity.getString(if (type == "masuk") R.string.check_in else R.string.check_out)
}
