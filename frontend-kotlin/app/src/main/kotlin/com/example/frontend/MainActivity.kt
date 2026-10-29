package com.example.frontend

import android.content.Context
import android.os.Bundle
import android.widget.Toast
import androidx.activity.ComponentActivity
import androidx.activity.compose.BackHandler
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.Surface
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import com.example.frontend.data.AppPage
import com.example.frontend.data.AttendanceApi
import com.example.frontend.data.AttendanceResult
import com.example.frontend.ui.screens.*
import com.example.frontend.ui.theme.BackgroundColor
import com.example.frontend.ui.theme.FrontendTheme
import kotlinx.coroutines.launch
import java.io.File

class MainActivity : ComponentActivity() {
    private val api = AttendanceApi()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val prefs = getSharedPreferences("absen_prefs", Context.MODE_PRIVATE)

        setContent {
            FrontendTheme {
                val coroutineScope = rememberCoroutineScope()

                var currentPage by remember {
                    val savedToken = prefs.getString("emp_token", null)
                    val savedName = prefs.getString("emp_name", null)
                    mutableStateOf(if (!savedToken.isNullOrBlank() && !savedName.isNullOrBlank()) AppPage.HOME else AppPage.LOGIN)
                }

                var employeeName by remember {
                    mutableStateOf(prefs.getString("emp_name", "") ?: "")
                }
                var employeeToken by remember {
                    mutableStateOf(prefs.getString("emp_token", "") ?: "")
                }
                var rememberedUsername by remember {
                    mutableStateOf(prefs.getString("remembered_username", "") ?: "")
                }

                var profilePhoto by remember {
                    val photoPath = prefs.getString("profile_photo_path", null)
                    mutableStateOf(photoPath?.let { File(it) }?.takeIf { it.exists() })
                }

                var pendingAttendanceType by remember { mutableStateOf("masuk") }
                var lastAttendanceResult by remember { mutableStateOf<AttendanceResult?>(null) }
                var isLoading by remember { mutableStateOf(false) }
                var loginError by remember { mutableStateOf<String?>(null) }
                var scanError by remember { mutableStateOf<String?>(null) }

                // Back button handling
                BackHandler(enabled = currentPage != AppPage.LOGIN && currentPage != AppPage.HOME) {
                    currentPage = AppPage.HOME
                }

                Surface(
                    modifier = Modifier.fillMaxSize(),
                    color = BackgroundColor
                ) {
                    when (currentPage) {
                        AppPage.LOGIN -> {
                            LoginScreen(
                                initialUsername = rememberedUsername,
                                isLoading = isLoading,
                                errorMessage = loginError,
                                onLoginClick = { user, pass, remember ->
                                    isLoading = true
                                    loginError = null
                                    coroutineScope.launch {
                                        try {
                                            val response = api.login(user, pass)
                                            employeeName = response.name
                                            employeeToken = response.token

                                            prefs.edit().apply {
                                                putString("emp_name", response.name)
                                                putString("emp_token", response.token)
                                                if (remember) {
                                                    putString("remembered_username", user)
                                                } else {
                                                    remove("remembered_username")
                                                }
                                                apply()
                                            }

                                            currentPage = AppPage.HOME
                                        } catch (e: Exception) {
                                            loginError = e.message ?: "Gagal masuk. Periksa koneksi atau kredensial."
                                        } finally {
                                            isLoading = false
                                        }
                                    }
                                }
                            )
                        }

                        AppPage.HOME -> {
                            HomeScreen(
                                employeeName = employeeName,
                                profilePhoto = profilePhoto,
                                onStartScan = { type ->
                                    pendingAttendanceType = type
                                    scanError = null
                                    currentPage = AppPage.SCAN
                                },
                                onNavigate = { page -> currentPage = page }
                            )
                        }

                        AppPage.SCAN -> {
                            ScanScreen(
                                attendanceType = pendingAttendanceType,
                                isLoading = isLoading,
                                errorMessage = scanError,
                                onBack = { currentPage = AppPage.HOME },
                                onSubmitSelfie = { selfieBytes, selfieName ->
                                    isLoading = true
                                    scanError = null
                                    coroutineScope.launch {
                                        try {
                                            val result = api.recordAttendanceVerified(
                                                token = employeeToken,
                                                type = pendingAttendanceType,
                                                selfieBytes = selfieBytes,
                                                selfieFileName = selfieName
                                            )
                                            lastAttendanceResult = result
                                            currentPage = AppPage.SUCCESS
                                        } catch (e: Exception) {
                                            scanError = e.message ?: "Verifikasi wajah gagal. Coba lagi."
                                        } finally {
                                            isLoading = false
                                        }
                                    }
                                }
                            )
                        }

                        AppPage.SUCCESS -> {
                            SuccessScreen(
                                result = lastAttendanceResult,
                                onDoneClick = { currentPage = AppPage.HOME }
                            )
                        }

                        AppPage.HISTORY -> {
                            HistoryScreen(
                                onBack = { currentPage = AppPage.HOME },
                                onNavigate = { page -> currentPage = page }
                            )
                        }

                        AppPage.IZIN -> {
                            IzinScreen(
                                onBackToHome = { currentPage = AppPage.HOME },
                                onNavigate = { page -> currentPage = page }
                            )
                        }

                        AppPage.ACCOUNT -> {
                            AccountScreen(
                                employeeName = employeeName,
                                deviceId = api.deviceId,
                                profilePhoto = profilePhoto,
                                onUpdateProfilePhoto = { file ->
                                    profilePhoto = file
                                    prefs.edit().putString("profile_photo_path", file.absolutePath).apply()
                                    Toast.makeText(this@MainActivity, "Foto profil diperbarui", Toast.LENGTH_SHORT).show()
                                },
                                onLogoutClick = {
                                    prefs.edit().apply {
                                        remove("emp_name")
                                        remove("emp_token")
                                        apply()
                                    }
                                    employeeName = ""
                                    employeeToken = ""
                                    currentPage = AppPage.LOGIN
                                },
                                onNavigate = { page -> currentPage = page }
                            )
                        }
                    }
                }
            }
        }
    }
}
