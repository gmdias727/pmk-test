USE donors_db;

DROP TABLE IF EXISTS donors;

DROP TABLE IF EXISTS payment_details;

DROP TABLE IF EXISTS addresses;

CREATE TABLE IF NOT EXISTS donors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  cpf VARCHAR(14) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  birth_date DATE NOT NULL,
  registration_date DATE NOT NULL,
  donation_interval ENUM('Unico','Bimestral','Semestral','Anual') NOT NULL,
  donation_value DECIMAL(10,2) NOT NULL,
  payment_method ENUM('Debito','Credito') NOT NULL,
  -- If payment_method=Debito
  account_number VARCHAR(50),
  -- If payment_method=Credito
  card_brand VARCHAR(20),
  card_first6 VARCHAR(6),
  card_last4 VARCHAR(4),
  address VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS payment_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    payment_method ENUM ('Débito', 'Crédito') NOT NULL,
    account_info JSON,
    card_info JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES donors (id),
    CONSTRAINT check_payment_info CHECK (
        (
            payment_method = 'Débito'
            AND account_info IS NOT NULL
            AND card_info IS NULL
        )
        OR (
            payment_method = 'Crédito'
            AND card_info IS NOT NULL
            AND account_info IS NULL
        )
    )
);

CREATE TABLE IF NOT EXISTS addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    street VARCHAR(255) NOT NULL,
    number INT NOT NULL,
    complement VARCHAR(255),
    neighborhood VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    state VARCHAR(255) NOT NULL,
    zip_code VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES donors (id)
);

CREATE INDEX idx_donors_email ON donors (email);

CREATE INDEX idx_donors_cpf ON donors (cpf);

CREATE INDEX idx_payment_details_donor_id ON payment_details (donor_id);
