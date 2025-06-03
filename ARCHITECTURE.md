# MS Simple Bank - Sistema de Pagamentos Simplificado

## Visão Geral

O MS Simple Bank é um sistema bancário simplificado com foco em transferências eletrônicas entre usuários. O sistema foi desenvolvido seguindo os princípios de Clean Architecture e Domain-Driven Design (DDD), permitindo uma estrutura modular, testável e de fácil manutenção.

## Arquitetura

### Escolha da Arquitetura: Clean Architecture com DDD

#### Prós:
- **Separação de Responsabilidades**: A separação clara entre domínio, aplicação, infraestrutura e interface do usuário facilita a manutenção e evolução do sistema.
- **Testabilidade**: A arquitetura facilita a escrita de testes unitários, de integração e end-to-end.
- **Independência Tecnológica**: O domínio não tem dependências externas, permitindo que as decisões técnicas (framework, banco de dados) possam mudar sem afetar a lógica de negócio.
- **Escalabilidade**: Cada camada pode escalar independentemente, conforme necessário.

#### Contras:
- **Complexidade Inicial**: Requer um entendimento maior para desenvolvedores que não estão familiarizados com os conceitos.
- **Mais Código**: Geralmente requer mais código inicial para implementar a separação das camadas.
- **Maior Curva de Aprendizado**: Equipes novas precisam se adaptar ao padrão.

#### Por que escolhemos:
A Clean Architecture combinada com DDD nos permite focar nas regras de negócio primeiro, que são o coração de um sistema bancário. Isso garante que as regras críticas (como verificação de saldo antes de transferências) sejam implementadas corretamente e isoladas de detalhes técnicos.

### Estrutura do Projeto

```
src/
  Core/              # Regras de Negócio e Domínio
    Payment/         # Módulo de Pagamentos
    Shared/          # Componentes compartilhados
    User/            # Módulo de Usuários
    Wallet/          # Módulo de Carteiras

app/                 # Adaptadores de UI e Framework
  Controller/        # Controladores HTTP
  Model/             # Modelos Eloquent
  Request/           # Validação de Requests
  Resource/          # Transformação de Resposta
```

## Framework e Tecnologias

### Hyperf Framework

#### Prós:
- **Alto Desempenho**: Baseado em Swoole, oferece excelente performance em requisições concorrentes.
- **Suporte a Coroutines**: Permite processamento assíncrono eficiente.
- **Ecossistema PHP**: Compatível com muitas bibliotecas do ecossistema PHP/Laravel.
- **Container de Injeção de Dependências**: Facilita testes e desacoplamento.

#### Contras:
- **Comunidade Menor**: Comparado a Laravel ou Symfony, tem uma comunidade menor.
- **Curva de Aprendizado**: Programação assíncrona tem seus desafios.
- **Documentação**: Menos documentação e exemplos disponíveis em comparação com frameworks mais populares.

#### Por que escolhemos:
Para um sistema bancário, performance e capacidade de lidar com múltiplas transações simultaneamente são críticas. O Hyperf oferece vantagens significativas nesse aspecto, superando frameworks tradicionais síncronos como Laravel em cenários de alta concorrência.

### Banco de Dados

Utilizamos MySQL com Eloquent ORM para persistência de dados.

#### Prós:
- **Confiabilidade**: MySQL é um banco de dados comprovado para aplicações financeiras.
- **Transações ACID**: Garantia de integridade em operações críticas.
- **Familiaridade**: Equipe já conhece e tem experiência com MySQL.
- **Eloquent ORM**: Facilita operações de banco de dados com uma API elegante.

#### Contras:
- **Escalabilidade Horizontal**: Não tão simples quanto em NoSQL.
- **Esquema Rígido**: Alterações de schema podem ser complexas em sistemas grandes.

#### Por que escolhemos:
Para sistemas financeiros, a integridade dos dados é prioritária. As garantias ACID e a confiabilidade do MySQL superam as limitações de escalabilidade horizontal, que podem ser resolvidas com estratégias de sharding se necessário no futuro.

## Domínios do Sistema

### Módulo de Usuários

O módulo de usuários gerencia dois tipos principais de contas:
- **Usuário Comum**: Pode enviar e receber transferências.
- **Usuário Lojista**: Pode apenas receber transferências.

#### Entidades e Regras:
- Usuários possuem nome completo, email, documento (CPF/CNPJ), senha e tipo.
- Emails e documentos devem ser únicos no sistema.
- Senhas são armazenadas com hash seguro (bcrypt).

### Módulo de Carteiras

Cada usuário possui uma carteira com saldo associado.

#### Entidades e Regras:
- Toda carteira está associada a um único usuário.
- Saldo não pode ser negativo.
- Operações de débito verificam se há saldo suficiente.

### Módulo de Pagamentos

Gerencia transferências entre usuários do sistema.

#### Entidades e Regras:
- Transferências registram pagador, recebedor, valor e timestamp.
- Usuários comuns podem enviar e receber dinheiro.
- Lojistas podem apenas receber dinheiro.
- O pagador deve ter saldo suficiente para realizar a transferência.
- Transferências são atômicas (ou completam integralmente ou falham).
- Um serviço externo autoriza transferências (simulado no sistema).

## Padrões de Design Utilizados

