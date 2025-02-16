<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Gym Membership and Log Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white text-center py-3">
        <h1>Fitness Gym Membership and Log Management</h1>
    </header>
    <main class="container mt-5">
        <section id="membership" class="mb-5">
            <h2>Membership Registration</h2>
            <form action="register.php" method="post">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="membership_type">Membership Type:</label>
                    <select class="form-control" id="membership_type" name="membership_type" required>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
        </section>
        <section id="log">
            <h2>Workout Log</h2>
            <form action="log.php" method="post">
                <div class="form-group">
                    <label for="member_id">Member ID:</label>
                    <input type="text" class="form-control" id="member_id" name="member_id" required>
                </div>
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="workout_details">Workout Details:</label>
                    <textarea class="form-control" id="workout_details" name="workout_details" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Log Workout</button>
            </form>
        </section>
    </main>
    <footer class="bg-light text-center py-3">
        <p>&copy; 2023 Fitness Gym. All rights reserved.</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>