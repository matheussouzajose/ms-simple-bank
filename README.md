# MS Simple Bank

## Resumo do Projeto

MS Simple Bank é um sistema bancário simplificado para transferências eletrônicas entre usuários, desenvolvido com PHP utilizando o framework Hyperf e arquitetura limpa.

### Funcionalidades Principais

- **Gerenciamento de Usuários**: Suporte para usuários comuns e lojistas
- **Carteiras Digitais**: Cada usuário possui uma carteira com saldo
- **Transferências**: Transferências seguras entre usuários
- **Notificações**: Envio assíncrono de notificações por email

### Arquitetura e Tecnologias

O projeto segue os princípios de Clean Architecture e Domain-Driven Design (DDD):

- **Framework**: Hyperf (alta performance baseado em Swoole)
- **Banco de Dados**: MySQL com Eloquent ORM
- **Padrões**: Repository Pattern, Unit of Work, Value Objects
- **Processamento**: Híbrido - transações síncronas, notificações assíncronas

### Estrutura do Projeto

```
src/Core/               # Domínio e regras de negócio
  Shared/               # Componentes compartilhados
  User/                 # Módulo de usuários
  Wallet/               # Módulo de carteiras
  Payment/              # Módulo de pagamentos

app/                    # Adaptadores e interface
  Controller/           # Controladores HTTP
  Model/                # Modelos Eloquent
  Job/                  # Jobs assíncronos
```

# 🚀 Executando o Projeto Hyperf com Docker


## 📦 Subir os containers

```bash
# Iniciar com Docker
docker compose up -d --build

# Acessar o container do Hyperf
docker compose exec app bash

# Executar migrações
php bin/hyperf.php migrate

# Executar testes
composer test
```

### Regras de Negócio Principais

- Usuários comuns podem enviar e receber transferências
- Usuários lojistas podem apenas receber transferências
- Transferências exigem saldo suficiente na carteira do pagador
- Todas as transferências são autorizadas por um serviço externo
- Valores monetários são armazenados em centavos para evitar problemas de precisão

### Referências

Para detalhes completos da arquitetura, decisões de design e estratégias de escalabilidade, consulte o arquivo [ARCHITECTURE.md](ARCHITECTURE.md).
