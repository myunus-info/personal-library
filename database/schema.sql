-- Users table (Single Admin)
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tags/Categories table
CREATE TABLE IF NOT EXISTS tags (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    color VARCHAR(7) DEFAULT '#3b82f6', -- HEX color code
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Books table
CREATE TABLE IF NOT EXISTS books (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) DEFAULT NULL,
    description TEXT,
    cover_image VARCHAR(255) DEFAULT NULL,
    condition VARCHAR(20) CHECK (condition IN ('new', 'good', 'damaged')) DEFAULT 'new',
    reading_status VARCHAR(20) CHECK (reading_status IN ('to_read', 'reading', 'completed')) DEFAULT 'to_read',
    current_page INT DEFAULT 0,
    total_pages INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Junction table for Books and Tags (Many-to-Many)
CREATE TABLE IF NOT EXISTS book_tags (
    book_id INT REFERENCES books(id) ON DELETE CASCADE,
    tag_id INT REFERENCES tags(id) ON DELETE CASCADE,
    PRIMARY KEY (book_id, tag_id)
);

-- Index for searching books
CREATE INDEX IF NOT EXISTS idx_books_title_author ON books(title, author);
CREATE INDEX IF NOT EXISTS idx_books_reading_status ON books(reading_status);
CREATE INDEX IF NOT EXISTS idx_books_condition ON books(condition);
