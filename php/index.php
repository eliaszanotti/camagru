<?php
$pageTitle = 'Home';
require_once 'includes/header.php';
?>

<!-- Main Content -->
<main class="container mx-auto px-4 py-8">
    <!-- Hero Section -->
    <section class="hero min-h-[300px] bg-gradient-to-r from-primary to-secondary text-primary-content rounded-2xl">
        <div class="hero-content text-center">
            <div>
                <h1 class="text-4xl font-bold mb-4">Welcome to Camagru!</h1>
                <p class="text-lg mb-6">Create, share and enjoy photo edits with webcam filters</p>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="space-x-4">
                        <a href="register.php" class="btn btn-accent btn-lg">Get Started</a>
                        <a href="login.php" class="btn btn-secondary btn-lg">Login</a>
                    </div>
                <?php else: ?>
                    <a href="create.php" class="btn btn-accent btn-lg">Start Creating</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Recent Posts Section -->
    <section class="mt-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold">Recent Posts</h2>
            <a href="gallery.php" class="btn btn-primary">View All</a>
        </div>

        <!-- Posts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="posts-container">
            <!-- Posts will be loaded here via JavaScript -->
        </div>

        <!-- Loading state -->
        <div id="loading" class="text-center py-8">
            <span class="loading loading-spinner loading-lg"></span>
            <p class="mt-4">Loading posts...</p>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-8" id="pagination">
            <!-- Pagination will be generated here -->
        </div>
    </section>

    <!-- Features Section -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16">
        <!-- Webcam Feature -->
        <div class="card bg-base-200 shadow-xl">
            <div class="card-body">
                <div class="text-4xl mb-4">📸</div>
                <h3 class="card-title">Webcam Capture</h3>
                <p>Take photos directly from your webcam with real-time preview</p>
            </div>
        </div>

        <!-- Filters Feature -->
        <div class="card bg-base-200 shadow-xl">
            <div class="card-body">
                <div class="text-4xl mb-4">🎨</div>
                <h3 class="card-title">Fun Filters</h3>
                <p>Apply various filters and effects to make your photos unique</p>
            </div>
        </div>

        <!-- Social Feature -->
        <div class="card bg-base-200 shadow-xl">
            <div class="card-body">
                <div class="text-4xl mb-4">💬</div>
                <h3 class="card-title">Share & Comment</h3>
                <p>Share your creations and interact with the community</p>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

<script>
// Mock posts data (replace with actual API call later)
const mockPosts = Array.from({ length: 47 }, (_, i) => ({
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
    document.getElementById('posts-container').scrollIntoView({ behavior: 'smooth' });
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    displayPosts(1);
});
</script>