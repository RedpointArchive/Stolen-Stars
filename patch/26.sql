ALTER TABLE user
ADD is_administrator INTEGER;

UPDATE user
SET is_administrator = 1
WHERE username = 'hach-que';