import face_recognition
import os
import logging
from utils.anti_spoof import is_real_face

def load_known_faces(employees):
    known_face_encodings = []
    known_face_ids = []

    base_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))

    for emp_id, fname, lname, photo_path in employees:
        try:
            # Normalize photo_path to absolute
            if not os.path.isabs(photo_path):
                photo_path = os.path.abspath(os.path.join(base_dir, photo_path))

            if not os.path.exists(photo_path):
                logging.warning(f"Photo not found for {fname} {lname} (EmpID: {emp_id}). Skipping...")
                continue

            image = face_recognition.load_image_file(photo_path)
            encoding = face_recognition.face_encodings(image)[0]
            known_face_encodings.append(encoding)
            known_face_ids.append(emp_id)
        except IndexError:
            logging.warning(f"Could not encode face for {fname} {lname} (EmpID: {emp_id}). Skipping...")
        except Exception as e:
            logging.error(f"Error processing photo for {fname} {lname} (EmpID: {emp_id}): {e}")

    return known_face_encodings, known_face_ids


def recognize_faces(image, known_encodings, known_ids, model_path):

    face_locations = face_recognition.face_locations(image)
    face_encodings = face_recognition.face_encodings(image, face_locations)
    recognized = []

    for encoding in face_encodings:
        matches = face_recognition.compare_faces(known_encodings, encoding)
        distances = face_recognition.face_distance(known_encodings, encoding)
        if len(distances) == 0:
            continue
        best_match_index = distances.argmin()
        if matches[best_match_index]:
            recognized.append(known_ids[best_match_index])
    return recognized


