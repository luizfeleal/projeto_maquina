use projeto_maquina;

create table clientes(
	id_cliente int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cliente_nome varchar(100) NOT NULL,
    cliente_celular varchar(100) NOT NULL,
    cliente_email varchar(100) NOT NULL,
    cliente_cpf_cnpj varchar(100) NOT NULL,
    data_criacao  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_alteracao timestamp DEFAULT CURRENT_TIMESTAMP
);

create table usuarios(
	id_usuario int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_grupo_acesso int,
    id_cliente int,
    usuario_nome varchar(100) NOT NULL,
    usuario_email varchar(100) NOT NULL,
   	usuario_ultimo_acesso varchar(100) NOT NULL,
    usuario_login varchar(100) NOT NULL,
    usuario_senha varchar(100) NOT NULL,
    data_inclusao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_alteracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

create table grupos_acesso(
	id_grupo_acesso int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    grupo_acesso_nome varchar(100),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_alteracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

create table acessos_tela(
	id_grupo_acesso int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    acesso_tela_viewname varchar(100),
    acesso_tela_nome varchar(100),
 	data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_alteracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

create table maquinas(
	id_maquina int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    maquina_referencia varchar(100),
    maquina_nome varchar(100),
    maquina_status varchar(50),
    maquina_ultimo_contato timestamp DEFAULT CURRENT_TIMESTAMP,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_alteracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

create table extrato_maquina(
	id_extrato_maquina INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_maquina INT,
    extrato_operacao_tipo varchar(50),
    extrato_operacao_valor float,
    extrato_operacao_status varchar(50),
    extrato_operacao_saldo float,
    extrato_operacao_data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_alteracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

create table extrato_cliente(
	id_extrato_cliente INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT,
    extrato_operacao_tipo varchar(50),
    extrato_operacao_valor float,
    extrato_operacao_status varchar(50),
    extrato_operacao_saldo float,
    extrato_operacao_data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_alteracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

create table logs(
	id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_usuario int,
    descricao varchar(200),
    status varchar(50),
    acao varchar(100),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
