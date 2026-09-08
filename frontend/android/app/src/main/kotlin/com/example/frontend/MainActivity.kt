package com.example.frontend

import android.Manifest
import android.content.pm.PackageManager
import android.os.Bundle
import android.view.View
import android.view.inputmethod.InputMethodManager
import android.widget.Button
import android.widget.CheckBox
import android.widget.EditText
import android.widget.FrameLayout
import android.widget.ScrollView
import android.widget.TextView
import androidx.activity.ComponentActivity
import androidx.activity.OnBackPressedCallback
import androidx.activity.result.contract.ActivityResultContracts
import androidx.camera.view.PreviewView
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import org.json.JSONObject
import java.util.concurrent.Executors

class MainActivity : ComponentActivity() {
    enum class Page(val layout: Int) {
        LOGIN(R.layout.screen_login), HOME(R.layout.screen_home), SCAN(R.layout.screen_scan),
        HISTORY(R.layout.screen_history), IZIN(R.layout.screen_izin),
        ACCOUNT(R.layout.screen_account), SUCCESS(R.layout.screen_success)
    }

    val api = AttendanceApi()
    val history by lazy { AttendanceHistory(this) }
    val photos by lazy { ProfilePhoto(this) }
    var username = ""
        private set
    var employee = ""
        private set
    var lastAttendance: JSONObject? = null
        private set
    private var page = Page.LOGIN
    private var attendanceType = "masuk"
    private var busy = false
    private val executor = Executors.newSingleThreadExecutor()
    private val camera by lazy { AttendanceCamera(this) }
    private val preferences by lazy { getSharedPreferences("native_settings", MODE_PRIVATE) }
    private lateinit var screen: View

