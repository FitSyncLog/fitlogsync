import customtkinter as ctk
import subprocess
from db_conn import validate_login

# Initialize the application
ctk.set_appearance_mode("Dark")  # Options: "Light", "Dark", "System"
ctk.set_default_color_theme("blue")

# Create the login window
root = ctk.CTk()
root.title("🔐 Secure Login")
root.geometry("400x450")
root.resizable(False, False)

# Center the window
def center_window(window):
    window.update_idletasks()
    width, height = window.winfo_width(), window.winfo_height()
    x = (window.winfo_screenwidth() // 2) - (width // 2)
    y = (window.winfo_screenheight() // 2) - (height // 2)
    window.geometry(f"{width}x{height}+{x}+{y}")

# Handle login
def login():
    username = login_username.get().strip()
    password = login_password.get().strip()

    username_error.configure(text="")
    password_error.configure(text="")

    has_error = False

    if not username:
        username_error.configure(text="⚠️ Username is Required!", text_color="red")
        has_error = True
    if not password:
        password_error.configure(text="⚠️ Password is Required!", text_color="red")
        has_error = True

    if has_error:
        return

    if validate_login(username, password):
        login_button.configure(text="✅ Logging in...", state="disabled")
        root.after(500, open_main_app)
    else:
        password_error.configure(text="❌ Incorrect username or password!", text_color="red")

def open_main_app():
    root.destroy()
    subprocess.Popen(["python", "Desktop/main.py"])

    # Open register.py
def open_register():
    root.destroy()
    subprocess.Popen(["python", "Desktop/register.py"])

# Title
title_label = ctk.CTkLabel(root, text="Welcome to FitLogSync", font=("Arial", 20, "bold"))
title_label.pack(pady=(30, 10))

# Username
ctk.CTkLabel(root, text="Username:", font=("Arial", 12)).pack(anchor="w", padx=75)
login_username = ctk.CTkEntry(root, width=250)
login_username.pack(padx=30, pady=5)
username_error = ctk.CTkLabel(root, text="", font=("Arial", 10))
username_error.pack(anchor="w", padx=75)

# Password
ctk.CTkLabel(root, text="Password:", font=("Arial", 12)).pack(anchor="w", padx=75)
login_password = ctk.CTkEntry(root, width=250, show="*")
login_password.pack(padx=30, pady=5)
password_error = ctk.CTkLabel(root, text="", font=("Arial", 10))
password_error.pack(anchor="w", padx=75)

# Login Button
login_button = ctk.CTkButton(root, text="Login", command=login, width=250)
login_button.pack(pady=15)

# Theme Toggle
theme_button = ctk.CTkButton(root, text="🌙 Toggle Theme", width=250, command=lambda: ctk.set_appearance_mode("Light" if ctk.get_appearance_mode() == "Dark" else "Dark"))
theme_button.pack()

# Register Redirect
register_label = ctk.CTkLabel(root, text="Not yet registered? Sign up", text_color="white", cursor="hand2")
register_label.pack()
register_label.bind("<Button-1>", lambda e: open_register())

# Footer
footer_label = ctk.CTkLabel(root, text="© 2025 FitLogSync | All Rights Reserved", font=("Arial", 9))
footer_label.pack(pady=10)

center_window(root)
root.mainloop()
