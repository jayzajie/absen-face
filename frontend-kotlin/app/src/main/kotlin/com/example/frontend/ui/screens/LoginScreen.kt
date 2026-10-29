package com.example.frontend.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardActions
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.outlined.Visibility
import androidx.compose.material.icons.outlined.VisibilityOff
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalFocusManager
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.ImeAction
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.frontend.R
import com.example.frontend.ui.components.FakeStatusBar
import com.example.frontend.ui.components.PrimaryButton
import com.example.frontend.ui.theme.*

@Composable
fun LoginScreen(
    onLoginClick: (String, String, Boolean) -> Unit,
    errorMessage: String? = null,
    isLoading: Boolean = false,
    initialUsername: String = "",
    modifier: Modifier = Modifier
) {
    var username by remember { mutableStateOf(initialUsername) }
    var password by remember { mutableStateOf("") }
    var passwordHidden by remember { mutableStateOf(true) }
    var rememberMe by remember { mutableStateOf(initialUsername.isNotEmpty()) }
    var localError by remember { mutableStateOf<String?>(null) }
    val focusManager = LocalFocusManager.current

    val displayError = errorMessage ?: localError

    Column(
        modifier = modifier
            .fillMaxSize()
            .background(BackgroundColor)
    ) {
        FakeStatusBar()

        Column(
            modifier = Modifier
                .fillMaxSize()
                .verticalScroll(rememberScrollState())
                .padding(horizontal = 22.dp)
                .padding(top = 40.dp, bottom = 24.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Image(
                painter = painterResource(id = R.drawable.company_logo),
                contentDescription = "Logo Perusahaan",
                modifier = Modifier
                    .width(165.dp)
                    .height(105.dp),
                contentScale = ContentScale.Fit
            )
            Spacer(modifier = Modifier.height(8.dp))
            Text(
                text = "Absensi Karyawan",
                fontSize = 17.sp,
                fontWeight = FontWeight.Bold,
                color = Ink
            )
            Spacer(modifier = Modifier.height(8.dp))
            Text(
                text = "Catat kehadiran Anda.",
                fontSize = 13.sp,
                color = Color(0xFF9A9C99)
            )

            Spacer(modifier = Modifier.height(43.dp))

            // Label Username
            Text(
                text = "Email atau Username",
                fontSize = 13.sp,
                fontWeight = FontWeight.SemiBold,
                color = Ink,
                modifier = Modifier.fillMaxWidth()
            )
            Spacer(modifier = Modifier.height(8.dp))
            OutlinedTextField(
                value = username,
                onValueChange = {
                    username = it
                    localError = null
                },
                placeholder = {
                    Text("Masukkan email atau username", fontSize = 13.sp, color = TextHint)
                },
                singleLine = true,
                keyboardOptions = KeyboardOptions(
                    keyboardType = KeyboardType.Email,
                    imeAction = ImeAction.Next
                ),
                shape = RoundedCornerShape(7.dp),
                colors = OutlinedTextFieldDefaults.colors(
                    focusedBorderColor = BrandBlue,
                    unfocusedBorderColor = BorderColor,
                    focusedContainerColor = Color.White,
                    unfocusedContainerColor = Color.White
                ),
                modifier = Modifier.fillMaxWidth()
            )

            Spacer(modifier = Modifier.height(20.dp))

            // Label Password
            Text(
                text = "Kata Sandi",
                fontSize = 13.sp,
                fontWeight = FontWeight.SemiBold,
                color = Ink,
                modifier = Modifier.fillMaxWidth()
            )
            Spacer(modifier = Modifier.height(8.dp))
            OutlinedTextField(
                value = password,
                onValueChange = {
                    password = it
                    localError = null
                },
                placeholder = {
                    Text("Masukkan kata sandi", fontSize = 13.sp, color = TextHint)
                },
                singleLine = true,
                visualTransformation = if (passwordHidden) PasswordVisualTransformation() else VisualTransformation.None,
                keyboardOptions = KeyboardOptions(
                    keyboardType = KeyboardType.Password,
                    imeAction = ImeAction.Done
                ),
                keyboardActions = KeyboardActions(
                    onDone = {
                        focusManager.clearFocus()
                        if (username.isBlank() || password.isBlank()) {
                            localError = "Wajib diisi"
                        } else {
                            onLoginClick(username.trim(), password, rememberMe)
                        }
                    }
                ),
                trailingIcon = {
                    IconButton(onClick = { passwordHidden = !passwordHidden }) {
                        Icon(
                            imageVector = if (passwordHidden) Icons.Outlined.VisibilityOff else Icons.Outlined.Visibility,
                            contentDescription = if (passwordHidden) "Tampilkan" else "Sembunyikan",
                            tint = Muted,
                            modifier = Modifier.size(20.dp)
                        )
                    }
                },
                shape = RoundedCornerShape(7.dp),
                colors = OutlinedTextFieldDefaults.colors(
                    focusedBorderColor = BrandBlue,
                    unfocusedBorderColor = BorderColor,
                    focusedContainerColor = Color.White,
                    unfocusedContainerColor = Color.White
                ),
                modifier = Modifier.fillMaxWidth()
            )

            Spacer(modifier = Modifier.height(12.dp))

            // Remember me & Forgot Password
            Row(
                modifier = Modifier.fillMaxWidth(),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Checkbox(
                    checked = rememberMe,
                    onCheckedChange = { rememberMe = it },
                    colors = CheckboxDefaults.colors(
                        checkedColor = BrandBlue,
                        uncheckedColor = LightGray
                    ),
                    modifier = Modifier.size(20.dp)
                )
                Spacer(modifier = Modifier.width(7.dp))
                Text(
                    text = "Ingat saya",
                    fontSize = 12.sp,
                    color = Muted,
                    modifier = Modifier.clickable { rememberMe = !rememberMe }
                )
                Spacer(modifier = Modifier.weight(1f))
                Text(
                    text = "Lupa kata sandi?",
                    fontSize = 12.sp,
                    color = BrandBlue,
                    fontWeight = FontWeight.SemiBold
                )
            }

            if (!displayError.isNullOrBlank()) {
                Spacer(modifier = Modifier.height(16.dp))
                Text(
                    text = displayError,
                    fontSize = 13.sp,
                    color = BrandRed,
                    fontWeight = FontWeight.Medium,
                    modifier = Modifier.fillMaxWidth()
                )
            }

            Spacer(modifier = Modifier.height(24.dp))

            PrimaryButton(
                label = "Masuk",
                isLoading = isLoading,
                onClick = {
                    focusManager.clearFocus()
                    if (username.isBlank() || password.isBlank()) {
                        localError = "Semua kolom wajib diisi"
                    } else {
                        onLoginClick(username.trim(), password, rememberMe)
                    }
                }
            )
        }
    }
}

@Preview(showBackground = true, widthDp = 390, heightDp = 800)
@Composable
fun LoginScreenPreview() {
    FrontendTheme {
        LoginScreen(
            onLoginClick = { _, _, _ -> },
            initialUsername = "dimas"
        )
    }
}
