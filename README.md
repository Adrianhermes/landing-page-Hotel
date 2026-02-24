# 🏨 Hotel Essentia – Gestão de Reservas

Aplicação web completa em PHP 8 para gestão de reservas de um hotel, integrando um site institucional com captura de reservas e um painel administrativo seguro e responsivo.
A arquitetura adota um padrão MVC, com separação clara de responsabilidades e forte validação de dados tanto no front quanto no back-end.

## Visão Geral

O sistema possibilita:

1. Exibição de catálogo de acomodações e reserva online;
2. Controle de disponibilidade e cadastro de quartos;
3. Gestão de usuários administrativos e permissões;
4. Painel interno com filtros, estatísticas e operações CRUD completas.

## Principais Módulos

🏠 Site Público:
- Landing page com catálogo de quartos e formulário de reserva simplificado.
- Validação client-side de datas, CPF e telefone com feedback em tempo real.
- Integração completa com backend, salvando a reserva no banco.

🔐 Painel Administrativo
- Login com autenticação de sessão e proteção de rotas.
- Dashboard com estatísticas e atalhos para gestão de reservas, quartos e usuários.
- CRUD completo para cada entidade, com filtros dinâmicos e validação server-side.
- Controle de ações administrativas (não é possível excluir o usuário logado).
- Ambiente atual de desenvolvimento com acesso padrão para facilitar testes locais.

⚙️ Serviços de Suporte
- Conexão centralizada com MySQL via PDO, com tratamento de exceções.
- API interna para consulta de disponibilidade e criação de reservas rápidas (“leads”).

## Fluxos Principais

1️⃣ Reserva Pública
1. Usuário seleciona quarto e datas.
2. Sistema valida disponibilidade e grava a reserva.
3. Cliente é redirecionado para uma tela de sucesso com instruções.

2️⃣ Gestão Interna
1. Administrador realiza login.
2. Painel exibe resumo geral e atalhos.
3. CRUD completo de reservas, quartos e usuários.
4. Ações protegidas e validadas server-side.


## 🗄️ Banco de Dados

usuarios_admin
| Campo      | Tipo            | Descrição           |
| ---------- | --------------- | ------------------- |
| id         | int (PK)        | Identificador       |
| nome       | varchar         | Nome do usuário     |
| email      | varchar (único) | Login               |
| senha_hash | varchar         | Senha criptografada |
| criado_em  | datetime        | Data de criação     |

quartos
| Campo     | Tipo     | Descrição             |
| --------- | -------- | --------------------- |
| id        | int (PK) | Identificador         |
| numero    | int      | Número do quarto      |
| tipo      | varchar  | Categoria             |
| preco     | decimal  | Valor da diária       |
| descricao | text     | Informações do quarto |
| ativo     | tinyint  | Status de exibição    |

reservas
| Campo                        | Tipo     | Descrição              |
| ---------------------------- | -------- | ---------------------- |
| id                           | int (PK) | Identificador          |
| quarto_id                    | int (FK) | Quarto reservado       |
| nome_cliente                 | varchar  | Nome do hóspede        |
| email                        | varchar  | Contato                |
| cpf                          | varchar  | Documento              |
| telefone                     | varchar  | Telefone               |
| data_checkin / data_checkout | date     | Período da reserva     |
| status                       | enum     | Confirmada / Cancelada |
| created_at                   | datetime | Data da criação        |



## ⚙️ Instalação e Configuração

1. Ambiente:
  - PHP 8+ e MySQL.
  - Servidor local MAMP ou XAMPP.

2. Banco de Dados:
  - Crie o banco `hotel_reservas`.
  - Importe o arquivo `hotel_reservas.sql`.

3. Configurações:
  - Edite `config/conexao.php` com host, banco, usuário e senha do seu ambiente.
  - Exemplo atual do projeto local: `localhost`, `hotel_reservas`, `root`, `root`.

4. Acesso:
  - Front público (MAMP porta padrão): `http://localhost:8888/landing-page-Hotel-principal/index.php`

5. Credenciais e dados iniciais (estado atual):
  - A tela de login já vem preenchida para testes com:
    - Email: `admin@essentia.com`
    - Senha: `admin`
    - 
 A versão estática é de demonstração e não grava dados de reserva.
 https://landing-page-hotel-alpha.vercel.app/

<img width="3024" height="6849" alt="home-hotel" src="https://github.com/user-attachments/assets/76671125-fb29-40ca-858e-0e5746041923" />

