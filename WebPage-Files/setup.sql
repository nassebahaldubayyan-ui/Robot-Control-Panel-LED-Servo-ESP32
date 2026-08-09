CREATE TABLE robot_state (
    id INT PRIMARY KEY AUTO_INCREMENT,
    command VARCHAR(10) NOT NULL DEFAULT 'S',
    led_state TINYINT(1) NOT NULL DEFAULT 0,
    servo_state VARCHAR(10) NOT NULL DEFAULT 'normal',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO robot_state (id, command, led_state, servo_state) VALUES (1, 'S', 0, 'normal');