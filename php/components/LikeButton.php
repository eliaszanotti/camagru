<?php
require_once __DIR__ . '/../models/Like.php';

class LikeButton
{
    private int $postId;
    private int $userId;
    private Like $likeModel;
    private bool $isLiked;
    private int $likesCount;

    public function __construct(int $postId, int $userId = 0)
    {
        $this->postId = $postId;
        $this->userId = $userId;
        $this->likeModel = new Like();

        // Get like status and count
        $this->likesCount = $this->likeModel->getCount($postId);
        $this->isLiked = $userId > 0 ? $this->likeModel->isLiked($postId, $userId) : false;
    }

    public function render(): string
    {
        $btnClass = $this->isLiked ? 'btn-error' : 'btn-neutral';

        if ($this->userId > 0) {
            return "
                <form method='POST' action='like.php' class='inline'>
                    <input type='hidden' name='post_id' value='{$this->postId}'>
                    <input type='hidden' name='action' value='" . ($this->isLiked ? 'unlike' : 'like') . "'>
                    <button type='submit' class='btn {$btnClass}'>
                        <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-heart-icon lucide-heart'><path d='M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5'/></svg>    
                        {$this->likesCount}
                    </button>
                </form>
            ";
        } else {
            return "
                <button class='btn btn-ghost' disabled>
                    <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-heart-icon lucide-heart'><path d='M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5'/></svg>        
                    {$this->likesCount}
                </button>
            ";
        }
    }

    public static function create(int $postId, int $userId = 0): string
    {
        $instance = new self($postId, $userId);
        return $instance->render();
    }
}
