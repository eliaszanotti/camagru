  <section class="mt-16">
        <h2 class="text-3xl font-bold mb-8">Recent Posts</h2>

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

<script>
    class PostsManager {
        constructor() {
            this.currentPage = 1;
            this.totalPages = 1;
            this.postsPerPage = 10;
            this.init();
        }

        async init() {
            await this.displayPosts(1);
        }

        async displayPosts(page) {
            this.currentPage = page;
            const postsContainer = document.getElementById('posts-container');
            const loading = document.getElementById('loading');

            this.showLoading(postsContainer, loading);

            try {
                const response = await fetch(`api/posts.php?page=${page}`);
                const data = await response.json();

                this.hideLoading(postsContainer, loading);

                if (data.success) {
                    this.renderPosts(data.posts, postsContainer);
                    this.totalPages = data.pagination.totalPages;
                    this.updatePagination(page);
                } else {
                    this.showError(postsContainer, 'Failed to load posts');
                }
            } catch (error) {
                this.hideLoading(postsContainer, loading);
                this.showError(postsContainer, 'Error loading posts');
                console.error('Error:', error);
            }
        }

        showLoading(postsContainer, loading) {
            loading.style.display = 'block';
            postsContainer.innerHTML = '';
        }

        hideLoading(postsContainer, loading) {
            loading.style.display = 'none';
        }

        showError(postsContainer, message) {
            postsContainer.innerHTML = `<p class="text-error">${message}</p>`;
        }

        renderPosts(posts, container) {
            posts.forEach(post => {
                const postCard = this.createPostCard(post);
                container.innerHTML += postCard;
            });
        }

        createPostCard(post) {
            const date = new Date(post.created_at).toLocaleDateString();
            const imageSrc = post.image_path || `https://picsum.photos/seed/${post.id}/400/300.jpg`;
            const caption = post.caption || 'Untitled Post';

            return `
            <div class="card bg-base-100 shadow-xl">
                <figure>
                    <img src="${imageSrc}" alt="${caption}" class="w-full h-48 object-cover">
                </figure>
                <div class="card-body">
                    <h3 class="card-title text-lg">${caption}</h3>
                    <div class="flex items-center gap-2 text-sm text-base-content/70 mb-2">
                        <span>By ${post.username}</span>
                        <span>•</span>
                        <span>${date}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex gap-4">
                            <button class="btn btn-sm btn-ghost" onclick="likePost(${post.id})">
                                ❤️ Like
                            </button>
                            <button class="btn btn-sm btn-ghost" onclick="commentPost(${post.id})">
                                💬 Comment
                            </button>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="viewPost(${post.id})">
                            View
                        </button>
                    </div>
                </div>
            </div>`;
        }

        updatePagination(page) {
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = this.createPagination(page);
        }

        createPagination(page) {
            let html = '<div class="join">';

            // Previous button
            html += `<button class="join-item btn" onclick="postsManager.goToPage(${page - 1})" ${page === 1 ? 'disabled' : ''}>«</button>`;

            // Page numbers
            const maxVisiblePages = 5;
            let startPage = Math.max(1, page - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(this.totalPages, startPage + maxVisiblePages - 1);

            if (endPage - startPage < maxVisiblePages - 1) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            if (startPage > 1) {
                html += `<button class="join-item btn" onclick="postsManager.goToPage(1)">1</button>`;
                if (startPage > 2) {
                    html += `<button class="join-item btn" disabled>...</button>`;
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                const active = i === page ? 'btn-active' : '';
                html += `<button class="join-item btn ${active}" onclick="postsManager.goToPage(${i})">${i}</button>`;
            }

            if (endPage < this.totalPages) {
                if (endPage < this.totalPages - 1) {
                    html += `<button class="join-item btn" disabled>...</button>`;
                }
                html += `<button class="join-item btn" onclick="postsManager.goToPage(${this.totalPages})">${this.totalPages}</button>`;
            }

            // Next button
            html += `<button class="join-item btn" onclick="postsManager.goToPage(${page + 1})" ${page === this.totalPages ? 'disabled' : ''}>»</button>`;

            html += '</div>';
            return html;
        }

        goToPage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.displayPosts(page);
            this.scrollToTop();
        }

        scrollToTop() {
            document.getElementById('posts-container').scrollIntoView({
                behavior: 'smooth'
            });
        }
    }

    // Global functions for button onclick events
    let postsManager;

    function likePost(postId) {
        console.log('Like post:', postId);
        // TODO: Implement like functionality
    }

    function commentPost(postId) {
        console.log('Comment on post:', postId);
        // TODO: Implement comment functionality
    }

    function viewPost(postId) {
        console.log('View post:', postId);
        // TODO: Implement view post functionality
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        postsManager = new PostsManager();
    });
</script>