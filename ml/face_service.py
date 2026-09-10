import base64
import binascii
import io
import json
import os
import urllib.request
from http.server import BaseHTTPRequestHandler, HTTPServer
from pathlib import Path

import cv2 as cv
import numpy as np
from PIL import Image, ImageOps


ROOT = Path(__file__).parent
MODEL_DIR = ROOT / "models"
YUNET_MODEL = MODEL_DIR / "face_detection_yunet_2023mar.onnx"
SFACE_MODEL = MODEL_DIR / "face_recognition_sface_2021dec.onnx"
MODELS = {
    YUNET_MODEL: "https://github.com/opencv/opencv_zoo/raw/main/models/face_detection_yunet/face_detection_yunet_2023mar.onnx",
    SFACE_MODEL: "https://github.com/opencv/opencv_zoo/raw/main/models/face_recognition_sface/face_recognition_sface_2021dec.onnx",
}
THRESHOLD = float(os.environ.get("FACE_MATCH_THRESHOLD", "0.45"))
MAX_BODY_BYTES = 16 * 1024 * 1024
if not -1 <= THRESHOLD <= 1:
    raise ValueError("FACE_MATCH_THRESHOLD must be between -1 and 1.")


def ensure_models() -> None:
    MODEL_DIR.mkdir(exist_ok=True)
    for destination, url in MODELS.items():
        if not destination.exists():
            print(f"Downloading {destination.name}...")
            urllib.request.urlretrieve(url, destination)


ensure_models()
detector = cv.FaceDetectorYN.create(str(YUNET_MODEL), "", (320, 320), 0.9, 0.3, 5000)
recognizer = cv.FaceRecognizerSF.create(str(SFACE_MODEL), "")


def embedding(encoded_image: str) -> np.ndarray:
    raw = base64.b64decode(encoded_image, validate=True)
    image = ImageOps.exif_transpose(Image.open(io.BytesIO(raw))).convert("RGB")
    frame = cv.cvtColor(np.asarray(image), cv.COLOR_RGB2BGR)
    detector.setInputSize((frame.shape[1], frame.shape[0]))
    _, faces = detector.detect(frame)
    count = 0 if faces is None else len(faces)
    if count != 1:
        raise ValueError(f"Foto harus memuat tepat satu wajah; terdeteksi {count}.")
    vector = recognizer.feature(recognizer.alignCrop(frame, faces[0])).flatten().astype(np.float32)
    norm = np.linalg.norm(vector)
    if norm == 0:
        raise ValueError("Embedding wajah tidak valid.")
    return vector / norm


def verify(payload: dict) -> dict:
    reference = embedding(payload["reference_base64"])
    selfie = embedding(payload["selfie_base64"])
    score = float(np.dot(reference, selfie))
    return {
        "matched": score >= THRESHOLD,
        "score": round(score, 7),
        "threshold": THRESHOLD,
        "model_version": "yunet-2023mar+sface-2021dec",
    }


class Handler(BaseHTTPRequestHandler):
    def do_GET(self) -> None:
        if self.path == "/health":
            self.respond(200, {"status": "ok", "threshold": THRESHOLD})
        else:
            self.respond(404, {"message": "Endpoint tidak ditemukan."})

    def do_POST(self) -> None:
        if self.path != "/verify":
            self.respond(404, {"message": "Endpoint tidak ditemukan."})
            return
        try:
            length = int(self.headers.get("Content-Length", "0"))
            if length <= 0 or length > MAX_BODY_BYTES:
                raise ValueError("Ukuran permintaan tidak valid.")
            payload = json.loads(self.rfile.read(length))
            if not payload.get("reference_base64") or not payload.get("selfie_base64"):
                raise ValueError("Foto acuan dan selfie wajib dikirim.")
            self.respond(200, verify(payload))
        except (ValueError, KeyError, json.JSONDecodeError, binascii.Error) as error:
            self.respond(422, {"message": str(error)})
        except Exception as error:
            print(f"Verification error: {error}")
            self.respond(500, {"message": "Verifikasi wajah gagal diproses."})

    def respond(self, status: int, payload: dict) -> None:
        body = json.dumps(payload, separators=(",", ":")).encode()
        self.send_response(status)
        self.send_header("Content-Type", "application/json")
        self.send_header("Content-Length", str(len(body)))
        self.end_headers()
        self.wfile.write(body)

    def log_message(self, format: str, *args) -> None:
        print(f"{self.address_string()} - {format % args}")


if __name__ == "__main__":
    port = int(os.environ.get("FACE_SERVICE_PORT", "8765"))
    print(f"Face service: http://127.0.0.1:{port} (threshold={THRESHOLD})")
    # ponytail: one worker is enough for a small office; add workers if requests queue.
    HTTPServer(("127.0.0.1", port), Handler).serve_forever()
