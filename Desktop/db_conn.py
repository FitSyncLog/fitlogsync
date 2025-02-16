import mysql.connector
import bcrypt

# Connect to database
def connect_to_db():
    try:
        conn = mysql.connector.connect(
            host="127.0.0.1",
            port="3307",
            user="root",
            password="",
            database="ipt101"
        )
        return conn
    except mysql.connector.Error as err:
        print(f"Error: {err}")
        return None

# Validate login credentials
def validate_login(username, password):
    conn = connect_to_db()
    if conn:
        try:
            cursor = conn.cursor()
            cursor.execute("SELECT password FROM user WHERE username = %s", (username,))
            result = cursor.fetchone()
            cursor.close()
            conn.close()

            if result and bcrypt.checkpw(password.encode('utf-8'), result[0].encode('utf-8')):
                return True
        except mysql.connector.Error as err:
            print(f"Error: {err}")
    return False
