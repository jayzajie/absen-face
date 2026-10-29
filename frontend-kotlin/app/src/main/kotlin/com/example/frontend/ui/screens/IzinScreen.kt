package com.example.frontend.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.BeachAccess
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.frontend.data.AppPage
import com.example.frontend.ui.components.AppBottomBar
import com.example.frontend.ui.components.FakeStatusBar
import com.example.frontend.ui.components.PrimaryButton
import com.example.frontend.ui.components.SectionCard
import com.example.frontend.ui.theme.*

@Composable
fun IzinScreen(
    onBackToHome: () -> Unit,
    onNavigate: (AppPage) -> Unit,
    modifier: Modifier = Modifier
) {
    Scaffold(
        bottomBar = {
            AppBottomBar(
                current = AppPage.IZIN,
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
                    .padding(16.dp)
            ) {
                Text(
                    text = "Pengajuan Izin / Cuti",
                    fontSize = 22.sp,
                    fontWeight = FontWeight.Bold,
                    color = Ink
                )

                Spacer(modifier = Modifier.height(16.dp))

                SectionCard(padding = PaddingValues(24.dp)) {
                    Column(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        Icon(
                            imageVector = Icons.Default.BeachAccess,
                            contentDescription = null,
                            tint = BrandBlue,
                            modifier = Modifier.size(56.dp)
                        )
                        Spacer(modifier = Modifier.height(16.dp))
                        Text(
                            text = "Fitur Izin Belum Tersedia",
                            fontSize = 16.sp,
                            fontWeight = FontWeight.Bold,
                            color = Ink
                        )
                        Spacer(modifier = Modifier.height(8.dp))
                        Text(
                            text = "Pengajuan izin/cuti akan segera hadir di pembaruan aplikasi berikutnya.",
                            textAlign = TextAlign.Center,
                            fontSize = 13.sp,
                            color = Muted,
                            lineHeight = 18.sp
                        )
                        Spacer(modifier = Modifier.height(24.dp))
                        PrimaryButton(
                            label = "Kembali ke Beranda",
                            onClick = onBackToHome
                        )
                    }
                }
            }
        }
    }
}

@Preview(showBackground = true, widthDp = 390, heightDp = 800)
@Composable
fun IzinScreenPreview() {
    FrontendTheme {
        IzinScreen(
            onBackToHome = {},
            onNavigate = {}
        )
    }
}
