-- Database schema for Camagru (PostgreSQL)

-- Users table
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    email_verification_token VARCHAR(255) NULL,
    password_reset_token VARCHAR(255) NULL,
    password_reset_expires TIMESTAMP NULL,
    email_notifications BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Posts table
CREATE TABLE posts (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    image_path VARCHAR(255) NOT NULL,
    caption TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Likes table
CREATE TABLE likes (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    post_id INTEGER NOT NULL REFERENCES posts(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, post_id)
);

-- Comments table
CREATE TABLE comments (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    post_id INTEGER NOT NULL REFERENCES posts(id) ON DELETE CASCADE,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for better performance
CREATE INDEX idx_posts_created_at ON posts(created_at DESC);
CREATE INDEX idx_comments_post_created ON comments(post_id, created_at DESC);

-- Insert some sample data for testing
INSERT INTO users (username, email, password_hash, is_verified) VALUES
('user1', 'user1@example.com', 'hashed_password_1', TRUE),
('user2', 'user2@example.com', 'hashed_password_2', TRUE),
('user3', 'user3@example.com', 'hashed_password_3', TRUE);

INSERT INTO posts (user_id, image_path, caption) VALUES
(1, 'uploads/post_1.jpg', 'My first creation!'),
(2, 'uploads/post_2.jpg', 'Having fun with filters'),
(3, 'uploads/post_3.jpg', 'Check this out!'),
(1, 'uploads/post_4.jpg', 'Another cool pic'),
(2, 'uploads/post_5.jpg', 'Testing the camera'),
(3, 'uploads/post_6.jpg', 'Love this app'),
(1, 'uploads/post_7.jpg', 'Weekend vibes'),
(2, 'uploads/post_8.jpg', 'Experimenting'),
(3, 'uploads/post_9.jpg', 'Creative time'),
(1, 'uploads/post_10.jpg', 'New filter unlocked');