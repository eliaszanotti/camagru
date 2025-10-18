<div class="card bg-base-200">
    <div class="card-body">
        <h3 class="card-title">Account Info</h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-base-content/50">Username:</span>
                <span><?php echo htmlspecialchars($user['username']); ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-base-content/50">Email:</span>
                <span><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-base-content/50">Email Verified:</span>
                <span class="badge <?php echo ($user['is_verified'] ?? false) ? 'badge-success' : 'badge-error'; ?>"><?php echo ($user['is_verified'] ?? false) ? 'Verified' : 'Not verified'; ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-base-content/50">Member Since:</span>
                <span><?php echo date('j M, Y', strtotime($user['created_at'])); ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-base-content/50">Photos Created:</span>
                <span><?php echo count($userPosts); ?></span>
            </div>
        </div>
    </div>
</div>