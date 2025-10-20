-- Migration script to add missing tables and columns for share functionality

-- Add is_published column to posts table if it doesn't exist
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_name='posts' AND column_name='is_published'
    ) THEN
        ALTER TABLE posts ADD COLUMN is_published BOOLEAN DEFAULT TRUE;
    END IF;
END $$;

-- Create captured_images table if it doesn't exist
CREATE TABLE IF NOT EXISTS captured_images (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    image_path VARCHAR(255) NOT NULL,
    source VARCHAR(50) DEFAULT 'webcam',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create index for better performance
CREATE INDEX IF NOT EXISTS idx_captured_images_user_created ON captured_images(user_id, created_at DESC);