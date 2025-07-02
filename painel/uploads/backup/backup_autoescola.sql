CREATE TABLE `alunos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `rg` varchar(20) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `data_nascimento` date NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `renach` varchar(20) DEFAULT NULL,
  `ladv` date DEFAULT NULL,
  `vencimento_processo` date NOT NULL,
  `rua` varchar(255) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `documento` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `alunos` VALUES ('11', '11', 'DJAIR ALMEIDA', 'DJAIR ALMEIDA', '3444456778', '3444456778', '45679807322', '45679807322', '2024-10-15', '2024-10-15', '92991515710', '92991515710', 'AM12345', 'AM12345', '2024-10-07', '2024-10-07', '2024-10-07', '2024-10-07', 'KJJJ', 'KJJJ', 'CHAGAS', 'CHAGAS', '44444', '44444', '', '', '', '', '', '');
INSERT INTO `alunos` VALUES ('15', '15', 'LUCAS DE SOUZA CORREA', 'LUCAS DE SOUZA CORREA', '586365', '586365', '067.368.982-42', '067.368.982-42', '2024-12-12', '2024-12-12', '9291515710', '9291515710', 'AM123452578', 'AM123452578', '', '', '2024-12-12', '2024-12-12', 'CAPITÃO PESSOA', 'CAPITÃO PESSOA', 'CHAGAS AGUIAR', 'CHAGAS AGUIAR', '717', '717', '', '', '', '', 'uploads/6759f68ce19ec.jpeg', 'uploads/6759f68ce19ec.jpeg');
INSERT INTO `alunos` VALUES ('16', '16', 'RAILSON FREITAS DOS SANTOS', 'RAILSON FREITAS DOS SANTOS', '1234567-9', '1234567-9', '067.368.222-77', '067.368.222-77', '2005-12-12', '2005-12-12', '9291515710', '9291515710', 'AM446082', 'AM446082', '', '', '2025-12-11', '2025-12-11', 'TESTE', 'TESTE', 'GRANDE VITORIA', 'GRANDE VITORIA', '105', '105', 'LUCAS@GMAIL.COM', 'LUCAS@GMAIL.COM', '', '', 'uploads/675a546e7a9d4.jpeg', 'uploads/675a546e7a9d4.jpeg');


CREATE TABLE `alunos_turmas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_aluno` int(11) NOT NULL,
  `nome_aluno` varchar(255) NOT NULL,
  `id_turma` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `alunos_turmas` VALUES ('4', '4', '15', '15', 'LUCAS DE SOUZA CORREA', 'LUCAS DE SOUZA CORREA', '5', '5');
INSERT INTO `alunos_turmas` VALUES ('6', '6', '11', '11', 'DJAIR ALMEIDA', 'DJAIR ALMEIDA', '5', '5');
INSERT INTO `alunos_turmas` VALUES ('12', '12', '15', '15', 'LUCAS DE SOUZA CORREA', 'LUCAS DE SOUZA CORREA', '7', '7');
INSERT INTO `alunos_turmas` VALUES ('13', '13', '11', '11', 'DJAIR ALMEIDA', 'DJAIR ALMEIDA', '7', '7');


CREATE TABLE `associacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_associacao` varchar(255) NOT NULL,
  `sobre_associacao` text NOT NULL,
  `logo_image` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `associacoes` VALUES ('1', '1', 'AUTOESCOLA DINÂMICA', 'AUTOESCOLA DINÂMICA', 'TESTE TESTE', 'TESTE TESTE', 'logo_6759f37badd055.78812212.png', 'logo_6759f37badd055.78812212.png');


CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `parcelado` varchar(255) DEFAULT NULL,
  `status` enum('Disponível','Indisponível','Promoção') NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categorias` VALUES ('12', '12', 'Categoria A', 'Categoria A', '780.00', '780.00', '880,00', '880,00', 'Promoção', 'Promoção', '../uploads/car-3.png', '../uploads/car-3.png', '2024-12-10 16:10:23', '2024-12-10 16:10:23');


