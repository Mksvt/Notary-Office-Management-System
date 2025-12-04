-- Створення бази даних
CREATE DATABASE IF NOT EXISTS notary_office
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE notary_office;

-- 1. Таблиця офісів (нот. контор)
CREATE TABLE offices (
    office_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(100) NOT NULL,
    address   VARCHAR(255) NOT NULL,
    city      VARCHAR(100) NOT NULL,
    phone     VARCHAR(20),
    email     VARCHAR(100),
    schedule  VARCHAR(255)
) ENGINE=InnoDB;

-- 2. Таблиця клієнтів
CREATE TABLE clients (
    client_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    last_name       VARCHAR(50)  NOT NULL,
    first_name      VARCHAR(50)  NOT NULL,
    middle_name     VARCHAR(50),
    birth_date      DATE,
    passport_series VARCHAR(10),
    passport_number VARCHAR(20),
    tax_id          VARCHAR(20),
    phone           VARCHAR(20),
    email           VARCHAR(100),
    address         VARCHAR(255),
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_clients_passport UNIQUE (passport_series, passport_number),
    CONSTRAINT uq_clients_tax_id   UNIQUE (tax_id)
) ENGINE=InnoDB;

-- 3. Таблиця нотаріусів
CREATE TABLE notaries (
    notary_id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    last_name          VARCHAR(50)  NOT NULL,
    first_name         VARCHAR(50)  NOT NULL,
    middle_name        VARCHAR(50),
    license_number     VARCHAR(30)  NOT NULL,
    license_issue_date DATE,
    phone              VARCHAR(20),
    email              VARCHAR(100),
    office_id          INT UNSIGNED NOT NULL,
    hired_at           DATE,
    is_active          TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT uq_notaries_license UNIQUE (license_number),
    CONSTRAINT fk_notaries_office
        FOREIGN KEY (office_id) REFERENCES offices(office_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 4. Таблиця послуг
CREATE TABLE services (
    service_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    description TEXT,
    base_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    is_active  TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- 5. Таблиця нотаріальних справ
CREATE TABLE notarial_cases (
    case_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    case_number VARCHAR(30)  NOT NULL,
    open_date   DATE         NOT NULL,
    close_date  DATE,
    client_id   INT UNSIGNED NOT NULL,
    notary_id   INT UNSIGNED NOT NULL,
    service_id  INT UNSIGNED NOT NULL,
    status      ENUM('open','in_progress','closed','cancelled')
                NOT NULL DEFAULT 'open',
    notes       TEXT,
    CONSTRAINT uq_cases_number UNIQUE (case_number),
    CONSTRAINT fk_cases_client
        FOREIGN KEY (client_id) REFERENCES clients(client_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_cases_notary
        FOREIGN KEY (notary_id) REFERENCES notaries(notary_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_cases_service
        FOREIGN KEY (service_id) REFERENCES services(service_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Індекси для пришвидшення пошуку по справах
CREATE INDEX idx_cases_client  ON notarial_cases(client_id);
CREATE INDEX idx_cases_notary  ON notarial_cases(notary_id);
CREATE INDEX idx_cases_service ON notarial_cases(service_id);
CREATE INDEX idx_cases_status  ON notarial_cases(status);

-- 6. Таблиця документів по справі
CREATE TABLE documents (
    document_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    case_id     INT UNSIGNED NOT NULL,
    doc_type    VARCHAR(50)  NOT NULL,
    doc_number  VARCHAR(50),
    issue_date  DATE,
    expiry_date DATE,
    file_path   VARCHAR(255),
    is_original TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT fk_documents_case
        FOREIGN KEY (case_id) REFERENCES notarial_cases(case_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_documents_case ON documents(case_id);
CREATE INDEX idx_documents_type ON documents(doc_type);

-- 7. Таблиця оплат по справі
CREATE TABLE payments (
    payment_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    case_id       INT UNSIGNED NOT NULL,
    payment_date  DATE         NOT NULL,
    amount        DECIMAL(10,2) NOT NULL,
    method        ENUM('cash','card','bank_transfer','other')
                  NOT NULL DEFAULT 'cash',
    receipt_number VARCHAR(50),
    status        ENUM('pending','paid','cancelled','refunded')
                  NOT NULL DEFAULT 'pending',
    comment       VARCHAR(255),
    CONSTRAINT fk_payments_case
        FOREIGN KEY (case_id) REFERENCES notarial_cases(case_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE INDEX idx_payments_case   ON payments(case_id);
CREATE INDEX idx_payments_status ON payments(status);
CREATE INDEX idx_payments_date   ON payments(payment_date);

-- 8. Таблиця користувачів для авторизації
CREATE TABLE users (
    user_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('admin','notary','receptionist','accountant') NOT NULL,
    related_id INT UNSIGNED,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_active  TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- Додати тестового адміністратора (пароль: admin123)
INSERT INTO users (username, password, role, is_active) VALUES
('admin', '$2y$10$pG5v4X4loXXdKxWQCFYfuee24peqY0W1tLCEGrxam7zAjsbonJImq', 'admin', 1);

-- ========================================
-- TEST DATA
-- ========================================

-- Add offices
INSERT INTO offices (name, address, city, phone, email, schedule) VALUES
('Central Notary Office', '25 Khreshchatyk St.', 'Kyiv', '+380441234567', 'central@notary.ua', 'Mon-Fri: 9:00-18:00'),
('Right Bank Notary Office', '45 Peremohy Ave.', 'Kyiv', '+380442345678', 'pravyy@notary.ua', 'Mon-Fri: 9:00-17:00'),
('Private Notary Office', '12 Velyka Vasylkivska St.', 'Kyiv', '+380443456789', 'private@notary.ua', 'Mon-Sat: 10:00-19:00');

-- Add services
INSERT INTO services (name, description, base_price, is_active) VALUES
('Real Estate Purchase Agreement Certification', 'Processing and certification of purchase agreements for apartments, houses, land plots', 5000.00, 1),
('Gift Agreement Certification', 'Processing of gratuitous transfer of property', 3000.00, 1),
('Power of Attorney Certification', 'Processing powers of attorney for representation of interests', 500.00, 1),
('Will Certification', 'Drafting and certification of wills', 2000.00, 1),
('Loan Agreement Certification', 'Processing loan agreements between individuals', 1500.00, 1),
('Document Copy Authentication', 'Authentication of document copies', 100.00, 1),
('Inheritance Certification', 'Processing inheritance rights', 4000.00, 1),
('Prenuptial Agreement Certification', 'Processing property relations of spouses', 2500.00, 1);

-- Add notaries
INSERT INTO notaries (last_name, first_name, middle_name, license_number, license_issue_date, phone, email, office_id, hired_at, is_active) VALUES
('Ivanenko', 'Olena', 'Petrivna', 'NT-2018-001234', '2018-05-15', '+380671234567', 'ivanenko@notary.ua', 1, '2018-06-01', 1),
('Petrenko', 'Andriy', 'Vasyliovych', 'NT-2019-005678', '2019-03-20', '+380672345678', 'petrenko@notary.ua', 1, '2019-04-01', 1),
('Sydorenko', 'Maria', 'Oleksandrivna', 'NT-2020-009012', '2020-07-10', '+380673456789', 'sydorenko@notary.ua', 2, '2020-08-01', 1),
('Kovalenko', 'Viktor', 'Mykolayovych', 'NT-2017-003456', '2017-11-25', '+380674567890', 'kovalenko@notary.ua', 3, '2017-12-01', 1);

-- Add users for notaries
INSERT INTO users (username, password, role, related_id, is_active) VALUES
('ivanenko', '$2y$10$pG5v4X4loXXdKxWQCFYfuee24peqY0W1tLCEGrxam7zAjsbonJImq', 'notary', 1, 1),
('petrenko', '$2y$10$pG5v4X4loXXdKxWQCFYfuee24peqY0W1tLCEGrxam7zAjsbonJImq', 'notary', 2, 1),
('receptionist', '$2y$10$pG5v4X4loXXdKxWQCFYfuee24peqY0W1tLCEGrxam7zAjsbonJImq', 'receptionist', NULL, 1),
('accountant', '$2y$10$pG5v4X4loXXdKxWQCFYfuee24peqY0W1tLCEGrxam7zAjsbonJImq', 'accountant', NULL, 1);

-- Add clients
INSERT INTO clients (last_name, first_name, middle_name, birth_date, passport_series, passport_number, tax_id, phone, email, address) VALUES
('Shevchenko', 'Taras', 'Hryhorovych', '1985-03-15', 'KN', '123456', '1234567890', '+380501234567', 'shevchenko@gmail.com', 'Kyiv, 10 Khreshchatyk St., apt. 5'),
('Melnyk', 'Olga', 'Ivanivna', '1990-07-22', 'KN', '234567', '2345678901', '+380502345678', 'melnyk@gmail.com', 'Kyiv, 20 Saksahanskoho St., apt. 12'),
('Bondarenko', 'Dmytro', 'Serhiyovych', '1978-11-05', 'KN', '345678', '3456789012', '+380503456789', 'bondarenko@gmail.com', 'Kyiv, 50 Peremohy Ave., apt. 8'),
('Tkachenko', 'Natalia', 'Volodymyrivna', '1995-02-18', 'KN', '456789', '4567890123', '+380504567890', 'tkachenko@gmail.com', 'Kyiv, 30 Velyka Vasylkivska St., apt. 15'),
('Koval', 'Mykhailo', 'Petrovych', '1982-09-30', 'KN', '567890', '5678901234', '+380505678901', 'koval@gmail.com', 'Kyiv, 15 Lvivska St., apt. 22'),
('Kravchenko', 'Anna', 'Andriivna', '1988-06-12', 'KN', '678901', '6789012345', '+380506789012', 'kravchenko@gmail.com', 'Kyiv, 40 Antonovycha St., apt. 7'),
('Moroz', 'Serhiy', 'Oleksiyovych', '1992-04-25', 'KN', '789012', '7890123456', '+380507890123', 'moroz@gmail.com', 'Kyiv, 60 Zhylianska St., apt. 18'),
('Lysenko', 'Yulia', 'Mykolaivna', '1987-12-08', 'KN', '890123', '8901234567', '+380508901234', 'lysenko@gmail.com', 'Kyiv, 25 Sahaidachnoho St., apt. 11');

-- Add notarial cases
INSERT INTO notarial_cases (case_number, open_date, close_date, client_id, notary_id, service_id, status, notes) VALUES
('2024-001', '2024-01-15', '2024-01-20', 1, 1, 1, 'closed', 'Apartment purchase on Khreshchatyk St.'),
('2024-002', '2024-01-18', '2024-01-22', 2, 1, 3, 'closed', 'Power of attorney for court representation'),
('2024-003', '2024-02-05', '2024-02-10', 3, 2, 2, 'closed', 'Land plot gift to son'),
('2024-004', '2024-02-12', NULL, 4, 2, 4, 'in_progress', 'Will drafting'),
('2024-005', '2024-03-01', '2024-03-05', 5, 3, 5, 'closed', 'Loan agreement for 50000 UAH'),
('2024-006', '2024-03-10', NULL, 6, 3, 7, 'in_progress', 'Inheritance processing after mother'),
('2024-007', '2024-03-15', NULL, 7, 1, 1, 'open', 'House purchase'),
('2024-008', '2024-03-20', '2024-03-22', 8, 4, 6, 'closed', 'Document copy authentication'),
('2024-009', '2024-04-01', NULL, 1, 1, 8, 'in_progress', 'Prenuptial agreement'),
('2024-010', '2024-04-05', '2024-04-08', 2, 2, 3, 'closed', 'General power of attorney for vehicle');

-- Add documents to cases
INSERT INTO documents (case_id, doc_type, doc_number, issue_date, expiry_date, file_path, is_original) VALUES
(1, 'Passport', 'KN123456', '2015-03-15', NULL, 'uploads/documents/passport_1.pdf', 1),
(1, 'Purchase Agreement', '2024-001-PA', '2024-01-20', NULL, 'uploads/documents/contract_1.pdf', 1),
(2, 'Passport', 'KN234567', '2016-07-22', NULL, 'uploads/documents/passport_2.pdf', 1),
(2, 'Power of Attorney', '2024-002-POA', '2024-01-22', '2025-01-22', 'uploads/documents/power_of_attorney_1.pdf', 1),
(3, 'Passport', 'KN345678', '2014-11-05', NULL, 'uploads/documents/passport_3.pdf', 1),
(3, 'Ownership Certificate', 'OC-2024-001', '2024-02-10', NULL, 'uploads/documents/ownership_1.pdf', 1),
(5, 'Passport', 'KN567890', '2012-09-30', NULL, 'uploads/documents/passport_5.pdf', 1),
(5, 'Loan Agreement', '2024-005-LA', '2024-03-05', NULL, 'uploads/documents/loan_1.pdf', 1);

-- Add payments
INSERT INTO payments (case_id, payment_date, amount, method, receipt_number, status, comment) VALUES
(1, '2024-01-15', 5000.00, 'card', 'RC-2024-001', 'paid', 'Purchase agreement service payment'),
(2, '2024-01-18', 500.00, 'cash', 'RC-2024-002', 'paid', 'Power of attorney payment'),
(3, '2024-02-05', 3000.00, 'bank_transfer', 'RC-2024-003', 'paid', 'Gift agreement payment'),
(4, '2024-02-12', 2000.00, 'card', 'RC-2024-004', 'paid', 'Will drafting payment'),
(5, '2024-03-01', 1500.00, 'cash', 'RC-2024-005', 'paid', 'Loan agreement payment'),
(6, '2024-03-10', 2000.00, 'card', 'RC-2024-006', 'paid', 'Partial inheritance processing payment'),
(6, '2024-03-20', 2000.00, 'card', 'RC-2024-007', 'pending', 'Remaining inheritance processing payment'),
(7, '2024-03-15', 5000.00, 'bank_transfer', 'RC-2024-008', 'pending', 'Purchase agreement payment'),
(8, '2024-03-20', 100.00, 'cash', 'RC-2024-009', 'paid', 'Copy authentication'),
(9, '2024-04-01', 2500.00, 'card', 'RC-2024-010', 'paid', 'Prenuptial agreement payment'),
(10, '2024-04-05', 500.00, 'cash', 'RC-2024-011', 'paid', 'Vehicle power of attorney payment');
