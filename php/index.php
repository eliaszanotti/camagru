<!DOCTYPE html>
<html lang="en" data-theme="cupcake">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - DaisyUI Test</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header with Navbar -->
    <header class="navbar bg-primary text-primary-content">
        <div class="navbar-start">
            <a class="btn btn-ghost text-xl">📸 Camagru</a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1">
                <li><a>Home</a></li>
                <li><a>Gallery</a></li>
                <li><a>Create</a></li>
            </ul>
        </div>
        <div class="navbar-end">
            <button class="btn btn-secondary">Login</button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Hero Section -->
        <section class="hero min-h-[300px] bg-gradient-to-r from-primary to-secondary text-primary-content rounded-2xl">
            <div class="hero-content text-center">
                <div>
                    <h1 class="text-4xl font-bold mb-4">DaisyUI is Working!</h1>
                    <p class="text-lg mb-6">Tailwind v4 + DaisyUI components are fully functional</p>
                    <button class="btn btn-accent btn-lg">Get Started</button>
                </div>
            </div>
        </section>

        <!-- Components Demo -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12">
            <!-- Alert Card -->
            <div class="card bg-base-200 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">✅ DaisyUI Components</h2>
                    <div class="alert alert-success mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>All DaisyUI components are working!</span>
                    </div>
                </div>
            </div>

            <!-- PHP Demo Card -->
            <div class="card bg-base-200 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">🐘 PHP is Working!</h2>
                    <div class="alert alert-info mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Current time: <?php echo date('H:i:s'); ?></span>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm">PHP Version: <?php echo phpversion(); ?></p>
                        <div class="mt-2">
                            <?php
                            // Demo loop
                            $colors = ['primary', 'secondary', 'accent', 'success', 'warning', 'error'];
                            echo '<div class="flex gap-1">';
                            foreach ($colors as $color) {
                                echo '<div class="badge badge-' . $color . '">' . $color . '</div>';
                            }
                            echo '</div>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Theme Switcher -->
        <section class="mt-12 text-center">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title justify-center">🎨 Theme Selector</h2>
                    <div class="flex justify-center gap-2 mt-4">
                        <button class="btn btn-sm" onclick="document.documentElement.setAttribute('data-theme', 'light')">Light</button>
                        <button class="btn btn-sm" onclick="document.documentElement.setAttribute('data-theme', 'dark')">Dark</button>
                        <button class="btn btn-sm" onclick="document.documentElement.setAttribute('data-theme', 'cupcake')">Cupcake</button>
                        <button class="btn btn-sm" onclick="document.documentElement.setAttribute('data-theme', 'emerald')">Emerald</button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer bg-base-300 text-base-content p-8 mt-16">
        <div class="container mx-auto text-center">
            <p>📸 Camagru - Created with ❤️ using Tailwind v4 + DaisyUI</p>
        </div>
    </footer>

    <script>
        console.log('🌼 DaisyUI + Tailwind v4 are working perfectly!');
    </script>
</body>
</html>