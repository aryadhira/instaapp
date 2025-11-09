<?php

class Migration001CreateCoreTables
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * SQL to apply: Create tables and the UUID extension.
     */
    public function up(): string
    {
        try {
            $this->pdo->exec("CREATE EXTENSION IF NOT EXISTS \"uuid-ossp\";");
            echo "UUID extension ensured.\n";
        } catch (\PDOException $e) {
            error_log("Extension creation warning: " . $e->getMessage());
        }

        return "
            -- users
            CREATE TABLE users (
                id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
                email TEXT UNIQUE NOT NULL,
                username TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                created_at TIMESTAMPTZ DEFAULT now()
            );

            -- posts
            CREATE TABLE posts (
                id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
                user_id UUID REFERENCES users(id) ON DELETE CASCADE,
                content TEXT,
                image_url TEXT,
                created_at TIMESTAMPTZ DEFAULT now()
            );

            -- likes (user can like a post once)
            CREATE TABLE likes (
                id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
                user_id UUID REFERENCES users(id) ON DELETE CASCADE,
                post_id UUID REFERENCES posts(id) ON DELETE CASCADE,
                created_at TIMESTAMPTZ DEFAULT now(),
                UNIQUE (user_id, post_id)
            );

            -- comments
            CREATE TABLE comments (
                id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
                user_id UUID REFERENCES users(id) ON DELETE CASCADE,
                post_id UUID REFERENCES posts(id) ON DELETE CASCADE,
                content TEXT NOT NULL,
                created_at TIMESTAMPTZ DEFAULT now()
            );
        ";
    }

    /**
     * SQL to reverse the changes.
     */
    public function down(): string
    {
        return "
            DROP TABLE IF EXISTS comments;
            DROP TABLE IF EXISTS likes;
            DROP TABLE IF EXISTS posts;
            DROP TABLE IF EXISTS users;
            -- We typically do NOT drop extensions here.
        ";
    }
}
