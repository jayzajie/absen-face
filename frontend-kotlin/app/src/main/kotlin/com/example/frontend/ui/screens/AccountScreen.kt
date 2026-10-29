package com.example.frontend.ui.screens

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CameraAlt
import androidx.compose.material.icons.filled.Logout
import androidx.compose.material.icons.filled.PhoneAndroid
import androidx.compose.material.icons.filled.Security
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.frontend.data.AppPage
import com.example.frontend.ui.components.AppBottomBar
import com.example.frontend.ui.components.FakeStatusBar
import com.example.frontend.ui.components.ProfileAvatar
import com.example.frontend.ui.components.SectionCard
import com.example.frontend.ui.theme.*
import java.io.File
import java.io.FileOutputStream

@Composable
fun AccountScreen(
    employeeName: String,
    deviceId: String,
    profilePhoto: File?,
    onUpdateProfilePhoto: (File) -> Unit,
    onLogoutClick: () -> Unit,
    onNavigate: (AppPage) -> Unit,
    modifier: Modifier = Modifier
) {
    val context = LocalContext.current

    val photoPickerLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.GetContent()
    ) { uri: Uri? ->
        if (uri != null) {
            try {
                val targetFile = File(context.filesDir, "profile_photo.jpg")
                context.contentResolver.openInputStream(uri)?.use { input ->
                    FileOutputStream(targetFile).use { output ->
                        input.copyTo(output)
                    }
                }
                onUpdateProfilePhoto(targetFile)
            } catch (_: Exception) {}
        }
    }

    Scaffold(
        bottomBar = {
            AppBottomBar(
                current = AppPage.ACCOUNT,
                onSelect = onNavigate
            )
        },
        containerColor = BackgroundColor,
        modifier = modifier.fillMaxSize()
    ) { innerPadding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(innerPadding)
        ) {
            FakeStatusBar()

            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .verticalScroll(rememberScrollState())
                    .padding(16.dp),
                verticalArrangement = Arrangement.spacedBy(16.dp)
            ) {
                Text(
                    text = "Pengaturan Akun",
                    fontSize = 22.sp,
                    fontWeight = FontWeight.Bold,
                    color = Ink
                )

                // Profile Card
                SectionCard(padding = PaddingValues(20.dp)) {
                    Column(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        Box(contentAlignment = Alignment.BottomEnd) {
                            ProfileAvatar(file = profilePhoto, size = 88.dp)

                            Box(
                                modifier = Modifier
                                    .size(32.dp)
                                    .clip(CircleShape)
                                    .background(BrandBlue)
                                    .clickable { photoPickerLauncher.launch("image/*") },
                                contentAlignment = Alignment.Center
                            ) {
                                Icon(
                                    imageVector = Icons.Default.CameraAlt,
                                    contentDescription = "Ganti Foto",
                                    tint = Color.White,
                                    modifier = Modifier.size(16.dp)
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(16.dp))

                        Text(
                            text = employeeName.ifBlank { "Karyawan" },
                            fontSize = 18.sp,
                            fontWeight = FontWeight.Bold,
                            color = Ink
                        )
                        Spacer(modifier = Modifier.height(3.dp))
                        Text(
                            text = "Staff Operasional",
                            fontSize = 13.sp,
                            color = Muted
                        )
                    }
                }

                // Device & Privacy Card
                SectionCard {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Icon(
                            imageVector = Icons.Default.PhoneAndroid,
                            contentDescription = null,
                            tint = BrandBlue,
                            modifier = Modifier.size(24.dp)
                        )
                        Spacer(modifier = Modifier.width(12.dp))
                        Column {
                            Text(text = "Perangkat Terdaftar", fontSize = 11.sp, color = Muted)
                            Text(text = deviceId, fontSize = 13.sp, fontWeight = FontWeight.SemiBold, color = Ink)
                        }
                    }

                    HorizontalDivider(thickness = 1.dp, color = BorderColor, modifier = Modifier.padding(vertical = 14.dp))

                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        verticalAlignment = Alignment.Top
                    ) {
                        Icon(
                            imageVector = Icons.Default.Security,
                            contentDescription = null,
                            tint = BrandBlue,
                            modifier = Modifier.size(24.dp)
                        )
                        Spacer(modifier = Modifier.width(12.dp))
                        Column {
                            Text(text = "Privasi & Keamanan", fontSize = 13.sp, fontWeight = FontWeight.Bold, color = Ink)
                            Spacer(modifier = Modifier.height(3.dp))
                            Text(
                                text = "Aplikasi ini memverifikasi absensi Anda secara aman menggunakan pengenalan biometrik wajah terenkripsi.",
                                fontSize = 11.sp,
                                color = Muted,
                                lineHeight = 15.sp
                            )
                        }
                    }
                }

                // Logout Button
                Button(
                    onClick = onLogoutClick,
                    shape = RoundedCornerShape(7.dp),
                    colors = ButtonDefaults.buttonColors(
                        containerColor = Color.White,
                        contentColor = BrandRed
                    ),
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(52.dp)
                ) {
                    Icon(imageVector = Icons.Default.Logout, contentDescription = null, modifier = Modifier.size(19.dp))
                    Spacer(modifier = Modifier.width(8.dp))
                    Text(
                        text = "Keluar dari Akun",
                        fontSize = 14.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
            }
        }
    }
}

@Preview(showBackground = true, widthDp = 390, heightDp = 800)
@Composable
fun AccountScreenPreview() {
    FrontendTheme {
        AccountScreen(
            employeeName = "Dimas Pratama",
            deviceId = "Google Pixel 8",
            profilePhoto = null,
            onUpdateProfilePhoto = {},
            onLogoutClick = {},
            onNavigate = {}
        )
    }
}
