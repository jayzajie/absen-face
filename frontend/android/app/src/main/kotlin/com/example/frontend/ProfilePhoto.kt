package com.example.frontend

import android.content.Context
import android.graphics.Bitmap
import android.graphics.BitmapFactory
import android.net.Uri
import android.util.AtomicFile
import java.io.File
import java.io.IOException
import java.security.MessageDigest

class ProfilePhoto(private val context: Context) {
    private fun file(username: String): File {
        val name = MessageDigest.getInstance("SHA-256").digest(username.toByteArray())
            .joinToString("") { "%02x".format(it) }
        return File(context.filesDir, "profile_$name.png")
    }

    fun load(username: String): Bitmap? = BitmapFactory.decodeFile(file(username).path)

    fun save(username: String, uri: Uri) {
        val resolver = context.contentResolver
        val bounds = BitmapFactory.Options().apply { inJustDecodeBounds = true }
        resolver.openInputStream(uri)?.use { BitmapFactory.decodeStream(it, null, bounds) }
        if (bounds.outWidth <= 0 || bounds.outHeight <= 0) throw IOException(context.getString(R.string.photo_failed))
        val options = BitmapFactory.Options().apply {
            inSampleSize = 1
            while (maxOf(bounds.outWidth, bounds.outHeight) / inSampleSize > 512) inSampleSize *= 2
        }
        val bitmap = resolver.openInputStream(uri)?.use { BitmapFactory.decodeStream(it, null, options) }
            ?: throw IOException(context.getString(R.string.photo_failed))
        val target = AtomicFile(file(username))
        val output = target.startWrite()
        try {
            if (!bitmap.compress(Bitmap.CompressFormat.PNG, 100, output)) throw IOException(context.getString(R.string.photo_failed))
            target.finishWrite(output)
        } catch (error: IOException) {
            target.failWrite(output)
            throw error
        } finally {
            bitmap.recycle()
        }
    }

    fun remove(username: String) {
        AtomicFile(file(username)).delete()
    }
}
