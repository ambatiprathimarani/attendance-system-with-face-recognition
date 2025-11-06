import mysql.connector
import logging

def get_db_connection():
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="admin",
            password="admin123",
            database="attendance_db"
        )
        cursor = conn.cursor()
        return conn, cursor
    except mysql.connector.Error as err:
        logging.error(f"Database connection failed: {err}")
        raise

