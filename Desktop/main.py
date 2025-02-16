import customtkinter as ctk
import webbrowser
import subprocess
import tkinter as tk
from tkinter import ttk, messagebox
from db_conn import connect_to_db

ctk.set_appearance_mode("Dark")
ctk.set_default_color_theme("blue")

def center_window(window, width, height):
    screen_width = window.winfo_screenwidth()
    screen_height = window.winfo_screenheight()
    x = (screen_width // 2) - (width // 2)
    y = (screen_height // 2) - (height // 2)
    window.geometry(f"{width}x{height}+{x}+{y}")

def fetch_users(filter_text=""):
    conn = connect_to_db()
    if conn:
        try:
            cursor = conn.cursor()
            query = "SELECT user_id, username, email FROM user WHERE username LIKE %s OR email LIKE %s"
            cursor.execute(query, (f"%{filter_text}%", f"%{filter_text}%"))
            results = cursor.fetchall()
            
            for item in treeview.get_children():
                treeview.delete(item)

            for index, row in enumerate(results):
                tag = "evenrow" if index % 2 == 0 else "oddrow"
                treeview.insert("", "end", values=row, tags=(tag,))
            
            cursor.close()
            conn.close()
        except Exception as err:
            messagebox.showerror("Database Error", f"Error fetching users: {err}")

def open_link():
    webbrowser.open("https://rmmcmain.com")

def logout():
    root.destroy()
    subprocess.Popen(["python", "Desktop/login.py"])

def toggle_sidebar():
    global sidebar_expanded
    sidebar.configure(width=50 if sidebar_expanded else 200)
    toggle_btn.configure(text="☰" if sidebar_expanded else "✖", width=40 if sidebar_expanded else 180)
    for widget in sidebar.winfo_children():
        if widget not in [toggle_btn]:
            widget.pack_forget() if sidebar_expanded else setup_sidebar_widgets()
    sidebar_expanded = not sidebar_expanded

def setup_sidebar_widgets():
    logo_label.pack(pady=10)
    home_button.pack(fill="x", padx=10, pady=5)
    users_button.pack(fill="x", padx=10, pady=5)
    settings_button.pack(fill="x", padx=10, pady=5)
    logout_button.pack(fill="x", padx=10, pady=5)

def delete_user():
    selected_item = treeview.selection()
    if selected_item:
        user_id = treeview.item(selected_item, "values")[0]
        conn = connect_to_db()
        if conn:
            try:
                cursor = conn.cursor()
                cursor.execute("DELETE FROM user WHERE user_id = %s", (user_id,))
                conn.commit()
                cursor.close()
                conn.close()
                fetch_users()
            except Exception as err:
                messagebox.showerror("Error", f"Failed to delete user: {err}")
    else:
        messagebox.showwarning("No Selection", "Please select a user to delete.")

root = ctk.CTk()
root.title("⚡ User Management System")
root.geometry("900x600")
center_window(root, 900, 600)

sidebar_expanded = True
sidebar = ctk.CTkFrame(root, width=200, height=600, fg_color="#2c3e50")
sidebar.pack(side="left", fill="y")

toggle_btn = ctk.CTkButton(sidebar, text="✖", fg_color="gray", command=toggle_sidebar, corner_radius=5, width=180)
toggle_btn.pack(fill="x", pady=10)

logo_label = ctk.CTkLabel(sidebar, text="⚡ Dashboard", font=("Arial", 14, "bold"), text_color="white")
home_button = ctk.CTkButton(sidebar, text="🏠 Home", fg_color="gray")
users_button = ctk.CTkButton(sidebar, text="👥 Users", fg_color="gray")
settings_button = ctk.CTkButton(sidebar, text="⚙ Settings", fg_color="gray")
logout_button = ctk.CTkButton(sidebar, text="🔓 Logout", fg_color="red", command=logout)

setup_sidebar_widgets()

content_frame = ctk.CTkFrame(root, fg_color="transparent")
content_frame.pack(side="right", fill="both", expand=True, padx=10, pady=10)

title_label = ctk.CTkLabel(content_frame, text="User Management", font=("Arial", 18, "bold"))
title_label.pack(pady=10)

search_var = tk.StringVar()
search_entry = ctk.CTkEntry(content_frame, textvariable=search_var, placeholder_text="Search Users", width=250)
search_entry.pack(pady=5)
search_entry.bind("<KeyRelease>", lambda e: fetch_users(search_var.get()))

user_frame = ctk.CTkFrame(content_frame, fg_color="#1e272e")
user_frame.pack(pady=10, fill="both", padx=20, expand=True)

style = ttk.Style()
style.configure("Treeview", font=("Arial", 12), rowheight=28, background="#2c3e50", foreground="white", fieldbackground="#2c3e50")
style.configure("Treeview.Heading", font=("Arial", 14, "bold"), background="#1abc9c", foreground="white")
style.map("Treeview", background=[("selected", "#3498db")], foreground=[("selected", "white")])

style.configure("Treeview", highlightthickness=0, bd=0, background="#2c3e50")
treeview = ttk.Treeview(user_frame, columns=("ID", "Username", "Email"), show="headings", height=10)
treeview.heading("ID", text="ID", anchor="w")
treeview.heading("Username", text="👤 Username", anchor="w")
treeview.heading("Email", text="📧 Email", anchor="w")
treeview.column("ID", width=50, anchor="w")
treeview.column("Username", width=180, anchor="w")
treeview.column("Email", width=250, anchor="w")
treeview.tag_configure("oddrow", background="#34495e")
treeview.tag_configure("evenrow", background="#2c3e50")
treeview.pack(fill="both", expand=True, padx=10, pady=10)

button_frame = ctk.CTkFrame(content_frame, fg_color="transparent")
button_frame.pack(pady=10)

ctk.CTkButton(button_frame, text="🔄 Reload Users", fg_color="blue", command=fetch_users, width=120).pack(side="left", padx=10)
ctk.CTkButton(button_frame, text="❌ Delete User", fg_color="red", command=delete_user, width=120).pack(side="left", padx=10)
ctk.CTkButton(button_frame, text="🌐 Visit RMMC", fg_color="green", command=open_link, width=120).pack(side="left", padx=10)

fetch_users()
root.mainloop()
