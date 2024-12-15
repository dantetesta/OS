# Sistema de Ordens de Serviço - Web Design

Sistema desenvolvido para gerenciamento de ordens de serviço de uma agência de web design.

## Requisitos

- PHP 8.3 ou superior
- MySQL 5.7 ou superior
- Composer
- Apache ou Nginx

## Instalação

1. Clone o repositório
2. Execute `composer install`
3. Importe o arquivo `database.sql` no seu banco de dados
4. Configure o arquivo `config/config.php` com suas credenciais
5. Configure o servidor web para apontar para a pasta `public`

## Configuração do Servidor

### Apache
Certifique-se que o mod_rewrite está habilitado e que o arquivo .htaccess na pasta public está sendo lido corretamente.

### Nginx
```nginx
server {
    listen 80;
    server_name seu-dominio.com;
    root /caminho/para/pasta/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Estrutura do Projeto

```
├── app/
│   ├── Controllers/    # Controladores da aplicação
│   ├── Models/         # Modelos de dados
│   ├── Core/           # Classes principais do sistema
│   └── Views/          # Views da aplicação
├── config/            # Arquivos de configuração
├── public/            # Pasta pública
│   ├── css/
│   ├── js/
│   └── index.php
├── routes/            # Definição das rotas
└── vendor/           # Dependências do Composer
```

## Funcionalidades

- Sistema de autenticação
- Gestão de clientes
- Gestão de ordens de serviço
- Dashboard com estatísticas
- Interface responsiva (Mobile First)
- Integração com AdminLTE

## Segurança

- Proteção contra CSRF
- Sanitização de dados
- Validação de formulários
- Controle de acesso baseado em sessão
- Senhas criptografadas

## Usuário Padrão

- Email: admin@wprevolution.com.br
- Senha: admin123

## Suporte

Para suporte, entre em contato através do email: suporte@wprevolution.com.br
