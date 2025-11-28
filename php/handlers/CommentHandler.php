<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../services/FormService.php';

class CommentHandler {
    private Post $postModel;
    private Comment $commentModel;
    private FormService $formService;
    private array $post;
    private array $comments;

    public function __construct(int $postId) {
        $this->postModel = new Post();
        $this->commentModel = new Comment();
        $this->formService = FormService::create();

        // Validate post exists and is published
        $this->post = $this->postModel->findById($postId);
        if (!$this->post || !$this->post['is_published']) {
            header('Location: index.php');
            exit;
        }

        $this->comments = $this->commentModel->getByPostId($postId);
        $this->handleRequest($postId);
    }

    public function getFormService(): FormService {
        return $this->formService;
    }

    public function getPost(): array {
        return $this->post;
    }

    public function getComments(): array {
        return $this->comments;
    }

    public function shouldClearForm(): bool {
        return $this->formService->hasSuccess();
    }

    private function handleRequest(int $postId): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            return;
        }

        $content = trim($_POST['content'] ?? '');

        // Validate comment
        if (empty($content)) {
            $this->formService->addError('content', 'Comment cannot be empty');
            return;
        }

        if (strlen($content) > 500) {
            $this->formService->addError('content', 'Comment must be less than 500 characters');
            return;
        }

        // Create comment
        $commentData = [
            'post_id' => $postId,
            'user_id' => $_SESSION['user_id'],
            'content' => $content
        ];

        if ($this->commentModel->create($commentData)) {
            $this->formService->setSuccess('Comment added successfully!');
            $this->comments = $this->commentModel->getByPostId($postId); // Refresh comments
        } else {
            $this->formService->addError('general', 'Failed to add comment');
        }
    }
}