CREATE TABLE `contratos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_aluno` int(11) NOT NULL,
  `texto_contrato` text NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `contratos` VALUES ('1', '1', '15', '15', 'As Partes têm entre si, justo e acertado, este Contrato, que será regulado pelas cláusulas e condições abaixo estabelecidas:\n        1. O prazo da prestação de serviço é de 1(um) ano, a contar da data de contratação do mesmo junto ao DETRAN-AM: cumprindo os prazos e procedimentos pelo CTB e resoluções pertinentes prestação desse serviço tem caráter individual, podendo ser transferida a sua titularidade, desde que seja por escrito e pessoalmente em um dos escritórios da contratada, antes do início do processo sendo vedada a transferência após o início do processo.\n        3. Este serviço poderá ser rescindido a qualquer momento por falta de pagamento das parcelas, descumprimento de alguma cláusula, ou por motivo de força maior por qualquer das partes, devendo ser comunicado por escrito.\n        4. O curso de Legislação começará sempre nos seguintes horários: segunda à sexta NOTURNO (de 18:00 às 21:00).\n        Quando dada a aptidão do exame psicotécnico, o candidato deverá apresentar o protocolo no CFC, prazo imediato para afirmação da Legislação, caso contrário, ocorrerá a pedia da vaga na turma de Legislação\n        PARAGRAFO 1\".: Quando do agendamento das aulas práticas, o candidato que necessitar faltar ou se atrasar a sua aula, deverá informar ao CFC ou ao instrutor. com 3 horas de antecedência\n        PARAGRAFO 2\" .: Para realização das aulas de direção o candidato deverá estar munido de: RG e LADV, não podendo fazer aula trajando bermuda, camisa sem manga, saia curta e calçados que não se fixam os pés.\n        PARAGRAFO 3\".: De acordo com a ma. N.º 778/19 do CONTRAN, o candidato que iniciar processo de 1\" Habilitação, inclusão ou troca de categoria a partir de 10/09/19. deverá cumprir obrigatoriamente 1 hora de sua carga horária pratica no turno Noturno.\n        6. Os pagamentos efetuados antes do vencimento não terão seus valores alterados\n        7. Todo pagamento referente a aulas extras, de reposição, reteste e aluguel de veículos para Auto Escola ou no dia da prova para a secretaria do CFC; sendo extremamente proibido fazer qualquer tipo de pagamento para o Instrutor.\n        0,2% ao dia. 8. Sobre os pagamentos efetuados após o vencimento das parcelas, devedo incidir multa pecuniária de 2%, juros mora de 0,2% ao dia\n        * Em caso de reprovação, o candidato que desejar utilizar veículo do CFC para reteste deverá agendar com antecedência a data do exame e pagar o valor de: cat. À R$ 55,00, cat. B R$ 90,00, cat. D R$ 150,00, referente ao aluguel do mesmo.\n        * De acordo com a resolução 285/08 do CONTRAN é considerado hora/aula o intervalo de 50 min.', 'As Partes têm entre si, justo e acertado, este Contrato, que será regulado pelas cláusulas e condições abaixo estabelecidas:\n        1. O prazo da prestação de serviço é de 1(um) ano, a contar da data de contratação do mesmo junto ao DETRAN-AM: cumprindo os prazos e procedimentos pelo CTB e resoluções pertinentes prestação desse serviço tem caráter individual, podendo ser transferida a sua titularidade, desde que seja por escrito e pessoalmente em um dos escritórios da contratada, antes do início do processo sendo vedada a transferência após o início do processo.\n        3. Este serviço poderá ser rescindido a qualquer momento por falta de pagamento das parcelas, descumprimento de alguma cláusula, ou por motivo de força maior por qualquer das partes, devendo ser comunicado por escrito.\n        4. O curso de Legislação começará sempre nos seguintes horários: segunda à sexta NOTURNO (de 18:00 às 21:00).\n        Quando dada a aptidão do exame psicotécnico, o candidato deverá apresentar o protocolo no CFC, prazo imediato para afirmação da Legislação, caso contrário, ocorrerá a pedia da vaga na turma de Legislação\n        PARAGRAFO 1\".: Quando do agendamento das aulas práticas, o candidato que necessitar faltar ou se atrasar a sua aula, deverá informar ao CFC ou ao instrutor. com 3 horas de antecedência\n        PARAGRAFO 2\" .: Para realização das aulas de direção o candidato deverá estar munido de: RG e LADV, não podendo fazer aula trajando bermuda, camisa sem manga, saia curta e calçados que não se fixam os pés.\n        PARAGRAFO 3\".: De acordo com a ma. N.º 778/19 do CONTRAN, o candidato que iniciar processo de 1\" Habilitação, inclusão ou troca de categoria a partir de 10/09/19. deverá cumprir obrigatoriamente 1 hora de sua carga horária pratica no turno Noturno.\n        6. Os pagamentos efetuados antes do vencimento não terão seus valores alterados\n        7. Todo pagamento referente a aulas extras, de reposição, reteste e aluguel de veículos para Auto Escola ou no dia da prova para a secretaria do CFC; sendo extremamente proibido fazer qualquer tipo de pagamento para o Instrutor.\n        0,2% ao dia. 8. Sobre os pagamentos efetuados após o vencimento das parcelas, devedo incidir multa pecuniária de 2%, juros mora de 0,2% ao dia\n        * Em caso de reprovação, o candidato que desejar utilizar veículo do CFC para reteste deverá agendar com antecedência a data do exame e pagar o valor de: cat. À R$ 55,00, cat. B R$ 90,00, cat. D R$ 150,00, referente ao aluguel do mesmo.\n        * De acordo com a resolução 285/08 do CONTRAN é considerado hora/aula o intervalo de 50 min.', '2024-12-11 15:59:06', '2024-12-11 15:59:06');


