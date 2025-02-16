import tkinter as tk
from tkinter import ttk
import ttkbootstrap as tb
from db_conn import validate_login
import subprocess

# Function to center the window
def center_window(window, width, height):
    screen_width = window.winfo_screenwidth()
    screen_height = window.winfo_screenheight()
    
    x = (screen_width // 2) - (width // 2)
    y = (screen_height // 2) - (height // 2)
    
    window.geometry(f"{width}x{height}+{x}+{y}")

# Function to handle login
def login():
    username = login_username.get().strip()
    password = login_password.get().strip()

    # Clear previous error messages
    username_error.config(text="")
    password_error.config(text="")

    has_error = False

    # Inline error handling
    if not username:
        username_error.config(text="⚠️ Username is required!", foreground="red")
        has_error = True
    if not password:
        password_error.config(text="⚠️ Password is required!", foreground="red")
        has_error = True

    if has_error:
        return

    # Validate credentials
    if validate_login(username, password):
        login_button.config(text="✅ Logging in...", state="disabled")
        root.after(500, open_main)  # Delay transition for smooth effect
    else:
        password_error.config(text="❌ Invalid username or password!", foreground="red")

# Open Main Application
def open_main():
    root.destroy()  # Close login window
    subprocess.Popen(["python", "Desktop/main.py"])  # Open main.py

# Create Login Window
root = tb.Window(themename="superhero")
root.title("🔐 User Login")

# Set the window size and center it
window_width, window_height = 400, 400
center_window(root, window_width, window_height)

frame = ttk.Frame(root, padding=20)
frame.pack(expand=True)

title_label = tb.Label(frame, text="User Login", font=("Arial", 18, "bold"))
title_label.pack(pady=10)

# Username Entry
ttk.Label(frame, text="Username:", font=("Arial", 11)).pack(anchor="w")
login_username = ttk.Entry(frame, width=30, font=("Arial", 11))
login_username.pack(pady=5)
username_error = tb.Label(frame, text="", font=("Arial", 9))
username_error.pack(anchor="w")

# Password Entry
ttk.Label(frame, text="Password:", font=("Arial", 11)).pack(anchor="w")
login_password = ttk.Entry(frame, width=30, font=("Arial", 11), show="*")
login_password.pack(pady=5)
password_error = tb.Label(frame, text="", font=("Arial", 9))
password_error.pack(anchor="w")

# Login Button
login_button = tb.Button(frame, text="Login", bootstyle="success", command=login, width=30)
login_button.pack(pady=10)

footer_label = tb.Label(frame, text="© 2025 SecureApp", font=("Arial", 9, "italic"), foreground="gray")
footer_label.pack(pady=10)

# Run App
root.mainloop()
