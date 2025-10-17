    <div class="w-full px-8">
        <footer class="navbar mx-auto max-w-6xl h-48">
            <div class="navbar-start">
                <a href="index.php" class="btn btn-ghost">Camagru</a>
            </div>
            <div class="navbar-center gap-2">
                <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?> btn btn-ghost">Home</a>
                <a href="gallery.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : ''; ?> btn btn-ghost">Gallery</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="create.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'create.php' ? 'active' : ''; ?> btn btn-ghost">Create</a>
                <?php endif; ?>
            </div>
            <div class="navbar-end gap-2">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-ghost">Login</a>
                    <a href="register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </footer>
    </div>
    </body>

    </html>