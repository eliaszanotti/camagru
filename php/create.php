<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Create Photo';
require_once 'includes/header.php';

require_once 'models/Post.php';

$postModel = new Post();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = trim($_POST['caption'] ?? '');

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Please select an image to upload';
    } else {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['image']['type'];

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = 'Only JPEG, PNG and GIF images are allowed';
        }

        if ($_FILES['image']['size'] > 5 * 1024 * 1024) { // 5MB limit
            $errors[] = 'Image size must be less than 5MB';
        }
    }

    if (empty($errors)) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . time() . '.jpg';
        $uploadPath = $uploadDir . $fileName;

        // Process image
        $sourcePath = $_FILES['image']['tmp_name'];

        // Create image resource based on file type
        switch ($fileType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                $sourceImage = false;
        }

        if ($sourceImage) {
            // Resize image to max 800x800
            $maxSize = 800;
            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);

            if ($width > $maxSize || $height > $maxSize) {
                $ratio = min($maxSize / $width, $maxSize / $height);
                $newWidth = $width * $ratio;
                $newHeight = $height * $ratio;

                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($sourceImage);
                $sourceImage = $resizedImage;
            }

            // Save as JPEG
            if (imagejpeg($sourceImage, $uploadPath, 90)) {
                $postData = [
                    'user_id' => $_SESSION['user_id'],
                    'image_path' => $uploadPath,
                    'caption' => $caption,
                    'is_published' => true
                ];

                if ($postModel->create($postData)) {
                    $success = 'Photo created successfully!';
                    unset($_POST);
                } else {
                    $errors[] = 'Failed to save photo to database';
                    if (file_exists($uploadPath)) {
                        unlink($uploadPath);
                    }
                }
            } else {
                $errors[] = 'Failed to save image file';
            }

            imagedestroy($sourceImage);
        } else {
            $errors[] = 'Failed to process image';
        }
    }
}
?>

<!-- Main Content -->
<main class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">Create Photo</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success mb-6">
                <?php echo htmlspecialchars($success); ?>
                <div class="mt-2">
                    <a href="gallery.php" class="btn btn-primary btn-sm">View in Gallery</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Upload Form -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Upload Image</h2>
                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Choose Image</span>
                            </label>
                            <input type="file" name="image" class="file-input file-input-bordered w-full"
                                   accept="image/jpeg,image/png,image/gif" required>
                            <label class="label">
                                <span class="label-text-alt">JPEG, PNG or GIF (Max 5MB)</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Caption (optional)</span>
                            </label>
                            <textarea name="caption" class="textarea textarea-bordered h-24"
                                      maxlength="500"><?php echo htmlspecialchars($_POST['caption'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-full">Upload Photo</button>
                    </form>
                </div>
            </div>

            <!-- Preview -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Preview</h2>
                    <div id="preview" class="aspect-square bg-base-200 rounded-lg flex items-center justify-center">
                        <div class="text-center text-base-content/50">
                            <div class="text-6xl mb-2">📷</div>
                            <p>Image preview will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Your Recent Photos -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4">Your Recent Photos</h2>
            <?php
            $userPosts = $postModel->getByUserId($_SESSION['user_id']);
            if (empty($userPosts)):
            ?>
                <div class="text-center py-8 bg-base-200 rounded-lg">
                    <p class="text-base-content/70">You haven't created any photos yet</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php foreach (array_slice($userPosts, 0, 8) as $post): ?>
                        <div class="card bg-base-100 shadow-lg">
                            <figure class="h-32">
                                <img src="<?php echo htmlspecialchars($post['image_path']); ?>"
                                     alt="Your photo" class="w-full h-full object-cover">
                            </figure>
                            <div class="card-body p-3">
                                <p class="text-xs text-base-content/70">
                                    <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                </p>
                                <form method="POST" action="delete-post.php" class="inline">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" class="btn btn-error btn-xs w-full"
                                            onclick="return confirm('Delete this photo?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
// Image preview
document.querySelector('input[type="file"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('preview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-contain rounded-lg">`;
        }
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = `
            <div class="text-center text-base-content/50">
                <div class="text-6xl mb-2">📷</div>
                <p>Image preview will appear here</p>
            </div>
        `;
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>