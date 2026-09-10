# Face verification service

The Flutter app sends a selfie to Laravel. Laravel sends that selfie and the employee's private reference photo to this local service. The service returns cosine similarity; Laravel records attendance only when the score reaches the calibrated threshold.

```powershell
cd ml
python -m venv .venv
.venv\Scripts\python -m pip install -r requirements.txt
$env:FACE_MATCH_THRESHOLD='0.45' # replace with the notebook result
.venv\Scripts\python face_service.py
```

Keep Laravel running separately with `php artisan serve`. Check the service at `http://127.0.0.1:8765/health`.

The default threshold is only a development fallback. Use the threshold produced by `face_attendance_colab.ipynb` before reporting evaluation results.
