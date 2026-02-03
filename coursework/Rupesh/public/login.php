<?php
require_once '../includes/header.php';

if (isLoggedIn()) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_username = sanitize($_POST['login_id']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role == 'admin') {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$email_username]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            redirect('dashboard.php', 'Admin Session Verified');
        } else { $error = "Invalid Auth Credentials"; }
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email_username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            redirect('index.php', 'Member Access Granted');
        } else { $error = "Invalid Member Credentials"; }
    }
}
?>

<div class="container">
    <div class="auth-wrapper">
        <h1 style="font-size: 4rem; font-weight: 900; line-height: 1; margin-bottom: 3rem; text-transform: uppercase;">Login<br>Identification.</h1>
        
        <?php if (isset($error)): ?>
            <div style="background: #000; color: #fff; padding: 1rem; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2rem;">
                ! Authentication Error. Please verify your credentials.
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Access Type</label>
                <select name="role">
                    <option value="user">Member Account</option>
                    <option value="admin">Admin Console</option>
                </select>
            </div>
            <div class="form-group">
                <label>Identifier</label>
                <input type="text" name="login_id" required placeholder="Email or Username">
            </div>
            <div class="form-group">
                <label>Secret Key</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn-cta full-width">Verify Identity</button>
        </form>

        <div style="margin-top: 4rem; text-align: center; border-top: 1px solid var(--border); padding-top: 2rem;">
            <p style="color: var(--text-muted); font-size: 0.85rem; font-weight: 700;">
                NOT YET IDENTIFIED? <a href="register.php" style="color: #000; text-decoration: none; border-bottom: 1px solid #000;">JOIN FLEET.</a>
            </p>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
