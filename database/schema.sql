-- ============================================================
-- RentaLuxuryCars — Schema da Base de Dados
-- MySQL 8.0+
-- ============================================================

CREATE DATABASE IF NOT EXISTS luxdrive CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE luxdrive;

-- Tabela de utilizadores (admin + clientes)
CREATE TABLE IF NOT EXISTS utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    apelido VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telefone VARCHAR(20),
    nif VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'cliente') DEFAULT 'cliente',
    ativo TINYINT(1) DEFAULT 1,
    email_verificado TINYINT(1) DEFAULT 0,
    token_verificacao VARCHAR(64),
    tentativas_login INT DEFAULT 0,
    bloqueado_ate DATETIME NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de veículos
CREATE TABLE IF NOT EXISTS veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) UNIQUE NOT NULL,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    ano INT NOT NULL,
    categoria ENUM('supercar', 'gran_turismo', 'suv_luxo', 'berlina_luxo', 'cabrio') NOT NULL,
    preco_dia DECIMAL(10,2) NOT NULL,
    preco_semana DECIMAL(10,2),
    preco_fim_semana DECIMAL(10,2),
    cavalos INT,
    motor VARCHAR(50),
    transmissao ENUM('manual', 'automatico', 'pdk', 'dct') DEFAULT 'automatico',
    lugares INT DEFAULT 4,
    velocidade_maxima INT,
    aceleracao VARCHAR(20),
    cor_exterior VARCHAR(50),
    cor_interior VARCHAR(50),
    descricao TEXT,
    caracteristicas JSON,
    imagem_principal VARCHAR(255),
    imagens JSON,
    disponivel TINYINT(1) DEFAULT 1,
    destaque TINYINT(1) DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de reservas
CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referencia VARCHAR(20) UNIQUE NOT NULL,
    veiculo_id INT NOT NULL,
    utilizador_id INT,
    cliente_nome VARCHAR(100) NOT NULL,
    cliente_apelido VARCHAR(100) NOT NULL,
    cliente_email VARCHAR(150) NOT NULL,
    cliente_telefone VARCHAR(20) NOT NULL,
    cliente_nif VARCHAR(20),
    cliente_carta_conducao VARCHAR(50) NOT NULL,
    cliente_nascimento DATE NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    local_entrega VARCHAR(200) NOT NULL,
    local_devolucao VARCHAR(200) NOT NULL,
    extras JSON,
    dias INT NOT NULL,
    preco_base DECIMAL(10,2) NOT NULL,
    preco_extras DECIMAL(10,2) DEFAULT 0,
    preco_total DECIMAL(10,2) NOT NULL,
    deposito DECIMAL(10,2),
    estado ENUM('pendente', 'confirmada', 'ativa', 'concluida', 'cancelada') DEFAULT 'pendente',
    notas TEXT,
    metodo_pagamento ENUM('transferencia', 'multibanco', 'cartao', 'presencial') DEFAULT 'transferencia',
    pago TINYINT(1) DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (veiculo_id) REFERENCES veiculos(id),
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id)
) ENGINE=InnoDB;

-- Tabela de disponibilidade / bloqueios de calendário
CREATE TABLE IF NOT EXISTS disponibilidade (
    id INT AUTO_INCREMENT PRIMARY KEY,
    veiculo_id INT NOT NULL,
    reserva_id INT,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    motivo VARCHAR(100),
    FOREIGN KEY (veiculo_id) REFERENCES veiculos(id),
    FOREIGN KEY (reserva_id) REFERENCES reservas(id)
) ENGINE=InnoDB;

-- Tabela de mensagens de contacto
CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefone VARCHAR(20),
    assunto VARCHAR(200),
    mensagem TEXT NOT NULL,
    respondido TINYINT(1) DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de extras de reserva
CREATE TABLE IF NOT EXISTS extras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(60) UNIQUE NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255),
    preco_dia DECIMAL(10,2) NOT NULL,
    icone VARCHAR(50),
    ativo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

CREATE INDEX idx_veiculos_categoria ON veiculos(categoria);
CREATE INDEX idx_veiculos_disponivel ON veiculos(disponivel);
CREATE INDEX idx_reservas_estado ON reservas(estado);
CREATE INDEX idx_reservas_datas ON reservas(data_inicio, data_fim);
CREATE INDEX idx_disponibilidade_veiculo ON disponibilidade(veiculo_id, data_inicio, data_fim);