CREATE TABLE `enderecos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cep` varchar(10) DEFAULT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `cnpj` varchar(100) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `enderecos` VALUES ('1', '1', '69460-000', '69460-000', '9291515710', '9291515710', 'Capitão pessoa', 'Capitão pessoa', 'Chagas Aguiar', 'Chagas Aguiar', '17', '17', 'COARI', 'COARI', 'CASA', 'CASA', '04.555.518/0001-42', '04.555.518/0001-42', 'AM', 'AM');


CREATE TABLE `feriados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` date NOT NULL,
  `descricao` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `feriados` VALUES ('3', '3', '2024-12-11', '2024-12-11', 'teste 1', 'teste 1');


CREATE TABLE `fichas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `ladv` date NOT NULL,
  `vencimento_processo` date NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `instrutor` varchar(255) NOT NULL,
  `placa` varchar(20) NOT NULL,
  `registro` varchar(20) DEFAULT NULL,
  `horario_inicio` time DEFAULT NULL,
  `horario_fim` time DEFAULT NULL,
  `data_ficha` date NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ficha` (`instrutor`,`placa`,`horario_inicio`,`horario_fim`,`data_ficha`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `fichas` VALUES ('7', '7', 'LUCAS DE SOUZA CORREA', 'LUCAS DE SOUZA CORREA', '586365', '586365', '067.368.982-42', '067.368.982-42', '1970-01-01', '1970-01-01', '2024-12-12', '2024-12-12', 'B', 'B', 'RAILSON FREITAS DOS SANTOS', 'RAILSON FREITAS DOS SANTOS', 'ABC1D34', 'ABC1D34', '1234567890', '1234567890', '08:00:00', '08:00:00', '10:00:00', '10:00:00', '2024-12-11', '2024-12-11', 'Finalizado', 'Finalizado');
INSERT INTO `fichas` VALUES ('8', '8', 'LUCAS DE SOUZA CORREA', 'LUCAS DE SOUZA CORREA', '586365', '586365', '067.368.982-42', '067.368.982-42', '1970-01-01', '1970-01-01', '2024-12-12', '2024-12-12', 'A', 'A', 'RAILSON FREITAS DOS SANTOS', 'RAILSON FREITAS DOS SANTOS', 'ABC1D34', 'ABC1D34', '1234567890', '1234567890', '08:00:00', '08:00:00', '10:00:00', '10:00:00', '2024-12-12', '2024-12-12', 'Finalizado', 'Finalizado');
INSERT INTO `fichas` VALUES ('9', '9', 'LUCAS DE SOUZA CORREA', 'LUCAS DE SOUZA CORREA', '586365', '586365', '067.368.982-42', '067.368.982-42', '1970-01-01', '1970-01-01', '2024-12-12', '2024-12-12', 'A', 'A', 'RAILSON FREITAS DOS SANTOS', 'RAILSON FREITAS DOS SANTOS', 'ABC1D34', 'ABC1D34', '1234567890', '1234567890', '08:00:00', '08:00:00', '10:00:00', '10:00:00', '2024-12-13', '2024-12-13', 'Finalizado', 'Finalizado');
INSERT INTO `fichas` VALUES ('11', '11', 'LUCAS DE SOUZA CORREA', 'LUCAS DE SOUZA CORREA', '586365', '586365', '067.368.982-42', '067.368.982-42', '1970-01-01', '1970-01-01', '2024-12-12', '2024-12-12', 'A', 'A', 'RAILSON FREITAS DOS SANTOS', 'RAILSON FREITAS DOS SANTOS', 'ABC1D34', 'ABC1D34', '1234567890', '1234567890', '08:00:00', '08:00:00', '10:00:00', '10:00:00', '2024-12-16', '2024-12-16', 'Finalizado', 'Finalizado');


CREATE TABLE `formas_pagamento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forma` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `formas_pagamento` VALUES ('15', '15', 'Pix', 'Pix');
INSERT INTO `formas_pagamento` VALUES ('16', '16', 'Dinheiro', 'Dinheiro');
INSERT INTO `formas_pagamento` VALUES ('17', '17', 'Boleto', 'Boleto');
INSERT INTO `formas_pagamento` VALUES ('18', '18', 'Carnê', 'Carnê');
INSERT INTO `formas_pagamento` VALUES ('19', '19', 'Cartão de Crédito', 'Cartão de Crédito');
INSERT INTO `formas_pagamento` VALUES ('20', '20', 'Cartão de Débito', 'Cartão de Débito');


CREATE TABLE `horarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dia_inicio` int(11) NOT NULL,
  `dia_fim` int(11) NOT NULL,
  `hora_inicio_1` time NOT NULL,
  `hora_fim_1` time NOT NULL,
  `hora_inicio_2` time DEFAULT NULL,
  `hora_fim_2` time DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `horarios` VALUES ('1', '1', '1', '1', '5', '5', '08:00:00', '08:00:00', '12:00:00', '12:00:00', '14:30:00', '14:30:00', '18:00:00', '18:00:00');


CREATE TABLE `info_parcelas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_aluno` varchar(255) NOT NULL,
  `servico` varchar(100) NOT NULL,
  `forma_pagamento` varchar(50) NOT NULL,
  `valor_entrada` decimal(10,2) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `numero_parcelas` int(11) DEFAULT 1,
  `data_pagamento` date DEFAULT NULL,
  `pago` varchar(10) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `excluido` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `instrutores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_instrutor` varchar(255) NOT NULL,
  `placa_instrutor` varchar(20) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `instrutores` VALUES ('1', '1', 'RAILSON FREITAS DOS SANTOS', 'RAILSON FREITAS DOS SANTOS', 'ABC1D34', 'ABC1D34', '2024-10-07 16:51:14', '2024-10-07 16:51:14');


CREATE TABLE `login_aluno` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_aluno` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `cpf_aluno` varchar(14) NOT NULL,
  `reset_token` int(11) DEFAULT NULL,
  `reset_token_validade` datetime DEFAULT NULL,
  `status_cadastro` varchar(20) DEFAULT 'Pendente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `login_aluno` VALUES ('40', '40', 'LUCAS DE SOUZA CORREA', 'LUCAS DE SOUZA CORREA', 'lucas@gmail.com', 'lucas@gmail.com', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '067.368.982-42', '067.368.982-42', '', '', '', '', 'Pendente', 'Pendente');
INSERT INTO `login_aluno` VALUES ('42', '42', 'RAILSON FREITAS DOS SANTOS', 'RAILSON FREITAS DOS SANTOS', 'railsonfreitas698@gmail.com', 'railsonfreitas698@gmail.com', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '067.368.222-77', '067.368.222-77', '', '', '', '', 'Cadastrado', 'Cadastrado');


CREATE TABLE `materiais_estudo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo_material` varchar(255) NOT NULL,
  `nome_capa` varchar(255) NOT NULL,
  `nome_material` varchar(255) NOT NULL,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `materiais_estudo` VALUES ('3', '3', 'Apostila dinâmica', 'Apostila dinâmica', 'seo-agency-website-template.jpg', 'seo-agency-website-template.jpg', 'material_1734006096.docx', 'material_1734006096.docx', '2024-12-12 08:21:36', '2024-12-12 08:21:36');


CREATE TABLE `placas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `placa` varchar(20) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `placas` VALUES ('1', '1', 'ABC1D34', 'ABC1D34', '2024-10-07 16:51:14', '2024-10-07 16:51:14');
INSERT INTO `placas` VALUES ('2', '2', 'ABC1D34', 'ABC1D34', '2024-12-11 23:49:30', '2024-12-11 23:49:30');


CREATE TABLE `relatorios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_aluno` varchar(255) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `data_preco` date NOT NULL,
  `categoria_preco` varchar(70) DEFAULT NULL,
  `valor_saida` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `relatorios` VALUES ('1', '1', 'LUCAS DE SOUZA CORREA', 'LUCAS DE SOUZA CORREA', '500.00', '500.00', '2024-12-11', '2024-12-11', 'AB', 'AB', '100.00', '100.00');


CREATE TABLE `servicos_aluno` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_aluno` varchar(255) NOT NULL,
  `servico` varchar(100) NOT NULL,
  `forma_pagamento` varchar(50) NOT NULL,
  `valor_entrada` varchar(50) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `numero_parcelas` int(11) DEFAULT 1,
  `data_pagamento` date DEFAULT NULL,
  `categoria` varchar(50) NOT NULL,
  `pago` varchar(3) NOT NULL,
  `status` enum('ativo','finalizado') DEFAULT 'ativo',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome_aluno` (`nome_aluno`,`servico`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `servicos_aluno` VALUES ('1', '1', 'LUCAS DE SOUZA CORREA', 'LUCAS DE SOUZA CORREA', 'PRIMEIRA HABILITAÇÃO ', 'PRIMEIRA HABILITAÇÃO ', 'Parcelado', 'Parcelado', '500', '500', '1500.00', '1500.00', '2', '2', '2025-01-11', '2025-01-11', 'AB', 'AB', 'SIM', 'SIM', 'ativo', 'ativo', '2024-12-11 17:43:35', '2024-12-11 17:43:35');


CREATE TABLE `tabela_saida` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) NOT NULL,
  `valor_saida` decimal(10,2) NOT NULL,
  `data_saida` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tabela_saida` VALUES ('1', '1', 'pagamento dos veiculos', 'pagamento dos veiculos', '300.00', '300.00', '2024-12-12', '2024-12-12', '2024-12-12 00:07:52', '2024-12-12 00:07:52');
INSERT INTO `tabela_saida` VALUES ('2', '2', 'pagamento da Luz', 'pagamento da Luz', '100.00', '100.00', '2024-12-12', '2024-12-12', '2024-12-12 00:08:48', '2024-12-12 00:08:48');


CREATE TABLE `turmas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `local` varchar(255) NOT NULL,
  `instrutor` varchar(255) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `horario_inicio` time NOT NULL,
  `horario_fim` time NOT NULL,
  `turno` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `turmas` VALUES ('5', '5', 'DETRAC ', 'DETRAC ', 'RAILSON FREITAS DOS SANTOS', 'RAILSON FREITAS DOS SANTOS', '2024-12-05', '2024-12-05', '2024-12-13', '2024-12-13', '12:51:00', '12:51:00', '13:53:00', '13:53:00', 'Vespertino', 'Vespertino');
INSERT INTO `turmas` VALUES ('7', '7', 'AUTO ESCOLA DINÂMICA', 'AUTO ESCOLA DINÂMICA', 'RAILSON FREITAS DOS SANTOS', 'RAILSON FREITAS DOS SANTOS', '2024-12-11', '2024-12-11', '2024-12-11', '2024-12-11', '13:55:00', '13:55:00', '16:59:00', '16:59:00', 'Vespertino', 'Vespertino');


CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `senha_hash` varchar(255) DEFAULT NULL,
  `nivel` enum('admin','presidente','suporte') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuarios` VALUES ('1', '1', 'Administrador', 'Administrador', 'admin@example.com', 'admin@example.com', 'af0ce0b798ec0ab103cd25d3a34859dff31684a8f2bde53af6d48136a0912962', 'af0ce0b798ec0ab103cd25d3a34859dff31684a8f2bde53af6d48136a0912962', 'admin', 'admin');
INSERT INTO `usuarios` VALUES ('2', '2', 'Presidente', 'Presidente', 'presidente@example.com', 'presidente@example.com', '746c468da84b7ec5fde2d19ef72b6822924d4988f371860bcc77b45402fecf35', '746c468da84b7ec5fde2d19ef72b6822924d4988f371860bcc77b45402fecf35', 'presidente', 'presidente');
INSERT INTO `usuarios` VALUES ('3', '3', 'Suporte', 'Suporte', 'suporte@example.com', 'suporte@example.com', 'c50cec879334e667a30b063d6f520f8ef5af38cb1fa6274b41492b08b58d0ef6', 'c50cec879334e667a30b063d6f520f8ef5af38cb1fa6274b41492b08b58d0ef6', 'suporte', 'suporte');


