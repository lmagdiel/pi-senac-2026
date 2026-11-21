-- Padronização do status do chamado (revisão Senac 2026)
-- O valor gravado passa de 'Andamento' para 'Em andamento', correspondendo aos rótulos das telas.
-- Execute em bancos de dados já existentes antes de utilizar a versão atualizada da aplicação.

USE `helpdesk`;

-- Atualiza registros legados com status 'Andamento' para 'Em andamento'
UPDATE chamados
   SET statuschamado = 'Em andamento'
 WHERE statuschamado = 'Andamento';

-- Amplia o tamanho da coluna de senha para 255 caracteres para suportar password_hash (bcrypt)
ALTER TABLE usuarios MODIFY COLUMN senha VARCHAR(255) NOT NULL;

-- Conferência:
-- SELECT statuschamado, COUNT(*) FROM chamados GROUP BY statuschamado;
-- Adiciona a data de abertura em bancos antigos
-- Execute somente se a coluna ainda não existir
ALTER TABLE chamados
ADD COLUMN data_abertura DATETIME NOT NULL
DEFAULT CURRENT_TIMESTAMP;