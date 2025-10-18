<?php
$pageTitle = 'Home';
require_once 'includes/header.php';
?>

<main class="p-16">
    <div class="container mx-auto">
        <section class="hero min-h-80 bg-gradient-to-r from-primary to-secondary text-primary-content rounded-box">
            <div class="hero-content text-center">
                <div class="space-y-4">
                    <h1 class="text-4xl font-bold">Welcome to Camagru!</h1>
                    <p class="text-lg">Create, share and enjoy photo edits with webcam filters</p>
                    <div class="space-x-2">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <a href="register.php" class="btn btn-lg btn-neutral">Register</a>
                            <a href="login.php" class="btn  btn-lg btn-neutral">Login</a>
                        <?php else: ?>
                            <a href="create.php" class="btn btn-neutral btn-lg">Start Creating</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php require_once 'includes/recent-posts.php'; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

<script>
    // Mock posts data (replace with actual API call later)
    const mockPosts = Array.from({
        length: 47
    }, (_, i) => ({
        id: i + 1,
        title: `Post ${i + 1}`,
        author: `User ${Math.floor(Math.random() * 20) + 1}`,
        likes: Math.floor(Math.random() * 100),
        comments: Math.floor(Math.random() * 30),
        created_at: new Date(Date.now() - Math.random() * 7 * 24 * 60 * 60 * 1000).toISOString()
    }));

    let currentPage = 1;
    const postsPerPage = 10;

    function displayPosts(page) {
        const postsContainer = document.getElementById('posts-container');
        const loading = document.getElementById('loading');

        // Show loading
        loading.style.display = 'block';
        postsContainer.innerHTML = '';

        // Simulate loading delay
        setTimeout(() => {
            const startIndex = (page - 1) * postsPerPage;
            const endIndex = startIndex + postsPerPage;
            const currentPosts = mockPosts.slice(startIndex, endIndex);

            // Hide loading
            loading.style.display = 'none';

            // Display posts
            currentPosts.forEach(post => {
                const postCard = createPostCard(post);
                postsContainer.appendChild(postCard);
            });

            // Update pagination
            updatePagination(page);
        }, 300);
    }

    function createPostCard(post) {
        const card = document.createElement('div');
        card.className = 'card bg-base-100 shadow-xl';

        const date = new Date(post.created_at).toLocaleDateString();
        const imageId = post.id % 1000; // Use post ID for varied images

        card.innerHTML = `
        <figure>
            <img src="https://picsum.photos/seed/${imageId}/400/300.jpg" alt="${post.title}" class="w-full h-48 object-cover">
        </figure>
        <div class="card-body">
            <h3 class="card-title text-lg">${post.title}</h3>
            <div class="flex items-center gap-2 text-sm text-base-content/70 mb-2">
                <span>By ${post.author}</span>
                <span>•</span>
                <span>${date}</span>
            </div>
            <div class="flex justify-between items-center">
                <div class="flex gap-4">
                    <button class="btn btn-sm btn-ghost">
                        ❤️ ${post.likes}
                    </button>
                    <button class="btn btn-sm btn-ghost">
                        💬 ${post.comments}
                    </button>
                </div>
                <button class="btn btn-sm btn-primary">View</button>
            </div>
        </div>
    `;

        return card;
    }

    function updatePagination(page) {
        const pagination = document.getElementById('pagination');
        const totalPages = Math.ceil(mockPosts.length / postsPerPage);

        let paginationHTML = '<div class="join">';

        // Previous button
        paginationHTML += `
        <button class="join-item btn" onclick="goToPage(${page - 1})" ${page === 1 ? 'disabled' : ''}>
            «
        </button>
    `;

        // Page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, page - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage < maxVisiblePages - 1) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        if (startPage > 1) {
            paginationHTML += `<button class="join-item btn" onclick="goToPage(1)">1</button>`;
            if (startPage > 2) {
                paginationHTML += `<button class="join-item btn" disabled>...</button>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
            <button class="join-item btn ${i === page ? 'btn-active' : ''}" onclick="goToPage(${i})">
                ${i}
            </button>
        `;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHTML += `<button class="join-item btn" disabled>...</button>`;
            }
            paginationHTML += `<button class="join-item btn" onclick="goToPage(${totalPages})">${totalPages}</button>`;
        }

        // Next button
        paginationHTML += `
        <button class="join-item btn" onclick="goToPage(${page + 1})" ${page === totalPages ? 'disabled' : ''}>
            »
        </button>
    `;

        paginationHTML += '</div>';
        pagination.innerHTML = paginationHTML;
    }

    function goToPage(page) {
        const totalPages = Math.ceil(mockPosts.length / postsPerPage);
        if (page < 1 || page > totalPages) return;

        currentPage = page;
        displayPosts(currentPage);

        // Scroll to top of posts
        document.getElementById('posts-container').scrollIntoView({
            behavior: 'smooth'
        });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        displayPosts(1);
    });
</script>