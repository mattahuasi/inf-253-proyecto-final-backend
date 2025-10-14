CREATE TABLE people (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    paternal_surname VARCHAR(20) NULL,
    maternal_surname VARCHAR(20) NULL,
    names VARCHAR(45) NOT NULL,
    gender ENUM('M', 'F') NOT NULL DEFAULT 'M',
    phone VARCHAR(15) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE addresses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    zona VARCHAR(90) NOT NULL,
    street VARCHAR(45) NOT NULL,
    detail VARCHAR(180) NOT NULL,
    person_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    person_id BIGINT UNSIGNED NULL UNIQUE,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE customers (
    person_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (person_id),
    FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE employees (
    person_id BIGINT UNSIGNED NOT NULL,
    type ENUM('AD', 'CO', 'CA', 'WA') NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (person_id),
    FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(45) NOT NULL,
    slug VARCHAR(45) NOT NULL UNIQUE,
    description VARCHAR(225) NOT NULL,
    priority ENUM('0', '1', '2', '3', '4', '5', '6', '7', '8', '9') NOT NULL DEFAULT '0',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE menus (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(90) NOT NULL,
    description VARCHAR(225) NOT NULL,
    price DECIMAL(10 , 2 ) NOT NULL DEFAULT '0',
    photo VARCHAR(180) NULL,
    stock INT UNSIGNED NOT NULL DEFAULT '0',
    priority ENUM('H', 'M', 'L') NOT NULL DEFAULT 'L',
    enable TINYINT(1) NOT NULL DEFAULT '1',
    category_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
	FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE tables (
    number BIGINT UNSIGNED PRIMARY KEY,
    status ENUM('A', 'B', 'W') NOT NULL DEFAULT 'A',
    ability INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE states (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(45) NOT NULL UNIQUE,
    description VARCHAR(180) NOT NULL UNIQUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tracking_code VARCHAR(45) NOT NULL UNIQUE,
    type ENUM('O1', 'O2', 'O3') NOT NULL DEFAULT 'O3',
    ordered_at DATETIME NOT NULL,
    delivered_at DATETIME NULL,
    customer_name VARCHAR(45) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    comment VARCHAR(180) NULL,
    payment_made TINYINT(1) NOT NULL DEFAULT '0',
    state_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NULL,
    address_id BIGINT UNSIGNED NULL,
    employee_id BIGINT UNSIGNED NOT NULL,
    table_number BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(person_id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(person_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (table_number) REFERENCES tables(number) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE order_details (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(90) NOT NULL,
    price DECIMAL(10 , 2 ) NOT NULL DEFAULT '0',
    comment VARCHAR(180) NULL,
    amount INT UNSIGNED NOT NULL,
    order_id BIGINT UNSIGNED NOT NULL,
    menu_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE (name, guard_name)
);

CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE (name, guard_name)
);

CREATE TABLE model_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (permission_id, model_id, model_type),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    INDEX (model_id, model_type)
);

CREATE TABLE model_has_roles (
    role_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    INDEX (model_id, model_type)
);

CREATE TABLE role_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (permission_id, role_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);
