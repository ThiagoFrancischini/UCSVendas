# Testes UCS Vendas

## Executando os Testes

```bash
# Teste de conexão
php tests/test_conn.php

# Teste de cadastro de fornecedor
php tests/test_cadastro.php

# Teste de cadastro de cliente
php tests/test_cadastro_cliente.php
```

## Estrutura dos Testes

| Arquivo | Descrição |
|---------|-----------|
| test_conexao.php | Verifica conexão com PostgreSQL |
| test_cadastro.php | Teste de fornecedor (original) |
| test_cadastro_cliente.php | Teste de cadastro de cliente |
| test_cadastro_fornecedor.php | Teste de fornecedor (redundante) |

## Observações

- Os testes limpam automaticamente o banco em caso de erro usando transação com rollBack
- Cada teste usa um email diferente para evitar conflitos de chave única