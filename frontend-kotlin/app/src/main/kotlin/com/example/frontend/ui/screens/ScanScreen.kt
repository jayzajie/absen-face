package com.example.frontend.ui.screens

import android.Manifest
import android.content.Context
import android.content.pm.PackageManager
import android.graphics.Bitmap
import android.graphics.BitmapFactory
import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.camera.core.CameraSelector
import androidx.camera.core.ImageCapture
import androidx.camera.core.ImageCaptureException
import androidx.camera.core.Preview as CameraPreview
import androidx.camera.lifecycle.ProcessCameraProvider
import androidx.camera.view.PreviewView
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CameraAlt
import androidx.compose.material.icons.filled.Image
import androidx.compose.material.icons.filled.Videocam
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.asImageBitmap
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.platform.LocalLifecycleOwner
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.viewinterop.AndroidView
import androidx.core.content.ContextCompat
import com.example.frontend.R
import com.example.frontend.ui.components.FakeStatusBar
import com.example.frontend.ui.components.PrimaryButton
import com.example.frontend.ui.components.ScreenTitle
import com.example.frontend.ui.theme.*
import java.io.ByteArrayOutputStream
import java.io.File
import java.util.concurrent.Executors

@Composable
fun ScanScreen(
    attendanceType: String,
    onBack: () -> Unit,
    onSubmitSelfie: (ByteArray, String) -> Unit,
    isLoading: Boolean = false,
    errorMessage: String? = null,
    modifier: Modifier = Modifier
) {
    val context = LocalContext.current
    val lifecycleOwner = LocalLifecycleOwner.current

    var hasCameraPermission by remember {
        mutableStateOf(
            ContextCompat.checkSelfPermission(context, Manifest.permission.CAMERA) == PackageManager.PERMISSION_GRANTED
        )
    }

    val permissionLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.RequestPermission()
    ) { isGranted ->
        hasCameraPermission = isGranted
    }

    LaunchedEffect(Unit) {
        if (!hasCameraPermission) {
            permissionLauncher.launch(Manifest.permission.CAMERA)
        }
    }

    var selectedImageBytes by remember { mutableStateOf<ByteArray?>(null) }
    var selectedImageName by remember { mutableStateOf<String?>(null) }
    var imageCapture by remember { mutableStateOf<ImageCapture?>(null) }
    var isCameraReady by remember { mutableStateOf(false) }

    val photoPickerLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.GetContent()
    ) { uri: Uri? ->
        if (uri != null) {
            try {
                context.contentResolver.openInputStream(uri)?.use { inputStream ->
                    val bytes = inputStream.readBytes()
                    selectedImageBytes = bytes
                    selectedImageName = "gallery_selfie.jpg"
                }
            } catch (_: Exception) {}
        }
    }

    Scaffold(
        containerColor = BackgroundColor,
        modifier = modifier.fillMaxSize()
    ) { innerPadding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(innerPadding)
        ) {
            FakeStatusBar()
            ScreenTitle(
                title = "Konfirmasi Absensi (${attendanceType.replaceFirstChar { it.uppercase() }})",
                onBack = onBack
            )

            Box(
                modifier = Modifier
                    .weight(1f)
                    .fillMaxWidth()
                    .background(Color.Black)
            ) {
                if (selectedImageBytes != null) {
                    val bitmap = remember(selectedImageBytes) {
                        BitmapFactory.decodeByteArray(selectedImageBytes, 0, selectedImageBytes!!.size)
                    }
                    if (bitmap != null) {
                        Image(
                            bitmap = bitmap.asImageBitmap(),
                            contentDescription = "Foto Selfie Terpilih",
                            contentScale = ContentScale.Crop,
                            modifier = Modifier.fillMaxSize()
                        )
                    }
                } else if (hasCameraPermission) {
                    AndroidView(
                        factory = { ctx ->
                            val previewView = PreviewView(ctx)
                            val cameraProviderFuture = ProcessCameraProvider.getInstance(ctx)
                            cameraProviderFuture.addListener({
                                val cameraProvider = cameraProviderFuture.get()
                                val preview = CameraPreview.Builder().build().also {
                                    it.setSurfaceProvider(previewView.surfaceProvider)
                                }
                                val capture = ImageCapture.Builder()
                                    .setCaptureMode(ImageCapture.CAPTURE_MODE_MINIMIZE_LATENCY)
                                    .build()
                                imageCapture = capture

                                val cameraSelector = try {
                                    CameraSelector.DEFAULT_FRONT_CAMERA
                                } catch (_: Exception) {
                                    CameraSelector.DEFAULT_BACK_CAMERA
                                }

                                try {
                                    cameraProvider.unbindAll()
                                    cameraProvider.bindToLifecycle(
                                        lifecycleOwner,
                                        cameraSelector,
                                        preview,
                                        capture
                                    )
                                    isCameraReady = true
                                } catch (_: Exception) {}
                            }, ContextCompat.getMainExecutor(ctx))
                            previewView
                        },
                        modifier = Modifier.fillMaxSize()
                    )
                } else {
                    Box(
                        modifier = Modifier
                            .fillMaxSize()
                            .background(Ink),
                        contentAlignment = Alignment.Center
                    ) {
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Text(
                                text = "Izin kamera diperlukan untuk absensi wajah.",
                                color = Color.White,
                                fontSize = 13.sp,
                                textAlign = TextAlign.Center,
                                modifier = Modifier.padding(horizontal = 24.dp)
                            )
                            Spacer(modifier = Modifier.height(16.dp))
                            Button(
                                onClick = { permissionLauncher.launch(Manifest.permission.CAMERA) },
                                colors = ButtonDefaults.buttonColors(containerColor = BrandBlue)
                            ) {
                                Text("Berikan Izin Kamera")
                            }
                        }
                    }
                }

                // Overlay bingkai wajah
                Box(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(horizontal = 48.dp, vertical = 70.dp)
                        .border(2.dp, Color.White.copy(alpha = 0.8f), RoundedCornerShape(24.dp))
                )

                // Status info text atas
                Text(
                    text = if (selectedImageBytes != null) "Foto galeri siap diverifikasi"
                           else "Posisikan wajah di dalam bingkai",
                    textAlign = TextAlign.Center,
                    color = Color.White,
                    fontSize = 12.sp,
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(top = 18.dp)
                )

                // Kamera aktif badge
                if (isCameraReady && selectedImageBytes == null) {
                    Box(
                        modifier = Modifier
                            .align(Alignment.TopCenter)
                            .padding(top = 46.dp)
                            .background(BrandBlue, RoundedCornerShape(6.dp))
                            .padding(horizontal = 12.dp, vertical = 6.dp)
                    ) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Icon(
                                imageVector = Icons.Default.Videocam,
                                contentDescription = null,
                                tint = Color.White,
                                modifier = Modifier.size(15.dp)
                            )
                            Spacer(modifier = Modifier.width(6.dp))
                            Text(
                                text = "Kamera aktif",
                                color = Color.White,
                                fontSize = 11.sp,
                                fontWeight = FontWeight.SemiBold
                            )
                        }
                    }
                }
            }

            // Bottom action panel
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(Color.White)
                    .padding(16.dp)
            ) {
                if (!errorMessage.isNullOrBlank()) {
                    Text(
                        text = errorMessage,
                        color = BrandRed,
                        fontSize = 13.sp,
                        fontWeight = FontWeight.Medium,
                        modifier = Modifier.padding(bottom = 12.dp)
                    )
                }

                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(10.dp)
                ) {
                    // Tombol Galeri / Test Photo
                    OutlinedButton(
                        onClick = { photoPickerLauncher.launch("image/*") },
                        shape = RoundedCornerShape(7.dp),
                        modifier = Modifier
                            .weight(0.45f)
                            .height(52.dp)
                    ) {
                        Icon(imageVector = Icons.Default.Image, contentDescription = null, modifier = Modifier.size(18.dp))
                        Spacer(modifier = Modifier.width(6.dp))
                        Text(text = "Galeri", fontSize = 12.sp)
                    }

                    // Tombol Ambil Foto / Konfirmasi
                    PrimaryButton(
                        label = if (selectedImageBytes != null) "Kirim Foto" else "Ambil Foto Selfie",
                        isLoading = isLoading,
                        icon = Icons.Default.CameraAlt,
                        onClick = {
                            if (selectedImageBytes != null) {
                                onSubmitSelfie(selectedImageBytes!!, selectedImageName ?: "selfie.jpg")
                            } else if (imageCapture != null) {
                                captureSelfie(context, imageCapture!!) { bytes, name ->
                                    onSubmitSelfie(bytes, name)
                                }
                            }
                        },
                        modifier = Modifier.weight(0.55f)
                    )
                }
            }
        }
    }
}

private fun captureSelfie(
    context: Context,
    imageCapture: ImageCapture,
    onSuccess: (ByteArray, String) -> Unit
) {
    val photoFile = File(context.cacheDir, "selfie_${System.currentTimeMillis()}.jpg")
    val outputOptions = ImageCapture.OutputFileOptions.Builder(photoFile).build()

    imageCapture.takePicture(
        outputOptions,
        ContextCompat.getMainExecutor(context),
        object : ImageCapture.OnImageSavedCallback {
            override fun onImageSaved(outputFileResults: ImageCapture.OutputFileResults) {
                try {
                    val bytes = photoFile.readBytes()
                    onSuccess(bytes, photoFile.name)
                } catch (_: Exception) {}
            }
            override fun onError(exception: ImageCaptureException) {}
        }
    )
}

@Preview(showBackground = true, widthDp = 390, heightDp = 800)
@Composable
fun ScanScreenPreview() {
    FrontendTheme {
        ScanScreen(
            attendanceType = "masuk",
            onBack = {},
            onSubmitSelfie = { _, _ -> }
        )
    }
}
