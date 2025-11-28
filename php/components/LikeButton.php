<?php
require_once __DIR__ . '/../models/Like.php';

class LikeButton {
    private int $postId;
    private int $userId;
    private Like $likeModel;
    private bool $isLiked;
    private int $likesCount;

    public function __construct(int $postId, int $userId = 0) {
        $this->postId = $postId;
        $this->userId = $userId;
        $this->likeModel = new Like();

        // Get like status and count
        $this->likesCount = $this->likeModel->getCount($postId);
        $this->isLiked = $userId > 0 ? $this->likeModel->isLiked($postId, $userId) : false;
    }

    public function render(): string {
        $heartIcon = $this->isLiked ? '❤️' : '🤍';
        $btnClass = $this->isLiked ? 'btn-error' : 'btn-ghost';

        if ($this->userId > 0) {
            // Logged in user - clickable button
            return "
                <form method='POST' action='like.php' class='inline'>
                    <input type='hidden' name='post_id' value='{$this->postId}'>
                    <input type='hidden' name='action' value='" . ($this->isLiked ? 'unlike' : 'like') . "'>
                    <button type='submit' class='btn btn-sm {$btnClass}'>
                        {$heartIcon} {$this->likesCount}
                    </button>
                </form>
            ";
        } else {
            // Not logged in - disabled button
            return "
                <button class='btn btn-sm btn-ghost' disabled>
                    🤍 {$this->likesCount}
                </button>
            ";
        }
    }

    public static function create(int $postId, int $userId = 0): string {
        $instance = new self($postId, $userId);
        return $instance->render();
    }
}