import os
import sys
import face_recognition

registered_img = sys.argv[1]
from_client_img = sys.argv[2]

on_db_img = face_recognition.load_image_file(registered_img)
alpha_face_encoding = face_recognition.face_encodings(on_db_img)[0]

unknown_picture = face_recognition.load_image_file(from_client_img)
unknown_face_encoding = face_recognition.face_encodings(unknown_picture)[0]

results = face_recognition.compare_faces([alpha_face_encoding], unknown_face_encoding)

if results[0] == True:
	return "PASSED"    
else:
    return "FAILED"