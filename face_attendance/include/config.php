<?php 
# Database connection setup
def get_db_connection():
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="empattndms"
        )
        return conn
    except mysql.connector.Error as err:
        logging.error(f"Error connecting to the database: {err}")
        return None

conn = get_db_connection()
if not conn:
    exit()
cursor = conn.cursor()
?>
