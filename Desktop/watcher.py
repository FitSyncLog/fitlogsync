import sys
import time
import subprocess
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler

class RestartOnChange(FileSystemEventHandler):
    def __init__(self, script):
        self.script = script
        self.process = self.start_process()

    def start_process(self):
        return subprocess.Popen([sys.executable, self.script])

    def on_modified(self, event):
        if event.src_path.endswith(".py"):
            print(f"🔄 Detected change in {event.src_path}, restarting application...")
            self.process.terminate()
            time.sleep(1)  # Short delay to prevent conflicts
            self.process = self.start_process()

if __name__ == "__main__":
    script_to_watch = "Desktop/login.py"  # Change this to main.py if needed
    event_handler = RestartOnChange(script_to_watch)
    observer = Observer()
    observer.schedule(event_handler, ".", recursive=True)
    observer.start()

    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        observer.stop()
    observer.join()