### Value Objects
Utilizamos Value Objects para encapsular conceitos como Email, Documento, Senha e Saldo, garantindo que regras de validação sejam aplicadas consistentemente.

### Repository Pattern
Abstraímos o acesso a dados através de repositories, permitindo que a lógica de negócio não dependa diretamente do ORM ou banco de dados.

### Use Cases / Application Services
Cada operação significativa do sistema é representada como um caso de uso, facilitando o entendimento das funcionalidades disponíveis.

### Unit of Work
Implementamos o padrão Unit of Work para garantir consistência em operações que afetam múltiplas entidades, como transferências.

## Testes

O projeto utiliza diferentes níveis de testes:

- **Testes Unitários**: Verificam comportamentos isolados de componentes.
- **Testes de Integração**: Validam a interação entre componentes.
- **Testes E2E**: Simulam operações completas de usuários através da API.

## Decisões Específicas de Implementação

### Valor em Centavos
Valores monetários são armazenados em centavos (inteiros) para evitar problemas de precisão com números de ponto flutuante.

### Notificações Assíncronas
Notificações de transferências são processadas assincronamente para não bloquear a conclusão da operação principal.

### Cache de Autorizações
Implementamos cache para reduzir chamadas ao serviço externo de autorização.

### Transações Síncronas vs. Notificações Assíncronas

Uma decisão crítica de design foi o processamento de transações financeiras de forma síncrona, enquanto o envio de notificações (emails) é feito de maneira assíncrona:

#### Transações Síncronas:
- **Consistência Imediata**: Operações financeiras exigem confirmação imediata para garantir a integridade do saldo.
- **Feedback ao Usuário**: O cliente precisa saber instantaneamente se sua transferência foi bem-sucedida.
- **Atomicidade**: As operações de débito e crédito precisam ser concluídas juntas ou falhar completamente.
- **Controle de Concorrência**: Evita condições de corrida que poderiam resultar em saldos inconsistentes.
- **Auditoria**: Facilita o rastreamento e auditoria de operações financeiras.

#### Notificações Assíncronas:
- **Desacoplamento**: Falhas no envio de emails não comprometem a operação financeira principal.
- **Performance**: O usuário não precisa esperar que o email seja enviado para receber a confirmação da transação.
- **Resiliência**: Em caso de falha temporária no serviço de email, as tentativas podem ser repetidas sem afetar a transação original.
- **Escalabilidade**: O sistema de notificações pode escalar independentemente do núcleo de processamento financeiro.
- **Priorização de Recursos**: Os recursos computacionais são priorizados para operações críticas financeiras.

Esta abordagem híbrida oferece o melhor dos dois mundos: segurança e consistência para as operações financeiras, e flexibilidade e desempenho para comunicações com o usuário.

## Pontos de Melhoria e Escalabilidade

À medida que o MS Simple Bank cresce em número de usuários e volume de transações, identificamos várias áreas para melhoria e estratégias de escalabilidade:

### Cobertura de Testes

#### Estado Atual:
- Testes unitários para os principais Value Objects e Entidades
- Testes de integração para casos de uso críticos
- Testes E2E para principais fluxos de API

#### Melhorias Propostas:
1. **Ampliação de Cobertura**: Aumentar para >90% a cobertura de testes unitários em todas as camadas de domínio
2. **Testes de Carga**: Implementar testes de carga para simular picos de transações em horários de alto volume
3. **Testes de Concorrência**: Adicionar testes específicos para cenários de concorrência em operações críticas
4. **Testes de Regressão Automatizados**: Pipeline CI/CD com testes automáticos para prevenir regressões
5. **Property-Based Testing**: Adicionar testes baseados em propriedades para identificar casos extremos em cálculos financeiros

### Melhorias de Domínio

#### Modularização:
1. **Bounded Contexts**: Refinar a separação dos contextos limitados para facilitar times independentes
2. **Módulo de Auditoria**: Criar um domínio específico para auditoria e rastreamento de operações
3. **Módulo de Compliance**: Separar regras de compliance e KYC (Know Your Customer) em um domínio próprio

#### Enriquecimento Funcional:
1. **Histórico de Transações**: Aprimorar com categorização e métricas para usuários
2. **Sistema de Limites**: Implementar limites de transação configuráveis por perfil de usuário
3. **Anti-Fraude**: Desenvolver módulo de detecção de fraudes baseado em padrões de comportamento

### Escalabilidade Técnica

#### Banco de Dados:
1. **Sharding**: Implementar sharding por ID de usuário para distribuir carga de leitura/escrita
2. **Read Replicas**: Adicionar réplicas de leitura para queries de relatórios e históricos
3. **Caching Multi-Nível**: Cache distribuído para dados frequentemente acessados como saldos

#### Arquitetura:
1. **Microserviços**: Evolução gradual para microserviços baseados nos bounded contexts
2. **CQRS**: Separar modelos de leitura e escrita para otimizar performance em consultas complexas
3. **Event Sourcing**: Implementar para operações críticas, facilitando auditoria e reconstrução de estados

#### Infraestrutura:
1. **Kubernetes**: Migrar para orquestração Kubernetes para auto-scaling baseado em demanda
2. **Multi-Região**: Deployments em múltiplas regiões geográficas para resiliência e menor latência
3. **Filas Distribuídas**: Kafka ou Rabbit
