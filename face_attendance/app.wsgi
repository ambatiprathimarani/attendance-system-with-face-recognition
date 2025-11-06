import sys
import logging
import site

# Add the virtualenv site-packages to sys.path
site.addsitedir('/var/www/html/attendance/face_attendance/venv/lib/python3.12/site-packages')

# Add the app directory to the Python path
sys.path.insert(0, '/var/www/html/attendance/face_attendance')

# Import the Flask app object
from app import app as application

# Optional: set up basic logging to Apache's error log
logging.basicConfig(stream=sys.stderr)
