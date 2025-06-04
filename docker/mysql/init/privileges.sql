CREATE USER IF NOT EXISTS 'hyperf'@'%' IDENTIFIED BY 'secret';
GRANT ALL PRIVILEGES ON hyperf.* TO 'hyperf'@'%';
GRANT ALL PRIVILEGES ON hyperf_testing.* TO 'hyperf'@'%';
FLUSH PRIVILEGES;