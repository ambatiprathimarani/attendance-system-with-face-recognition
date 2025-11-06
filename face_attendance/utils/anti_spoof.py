import cv2
import numpy as np
import os
import sys

# Absolute path to SFAS/src
current_dir = os.path.dirname(os.path.abspath(__file__))
sfas_src_path = os.path.join(current_dir, '..', 'SFAS')
sys.path.append(os.path.abspath(sfas_src_path))

from src.anti_spoof_predict import AntiSpoofPredict

# Initialize once at app startup
predictor = AntiSpoofPredict(device_id=0)  # or 1, depending on your GPU

def is_real_face(face_bgr, model_path):
    try:
        # Resize image for spoofing model (SFAS uses fixed size)
        resized = cv2.resize(face_bgr, (80, 80))  # Can be 224 or others depending on model
        result = predictor.predict(resized, model_path)

        # Result[0] is [live_prob, spoof_prob]
        live_score = result[0][1]  # Usually index 1 is live; check model if unsure
        print(f"[Anti-Spoofing] Live score: {live_score:.4f}")

        return live_score > 0.5
    except Exception as e:
        print(f"[Anti-Spoofing] Error: {e}")
        return False

