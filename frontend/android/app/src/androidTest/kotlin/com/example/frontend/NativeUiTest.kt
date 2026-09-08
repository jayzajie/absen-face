package com.example.frontend

import android.Manifest
import android.content.Context
import android.content.pm.PackageManager
import android.graphics.Bitmap
import android.os.SystemClock
import android.view.View
import androidx.test.core.app.ActivityScenario
import androidx.test.espresso.Espresso.onView
import androidx.test.espresso.action.ViewActions.*
import androidx.test.espresso.assertion.ViewAssertions.matches
import androidx.test.espresso.matcher.ViewMatchers.*
import androidx.test.platform.app.InstrumentationRegistry
import org.junit.Assert.*
import org.junit.Test
import java.io.File

class NativeUiTest {
    private val instrumentation = InstrumentationRegistry.getInstrumentation()

    @Test fun loginAndPermissionFreeNavigation() {
        val context = instrumentation.targetContext
        val permissions = context.packageManager.getPackageInfo(context.packageName, PackageManager.GET_PERMISSIONS).requestedPermissions.orEmpty()
        assertFalse(permissions.any { it.contains("STORAGE") || it.contains("READ_MEDIA") || it.contains("ACCESS_MEDIA_LOCATION") })
        context.getSharedPreferences("native_settings", Context.MODE_PRIVATE).edit().clear().commit()
        context.getSharedPreferences("attendance_history", Context.MODE_PRIVATE).edit().clear().commit()
        ActivityScenario.launch(MainActivity::class.java).use { scenario ->
            capture("login")
            onView(withId(R.id.login_button)).perform(scrollTo(), click())
            onView(withId(R.id.username)).check(matches(hasErrorText(context.getString(R.string.field_required))))
            onView(withId(R.id.username)).perform(replaceText("qa-user"))
            onView(withId(R.id.password)).perform(replaceText("wrong"), closeSoftKeyboard())
            onView(withId(R.id.login_button)).perform(scrollTo(), click())
            waitFor(scenario, R.id.login_error)
            capture("login-error")
            onView(withId(R.id.password)).perform(replaceText("qa-pass"), closeSoftKeyboard())
            onView(withId(R.id.login_button)).perform(scrollTo(), click())
            waitFor(scenario, R.id.check_in)
            assertEquals(PackageManager.PERMISSION_DENIED, context.checkSelfPermission(Manifest.permission.CAMERA))
            capture("home")
            onView(withId(R.id.nav_history)).perform(click())
            onView(withId(R.id.history_empty)).check(matches(isDisplayed()))
            capture("history")
            onView(withId(R.id.nav_izin)).perform(click())
            capture("izin")
            onView(withId(R.id.nav_account)).perform(click())
            capture("account")
            onView(withId(R.id.nav_home)).perform(click())
            onView(withId(R.id.check_in)).perform(scrollTo(), click())
            onView(withId(R.id.confirm_attendance)).perform(scrollTo()).check(matches(isEnabled()))
            assertEquals(PackageManager.PERMISSION_DENIED, context.checkSelfPermission(Manifest.permission.CAMERA))
            capture("scan-idle")
            onView(withId(R.id.confirm_attendance)).perform(scrollTo(), click())
            waitFor(scenario, R.id.success_detail)
            capture("success")
            onView(withId(R.id.back_home)).perform(scrollTo(), click())
            onView(withId(R.id.nav_history)).perform(click())
            onView(withId(R.id.row_kind)).check(matches(withText(context.getString(R.string.check_in))))
            capture("history-recorded")
            onView(withId(R.id.nav_home)).perform(click())
            onView(withId(R.id.check_out)).perform(scrollTo(), click())
            onView(withId(R.id.back_home)).perform(scrollTo(), click())
            onView(withId(R.id.check_out)).check(matches(isDisplayed()))
        }
    }

    private fun waitFor(scenario: ActivityScenario<MainActivity>, id: Int) {
        val deadline = SystemClock.elapsedRealtime() + 20_000
        while (SystemClock.elapsedRealtime() < deadline) {
            var visible = false
            scenario.onActivity { visible = it.findViewById<View>(id)?.isShown == true }
            if (visible) return
            SystemClock.sleep(100)
        }
        fail("View $id did not appear")
    }

    private fun capture(name: String) {
        instrumentation.waitForIdleSync()
        val bitmap = instrumentation.uiAutomation.takeScreenshot()
        val directory = File(instrumentation.targetContext.getExternalFilesDir(null), "qa")
        directory.mkdirs()
        File(directory, "$name.png").outputStream().use { bitmap.compress(Bitmap.CompressFormat.PNG, 100, it) }
        bitmap.recycle()
    }
}