    private val cameraPermission = registerForActivityResult(ActivityResultContracts.RequestPermission()) { granted ->
        if (page == Page.SCAN) {
            if (granted) startCamera()
            else screen.findViewById<TextView>(R.id.camera_status).setText(R.string.camera_denied)
        }
    }
    private val photoPicker = registerForActivityResult(ActivityResultContracts.GetContent()) { uri ->
        if (uri != null && page == Page.ACCOUNT) {
            perform(screen.findViewById(R.id.choose_photo), { photos.save(username, uri) }) {
                show(Page.ACCOUNT)
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.app_root)) { view, insets ->
            val bars = insets.getInsets(WindowInsetsCompat.Type.systemBars() or WindowInsetsCompat.Type.ime())
            view.setPadding(bars.left, bars.top, bars.right, bars.bottom)
            insets
        }
        onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
            override fun handleOnBackPressed() {
                if (busy) return
                if (page == Page.LOGIN || page == Page.HOME) finish() else show(Page.HOME)
            }
        })
        mapOf(R.id.nav_home to Page.HOME, R.id.nav_history to Page.HISTORY,
            R.id.nav_izin to Page.IZIN, R.id.nav_account to Page.ACCOUNT).forEach { (id, destination) ->
            findViewById<View>(id).setOnClickListener { if (!busy) show(destination) }
        }
        username = savedInstanceState?.getString("username").orEmpty()
        employee = savedInstanceState?.getString("employee").orEmpty()
        attendanceType = savedInstanceState?.getString("attendance_type") ?: "masuk"
        lastAttendance = savedInstanceState?.getString("last_attendance")?.let { JSONObject(it) }
        val restored = savedInstanceState?.getString("page")?.let { value -> Page.entries.find { it.name == value } }
        show(if (employee.isEmpty()) Page.LOGIN else restored ?: Page.HOME)
    }

    fun show(destination: Page) {
        if (busy) return
        camera.stop()
        page = destination
        val host = findViewById<FrameLayout>(R.id.screen_host)
        host.removeAllViews()
        screen = layoutInflater.inflate(page.layout, host, false)
        host.addView(screen)
        findViewById<View>(R.id.bottom_nav).visibility = if (page == Page.LOGIN) View.GONE else View.VISIBLE
        findViewById<ScrollView>(R.id.screen_scroll).scrollTo(0, 0)
        mapOf(R.id.nav_home to Page.HOME, R.id.nav_history to Page.HISTORY,
            R.id.nav_izin to Page.IZIN, R.id.nav_account to Page.ACCOUNT).forEach { (id, target) ->
            findViewById<View>(id).isSelected = page == target
        }
        screen.findViewById<View>(R.id.back_home)?.setOnClickListener { if (!busy) show(Page.HOME) }
        when (page) {
            Page.LOGIN -> bindLogin()
            Page.SCAN -> bindScan()
            else -> NativeScreens(this).bind(page, screen)
        }
    }

    private fun bindLogin() {
        val user = screen.findViewById<EditText>(R.id.username)
        val password = screen.findViewById<EditText>(R.id.password)
        val remember = screen.findViewById<CheckBox>(R.id.remember_username)
        user.setText(preferences.getString("username", ""))
        remember.isChecked = user.text.isNotEmpty()
        screen.findViewById<Button>(R.id.login_button).setOnClickListener { button ->
            if (user.text.isBlank()) { user.error = getString(R.string.field_required); return@setOnClickListener }
            if (password.text.isEmpty()) { password.error = getString(R.string.field_required); return@setOnClickListener }
            val inputUsername = user.text.toString().trim()
            val inputPassword = password.text.toString()
            val rememberInput = remember.isChecked
            (getSystemService(INPUT_METHOD_SERVICE) as InputMethodManager).hideSoftInputFromWindow(password.windowToken, 0)
            screen.findViewById<TextView>(R.id.login_error).text = ""
            perform(button as Button, { api.login(inputUsername, inputPassword) }) { name ->
                username = inputUsername
                employee = name
                preferences.edit().putString("username", if (rememberInput) username else "").apply()
                password.text.clear()
                show(Page.HOME)
            }
        }
    }

    fun scan(type: String) {
        attendanceType = type
        show(Page.SCAN)
    }

    private fun bindScan() {
        screen.findViewById<Button>(R.id.enable_camera).setOnClickListener {
            if (busy) return@setOnClickListener
            if (checkSelfPermission(Manifest.permission.CAMERA) == PackageManager.PERMISSION_GRANTED) startCamera()
            else cameraPermission.launch(Manifest.permission.CAMERA)
        }
        screen.findViewById<Button>(R.id.confirm_attendance).setOnClickListener { button ->
            if (busy) return@setOnClickListener
            val cameraActive = camera.ready
            perform(button as Button, { api.record(employee, attendanceType, cameraActive) }) { data ->
                lastAttendance = data
                history.add(username, data)
                show(Page.SUCCESS)
            }
        }
        if (checkSelfPermission(Manifest.permission.CAMERA) == PackageManager.PERMISSION_GRANTED) startCamera()
    }

    private fun startCamera() {
        camera.start(screen.findViewById<PreviewView>(R.id.camera_preview)) { _, message ->
            if (page == Page.SCAN) {
                screen.findViewById<TextView>(R.id.camera_status).text = message
                screen.findViewById<Button>(R.id.confirm_attendance).isEnabled = !busy
            }
        }
    }

    fun choosePhoto() { if (!busy) photoPicker.launch("image/*") }

    fun logout() {
        if (busy) return
        employee = ""
        username = ""
        lastAttendance = null
        show(Page.LOGIN)
    }

    private fun <T> perform(button: Button, work: () -> T, complete: (T) -> Unit) {
        if (busy) return
        busy = true
        val originalLabel = button.text
        button.isEnabled = false
        button.setText(if (page == Page.LOGIN) R.string.login_pending else R.string.attendance_pending)
        executor.execute {
            val result = runCatching(work)
            runOnUiThread {
                if (isDestroyed || isFinishing) return@runOnUiThread
                busy = false
                button.text = originalLabel
                button.isEnabled = true
                result.fold(complete) { error ->
                    val message = when (error) {
                        is java.net.SocketTimeoutException -> getString(R.string.request_timeout)
                        is java.net.ConnectException, is java.net.UnknownHostException -> getString(R.string.request_failed)
                        is java.io.IOException -> error.message ?: getString(R.string.request_failed)
                        else -> getString(R.string.request_failed)
                    }
                    if (page == Page.LOGIN) screen.findViewById<TextView>(R.id.login_error).apply { text = message; visibility = View.VISIBLE }
                    else android.app.AlertDialog.Builder(this).setMessage(message).setPositiveButton(android.R.string.ok, null).show()
                }
            }
        }
    }

    override fun onStart() {
        super.onStart()
        if (::screen.isInitialized && page == Page.SCAN &&
            checkSelfPermission(Manifest.permission.CAMERA) == PackageManager.PERMISSION_GRANTED) startCamera()
    }

    override fun onStop() {
        camera.stop()
        if (page == Page.SCAN) screen.findViewById<TextView>(R.id.camera_status).setText(R.string.camera_stopped)
        super.onStop()
    }

    override fun onSaveInstanceState(outState: Bundle) {
        outState.putString("username", username)
        outState.putString("employee", employee)
        outState.putString("page", if (busy) Page.HOME.name else page.name)
        outState.putString("attendance_type", attendanceType)
        outState.putString("last_attendance", lastAttendance?.toString())
        super.onSaveInstanceState(outState)
    }

    override fun onDestroy() {
        camera.stop()
        executor.shutdownNow()
        super.onDestroy()
    }
}
