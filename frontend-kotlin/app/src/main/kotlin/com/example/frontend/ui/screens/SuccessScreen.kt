package com.example.frontend.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Check
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.frontend.data.AttendanceResult
import com.example.frontend.ui.components.FakeStatusBar
import com.example.frontend.ui.components.PrimaryButton
import com.example.frontend.ui.theme.*
import java.text.SimpleDateFormat
import java.util.*

@Composable
fun SuccessScreen(
    result: AttendanceResult?,
    onDoneClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    val isMasuk = result?.type == "masuk"

    // Parse date and time
    val (dayStr, timeStr) = try {
        val isoFormat = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss", Locale.getDefault())
        val date = result?.occurredAt?.let { isoFormat.parse(it) } ?: Date()
        val dayFormatter = SimpleDateFormat("EEEE, d MMM yyyy", Locale("id", "ID"))
        val timeFormatter = SimpleDateFormat("HH:mm", Locale("id", "ID"))
        Pair(dayFormatter.format(date), timeFormatter.format(date))
    } catch (_: Exception) {
        val now = Date()
        val dayFormatter = SimpleDateFormat("EEEE, d MMM yyyy", Locale("id", "ID"))
        val timeFormatter = SimpleDateFormat("HH:mm", Locale("id", "ID"))
        Pair(dayFormatter.format(now), timeFormatter.format(now))
    }

    Scaffold(
        containerColor = BrandBlue,
        modifier = modifier.fillMaxSize()
    ) { innerPadding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(innerPadding)
        ) {
            FakeStatusBar(textColor = Color.White)

            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 24.dp),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.Center
            ) {
                // Circle Checkmark
                Box(
                    modifier = Modifier
                        .size(80.dp)
                        .clip(CircleShape)
                        .background(Color.White),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = Icons.Default.Check,
                        contentDescription = "Berhasil",
                        tint = BrandBlue,
                        modifier = Modifier.size(50.dp)
                    )
                }

                Spacer(modifier = Modifier.height(24.dp))

                Text(
                    text = if (isMasuk) "Absen Masuk Berhasil!" else "Absen Pulang Berhasil!",
                    fontSize = 22.sp,
                    fontWeight = FontWeight.ExtraBold,
                    color = Color.White
                )

                Spacer(modifier = Modifier.height(8.dp))

                Text(
                    text = "$dayStr jam $timeStr WIB",
                    fontSize = 14.sp,
                    color = Color.White.copy(alpha = 0.85f)
                )

                Spacer(modifier = Modifier.height(32.dp))

                // Detail Card
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(12.dp),
                    colors = CardDefaults.cardColors(containerColor = Color.White)
                ) {
                    Column(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(20.dp)
                    ) {
                        if (isMasuk) {
                            val isLate = result?.isLate ?: false
                            val diffMins = result?.diffMinutes ?: 0

                            SuccessInfoRow(
                                label = "Status",
                                value = if (isLate) "Terlambat" else "Tepat Waktu",
                                valueColor = if (isLate) BrandRed else BrandBlue
                            )
                            HorizontalDivider(thickness = 1.dp, color = BorderColor, modifier = Modifier.padding(vertical = 12.dp))
                            SuccessInfoRow(
                                label = "Selisih Waktu",
                                value = if (isLate) "Telat $diffMins menit" else "Lebih cepat $diffMins menit",
                                valueColor = Ink
                            )
                            HorizontalDivider(thickness = 1.dp, color = BorderColor, modifier = Modifier.padding(vertical = 12.dp))
                            SuccessInfoRow(
                                label = "Target Jam",
                                value = "09:00 WIB",
                                valueColor = Muted
                            )
                        } else {
                            SuccessInfoRow(
                                label = "Absen Masuk",
                                value = result?.checkInTime ?: "--:--",
                                valueColor = Ink
                            )
                            HorizontalDivider(thickness = 1.dp, color = BorderColor, modifier = Modifier.padding(vertical = 12.dp))
                            SuccessInfoRow(
                                label = "Absen Keluar",
                                value = timeStr,
                                valueColor = BrandBlue
                            )
                        }

                        if (result?.faceMatchScore != null && result.faceThreshold != null) {
                            HorizontalDivider(thickness = 1.dp, color = BorderColor, modifier = Modifier.padding(vertical = 12.dp))
                            SuccessInfoRow(
                                label = "Kemiripan Wajah",
                                value = "%.3f / %.3f".format(result.faceMatchScore, result.faceThreshold),
                                valueColor = BrandBlue
                            )
                        }
                    }
                }

                Spacer(modifier = Modifier.height(48.dp))

                PrimaryButton(
                    label = "Selesai",
                    onClick = onDoneClick,
                    backgroundColor = Color.White,
                    contentColor = BrandBlue
                )
            }
        }
    }
}

@Composable
private fun SuccessInfoRow(
    label: String,
    value: String,
    valueColor: Color
) {
    Row(
        modifier = Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(text = label, fontSize = 14.sp, color = Muted)
        Text(text = value, fontSize = 15.sp, fontWeight = FontWeight.Bold, color = valueColor)
    }
}

@Preview(showBackground = true, widthDp = 390, heightDp = 800)
@Composable
fun SuccessScreenPreview() {
    FrontendTheme {
        SuccessScreen(
            result = AttendanceResult(
                employeeName = "Dimas Pratama",
                type = "masuk",
                occurredAt = "2026-10-29T08:45:00",
                status = "Tepat Waktu",
                isLate = false,
                diffMinutes = 15,
                faceMatchScore = 0.892,
                faceThreshold = 0.700
            ),
            onDoneClick = {}
        )
    }
}
