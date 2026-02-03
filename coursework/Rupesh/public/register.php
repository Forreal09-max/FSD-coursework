<?php
require_once '../includes/header.php';

if (isLoggedIn()) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $phone, $hashed_password])) {
                redirect('login.php', 'Registration successful! Please login.', 'success');
            } else { $error = "Registration failed."; }
        }
    }
}
?>

<div style="max-width: 500px; margin: 4rem auto;">
    <div class="card" style="padding: 3rem;">
        <div style="text-align: center; margin-bottom: 2.5rem;">
            <div style="width: 64px; height: 64px; background: rgba(59, 130, 246, 0.1); border-radius: 20px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: var(--primary);">
                <i data-lucide="user-plus" style="width: 32px; height: 32px;"></i>
            </div>
            <h2 style="font-size: 1.75rem;">Create Account</h2>
            <p style="color: var(--text-secondary); font-size: 0.95rem;">Join VRMS and start your journey</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required placeholder="John Doe" style="width: 100%;">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="john@example.com" style="width: 100%;">
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" placeholder="+1 234 567 890" style="width: 100%;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="••••••••" style="width: 100%;">
                </div>
                <div class="form-group">
                    <label>Confirm</label>
                    <input type="password" name="confirm_password" required placeholder="••••••••" style="width: 100%;">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 1rem;">Create Account</button>
        </form>
        
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-light); text-align: center;">
            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                Already have an account? <a href="login.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">Sign in here</a>
            </p>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
