CREATE TABLE usuario (
    id SERIAL PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil VARCHAR(20) NOT NULL 
);

CREATE TABLE endereco (
    id SERIAL PRIMARY KEY,
    rua VARCHAR(150) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    complemento VARCHAR(100),
    bairro VARCHAR(100) NOT NULL,
    cep VARCHAR(20) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado VARCHAR(2) NOT NULL
);

CREATE TABLE fornecedor (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    telefone VARCHAR(20),
    cnpj VARCHAR(20),
    usuario_id INT NOT NULL,
    endereco_id INT NOT NULL,
    CONSTRAINT fk_forn_usuario FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE,
    CONSTRAINT fk_forn_endereco FOREIGN KEY (endereco_id) REFERENCES endereco(id) ON DELETE RESTRICT
);

CREATE TABLE cliente (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    telefone VARCHAR(20),
    cartao_credito VARCHAR(50),
    usuario_id INT NOT NULL,
    endereco_id INT NOT NULL,
    CONSTRAINT fk_cli_usuario FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE,
    CONSTRAINT fk_cli_endereco FOREIGN KEY (endereco_id) REFERENCES endereco(id) ON DELETE RESTRICT
);

CREATE TABLE produto (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    foto TEXT,
    fornecedor_id INT NOT NULL,
    CONSTRAINT fk_prod_fornecedor FOREIGN KEY (fornecedor_id) REFERENCES fornecedor(id) ON DELETE CASCADE
);

CREATE TABLE estoque (
    id SERIAL PRIMARY KEY,
    quantidade INT NOT NULL DEFAULT 0,
    preco NUMERIC(10, 2) NOT NULL,
    preco_custo NUMERIC(10, 2),
    lote VARCHAR(50),
    produto_id INT NOT NULL,
    CONSTRAINT fk_est_produto FOREIGN KEY (produto_id) REFERENCES produto(id) ON DELETE CASCADE
);

CREATE TABLE pedido (
    id SERIAL PRIMARY KEY,
    cliente_id INT NOT NULL,
    data_pedido TIMESTAMP NOT NULL DEFAULT NOW(),
    status VARCHAR(30) NOT NULL DEFAULT 'PENDENTE',
    data_envio TIMESTAMP,
    data_cancelamento TIMESTAMP,
    valor_total NUMERIC(10, 2) NOT NULL DEFAULT 0,
    CONSTRAINT fk_ped_cliente FOREIGN KEY (cliente_id) REFERENCES cliente(id) ON DELETE RESTRICT
);

CREATE TABLE item_pedido (
    id SERIAL PRIMARY KEY,
    pedido_id INT NOT NULL,
    estoque_id INT NOT NULL,
    quantidade INT NOT NULL,
    valor_unitario NUMERIC(10, 2) NOT NULL,
    CONSTRAINT fk_item_pedido FOREIGN KEY (pedido_id) REFERENCES pedido(id) ON DELETE CASCADE,
    CONSTRAINT fk_item_estoque FOREIGN KEY (estoque_id) REFERENCES estoque(id) ON DELETE RESTRICT
);