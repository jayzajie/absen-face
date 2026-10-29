package com.example.frontend.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.KeyboardArrowDown
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.frontend.data.AppPage
import com.example.frontend.data.AttendanceRecord
import com.example.frontend.ui.components.AppBottomBar
import com.example.frontend.ui.components.FakeStatusBar
import com.example.frontend.ui.components.ScreenTitle
import com.example.frontend.ui.theme.*

@Composable
fun HistoryScreen(
    onBack: () -> Unit,
    onNavigate: (AppPage) -> Unit,
    records: List<AttendanceRecord> = defaultDummyRecords,
    modifier: Modifier = Modifier
) {
    Scaffold(
        bottomBar = {
            AppBottomBar(
                current = AppPage.HISTORY,
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
            ScreenTitle(title = "Riwayat Absensi", onBack = onBack)

            LazyColumn(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 14.dp),
                verticalArrangement = Arrangement.spacedBy(16.dp),
                contentPadding = PaddingValues(bottom = 20.dp)
            ) {
                // Filter row
                item {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        FilterDropdown(text = "Bulan Ini", modifier = Modifier.weight(1f))
                        FilterDropdown(text = "Semua Status", modifier = Modifier.weight(1f))
                    }
                }

                // Stat Cards
                item {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        StatCard(title = "Total Hadir", count = "20", unit = "hari", modifier = Modifier.weight(1f))
                        StatCard(title = "Tepat Waktu", count = "18", unit = "hari", modifier = Modifier.weight(1f))
                        StatCard(title = "Terlambat", count = "2", unit = "hari", modifier = Modifier.weight(1f))
                    }
                }

                // Grouped items
                items(records) { item ->
                    AttendanceItemCard(item = item)
                }
            }
        }
    }
}

@Composable
private fun FilterDropdown(text: String, modifier: Modifier = Modifier) {
    Box(
        modifier = modifier
            .height(42.dp)
            .background(Color.White, RoundedCornerShape(7.dp))
            .border(1.dp, BorderColor, RoundedCornerShape(7.dp))
            .padding(horizontal = 12.dp),
        contentAlignment = Alignment.CenterStart
    ) {
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text(text = text, fontSize = 12.sp, color = Ink)
            Icon(
                imageVector = Icons.Default.KeyboardArrowDown,
                contentDescription = null,
                tint = Muted,
                modifier = Modifier.size(19.dp)
            )
        }
    }
}

@Composable
private fun StatCard(
    title: String,
    count: String,
    unit: String,
    modifier: Modifier = Modifier
) {
    Card(
        modifier = modifier.border(1.dp, BorderColor, RoundedCornerShape(8.dp)),
        shape = RoundedCornerShape(8.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White)
    ) {
        Column(
            modifier = Modifier.padding(vertical = 12.dp, horizontal = 10.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Text(text = title, fontSize = 11.sp, color = Muted, maxLines = 1)
            Spacer(modifier = Modifier.height(4.dp))
            Row(verticalAlignment = Alignment.Bottom) {
                Text(text = count, fontSize = 20.sp, fontWeight = FontWeight.Bold, color = Ink)
                Spacer(modifier = Modifier.width(3.dp))
                Text(text = unit, fontSize = 10.sp, color = Muted, modifier = Modifier.padding(bottom = 2.dp))
            }
        }
    }
}

@Composable
private fun AttendanceItemCard(item: AttendanceRecord) {
    val isTepatWaktu = item.status.contains("Tepat", ignoreCase = true)

    Card(
        modifier = Modifier
            .fillMaxWidth()
            .border(1.dp, BorderColor, RoundedCornerShape(8.dp)),
        shape = RoundedCornerShape(8.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White)
    ) {
        Column(modifier = Modifier.padding(14.dp)) {
            Text(text = item.date, fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Muted)
            Spacer(modifier = Modifier.height(8.dp))
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Column {
                    Text(
                        text = if (item.type == "masuk") "Absen Masuk" else "Absen Pulang",
                        fontSize = 14.sp,
                        fontWeight = FontWeight.Bold,
                        color = Ink
                    )
                    Text(text = item.time, fontSize = 12.sp, color = Muted)
                }

                Box(
                    modifier = Modifier
                        .background(
                            if (isTepatWaktu) TintColor else Color(0xFFFFEEEE),
                            RoundedCornerShape(6.dp)
                        )
                        .padding(horizontal = 10.dp, vertical = 5.dp)
                ) {
                    Text(
                        text = item.status,
                        fontSize = 11.sp,
                        fontWeight = FontWeight.SemiBold,
                        color = if (isTepatWaktu) BrandBlue else BrandRed
                    )
                }
            }
        }
    }
}

private val defaultDummyRecords = listOf(
    AttendanceRecord("Hari Ini", "08:30 WIB", "masuk", "Tepat Waktu"),
    AttendanceRecord("Kemarin", "17:05 WIB", "pulang", "Tepat Waktu"),
    AttendanceRecord("Kemarin", "08:25 WIB", "masuk", "Tepat Waktu"),
    AttendanceRecord("27 Oktober 2026", "17:10 WIB", "pulang", "Tepat Waktu"),
    AttendanceRecord("27 Oktober 2026", "09:15 WIB", "masuk", "Terlambat")
)

@Preview(showBackground = true, widthDp = 390, heightDp = 800)
@Composable
fun HistoryScreenPreview() {
    FrontendTheme {
        HistoryScreen(
            onBack = {},
            onNavigate = {}
        )
    }
}
