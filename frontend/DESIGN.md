# GJP Absensi UI

## 1. Atmosphere & Identity
Preserve the existing application's blue/red company branding, white cards, and clear Indonesian labels. Android uses real editable Views, not a Flutter screenshot or runtime layout generator. The company logo remains the identifying asset.

## 2. Color
Native resources: brand #2F88BE, brand_dark #236C98 (white button text contrast), brand_red #CF403B, ink #17222B, muted #687782, border #DCE7ED, background #F7FAFC, surface #FFFFFF, tint #E7F2F8. These are extracted from Flutter app.dart/widgets.dart; brand_dark is the accessible action variant.

## 3. Typography
Android system sans-serif. Title 24sp bold, section 18sp bold, body 16sp, secondary 14sp. Text scales with device accessibility settings. Dynamic employee names and statuses may wrap; do not hardcode their values into runtime labels.

## 4. Spacing & Layout
4dp base; 8/12/16/24/32dp gaps, 20dp screen padding. Minimum touch target 48dp, standard action height 52dp, logo 160x96dp, profile photo 88dp. Each screen is a ConstraintLayout within a scrolling host. Use real top/start/end constraints, never tools:layout_editor_absoluteX/Y as runtime positioning. Android system bars supply the real clock; no fake status bar.

## 5. Components
Primary/secondary Button: shared XML styles, focus and pressed native ripple, disabled while request pending. TextView: title/body/muted styles. EditText: visible label and hint, required-field error, password input. White card: border drawable, 12dp corners, 16dp padding. Navigation: four labelled buttons, selected state and 48dp targets, shared XML include. Camera: PreviewView in XML; idle explanation, explicit permission action, denied message, streaming, stopped. Profile image: local user-selected image only; system picker cancellation leaves prior image unchanged. History row: editable XML template, real locally acknowledged attendance data, empty state.

## 6. Motion & Interaction
Native press/focus feedback; no decorative animation. Navigation preserves business bindings through stable view IDs. Changing button text, colors, margins, constraints, or logo must not change its action. New actions still require code; visual editing cannot invent backend behavior.

## 7. Depth & Surface
Borders-only: white cards, 1dp border, 12dp corner radius against background. Native dialogs and picker retain platform presentation.

## 8. Accessibility Constraints & Accepted Debt
48dp touch targets, visible form labels, meaningful image descriptions, wrapping text, scroll access with keyboard/large fonts. Preserve contrast when customizing colors. App language is Indonesian.
Existing limitations: no facial recognition (camera preview only), no leave submission endpoint, backend uses a shared device token rather than per-user sessions. Native UI states these limits honestly. History shows this installation's successful submissions only, not hardcoded historical examples. Login credentials are not persisted in the native client. Android and Flutter UI become separately maintained; XML edits affect Android only.
