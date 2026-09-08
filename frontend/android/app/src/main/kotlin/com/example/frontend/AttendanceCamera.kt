package com.example.frontend

import androidx.activity.ComponentActivity
import androidx.camera.core.CameraSelector
import androidx.camera.core.Preview
import androidx.camera.lifecycle.ProcessCameraProvider
import androidx.camera.view.PreviewView
import androidx.core.content.ContextCompat

class AttendanceCamera(private val activity: ComponentActivity) {
    private var provider: ProcessCameraProvider? = null
    private var generation = 0
    private var view: PreviewView? = null
    var ready = false
        private set

    fun start(previewView: PreviewView, onState: (Boolean, String) -> Unit) {
        stop()
        val current = generation
        view = previewView
        onState(false, activity.getString(R.string.camera_starting))
        val future = ProcessCameraProvider.getInstance(activity)
        future.addListener({
            if (current != generation || activity.isDestroyed) return@addListener
            try {
                val cameraProvider = future.get()
                provider = cameraProvider
                val selector = if (cameraProvider.hasCamera(CameraSelector.DEFAULT_FRONT_CAMERA)) {
                    CameraSelector.DEFAULT_FRONT_CAMERA
                } else CameraSelector.DEFAULT_BACK_CAMERA
                val preview = Preview.Builder().build()
                preview.setSurfaceProvider(previewView.surfaceProvider)
                previewView.previewStreamState.observe(activity) { state ->
                    if (current == generation) {
                        ready = state == PreviewView.StreamState.STREAMING
                        onState(ready, activity.getString(if (ready) R.string.camera_ready else R.string.camera_starting))
                    }
                }
                cameraProvider.bindToLifecycle(activity, selector, preview)
            } catch (_: Exception) {
                ready = false
                onState(false, activity.getString(R.string.camera_failed))
            }
        }, ContextCompat.getMainExecutor(activity))
    }

    fun stop() {
        generation++
        ready = false
        view?.previewStreamState?.removeObservers(activity)
        view = null
        provider?.unbindAll()
    }
}
