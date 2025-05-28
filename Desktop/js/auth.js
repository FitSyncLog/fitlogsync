// Auth utility functions
const auth = {
    // Get the authentication token
    getToken() {
        const token = localStorage.getItem('authToken');
        console.log('Retrieved token:', token ? 'exists' : 'not found');
        return token;
    },

    // Get the current user
    getCurrentUser() {
        const userJson = localStorage.getItem('currentUser');
        console.log('Retrieved user data:', userJson);
        return userJson ? JSON.parse(userJson) : null;
    },

    // Check if user is authenticated
    isAuthenticated() {
        const isAuth = !!this.getToken();
        console.log('Is authenticated:', isAuth);
        return isAuth;
    },

    // Verify authentication with the server
    async verifyAuth() {
        try {
            console.log('Starting auth verification...');
            const token = this.getToken();
            if (!token) {
                console.error('No token found in localStorage');
                throw new Error('No authentication token found');
            }

            console.log('Making verification request...');
            // Use relative path for Electron
            const response = await fetch('../controller/UserController.php?action=getCurrentUser', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Cache-Control': 'no-cache',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            console.log('Verification response status:', response.status);

            const data = await response.json();
            console.log('Verification response data:', data);

            if (!data.success) {
                console.error('Server returned error:', data.message);
                throw new Error(data.message || 'Authentication failed');
            }

            // Update stored user data
            console.log('Updating stored user data:', data.user);
            localStorage.setItem('currentUser', JSON.stringify(data.user));
            return data.user;
        } catch (error) {
            console.error('Auth verification failed:', error);
            this.logout();
            throw error;
        }
    },

    // Logout function
    logout() {
        console.log('Logging out...');
        localStorage.removeItem('authToken');
        localStorage.removeItem('currentUser');
        // Use relative path for Electron
        window.location.href = '../views/login.html';
    },

    // Initialize authentication check
    async initAuth() {
        try {
            console.log('Initializing authentication...');
            if (!this.isAuthenticated()) {
                console.error('Not authenticated, token missing');
                throw new Error('Not authenticated');
            }
            const user = await this.verifyAuth();
            console.log('Authentication successful, user:', user);
            return user;
        } catch (error) {
            console.error('Authentication initialization failed:', error);
            this.logout();
            throw error;
        }
    }
}; 