import tkinter as tk
from tkinter import ttk
import ttkbootstrap as tb
import webbrowser
import subprocess
from db_conn import connect_to_db

# Function to center the window
def center_window(window, width, height):
    screen_width = window.winfo_screenwidth()
    screen_height = window.winfo_screenheight()
    
    x = (screen_width // 2) - (width // 2)
    y = (screen_height // 2) - (height // 2)
    
    window.geometry(f"{width}x{height}+{x}+{y}")

# Function to fetch users
def fetch_users():
    conn = connect_to_db()
    if conn:
        try:
            cursor = conn.cursor()
            cursor.execute("SELECT username, email FROM user")
            results = cursor.fetchall()
            user_listbox.delete(*user_listbox.get_children())  # Clear table
            
            for row in results:
                user_listbox.insert("", "end", values=row)
                
            cursor.close()
            conn.close()
        except Exception as err:
            print(f"Error: {err}")

# Open RMMC Website
def open_link():
    webbrowser.open("https://rmmcmain.com")

# Logout Function
def logout():
    root.destroy()
    subprocess.Popen(["python", "Desktop/login.py"])  # Reopen login window

# Toggle Sidebar
def toggle_sidebar():
    global sidebar_expanded
    if sidebar_expanded:
        sidebar.config(width=50)
        toggle_btn.config(text="☰")
        for widget in sidebar.winfo_children():
            if widget not in [toggle_btn]:
                widget.pack_forget()
    else:
        sidebar.config(width=200)
        toggle_btn.config(text="✖")
        setup_sidebar_widgets()
    sidebar_expanded = not sidebar_expanded

# Setup Sidebar Widgets
def setup_sidebar_widgets():
    logo_label.pack(pady=10)
    home_button.pack(fill="x", padx=10, pady=5)
    users_button.pack(fill="x", padx=10, pady=5)
    settings_button.pack(fill="x", padx=10, pady=5)
    logout_button.pack(fill="x", padx=10, pady=5)

# Create Main Window
root = tb.Window(themename="superhero")
root.title("⚡ User Management System")

# Set the window size and center it
window_width, window_height = 700, 600
center_window(root, window_width, window_height)

# Sidebar
sidebar_expanded = True
sidebar = tk.Frame(root, width=200, bg="#2c3e50", height=600)
sidebar.pack(side="left", fill="y")

toggle_btn = tb.Button(sidebar, text="✖", bootstyle="light", command=toggle_sidebar)
toggle_btn.pack(fill="x", pady=10)

logo_label = tb.Label(sidebar, text="⚡ Dashboard", font=("Arial", 14, "bold"), foreground="white", background="#2c3e50")
home_button = tb.Button(sidebar, text="🏠 Home", bootstyle="secondary")
users_button = tb.Button(sidebar, text="👥 Users", bootstyle="secondary")
settings_button = tb.Button(sidebar, text="⚙ Settings", bootstyle="secondary")
logout_button = tb.Button(sidebar, text="🔓 Logout", bootstyle="warning", command=logout)

setup_sidebar_widgets()

# Content Area
content_frame = tk.Frame(root, bg="white")
content_frame.pack(side="right", fill="both", expand=True)

title_label = tb.Label(content_frame, text="User Management", font=("Arial", 18, "bold"), background="white")
title_label.pack(pady=10)

user_frame = tb.LabelFrame(content_frame, text="Registered Users", padding=10)
user_frame.pack(pady=10, fill="both", padx=20, expand=True)

columns = ("Username", "Email")
user_listbox = ttk.Treeview(user_frame, columns=columns, show="headings", height=8)
user_listbox.heading("Username", text="Username")
user_listbox.heading("Email", text="Email")
user_listbox.column("Username", width=150)
user_listbox.column("Email", width=250)
user_listbox.pack(fill="both", expand=True)

button_frame = tb.Frame(content_frame)
button_frame.pack(pady=10)

tb.Button(button_frame, text="🔄 Reload Users", bootstyle="primary", command=fetch_users, width=18).pack(side="left", padx=10)
tb.Button(button_frame, text="🌐 Visit RMMC", bootstyle="info", command=open_link, width=18).pack(side="left", padx=10)

# Run App
root.mainloop()
