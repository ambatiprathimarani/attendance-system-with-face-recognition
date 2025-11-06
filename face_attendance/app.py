import base64
from io import BytesIO
from flask import Flask, request, jsonify, render_template
from PIL import Image
import numpy as np
import cv2
import logging
import os
from datetime import datetime
from face_recognition import face_locations
from include.db import get_db_connection
from utils.face_utils import load_known_faces, recognize_faces
from utils.anti_spoof import is_real_face
from utils.anti_spoof import predictor

app = Flask(__name__, static_folder='css', template_folder='templates')

# Load anti-spoofing model once
current_dir = os.path.dirname(os.path.abspath(__file__))
model_path = os.path.join(current_dir, 'SFAS', 'resources', 'anti_spoof_models', '2.7_80x80_MiniFASNetV2.pth')

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

# Connect to database
conn, cursor = get_db_connection()

# Load employees and known face encodings
cursor.execute("SELECT id, fname, lname, photo FROM tblemployee")
employees = cursor.fetchall()
known_face_encodings, known_face_ids = load_known_faces(employees)

@app.route('/')
def index():
    return render_template('index.html')


@app.route('/recognize', methods=['POST'])

def recognize():

    data = request.get_json()
    image_data = data.get("image")
    if not image_data:
        return jsonify({"error": "No image data received."}), 400

    try:
        _, encoded = image_data.split(',', 1)
        image_bytes = base64.b64decode(encoded)
        image = Image.open(BytesIO(image_bytes)).convert('RGB')
        image = np.array(image)
        image = cv2.cvtColor(image, cv2.COLOR_RGB2BGR)

        # Anti-spoofing check
        if not is_real_face(image,model_path):
            return jsonify({"error": 'spoof_detected'}), 403

        # Face recognition step
        recognized_ids = recognize_faces(image, known_face_encodings, known_face_ids, face_locations)
        recognized_employees = []

        for emp_id in recognized_ids:
            cursor.execute("SELECT fname, lname FROM tblemployee WHERE id=%s", (emp_id,))
            result = cursor.fetchone()
            if result:
                fname, lname = result
                attendance_info = mark_attendance(emp_id, fname, lname)

                recognized_employees.append({
                    "id": emp_id,
                    "fname": fname,
                    "lname": lname,
                    **attendance_info
                })

        return jsonify({"recognized_employees": recognized_employees})

    except Exception as e:
        logging.error(f"Image processing failed: {e}")
        return jsonify({"error": "Image processing failed."}), 500

def mark_attendance(emp_id, fname, lname):
    now = datetime.now()
    date_str = now.strftime("%Y-%m-%d")
    time_str = now.strftime("%H:%M:%S")

    try:
        cursor.execute("SELECT id, checkInTime FROM tblattendance WHERE empId=%s AND DATE(checkInTime)=%s", (emp_id, date_str))
        result = cursor.fetchone()

        if result:
            attendance_id, check_in_time = result
            checkout_time = f"{date_str} {time_str}"
            cursor.execute("UPDATE tblattendance SET checkOutTime=%s WHERE id=%s", (checkout_time, attendance_id))
            conn.commit()

            # Calculate total hours worked
            in_time = datetime.strptime(str(check_in_time), "%Y-%m-%d %H:%M:%S")
            out_time = datetime.strptime(checkout_time, "%Y-%m-%d %H:%M:%S")
            total_hours = str(out_time - in_time).split('.')[0]

            logging.info(f"Check-out updated for {fname} {lname} at {time_str}")
            return {
                "type": "checkout",
                "check_in": check_in_time.strftime("%Y-%m-%d %I:%M %p"),
                "check_out": out_time.strftime("%Y-%m-%d %I:%M %p"),
                "worked": total_hours
            }
        else:
            check_in_time = f"{date_str} {time_str}"
            cursor.execute("INSERT INTO tblattendance (empId, checkInTime) VALUES (%s, %s)", (emp_id, check_in_time))
            conn.commit()

            logging.info(f"Check-in recorded for {fname} {lname} at {time_str}")
            return {
                "type": "checkin",
                "check_in": datetime.strptime(check_in_time, "%Y-%m-%d %H:%M:%S").strftime("%Y-%m-%d %I:%M %p")
            }

    except Exception as err:
        logging.error(f"Attendance update failed: {err}")
        return {"error": "Database error"}


