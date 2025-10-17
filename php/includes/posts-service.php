<?php

class PostsService {
    private const API_URL = 'api/posts.php';
    private const POSTS_PER_PAGE = 10;

    private int $currentPage = 1;
    private int $totalPages = 1;

    public function getCurrentPage(): int {
        return $this->currentPage;
    }

    public function getTotalPages(): int {
        return $this->totalPages;
    }

    public function fetchPosts(int $page = 1): array {
        $this->currentPage = $page;

        try {
            $response = file_get_contents(self::API_URL . '?page=' . $page);
            $data = json_decode($response, true);

            if ($data['success']) {
                $this->totalPages = $data['pagination']['totalPages'];
                return $data['posts'];
            }

            return [];
        } catch (Exception $e) {
            error_log('Error fetching posts: ' . $e->getMessage());
            return [];
        }
    }

    public function createPostCard(array $post): string {
        $date = date('M j, Y', strtotime($post['created_at']));
        $imageSrc = !empty($post['image_path']) ? $post['image_path'] : "https://picsum.photos/seed/{$post['id']}/400/300.jpg";
        $caption = !empty($post['caption']) ? htmlspecialchars($post['caption']) : 'Untitled Post';

        return "
        <div class='card bg-base-100 shadow-xl'>
            <figure>
                <img src='{$imageSrc}' alt='{$caption}' class='w-full h-48 object-cover'>
            </figure>
            <div class='card-body'>
                <h3 class='card-title text-lg'>{$caption}</h3>
                <div class='flex items-center gap-2 text-sm text-base-content/70 mb-2'>
                    <span>By " . htmlspecialchars($post['username']) . "</span>
                    <span>•</span>
                    <span>{$date}</span>
                </div>
                <div class='flex justify-between items-center'>
                    <div class='flex gap-4'>
                        <button class='btn btn-sm btn-ghost' onclick='likePost({$post['id']})'>
                            ❤️ Like
                        </button>
                        <button class='btn btn-sm btn-ghost' onclick='commentPost({$post['id']})'>
                            💬 Comment
                        </button>
                    </div>
                    <button class='btn btn-sm btn-primary' onclick='viewPost({$post['id']})'>
                        View
                    </button>
                </div>
            </div>
        </div>";
    }

    public function createPagination(int $currentPage): string {
        $html = '<div class="join">';

        // Previous button
        $prevDisabled = $currentPage === 1 ? 'disabled' : '';
        $html .= "<button class='join-item btn' onclick='goToPage(" . ($currentPage - 1) . ")' {$prevDisabled}>«</button>";

        // Page numbers
        $maxVisiblePages = 5;
        $startPage = max(1, $currentPage - floor($maxVisiblePages / 2));
        $endPage = min($this->totalPages, $startPage + $maxVisiblePages - 1);

        if ($endPage - $startPage < $maxVisiblePages - 1) {
            $startPage = max(1, $endPage - $maxVisiblePages + 1);
        }

        if ($startPage > 1) {
            $html .= "<button class='join-item btn' onclick='goToPage(1)'>1</button>";
            if ($startPage > 2) {
                $html .= "<button class='join-item btn' disabled>...</button>";
            }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            $active = $i === $currentPage ? 'btn-active' : '';
            $html .= "<button class='join-item btn {$active}' onclick='goToPage({$i})'>{$i}</button>";
        }

        if ($endPage < $this->totalPages) {
            if ($endPage < $this->totalPages - 1) {
                $html .= "<button class='join-item btn' disabled>...</button>";
            }
            $html .= "<button class='join-item btn' onclick='goToPage({$this->totalPages})'>{$this->totalPages}</button>";
        }

        // Next button
        $nextDisabled = $currentPage === $this->totalPages ? 'disabled' : '';
        $html .= "<button class='join-item btn' onclick='goToPage(" . ($currentPage + 1) . ")' {$nextDisabled}>»</button>";

        $html .= '</div>';
        return $html;
    }
}

?>