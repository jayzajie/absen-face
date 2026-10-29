package com.example.frontend.ui.screens

import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CalendarToday
import androidx.compose.material.icons.filled.Login
import androidx.compose.material.icons.filled.NotificationsNone
import androidx.compose.material.icons.filled.Schedule
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.StrokeCap
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
import java.text.SimpleDateFormat
import java.util.*

@Composable
fun HomeScreen(
    employeeName: String,
    profilePhoto: File?,
    onStartScan: (String) -> Unit,
    onNavigate: (AppPage) -> Unit,
    modifier: Modifier = Modifier
) {
    val dateFormat = SimpleDateFormat("EEEE, d MMMM yyyy", Locale("id", "ID"))
    val todayDate = dateFormat.format(Date())

    Scaffold(
        bottomBar = {
            AppBottomBar(
                current = AppPage.HOME,
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
                    .padding(horizontal = 16.dp, vertical = 14.dp),
                verticalArrangement = Arrangement.spacedBy(14.dp)
            ) {
                // Header
                HomeHeader(
                    employeeName = employeeName,
                    profilePhoto = profilePhoto
                )

                Spacer(modifier = Modifier.height(4.dp))

                // Ringkasan Hari Ini
                TodaySummaryCard(todayDate = todayDate)

                // Buttons: Absen Masuk & Absen Pulang
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    AttendanceActionButton(
                        title = "Absen Masuk",
                        time = "08.30 WIB",
                        isActive = true,
                        onClick = { onStartScan("masuk") },
                        modifier = Modifier.weight(1f)
                    )
                    AttendanceActionButton(
                        title = "Absen Pulang",
                        time = "",
                        isActive = false,
                        onClick = { onStartScan("pulang") },
                        modifier = Modifier.weight(1f)
                    )
                }

                // Lokasi Kantor
                OfficeLocationCard()

                // Jadwal Kerja
                WorkScheduleCard()
            }
        }
    }
}

@Composable
private fun HomeHeader(
    employeeName: String,
    profilePhoto: File?
) {
    Row(
        modifier = Modifier.fillMaxWidth(),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Column(modifier = Modifier.weight(1f)) {
            Text(
                text = "Selamat pagi,",
                fontSize = 14.sp,
                color = Muted
            )
            Spacer(modifier = Modifier.height(3.dp))
            Text(
                text = employeeName.ifBlank { "Karyawan" },
                fontSize = 22.sp,
                fontWeight = FontWeight.Bold,
                color = Ink
            )
            Spacer(modifier = Modifier.height(2.dp))
            Text(
                text = "Staff",
                fontSize = 13.sp,
                color = Muted
            )
        }
        Icon(
            imageVector = Icons.Default.NotificationsNone,
            contentDescription = "Notifikasi",
            tint = Ink,
            modifier = Modifier.size(23.dp)
        )
        Spacer(modifier = Modifier.width(16.dp))
        ProfileAvatar(file = profilePhoto, size = 50.dp)
    }
}

@Composable
private fun TodaySummaryCard(todayDate: String) {
    SectionCard {
        Row(
            modifier = Modifier.fillMaxWidth(),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text(
                text = "Ringkasan Hari Ini",
                fontSize = 15.sp,
                fontWeight = FontWeight.Bold,
                color = Ink,
                modifier = Modifier.weight(1f)
            )
            Icon(
                imageVector = Icons.Default.CalendarToday,
                contentDescription = null,
                tint = Muted,
                modifier = Modifier.size(19.dp)
            )
        }
        Spacer(modifier = Modifier.height(5.dp))
        Text(
            text = todayDate,
            fontSize = 11.sp,
            color = Muted
        )

        HorizontalDivider(
            thickness = 1.dp,
            color = BorderColor,
            modifier = Modifier.padding(vertical = 14.dp)
        )

        Row(
            modifier = Modifier.fillMaxWidth(),
            verticalAlignment = Alignment.Top
        ) {
            // Jam Kerja
            Column(modifier = Modifier.weight(1f)) {
                Text(text = "Jam Kerja", fontSize = 11.sp, color = Muted)
                Spacer(modifier = Modifier.height(8.dp))
                Row(verticalAlignment = Alignment.Bottom) {
                    Text(
                        text = "08:00",
                        fontSize = 23.sp,
                        fontWeight = FontWeight.Bold,
                        color = Ink
                    )
                    Spacer(modifier = Modifier.width(4.dp))
                    Text(
                        text = "jam",
                        fontSize = 11.sp,
                        color = Muted,
                        modifier = Modifier.padding(bottom = 3.dp)
                    )
                }
                Spacer(modifier = Modifier.height(8.dp))
                Text(text = "Target 08:00 jam", fontSize = 10.sp, color = Muted)
            }

            // Divider vertikal
            Box(
                modifier = Modifier
                    .width(1.dp)
                    .height(72.dp)
                    .background(BorderColor)
            )

            Spacer(modifier = Modifier.width(17.dp))

            // Status Kehadiran
            Column(modifier = Modifier.weight(1f)) {
                Text(text = "Status Kehadiran", fontSize = 11.sp, color = Muted)
                Spacer(modifier = Modifier.height(8.dp))
                Text(
                    text = "Hadir",
                    fontSize = 20.sp,
                    fontWeight = FontWeight.Bold,
                    color = BrandBlue
                )
                Spacer(modifier = Modifier.height(8.dp))
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Text(
                        text = "Tepat waktu",
                        fontSize = 10.sp,
                        fontWeight = FontWeight.SemiBold,
                        color = BrandBlue
                    )
                    Spacer(modifier = Modifier.width(6.dp))
                    Box(
                        modifier = Modifier
                            .size(6.dp)
                            .background(BrandBlue, CircleShape)
                    )
                }
            }
        }
    }
}

