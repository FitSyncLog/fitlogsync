import customtkinter as ctk
import mysql.connector
import subprocess
from db_conn import connect_to_db  # Ensure this function is defined to return a MySQL connection

# Initialize the application
ctk.set_appearance_mode("Dark")  # Options: "Light", "Dark", "System"
ctk.set_default_color_theme("blue")

# Create the register window
register_window = ctk.CTk()
register_window.title("📝 Register Account")
register_window.geometry("400x500")
register_window.resizable(False, False)

# Center the window
def center_window(window):
    window.update_idletasks()
    width, height = window.winfo_width(), window.winfo_height()
    x = (window.winfo_screenwidth() // 2) - (width // 2)
    y = (window.winfo_screenheight() // 2) - (height // 2)
    window.geometry(f"{width}x{height}+{x}+{y}")

# Handle registration
def register():
    username = reg_username.get().strip()
    password = reg_password.get().strip()
    confirm_password = reg_confirm_password.get().strip()
    
    username_error.configure(text="")
    password_error.configure(text="")
    confirm_password_error.configure(text="")
    
    has_error = False

    if not username:
        username_error.configure(text="⚠️ Username is required!", text_color="red")
        has_error = True
    if not password:
        password_error.configure(text="⚠️ Password is required!", text_color="red")
        has_error = True
    if password != confirm_password:
        confirm_password_error.configure(text="❌ Passwords do not match!", text_color="red")
        has_error = True
    
    if has_error:
        return
    
    # Check if username exists
    try:
        conn = connect_to_db()
        cursor = conn.cursor()
        cursor.execute("SELECT COUNT(*) FROM users WHERE username = %s", (username,))
        if cursor.fetchone()[0] > 0:
            username_error.configure(text="❌ Username already exists!", text_color="red")
            return
        
        # Insert new user
        cursor.execute("INSERT INTO users (username, password) VALUES (%s, %s)", (username, password))
        conn.commit()
        success_label.configure(text="✅ Registration successful!", text_color="green")
    
    except mysql.connector.Error as err:
        success_label.configure(text=f"Database error: {err}", text_color="red")
    
    finally:
        cursor.close()
        conn.close()

# Function to open the login page
def open_login():
    register_window.destroy()  # Destroy register window
    subprocess.Popen(["python", "Desktop/login.py"])  # Open the login script

# Title
ctk.CTkLabel(register_window, text="Create an Account", font=("Arial", 20, "bold")).pack(pady=(30, 10))

# Username
ctk.CTkLabel(register_window, text="Username:", font=("Arial", 12)).pack(anchor="w", padx=75)
reg_username = ctk.CTkEntry(register_window, width=250)
reg_username.pack(padx=30, pady=5)
username_error = ctk.CTkLabel(register_window, text="", font=("Arial", 10))
username_error.pack(anchor="w", padx=75)

# Password
ctk.CTkLabel(register_window, text="Password:", font=("Arial", 12)).pack(anchor="w", padx=75)
reg_password = ctk.CTkEntry(register_window, width=250, show="*")
reg_password.pack(padx=30, pady=5)
password_error = ctk.CTkLabel(register_window, text="", font=("Arial", 10))
password_error.pack(anchor="w", padx=75)

# Confirm Password
ctk.CTkLabel(register_window, text="Confirm Password:", font=("Arial", 12)).pack(anchor="w", padx=75)
reg_confirm_password = ctk.CTkEntry(register_window, width=250, show="*")
reg_confirm_password.pack(padx=30, pady=5)
confirm_password_error = ctk.CTkLabel(register_window, text="", font=("Arial", 10))
confirm_password_error.pack(anchor="w", padx=75)

# Register Button
register_button = ctk.CTkButton(register_window, text="Register", command=register, width=250)
register_button.pack(pady=15)

# Register Redirect
login_label = ctk.CTkLabel(register_window, text="Already registered? Login", text_color="white", cursor="hand2")
login_label.pack(pady=10)
login_label.bind("<Button-1>", lambda e: open_login())  # Click to open login

# Success message
success_label = ctk.CTkLabel(register_window, text="", font=("Arial", 10))
success_label.pack()

# Footer
ctk.CTkLabel(register_window, text="© 2025 FitLogSync | All Rights Reserved", font=("Arial", 9)).pack(pady=10)

# Center window
center_window(register_window)
register_window.mainloop()
