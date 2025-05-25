const { app, BrowserWindow, ipcMain, shell } = require('electron');
const path = require('path');

// Disable GPU acceleration to avoid crashes on some systems
app.disableHardwareAcceleration();

let mainWindow;

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1200,
        height: 800,
        webPreferences: {
            nodeIntegration: true,
            contextIsolation: false
        },
        show: false,
        autoHideMenuBar: true,
        icon: path.join(__dirname, 'assets', 'logo.png') // Add your app icon
    });

    // Load the login page
    mainWindow.loadFile(path.join(__dirname, 'views', 'login.html'));

    mainWindow.once('ready-to-show', () => {
        mainWindow.show();
    });

    mainWindow.on('closed', () => {
        mainWindow = null;
    });

    // Handle external links
    mainWindow.webContents.setWindowOpenHandler(({ url }) => {
        shell.openExternal(url);
        return { action: 'deny' };
    });
}

app.whenReady().then(() => {
    createWindow();

    app.on('activate', () => {
        if (BrowserWindow.getAllWindows().length === 0) {
            createWindow();
        }
    });
});

app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

// IPC handlers for navigation
ipcMain.handle('navigate-to', (event, page) => {
    const pagePath = path.join(__dirname, 'views', `${page}.html`);
    mainWindow.loadFile(pagePath);
});

// IPC handler for opening external URLs
ipcMain.handle('open-external', (event, url) => {
    shell.openExternal(url);
});

// Development tools (remove in production)
if (process.env.NODE_ENV === 'development') {
    app.whenReady().then(() => {
        mainWindow.webContents.openDevTools();
    });
}