@Composable
private fun AttendanceActionButton(
    title: String,
    time: String,
    isActive: Boolean,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    val containerColor = if (isActive) BrandBlue else Color.White
    val contentColor = if (isActive) Color.White else Ink

    Button(
        onClick = onClick,
        shape = RoundedCornerShape(7.dp),
        colors = ButtonDefaults.buttonColors(
            containerColor = containerColor,
            contentColor = contentColor
        ),
        modifier = modifier
            .height(76.dp)
            .then(
                if (!isActive) Modifier.border(1.dp, BorderColor, RoundedCornerShape(7.dp))
                else Modifier
            ),
        contentPadding = PaddingValues(horizontal = 14.dp)
    ) {
        Row(
            modifier = Modifier.fillMaxWidth(),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Icon(
                imageVector = Icons.Default.Login,
                contentDescription = null,
                tint = contentColor,
                modifier = Modifier.size(26.dp)
            )
            Spacer(modifier = Modifier.width(11.dp))
            Column(
                verticalArrangement = Arrangement.Center,
                horizontalAlignment = Alignment.Start
            ) {
                Text(
                    text = title,
                    fontSize = 13.sp,
                    fontWeight = FontWeight.Bold,
                    color = contentColor
                )
                if (time.isNotEmpty()) {
                    Spacer(modifier = Modifier.height(5.dp))
                    Text(
                        text = time,
                        fontSize = 10.sp,
                        color = contentColor.copy(alpha = 0.7f)
                    )
                }
            }
        }
    }
}

@Composable
private fun OfficeLocationCard() {
    SectionCard {
        Row(
            modifier = Modifier.fillMaxWidth(),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = "Lokasi Kantor",
                    fontSize = 13.sp,
                    fontWeight = FontWeight.Bold,
                    color = Ink
                )
                Spacer(modifier = Modifier.height(8.dp))
                Text(
                    text = "Kantor Pusat",
                    fontSize = 12.sp,
                    fontWeight = FontWeight.Bold,
                    color = Ink
                )
                Spacer(modifier = Modifier.height(3.dp))
                Text(
                    text = "Jl. Jend. Sudirman Kav. 52-53,\nJakarta Selatan",
                    fontSize = 10.sp,
                    color = Muted,
                    lineHeight = 14.sp
                )
            }
            Box(
                modifier = Modifier
                    .width(96.dp)
                    .height(76.dp)
                    .background(Color(0xFFE7F2F8), RoundedCornerShape(7.dp))
            ) {
                Canvas(modifier = Modifier.fillMaxSize()) {
                    val roadPaint = Color.White
                    drawLine(
                        color = roadPaint,
                        start = Offset(5f, size.height * 0.2f),
                        end = Offset(size.width * 0.85f, size.height),
                        strokeWidth = 7f,
                        cap = StrokeCap.Round
                    )
                    drawLine(
                        color = roadPaint,
                        start = Offset(size.width * 0.2f, size.height),
                        end = Offset(size.width * 0.95f, 4f),
                        strokeWidth = 7f,
                        cap = StrokeCap.Round
                    )
                    drawLine(
                        color = roadPaint,
                        start = Offset(0f, size.height * 0.65f),
                        end = Offset(size.width, size.height * 0.3f),
                        strokeWidth = 7f,
                        cap = StrokeCap.Round
                    )
                    // Pin merah
                    val pinCenter = Offset(size.width * 0.55f, size.height * 0.48f)
                    drawCircle(color = BrandRed, radius = 9.dp.toPx(), center = pinCenter)
                    drawCircle(color = Color.White, radius = 3.dp.toPx(), center = pinCenter)
                }
            }
        }
    }
}

@Composable
private fun WorkScheduleCard() {
    SectionCard {
        Row(
            modifier = Modifier.fillMaxWidth(),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = "Jadwal Kerja",
                    fontSize = 13.sp,
                    fontWeight = FontWeight.Bold,
                    color = Ink
                )
                Spacer(modifier = Modifier.height(8.dp))
                Text(text = "Senin – Jumat", fontSize = 11.sp, color = Ink)
                Spacer(modifier = Modifier.height(3.dp))
                Text(text = "09:00 – 17:00 WIB", fontSize = 11.sp, color = Muted)
            }
            Icon(
                imageVector = Icons.Default.Schedule,
                contentDescription = null,
                tint = Muted,
                modifier = Modifier.size(30.dp)
            )
        }
    }
}

@Preview(showBackground = true, widthDp = 390, heightDp = 800)
@Composable
fun HomeScreenPreview() {
    FrontendTheme {
        HomeScreen(
            employeeName = "Dimas Pratama",
            profilePhoto = null,
            onStartScan = {},
            onNavigate = {}
        )
    }
}
