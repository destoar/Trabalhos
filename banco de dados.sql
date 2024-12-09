use angus_house;
 
SHOW TABLES;
desc clientes;
SELECT COUNT(*) FROM clientes;

SELECT * FROM clientes;

GRANT ALL PRIVILEGES ON angus_house.* TO 'root'@'localhost';
FLUSH PRIVILEGES;

-- Inserir dados fictícios na tabela de clientes
INSERT INTO clientes (nome, email, senha, logradouro, numero, bairro, cidade, estado) 
VALUES 
('João Silva', 'joao@example.com', 'senha123', 'Rua A', '123', 'Centro', 'Rio de Janeiro', 'RJ'),
('Maria Oliveira', 'maria@example.com', 'senha456', 'Rua B', '456', 'Bangu', 'São Paulo', 'SP');

