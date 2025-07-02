INSERT INTO usuarios (nome, email, senha_hash, nivel) 
VALUES 
('PAULO DA SILVA PEREIRA', 'autoescoladinamica918@gmail.com', SHA2('142536Pa', 256), 'presidente');
('Suporte', 'suportecodegeek@gmail.com', SHA2('123', 256), 'presidente');

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    senha_hash VARCHAR(255),
    nivel ENUM('admin', 'presidente', 'suporte')
);

CREATE TABLE associacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_associacao VARCHAR(255) NOT NULL,
    sobre_associacao TEXT NOT NULL,
    logo_image VARCHAR(255) NOT NULL
);

CREATE TABLE enderecos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cep VARCHAR(10),
    telefone VARCHAR(15),
    endereco VARCHAR(255),
    bairro VARCHAR(100),
    numero VARCHAR(10),
    cidade VARCHAR(100),
    complemento VARCHAR(100),
    cnpj VARCHAR(100),
    uf VARCHAR(2)
);

CREATE TABLE horarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dia_inicio INT NOT NULL,
    dia_fim INT NOT NULL,
    hora_inicio_1 TIME NOT NULL,
    hora_fim_1 TIME NOT NULL,
    hora_inicio_2 TIME DEFAULT NULL,
    hora_fim_2 TIME DEFAULT NULL
);

CREATE TABLE feriados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data DATE NOT NULL,
    descricao TEXT NOT NULL
);

CREATE TABLE formas_pagamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    forma VARCHAR(255) NOT NULL
);

CREATE TABLE materiais_estudo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo_material VARCHAR(255) NOT NULL,
    nome_capa VARCHAR(255) NOT NULL,
    nome_material VARCHAR(255) NOT NULL,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    rg VARCHAR(20) NOT NULL,
    cpf VARCHAR(14) NOT NULL,
    data_nascimento DATE NOT NULL,
    telefone VARCHAR(20),
    renach VARCHAR(20),
    ladv DATE,
    vencimento_processo DATE NOT NULL,
    rua VARCHAR(255),
    bairro VARCHAR(100),
    numero VARCHAR(10),
    observacao TEXT,
    documento VARCHAR(255),
    foto VARCHAR(255) -- Coluna para armazenar o caminho da foto do aluno
);

CREATE TABLE servicos_aluno (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_aluno VARCHAR(255) NOT NULL,
    servico VARCHAR(100) NOT NULL,
    forma_pagamento VARCHAR(50) NOT NULL,
    valor_entrada VARCHAR(50) NOT NULL,
    preco DECIMAL(10,2) NULL,
    numero_parcelas INT DEFAULT 1,
    data_pagamento DATE,
    categoria VARCHAR(50) NOT NULL,
    pago VARCHAR(3) NOT NULL,
    status ENUM('ativo', 'finalizado') DEFAULT 'ativo',
    data_cadastro TIMESTAMP,
    UNIQUE KEY (nome_aluno, servico) -- Garante que não haja duplicados para (nome_aluno, servico)
);

CREATE TABLE login_aluno (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_aluno VARCHAR(255) NOT NULL,  -- Associa ao nome do aluno da tabela `alunos`
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    cpf_aluno VARCHAR(14) NOT NULL,
    status_cadastro VARCHAR(20) DEFAULT 'Pendente',
    reset_token INT,  -- Coluna para armazenar o token de redefinição de senha
    reset_token_validade DATETIME  -- Coluna para armazenar a validade do token
);

CREATE TABLE info_parcelas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_aluno VARCHAR(255) NOT NULL,
    servico VARCHAR(100) NOT NULL,
    forma_pagamento VARCHAR(50) NOT NULL,
    valor_entrada DECIMAL(10, 2) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    numero_parcelas INT DEFAULT 1,
    data_pagamento DATE,
    pago VARCHAR(10),
    categoria VARCHAR(100),
    status VARCHAR(50),
    excluido TINYINT(1) , -- 0 para não excluído, 1 para excluído
    created_at TIMESTAMP 
);

CREATE TABLE instrutores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_instrutor VARCHAR(255) NOT NULL,
    placa_instrutor VARCHAR(20) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE placas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(20) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE fichas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    rg VARCHAR(20),
    cpf VARCHAR(14),
    ladv DATE NOT NULL,
    vencimento_processo DATE NOT NULL,
    categoria VARCHAR(50),
    instrutor VARCHAR(255) NOT NULL,
    placa VARCHAR(20) NOT NULL,
    registro VARCHAR(20),
    horario_inicio TIME,
    horario_fim TIME,
    data_ficha DATE NOT NULL,
    status VARCHAR(50),
    UNIQUE KEY unique_ficha (instrutor, placa, horario_inicio, horario_fim, data_ficha)
);

CREATE TABLE relatorios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_aluno VARCHAR(255) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    data_preco DATE NOT NULL,
    categoria_preco VARCHAR(70),
    valor_saida DECIMAL(10,2) NOT NULL
);

CREATE TABLE tabela_saida (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(255) NOT NULL,
    valor_saida DECIMAL(10, 2) NOT NULL,
    data_saida DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE turmas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    local VARCHAR(255) NOT NULL,
    instrutor VARCHAR(255) NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    horario_inicio TIME NOT NULL,
    horario_fim TIME NOT NULL,
    turno VARCHAR(50) NOT NULL
);

CREATE TABLE alunos_turmas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_aluno INT NOT NULL,
    nome_aluno VARCHAR(255) NOT NULL,
    id_turma INT NOT NULL
);

CREATE TABLE contratos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_aluno INT NOT NULL,
    texto_contrato TEXT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    parcelado VARCHAR(255),
    status ENUM('Disponível', 'Indisponível', 'Promoção') NOT NULL,
    imagem VARCHAR(255),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
