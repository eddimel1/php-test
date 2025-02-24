BEGIN;

-- Dictionaries

CREATE TABLE IF NOT EXISTS gender (
    id SERIAL PRIMARY KEY,
    name VARCHAR(20) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS user_status (
    id SERIAL PRIMARY KEY,
    name VARCHAR(20) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS contact_type (
    id SERIAL PRIMARY KEY,
    name VARCHAR(20) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS currency (
    id SERIAL PRIMARY KEY,
    name VARCHAR(10) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS bet_status (
    id SERIAL PRIMARY KEY,
    name VARCHAR(20) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS transaction_type (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS odds_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS outcomes (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

-- System Variables

CREATE TABLE IF NOT EXISTS system_config (
    id SERIAL PRIMARY KEY,
    key VARCHAR(50) UNIQUE NOT NULL,
    value DECIMAL(10,2) NOT NULL
);

-- Insert Dictionary Data

INSERT INTO gender (name) VALUES ('male'), ('female'), ('other');

INSERT INTO user_status (name) VALUES ('active'), ('banned'), ('archived');

INSERT INTO contact_type (name) VALUES ('phone'), ('email');

INSERT INTO currency (name) VALUES ('EUR'), ('USD'), ('RUB');

INSERT INTO bet_status (name) VALUES ('pending'), ('won'), ('lost');

INSERT INTO transaction_type (name) VALUES 
('deposit'),
('withdrawal'),
('bet_placed'),
('bet_won'),
('bet_lost');

INSERT INTO roles (name) VALUES 
('user'),
('admin'),
('manager');

INSERT INTO odds_types (name) VALUES 
('Team 1 Win'),
('Draw'),
('Team 2 Win');

-- Users Table

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    gender_id INT NOT NULL,
    birth_date DATE NOT NULL,
    status_id INT DEFAULT 1,
    role_id INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (gender_id) REFERENCES gender(id),
    FOREIGN KEY (status_id) REFERENCES user_status(id),
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- User Contacts (Many-to-One)

CREATE TABLE IF NOT EXISTS user_contacts (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    contact_type_id INT NOT NULL,
    contact_value VARCHAR(100) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (contact_type_id) REFERENCES contact_type(id)
);

-- User Balance (Many-to-One)

CREATE TABLE IF NOT EXISTS user_balances (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    currency_id INT NOT NULL,
    -- Stored in cents
    balance INTEGER DEFAULT 0,
    active BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (currency_id) REFERENCES currency(id)
);

-- Events Table

CREATE TABLE IF NOT EXISTS events (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    event_date TIMESTAMP NOT NULL,
    league VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bets Table

CREATE TABLE IF NOT EXISTS bets (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    odds DECIMAL(5,2) NOT NULL,
    odds_type_id INTEGER NOT NULL,
    -- Stored in cents
    amount INTEGER NOT NULL,
    currency_id INT NOT NULL,
    status_id INT DEFAULT 1, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (currency_id) REFERENCES currency(id),
    FOREIGN KEY (status_id) REFERENCES bet_status(id),
    FOREIGN KEY (odds_type_id) REFERENCES odds_types(id)
);

-- Transactions Table

CREATE TABLE IF NOT EXISTS transactions (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    bet_id INT NULL,
    transaction_type_id INT NOT NULL, 
    amount INTEGER DEFAULT 0,
    currency_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (bet_id) REFERENCES bets(id) ON DELETE SET NULL,
    FOREIGN KEY (transaction_type_id) REFERENCES transaction_type(id),
    FOREIGN KEY (currency_id) REFERENCES currency(id)
);

-- Insert System Configuration Variables

INSERT INTO system_config (key, value) VALUES 
('min_bet_amount', 100), -- Stored in cents (1.00 is 100 cents)
('max_bet_amount', 50000), -- Stored in cents (500.00 is 50000 cents)
('min_odds', 1.01), 
('max_odds', 40.00);

-- Insert Events Data

CREATE TABLE IF NOT EXISTS events (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    event_date TIMESTAMP NOT NULL,
    league VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Events Data

INSERT INTO events (name, event_date, league) VALUES
('TEAM 1 Man City TEAM 2 Arsenal', '2024-04-15 18:00:00', 'Premier League'),
('TEAM 1 Real Madrid TEAM 2 Barcelona', '2024-04-16 20:30:00', 'La Liga'),
('TEAM 1 Bayern Munich TEAM 2 Dortmund', '2024-04-17 19:00:00', 'Bundesliga'),
('TEAM 1 Juventus TEAM 2 Inter Milan', '2024-04-18 20:45:00', 'Serie A'),
('TEAM 1 PSG TEAM 2 Lyon', '2024-04-19 21:00:00', 'Ligue 1'),
('TEAM 1 Ajax TEAM 2 PSV Eindhoven', '2024-04-20 18:30:00', 'Eredivisie'),
('TEAM 1 LA Galaxy TEAM 2 New York City FC', '2024-04-21 22:00:00', 'MLS'),
('TEAM 1 Benfica TEAM 2 Porto', '2024-04-22 20:00:00', 'Primeira Liga'),
('TEAM 1 RB Leipzig TEAM 2 Schalke 04', '2024-04-23 20:30:00', 'Bundesliga'),
('TEAM 1 Sevilla TEAM 2 Real Sociedad', '2024-04-24 19:30:00', 'La Liga'),
('TEAM 1 Liverpool TEAM 2 Chelsea', '2024-04-25 21:00:00', 'Premier League'),
('TEAM 1 AC Milan TEAM 2 Napoli', '2024-04-26 19:45:00', 'Serie A'),
('TEAM 1 Marseille TEAM 2 Monaco', '2024-04-27 22:15:00', 'Ligue 1');



-- Insert Test Users

INSERT INTO users (login, password_hash, name, gender_id, birth_date, status_id, role_id) VALUES
('user1', '12345', 'John Doe', (SELECT id FROM gender WHERE name='male'), '1990-01-01', (SELECT id FROM user_status WHERE name='active'), (SELECT id FROM roles WHERE name='user')),
('user2', '12345', 'Jane Smith', (SELECT id FROM gender WHERE name='female'), '1995-05-15', (SELECT id FROM user_status WHERE name='active'), (SELECT id FROM roles WHERE name='user'));

-- Insert User Contacts

INSERT INTO user_contacts (user_id, contact_type_id, contact_value) VALUES
(1, (SELECT id FROM contact_type WHERE name='phone'), '+1234567890'),
(1, (SELECT id FROM contact_type WHERE name='email'), 'john.doe@example.com'),
(1, (SELECT id FROM contact_type WHERE name='phone'), '+9876543210'),
(1, (SELECT id FROM contact_type WHERE name='email'), 'john.doe2@example.com'),
(2, (SELECT id FROM contact_type WHERE name='phone'), '+0987654321'),
(2, (SELECT id FROM contact_type WHERE name='email'), 'jane.smith@example.com');

-- Insert User Balances

INSERT INTO user_balances (user_id, currency_id, balance,active) VALUES
(1, (SELECT id FROM currency WHERE name='USD'), 10000000,true),
(1, (SELECT id FROM currency WHERE name='RUB'), 50000000,false),
(2, (SELECT id FROM currency WHERE name='USD'), 10000000,true),
(2, (SELECT id FROM currency WHERE name='RUB'), 50000000,false);

COMMIT;




