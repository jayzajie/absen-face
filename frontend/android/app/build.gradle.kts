plugins {
    id("com.android.application")
    id("org.jetbrains.kotlin.android")
    id("dev.flutter.flutter-gradle-plugin")
}

fun quoted(value: String): String = "\"" + value.replace("\\", "\\\\").replace("\"", "\\\"") + "\""

android {
    namespace = "com.example.frontend"
    compileSdk = flutter.compileSdkVersion
    ndkVersion = flutter.ndkVersion
    defaultConfig {
        applicationId = "com.example.frontend"
        minSdk = 24
        targetSdk = 35
        versionCode = 2
        versionName = "1.1.0"
        testInstrumentationRunner = "androidx.test.runner.AndroidJUnitRunner"
    }
    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }
    kotlinOptions { jvmTarget = JavaVersion.VERSION_17.toString() }
    buildFeatures { buildConfig = true }
    buildTypes {
        debug {
            buildConfigField("String", "API_URL", quoted(providers.gradleProperty("API_URL").getOrElse("http://10.0.2.2:8000/api")))
            buildConfigField("String", "MOBILE_API_TOKEN", quoted(providers.gradleProperty("MOBILE_API_TOKEN").getOrElse("")))
        }
        release {
            buildConfigField("String", "API_URL", quoted(providers.gradleProperty("API_URL").getOrElse("")))
            buildConfigField("String", "MOBILE_API_TOKEN", quoted(providers.gradleProperty("MOBILE_API_TOKEN").getOrElse("")))
        }
    }
}

kotlin {
    sourceSets["main"].kotlin.exclude(
        "com/example/frontend/AttendanceApi.kt",
        "com/example/frontend/AttendanceCamera.kt",
        "com/example/frontend/AttendanceHistory.kt",
        "com/example/frontend/MainActivity.kt",
        "com/example/frontend/NativeScreens.kt",
        "com/example/frontend/ProfilePhoto.kt",
    )
}

dependencies {
    implementation("androidx.activity:activity-ktx:1.10.1")
    implementation("androidx.constraintlayout:constraintlayout:2.2.1")
    implementation("androidx.camera:camera-camera2:1.5.3")
    implementation("androidx.camera:camera-lifecycle:1.5.3")
    implementation("androidx.camera:camera-view:1.5.3")
    androidTestImplementation("androidx.test:runner:1.6.2")
    androidTestImplementation("androidx.test:rules:1.6.1")
    androidTestImplementation("androidx.test.espresso:espresso-core:3.6.1")
}

flutter {
    source = "../.."
}
