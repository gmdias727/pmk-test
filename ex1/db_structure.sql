USE donations;

DROP TABLE IF EXISTS donors;

DROP TABLE IF EXISTS payment_details;

DROP TABLE IF EXISTS addresses;

CREATE TABLE donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    cpf VARCHAR(255) NOT NULL UNIQUE,
    telefone VARCHAR(255) NOT NULL UNIQUE,
    birthday TIMESTAMP NOT NULL,
    registration_date TIMESTAMP NOT NULL,
    donation_interval ENUM ('Único', 'Bimestral', 'Semestral', 'Anual'),
    donation_value INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE payment_details (
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

CREATE TABLE addresses (
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

INSERT INTO
    donors (
        name,
        email,
        cpf,
        telefone,
        birthday,
        registration_date,
        donation_interval,
        donation_value
    )
VALUES
    (
        'John Doe',
        'john@example.com',
        '12345678901',
        '11987654321',
        '1990-01-01',
        NOW (),
        'Único',
        100
    );

INSERT INTO
    payment_details (donor_id, payment_method, account_info)
VALUES
    (
        1,
        'Débito',
        '{"bank": "Bank Name", "branch": "1234", "account_number": "56789-0"}'
    );
