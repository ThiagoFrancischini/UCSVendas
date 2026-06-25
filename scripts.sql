-- ============================================================
-- UCS Vendas — Script de criação do banco de dados
-- ============================================================

CREATE TABLE usuario (
    id SERIAL PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil VARCHAR(20) NOT NULL
        CHECK (perfil IN ('ADMIN', 'CLIENTE', 'FORNECEDOR'))
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
    CONSTRAINT fk_forn_usuario  FOREIGN KEY (usuario_id)  REFERENCES usuario(id)  ON DELETE CASCADE,
    CONSTRAINT fk_forn_endereco FOREIGN KEY (endereco_id) REFERENCES endereco(id) ON DELETE RESTRICT
);

CREATE TABLE cliente (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    telefone VARCHAR(20),
    cartao_credito VARCHAR(50),
    usuario_id INT NOT NULL,
    endereco_id INT NOT NULL,
    CONSTRAINT fk_cli_usuario  FOREIGN KEY (usuario_id)  REFERENCES usuario(id)  ON DELETE CASCADE,
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

-- Sem UNIQUE em produto_id: um produto pode ter múltiplos lotes de estoque
-- com preços distintos. O sistema exibe e vende pelo lote mais caro primeiro.
CREATE TABLE estoque (
    id SERIAL PRIMARY KEY,
    quantidade INT NOT NULL DEFAULT 0,
    preco NUMERIC(10, 2) NOT NULL,
    preco_custo NUMERIC(10, 2),
    lote VARCHAR(50),
    produto_id INT NOT NULL,
    CONSTRAINT fk_est_produto FOREIGN KEY (produto_id) REFERENCES produto(id) ON DELETE CASCADE
);

-- status válidos:
--   AGUARDANDO_PAGAMENTO  → pedido criado, aguarda confirmação de pagamento
--   PENDENTE              → pagamento confirmado, aguarda envio pelo fornecedor
--   ENVIADO               → fornecedor marcou como enviado
--   CANCELADO             → pedido cancelado
CREATE TABLE pedido (
    id SERIAL PRIMARY KEY,
    cliente_id INT NOT NULL,
    data_pedido TIMESTAMP NOT NULL DEFAULT NOW(),
    status VARCHAR(30) NOT NULL DEFAULT 'AGUARDANDO_PAGAMENTO'
        CHECK (status IN ('AGUARDANDO_PAGAMENTO', 'PENDENTE', 'ENVIADO', 'CANCELADO')),
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
    CONSTRAINT fk_item_pedido  FOREIGN KEY (pedido_id)  REFERENCES pedido(id)  ON DELETE CASCADE,
    CONSTRAINT fk_item_estoque FOREIGN KEY (estoque_id) REFERENCES estoque(id) ON DELETE RESTRICT
);

-- ============================================================
-- Usuário administrador padrão
-- Senha: admin123  (hash bcrypt)
-- Troque a senha após o primeiro acesso.
-- ============================================================
INSERT INTO usuario (email, senha, perfil)
VALUES (
    'admin@ucsvendas.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uSc5yBHi.',  -- admin123
    'ADMIN'
);
