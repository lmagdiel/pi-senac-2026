-- Script de criação e configuração do banco de dados helpdesk
-- Projeto Integrador Senac 2026

CREATE DATABASE IF NOT EXISTS `helpdesk`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `helpdesk`;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(120) NOT NULL,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `senha` VARCHAR(255) NOT NULL,
  `perfil` VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de chamados (já contemplando status padronizado 'Em andamento')
CREATE TABLE IF NOT EXISTS `chamados` (
  `id_chamado` INT AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(90) NOT NULL,
  `categoria` VARCHAR(90) NOT NULL,
  `descricao` LONGTEXT NOT NULL,
  `descricaotecnico` LONGTEXT NULL,
  `statuschamado` VARCHAR(50) NOT NULL DEFAULT 'Aberto',
  `valor` DOUBLE NULL DEFAULT 0,
  `id_usuario` INT NOT NULL,
  CONSTRAINT `fkChamadoUsuario` FOREIGN KEY (`id_usuario`)
    REFERENCES `usuarios` (`id_usuario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuário Administrador padrão para acesso inicial (senha: 123)
-- A aplicação valida senhas com password_hash / password_verify e faz upgrade automático de MD5 legado
INSERT INTO `usuarios` (`nome`, `email`, `senha`, `perfil`)
VALUES ('Marcos', 'marcos@senacsp.edu.br', '202cb962ac59075b964b07152d234b70', 'Adm')
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);
