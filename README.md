# 🗓️ API de Agendamento de Serviços

Sistema de agendamento de serviços com arquitetura moderna, baseado em PHP com Laravel e os princípios de DDD + Hexagonal Architecture.

---

## 🚀 Visão Geral

Esta API permite que usuários agendem serviços com prestadores (ex: salões de beleza, técnicos, médicos), oferecendo:

* Estrutura modular e escalável.
* Separação clara entre domínio, aplicação e infraestrutura.
* Suporte a eventos de domínio e filas assíncronas.
* Testes automatizados (unitários e de integração).
* Documentação Swagger (OpenAPI 3.0).

---

## 🧱 Arquitetura

O projeto segue uma **Arquitetura Hexagonal (Ports & Adapters)** com inspiração em DDD.

```
app/
├── Domain/
│   ├── Entities/
│   ├── ValueObjects/
│   ├── Repositories/
│   └── Events/
├── Application/
│   ├── Services/
│   └── DTOs/
├── Infrastructure/
│   ├── Repositories/
│   ├── EventListeners/
│   ├── Providers/
│   └── Persistence/
└── Interfaces/
    ├── Http/Controllers/
    └── Routes/
```

---

## 📦 Tecnologias

* PHP 8.3+
* Laravel 10/11
* MySQL / PostgreSQL
* Redis (cache e filas)
* Docker
* Swagger (OpenAPI)
* PHPUnit / Pest

---

## 🥪 Funcionalidades

* [x] Cadastro de clientes
* [x] Cadastro de prestadores
* [x] Criação de agendamento
* [x] Cancelamento e reagendamento
* [x] Fila de envio de notificações (eventos)
* [x] API Documentada com Swagger
* [ ] Autenticação JWT (em breve)
* [ ] Painel de controle (em breve)

---

## ⚙️ Instalação

```bash
# Clone o projeto
git clone https://github.com/seu-usuario/agenda-api.git
cd agenda-api

# Instale as dependências
composer install

# Configure o .env
cp .env.example .env
php artisan key:generate

# Suba os containers
docker-compose up -d

# Rode as migrations
php artisan migrate

# Opcional: seed inicial
php artisan db:seed
```

---

## 📚 Documentação da API

Após subir a aplicação, acesse:

```
http://localhost:8000/api/documentation
```

(Usando Swagger UI via `L5-Swagger` ou `Swagger-PHP`)

---

## ✅ Testes

```bash
# Unitários e de integração
php artisan test
# ou com Pest
./vendor/bin/pest
```

---

## 📌 Padrões e Boas Práticas

* SOLID principles aplicados no domínio
* Inversão de dependência (Repositórios como interfaces)
* Serviços de aplicação desacoplados de framework
* Eventos de domínio + ouvintes
* Validações com Form Requests + ValueObjects

---

## 🗓️ Planejamento de Iterações

| Iteração | Objetivo                                             |
| -------- | ---------------------------------------------------- |
| 1        | Estrutura do projeto + cadastro de cliente/prestador |
| 2        | Agendamento + Reagendamento                          |
| 3        | Eventos + Fila Redis                                 |
| 4        | Documentação Swagger                                 |
| 5        | Testes unitários e integração                        |
| 6        | Otimizações e deploy                                 |

---

## 🧑‍💻 Autor

Thiago Tavares Mendes
[LinkedIn](https://linkedin.com/in/thiagotavares) • [YouTube](https://youtube.com/@thiagotavares) • [GitHub](https://github.com/thiagotavares)

---

## 📝 Licença

MIT © Thiago Tavares Mendes
