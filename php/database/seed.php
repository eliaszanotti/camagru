<?php

require_once __DIR__ . '/../config/database.php';

$db = Database::getConnection();
$passwordHash = password_hash('MotDePasse1!', PASSWORD_DEFAULT);
$userIds = [];
$seedImageDirectory = __DIR__ . '/../uploads/posts';

if (!is_dir($seedImageDirectory) && !mkdir($seedImageDirectory, 0775, true) && !is_dir($seedImageDirectory)) {
    throw new RuntimeException('Could not create the seed image directory.');
}

$db->beginTransaction();

try {
    $upsertUser = $db->prepare(
        'INSERT INTO users (username, email, password_hash, is_verified)
         VALUES (:username, :email, :password_hash, TRUE)
         ON CONFLICT (email) DO UPDATE SET
             username = EXCLUDED.username,
             password_hash = EXCLUDED.password_hash,
             is_verified = TRUE,
             email_verification_token = NULL
         RETURNING id'
    );

    for ($accountNumber = 1; $accountNumber <= 10; $accountNumber++) {
        $upsertUser->execute([
            ':username' => 'test' . $accountNumber,
            ':email' => 'test' . $accountNumber . '@test.fr',
            ':password_hash' => $passwordHash
        ]);
        $userIds[] = (int) $upsertUser->fetchColumn();
    }

    $deleteSeedPosts = $db->prepare(
        "DELETE FROM posts WHERE user_id = ANY(CAST(:user_ids AS INTEGER[])) AND caption LIKE '[SEED]%'"
    );
    $deleteSeedPosts->execute([':user_ids' => '{' . implode(',', $userIds) . '}']);

    $insertPost = $db->prepare(
        'INSERT INTO posts (user_id, image_path, caption, is_published, created_at)
         VALUES (:user_id, :image_path, :caption, TRUE, :created_at)
         RETURNING id'
    );
    $insertComment = $db->prepare(
        'INSERT INTO comments (post_id, user_id, content, created_at)
         VALUES (:post_id, :user_id, :content, :created_at)'
    );

    $commentTexts = [
        'Great photo!',
        'I love the colors.',
        'Really nice work 👏',
        'Thanks for sharing!',
        'The result looks great.',
        'Well done!',
        'This composition is excellent.',
        'A lovely memory 😊'
    ];

    for ($postNumber = 1; $postNumber <= 100; $postNumber++) {
        $accountIndex = ($postNumber - 1) % count($userIds);
        $caption = sprintf('[SEED] Test photo %03d', $postNumber);
        $imageName = sprintf('seed-post-%03d.svg', $postNumber);
        $imagePath = $seedImageDirectory . '/' . $imageName;
        $publicImagePath = 'uploads/posts/' . $imageName;
        $daysAgo = random_int(0, 89);
        $createdAt = (new DateTimeImmutable())->modify('-' . $daysAgo . ' days')->format('Y-m-d H:i:s');

        $hue = ($postNumber * 37) % 360;
        $secondaryHue = ($hue + 70) % 360;
        $svg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop stop-color="hsl(%d 72%% 55%%)"/><stop offset="1" stop-color="hsl(%d 76%% 38%%)"/></linearGradient></defs><rect width="800" height="800" fill="url(#g)"/><circle cx="650" cy="160" r="210" fill="#fff" opacity=".12"/><circle cx="110" cy="700" r="290" fill="#fff" opacity=".1"/><text x="400" y="370" text-anchor="middle" fill="#fff" font-family="sans-serif" font-size="52" font-weight="700">CAMAGRU</text><text x="400" y="445" text-anchor="middle" fill="#fff" font-family="sans-serif" font-size="28" opacity=".9">Test photo %03d</text></svg>',
            $hue,
            $secondaryHue,
            $postNumber
        );

        if (file_put_contents($imagePath, $svg) === false) {
            throw new RuntimeException('Could not write seed image: ' . $imageName);
        }

        $insertPost->execute([
            ':user_id' => $userIds[$accountIndex],
            ':image_path' => $publicImagePath,
            ':caption' => $caption,
            ':created_at' => $createdAt
        ]);
        $postId = (int) $insertPost->fetchColumn();

        $commentCount = random_int(0, 5);
        for ($commentNumber = 0; $commentNumber < $commentCount; $commentNumber++) {
            $commenterOffset = random_int(1, count($userIds) - 1);
            $commenterIndex = ($accountIndex + $commenterOffset) % count($userIds);
            $commentText = $commentTexts[array_rand($commentTexts)];
            $commentDate = (new DateTimeImmutable($createdAt))
                ->modify('+' . random_int(0, 23) . ' hours')
                ->format('Y-m-d H:i:s');

            $insertComment->execute([
                ':post_id' => $postId,
                ':user_id' => $userIds[$commenterIndex],
                ':content' => $commentText,
                ':created_at' => $commentDate
            ]);
        }
    }

    $db->commit();
    fwrite(STDOUT, "Seed complete: 10 test accounts, 100 posts, and 0–5 random comments per post.\n");
    fwrite(STDOUT, "Password for every account: MotDePasse1!\n");
} catch (Throwable $error) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    throw $error;
}
