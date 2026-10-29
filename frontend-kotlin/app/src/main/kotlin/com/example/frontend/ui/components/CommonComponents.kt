package com.example.frontend.ui.components

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material.icons.filled.BatteryFull
import androidx.compose.material.icons.filled.CalendarMonth
import androidx.compose.material.icons.filled.History
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.NetworkCell
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.Wifi
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.Dp
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import com.example.frontend.R
import com.example.frontend.data.AppPage
import com.example.frontend.ui.theme.*
import java.io.File

@Composable
fun FakeStatusBar(
    modifier: Modifier = Modifier,
    textColor: Color = Ink
) {
    Row(
        modifier = modifier
            .fillMaxWidth()
            .height(36.dp)
            .padding(horizontal = 22.dp),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(
            text = "9:41",
            fontSize = 13.sp,
            fontWeight = FontWeight.Bold,
            color = textColor
        )
        Row(
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.spacedBy(4.dp)
        ) {
            Icon(
                imageVector = Icons.Default.NetworkCell,
                contentDescription = null,
                tint = textColor,
                modifier = Modifier.size(15.dp)
            )
            Icon(
                imageVector = Icons.Default.Wifi,
                contentDescription = null,
                tint = textColor,
                modifier = Modifier.size(15.dp)
            )
            Icon(
                imageVector = Icons.Default.BatteryFull,
                contentDescription = null,
                tint = textColor,
                modifier = Modifier.size(17.dp)
            )
        }
    }
}

@Composable
fun ProfileAvatar(
    file: File?,
    size: Dp = 50.dp,
    modifier: Modifier = Modifier
) {
    Box(
        modifier = modifier
            .size(size)
            .clip(CircleShape)
            .background(Color(0xFFE4F1F8))
            .border(2.dp, BorderColor, CircleShape),
        contentAlignment = Alignment.Center
    ) {
        if (file != null && file.exists()) {
            AsyncImage(
                model = file,
                contentDescription = "Foto Profil",
                contentScale = ContentScale.Crop,
                modifier = Modifier.fillMaxSize()
            )
        } else {
            androidx.compose.foundation.Image(
                painter = painterResource(id = R.drawable.face_portrait),
                contentDescription = "Foto Profil Default",
                contentScale = ContentScale.Crop,
                modifier = Modifier.fillMaxSize()
            )
        }
    }
}

@Composable
fun PrimaryButton(
    label: String,
    onClick: () -> Unit,
    modifier: Modifier = Modifier,
    enabled: Boolean = true,
    isLoading: Boolean = false,
    icon: ImageVector? = null,
    backgroundColor: Color = BrandBlue,
    contentColor: Color = Color.White
) {
    Button(
        onClick = onClick,
        enabled = enabled && !isLoading,
        shape = RoundedCornerShape(7.dp),
        colors = ButtonDefaults.buttonColors(
            containerColor = backgroundColor,
            contentColor = contentColor,
            disabledContainerColor = backgroundColor.copy(alpha = 0.5f),
            disabledContentColor = contentColor.copy(alpha = 0.7f)
        ),
        modifier = modifier
            .fillMaxWidth()
            .height(52.dp)
    ) {
        if (isLoading) {
            CircularProgressIndicator(
                color = contentColor,
                strokeWidth = 2.dp,
                modifier = Modifier.size(20.dp)
            )
        } else {
            Row(
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.Center
            ) {
                if (icon != null) {
                    Icon(imageVector = icon, contentDescription = null, modifier = Modifier.size(20.dp))
                    Spacer(modifier = Modifier.width(8.dp))
                }
                Text(
                    text = label,
                    fontSize = 14.sp,
                    fontWeight = FontWeight.Bold
                )
            }
        }
    }
}

@Composable
fun SectionCard(
    modifier: Modifier = Modifier,
    padding: PaddingValues = PaddingValues(16.dp),
    content: @Composable ColumnScope.() -> Unit
) {
    Card(
        modifier = modifier
            .fillMaxWidth()
            .border(1.dp, BorderColor, RoundedCornerShape(8.dp)),
        shape = RoundedCornerShape(8.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White)
    ) {
        Column(
            modifier = Modifier.padding(padding),
            content = content
        )
    }
}

@Composable
fun ScreenTitle(
    title: String,
    onBack: () -> Unit,
    modifier: Modifier = Modifier
) {
    Box(
        modifier = modifier
            .fillMaxWidth()
            .height(58.dp)
            .padding(horizontal = 4.dp),
        contentAlignment = Alignment.Center
    ) {
        IconButton(
            onClick = onBack,
            modifier = Modifier.align(Alignment.CenterStart)
        ) {
            Icon(
                imageVector = Icons.AutoMirrored.Filled.ArrowBack,
                contentDescription = "Kembali",
                tint = Ink
            )
        }
        Text(
            text = title,
            style = MaterialTheme.typography.titleMedium,
            fontWeight = FontWeight.Bold
        )
    }
}

@Composable
fun AppBottomBar(
    current: AppPage,
    onSelect: (AppPage) -> Unit,
    modifier: Modifier = Modifier
) {
    Surface(
        modifier = modifier
            .fillMaxWidth()
            .height(72.dp),
        color = Color.White,
        shadowElevation = 0.dp
    ) {
        Column {
            HorizontalDivider(thickness = 1.dp, color = BorderColor)
            Row(
                modifier = Modifier.fillMaxSize(),
                verticalAlignment = Alignment.CenterVertically
            ) {
                BottomNavItem(
                    label = "Beranda",
                    icon = Icons.Default.Home,
                    selected = current == AppPage.HOME,
                    onClick = { onSelect(AppPage.HOME) },
                    modifier = Modifier.weight(1f)
                )
                BottomNavItem(
                    label = "Riwayat",
                    icon = Icons.Default.History,
                    selected = current == AppPage.HISTORY,
                    onClick = { onSelect(AppPage.HISTORY) },
                    modifier = Modifier.weight(1f)
                )
                BottomNavItem(
                    label = "Izin",
                    icon = Icons.Default.CalendarMonth,
                    selected = current == AppPage.IZIN,
                    onClick = { onSelect(AppPage.IZIN) },
                    modifier = Modifier.weight(1f)
                )
                BottomNavItem(
                    label = "Akun",
                    icon = Icons.Default.Person,
                    selected = current == AppPage.ACCOUNT,
                    onClick = { onSelect(AppPage.ACCOUNT) },
                    modifier = Modifier.weight(1f)
                )
            }
        }
    }
}

@Composable
private fun BottomNavItem(
    label: String,
    icon: ImageVector,
    selected: Boolean,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    val activeColor = BrandBlue
    val inactiveColor = Color(0xFF929592)

    Column(
        modifier = modifier
            .fillMaxHeight()
            .clickable(onClick = onClick),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center
    ) {
        Icon(
            imageVector = icon,
            contentDescription = label,
            tint = if (selected) activeColor else inactiveColor,
            modifier = Modifier.size(23.dp)
        )
        Spacer(modifier = Modifier.height(4.dp))
        Text(
            text = label,
            fontSize = 10.sp,
            fontWeight = if (selected) FontWeight.Bold else FontWeight.Normal,
            color = if (selected) activeColor else Muted
        )
    }
}
