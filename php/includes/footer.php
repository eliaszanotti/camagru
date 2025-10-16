  <!-- Footer -->
    <footer class="footer bg-base-300 text-base-content p-8 mt-16">
        <div class="container mx-auto text-center">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">📸 Camagru</h3>
                    <p class="text-sm">Create, share and enjoy photo edits with webcam filters.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Quick Links</h3>
                    <ul class="text-sm space-y-2">
                        <li><a href="index.php" class="link link-hover">Home</a></li>
                        <li><a href="gallery.php" class="link link-hover">Gallery</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="create.php" class="link link-hover">Create</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Theme</h3>
                    <div class="flex justify-center gap-2">
                        <button class="btn btn-sm" onclick="document.documentElement.setAttribute('data-theme', 'light')">Light</button>
                        <button class="btn btn-sm" onclick="document.documentElement.setAttribute('data-theme', 'dark')">Dark</button>
                        <button class="btn btn-sm" onclick="document.documentElement.setAttribute('data-theme', 'cupcake')">Cupcake</button>
                    </div>
                </div>
            </div>
            <div class="divider mt-8"></div>
            <p class="text-sm">Created with ❤️ using Tailwind v4 + DaisyUI</p>
        </div>
    </footer>

    <script>
        console.log('🌼 DaisyUI + Tailwind v4 are working perfectly!');
    </script>
</body>
</html>