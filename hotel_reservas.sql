-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29/10/2025 às 03:31
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `hotel_reservas`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `quartos`
--

CREATE TABLE `quartos` (
  `id` int(11) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `quartos`
--

INSERT INTO `quartos` (`id`, `numero`, `tipo`, `preco`, `descricao`, `ativo`, `created_at`) VALUES
(1, '101', 'Standard', 150.00, 'Quarto confortável com cama de casal, ar condicionado e TV.', 1, '2025-10-10 00:19:56'),
(3, '301', 'Suite', 350.00, 'Cobertura com sala de estar e vista panorâmica.', 1, '2025-10-10 00:19:56'),
(23, '999', 'Luxo', 99999999.99, 'Quarto mais caro', 0, '2025-10-14 01:40:35'),
(28, '202', 'Luxo', 599.00, 'Quarto de Pura Riquesa.', 1, '2025-10-24 19:41:07');

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `quarto_id` int(11) NOT NULL,
  `nome_cliente` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `cpf` varchar(15) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `data_checkin` date NOT NULL,
  `data_checkout` date NOT NULL,
  `status` enum('confirmada','cancelada') DEFAULT 'confirmada',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `reservas`
--

INSERT INTO `reservas` (`id`, `quarto_id`, `nome_cliente`, `email`, `cpf`, `telefone`, `data_checkin`, `data_checkout`, `status`, `created_at`) VALUES
(3, 3, 'Adrian Hermes De Souza', 'adrianhermes11@gmail.com', '133.139.849-59', '(48) 99175-7001', '2026-10-20', '2026-10-30', 'cancelada', '2025-10-15 01:58:03'),
(4, 1, 'Adrian Hermes De Souza', 'adrianhermes11@gmail.com', '133.139.849-59', '(48) 99175-7001', '2005-09-28', '2006-09-28', 'confirmada', '2025-10-16 01:03:38'),
(5, 3, 'Adrian Hermes', 'adrianhermes11@gmail.com', '133.139.849-59', '(48) 99175-7001', '2025-10-23', '2025-10-29', 'confirmada', '2025-10-24 19:37:50'),
(6, 28, 'Adrian Hermes De Souza', 'adrianhermes11@gmail.com', '133.139.849-59', '(48) 99175-7001', '2025-10-22', '2025-10-30', 'confirmada', '2025-10-25 15:34:58'),
(8, 1, 'Nilceia Hermes', 'adrianhermes11@gmail.com', '133.139.849-59', '(48) 99175-7001', '2025-10-26', '2025-11-02', 'cancelada', '2025-10-27 00:34:12'),
(9, 28, 'Adrian Hermes De Souza', 'adrianhermes11@gmail.com', '133.139.849-59', '(48) 99175-7001', '2025-10-01', '2025-10-02', 'confirmada', '2025-10-27 00:35:39'),
(10, 3, 'Adrian Hermes De Souza', 'adrianhermes11@gmail.com', '133.139.849-59', '(48) 99175-7001', '2025-11-27', '2025-12-02', 'cancelada', '2025-10-27 00:36:30'),
(11, 3, 'Adrian Hermes De Souza', 'adrianhermes11@gmail.com', '133.139.849-59', '(48) 99175-7001', '2025-08-20', '2025-08-26', 'cancelada', '2025-10-27 00:44:27'),
(12, 1, 'Lead Saiba Mais - Quarto 101', 'lead+101@hotelteste.com', '000.000.000-00', '(00) 00000-0000', '2025-11-04', '2025-11-06', 'confirmada', '2025-10-28 00:42:24'),
(13, 3, 'Lead Saiba Mais - Quarto 301', 'lead+301@hotelteste.com', '000.000.000-00', '(00) 00000-0000', '2025-11-04', '2025-11-06', 'confirmada', '2025-10-28 00:49:55'),
(14, 28, 'Lead Saiba Mais - Quarto 202', 'lead+202@hotelteste.com', '000.000.000-00', '(00) 00000-0000', '2025-11-04', '2025-11-06', 'confirmada', '2025-10-28 00:50:00'),
(15, 1, 'Adrian Hermes De Souza', 'adrianhermes11@gmail.com', '133.139.849-59', '(48) 99175-7001', '2025-12-30', '2026-01-01', 'confirmada', '2025-10-28 01:02:08'),
(16, 1, 'Lead Saiba Mais - Quarto 101', 'lead+101@hotelteste.com', '000.000.000-00', '(00) 00000-0000', '2025-11-07', '2025-11-09', 'confirmada', '2025-10-28 15:23:39'),
(17, 3, 'Lead Saiba Mais - Quarto 301', 'lead+301@hotelteste.com', '000.000.000-00', '(00) 00000-0000', '2025-11-07', '2025-11-09', 'confirmada', '2025-10-28 16:59:37'),
(18, 28, 'Lead Saiba Mais - Quarto 202', 'lead+202@hotelteste.com', '000.000.000-00', '(00) 00000-0000', '2025-11-07', '2025-11-09', 'confirmada', '2025-10-28 16:59:44');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios_admin`
--

CREATE TABLE `usuarios_admin` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios_admin`
--

INSERT INTO `usuarios_admin` (`id`, `nome`, `email`, `senha_hash`, `criado_em`) VALUES
(5, 'Super Admin', 'admin@essentia.com', '$2y$10$tZzoeo9JiwtaXTkvuTm0HOIbGKNM/CEwRWvlkL.I5z4zPq7q4mpiu', '2025-10-27 22:20:05');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `quartos`
--
ALTER TABLE `quartos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`);

--
-- Índices de tabela `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quarto_id` (`quarto_id`);

--
-- Índices de tabela `usuarios_admin`
--
ALTER TABLE `usuarios_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `quartos`
--
ALTER TABLE `quartos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `usuarios_admin`
--
ALTER TABLE `usuarios_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`quarto_id`) REFERENCES `quartos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
