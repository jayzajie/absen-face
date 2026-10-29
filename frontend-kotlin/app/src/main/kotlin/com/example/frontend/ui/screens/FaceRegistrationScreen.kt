package com.example.frontend.ui.screens

import android.Manifest
import android.content.Context
import android.content.pm.PackageManager
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
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CameraAlt
import androidx.compose.material.icons.filled.Check
import androidx.compose.material.icons.filled.Face
import androidx.compose.material.icons.filled.Image
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.asImageBitmap
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.platform.LocalLifecycleOwner
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.viewinterop.AndroidView
import androidx.core.content.ContextCompat
import com.example.frontend.ui.components.FakeStatusBar
import com.example.frontend.ui.components.PrimaryButton
import com.example.frontend.ui.theme.*
import java.io.File

@Composable
fun FaceRegistrationScreen(
    employeeName: String,
    onRegisterFace: (ByteArray, String) -> Unit,
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

    val photoPickerLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.GetContent()
    ) { uri: Uri? ->
        if (uri != null) {
            try {
                context.contentResolver.openInputStream(uri)?.use { inputStream ->
                    val bytes = inputStream.readBytes()
                    selectedImageBytes = bytes
                    selectedImageName = "registered_face.jpg"
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

            // Header Onboarding
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 20.dp, vertical = 12.dp)
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Box(
                        modifier = Modifier
                            .size(36.dp)
                            .background(TintColor, CircleShape),
                        contentAlignment = Alignment.Center
                    ) {
                        Icon(
                            imageVector = Icons.Default.Face,
                            contentDescription = null,
                            tint = BrandBlue,
                            modifier = Modifier.size(22.dp)
                        )
                    }
                    Spacer(modifier = Modifier.width(12.dp))
                    Column {
                        Text(
                            text = "Registrasi Wajah Pertama Kali",
                            fontSize = 17.sp,
                            fontWeight = FontWeight.Bold,
                            color = Ink
                        )
                        Text(
                            text = "Halo, ${employeeName.ifBlank { "Karyawan" }}! Daftarkan wajah Anda.",
                            fontSize = 12.sp,
                            color = Muted
                        )
                    }
                }
            }

            // Area Kamera / Preview
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
                            contentDescription = "Foto Wajah Terpilih",
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
                                text = "Izin kamera diperlukan untuk mendaftarkan wajah.",
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

                // Overlay Oval Panduan Wajah
                Box(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(horizontal = 56.dp, vertical = 50.dp)
                        .border(2.dp, Color.White.copy(alpha = 0.85f), RoundedCornerShape(100.dp))
                )

                // Keterangan teks atas
                Text(
                    text = if (selectedImageBytes != null) "Foto siap didaftarkan"
                           else "Posisikan wajah tepat di dalam lingkaran",
                    textAlign = TextAlign.Center,
                    color = Color.White,
                    fontSize = 12.sp,
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(top = 18.dp)
                )

                // Tombol ulangi jika sudah pilih foto
                if (selectedImageBytes != null) {
                    IconButton(
                        onClick = {
                            selectedImageBytes = null
                            selectedImageName = null
                        },
                        modifier = Modifier
                            .align(Alignment.TopEnd)
                            .padding(16.dp)
                            .background(Color.Black.copy(alpha = 0.5f), CircleShape)
                    ) {
                        Icon(
                            imageVector = Icons.Default.Refresh,
                            contentDescription = "Foto Ulang",
                            tint = Color.White
                        )
                    }
                }
            }

            // Bottom action panel
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(Color.White)
                    .padding(20.dp)
            ) {
                Text(
                    text = "Foto ini akan disimpan sebagai acuan verifikasi setiap kali Anda melakukan Absen Masuk dan Pulang.",
                    fontSize = 12.sp,
                    color = Muted,
                    lineHeight = 16.sp,
                    modifier = Modifier.padding(bottom = 12.dp)
                )

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
                    // Tombol Galeri
                    OutlinedButton(
                        onClick = { photoPickerLauncher.launch("image/*") },
                        shape = RoundedCornerShape(7.dp),
                        modifier = Modifier
                            .weight(0.4f)
                            .height(52.dp)
                    ) {
                        Icon(imageVector = Icons.Default.Image, contentDescription = null, modifier = Modifier.size(18.dp))
                        Spacer(modifier = Modifier.width(6.dp))
                        Text(text = "Galeri", fontSize = 12.sp)
                    }

                    // Tombol Ambil & Daftarkan Wajah
                    PrimaryButton(
                        label = if (selectedImageBytes != null) "Daftarkan Wajah" else "Ambil & Daftarkan",
                        isLoading = isLoading,
                        icon = if (selectedImageBytes != null) Icons.Default.Check else Icons.Default.CameraAlt,
                        onClick = {
                            if (selectedImageBytes != null) {
                                onRegisterFace(selectedImageBytes!!, selectedImageName ?: "face.jpg")
                            } else if (imageCapture != null) {
                                captureFace(context, imageCapture!!) { bytes, name ->
                                    onRegisterFace(bytes, name)
                                }
                            }
                        },
                        modifier = Modifier.weight(0.6f)
                    )
                }
            }
        }
    }
}

private fun captureFace(
    context: Context,
    imageCapture: ImageCapture,
    onSuccess: (ByteArray, String) -> Unit
) {
    val photoFile = File(context.cacheDir, "face_${System.currentTimeMillis()}.jpg")
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
fun FaceRegistrationScreenPreview() {
    FrontendTheme {
        FaceRegistrationScreen(
            employeeName = "Dimas Pratama",
            onRegisterFace = { _, _ -> }
        )
    }
}
