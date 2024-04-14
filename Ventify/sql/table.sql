CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(100),
    avatar_path VARCHAR(255);
);

/** New table **/

ALTER TABLE users ADD COLUMN avatar_path VARCHAR(255);

