<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../models/Post.php';

try {
    $postModel = new Post();

    // Get pagination parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    // Get posts (8 per page)
    $postsPerPage = 8;
    $posts = $postModel->getAll($page, $postsPerPage);
    $totalCount = $postModel->getTotalCount();

    // Format response
    $response = [
        'success' => true,
        'posts' => $posts,
        'pagination' => [
            'currentPage' => $page,
            'totalPosts' => $totalCount,
            'totalPages' => ceil($totalCount / $postsPerPage),
            'postsPerPage' => $postsPerPage
        ]
    ];

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch posts'
    ]);
}
