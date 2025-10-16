<!DOCTYPE html>
<html lang="en" data-theme="cupcake">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="navbar bg-base-300 shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex-1">
                <a class="btn btn-ghost text-xl">📸 Camagru</a>
            </div>
            <div class="flex-none gap-2">
                <button class="btn btn-ghost btn-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full">
                            <img alt="User Avatar" src="https://daisyui.com/images/stock/photo-1534528741775-53994a69daeb.jpg" />
                        </div>
                    </div>
                    <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
                        <li><a>Profile</a></li>
                        <li><a>Settings</a></li>
                        <li><a>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Hero Section -->
        <section class="hero min-h-[400px] bg-gradient-to-r from-primary to-secondary rounded-2xl">
            <div class="hero-content text-center text-neutral-content">
                <div class="max-w-md">
                    <h1 class="mb-5 text-5xl font-bold">Welcome to Camagru!</h1>
                    <p class="mb-5">Create amazing photos with your webcam and fun overlays. Share, like, and comment with the community!</p>
                    <div class="flex gap-4 justify-center">
                        <button class="btn btn-primary">Get Started</button>
                        <button class="btn btn-outline btn-accent">Browse Gallery</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Cards -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
            <!-- Camera Feature -->
            <div class="card bg-base-200 shadow-xl">
                <figure class="px-10 pt-10">
                    <div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary-content" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">Webcam Capture</h2>
                    <p>Take photos directly from your webcam with real-time preview</p>
                    <div class="card-actions">
                        <button class="btn btn-primary">Try Camera</button>
                    </div>
                </div>
            </div>

            <!-- Overlay Feature -->
            <div class="card bg-base-200 shadow-xl">
                <figure class="px-10 pt-10">
                    <div class="w-24 h-24 bg-secondary rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-secondary-content" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">Fun Overlays</h2>
                    <p>Add creative overlays and filters to make your photos unique</p>
                    <div class="card-actions">
                        <button class="btn btn-secondary">Browse Overlays</button>
                    </div>
                </div>
            </div>

            <!-- Gallery Feature -->
            <div class="card bg-base-200 shadow-xl">
                <figure class="px-10 pt-10">
                    <div class="w-24 h-24 bg-accent rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-accent-content" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">Community Gallery</h2>
                    <p>Share your creations, like, and comment on photos from others</p>
                    <div class="card-actions">
                        <button class="btn btn-accent">View Gallery</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="mt-16 text-center">
            <div class="alert alert-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="font-bold">Ready to start creating?</h3>
                    <div class="text-xs">Sign up now and join our creative community!</div>
                </div>
            </div>
            <div class="mt-6">
                <button class="btn btn-primary btn-lg">Sign Up Now</button>
                <button class="btn btn-outline btn-lg ml-4">Login</button>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer bg-base-300 text-base-content p-10 mt-16">
        <div class="container mx-auto">
            <aside>
                <svg width="50" height="50" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" class="fill-current">
                    <path d="M22.672 15.226l-2.432.811.841 2.515c.33 1.019-.209 2.127-1.23 2.456-1.15.325-2.148-.321-2.463-1.226l-.84-2.518-5.013 1.677.84 2.517c.391 1.203-.434 2.542-1.831 2.542-.152 0-.297-.014-.435-.04-.234-.064-.446-.166-.637-.295-.317-.229-.566-.543-.726-.913l-.84-2.517-2.433.809c-1.15.325-2.149-.321-2.464-1.226-.309-.9.085-1.889.928-2.342l2.432-.809-.842-2.515c-.33-1.02.209-2.127 1.23-2.456 1.15-.325 2.148.321 2.463 1.226l.84 2.518 5.013-1.677-.84-2.517c-.391-1.203.434-2.542 1.831-2.542.152 0 .297.014.435.04.234.064.446.166.637.295.317.229.566.543.726.913l.84 2.517 2.433-.809c1.15-.325 2.149.321 2.464 1.226.309.9-.085 1.889-.928 2.342z"/>
                </svg>
                <p class="font-bold">Camagru Industries Ltd.<br/>Providing creative photo fun since 2024</p>
            </aside>
            <nav>
                <h6 class="footer-title">Quick Links</h6>
                <a class="link link-hover">Gallery</a>
                <a class="link link-hover">Create</a>
                <a class="link link-hover">Profile</a>
                <a class="link link-hover">About</a>
            </nav>
            <nav>
                <h6 class="footer-title">Social</h6>
                <div class="grid grid-flow-col gap-4">
                    <a><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="fill-current"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></a>
                    <a><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="fill-current"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg></a>
                    <a><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="fill-current"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg></a>
                </div>
            </nav>
        </div>
    </footer>

    <script>
        // Simple JavaScript for demo
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Camagru - Tailwind v4 + DaisyUI working!');
        });
    </script>
</body>
</html>