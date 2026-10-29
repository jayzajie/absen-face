package com.example.frontend.ui.theme

import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable

private val LightColorScheme = lightColorScheme(
    primary = BrandBlue,
    onPrimary = SurfaceColor,
    secondary = BrandBlueDark,
    background = BackgroundColor,
    surface = SurfaceColor,
    onBackground = Ink,
    onSurface = Ink,
    error = BrandRed
)

@Composable
fun FrontendTheme(
    darkTheme: Boolean = isSystemInDarkTheme(),
    content: @Composable () -> Unit
) {
    MaterialTheme(
        colorScheme = LightColorScheme,
        typography = Typography,
        content = content
    )
}
