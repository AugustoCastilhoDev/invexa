# 🛑 Invexa — Procedimento de Disaster Recovery

> **Última revisão:** Junho 2026  
> **Responsável:** Augusto Castilho

---

## 📊 Visão Geral da Estratégia de Backup

| Item | Configuração |
|------|-------------|
| Frequência | Diário — 03h00 (cron no VPS) |
| Armazenamento local | `/var/backups/invexa/` (retenção 7 dias) |
| Armazenamento externo | Backblaze B2 / S3 (retenção 30 dias) |
| Formato | `.sql.gz` (dump comprimido) |
| Alerta de falha | E-mail para `suporte@invexa.com.br` |
| Script | `/usr/local/bin/invexa-backup.sh` |

---

## ⚙️ Configuração Inicial no VPS

### 1. Copiar o script para o VPS

```bash
scp scripts/backup/invexa-backup.sh root@SEU_VPS:/usr/local/bin/invexa-backup.sh
chmod +x /usr/local/bin/invexa-backup.sh
```

### 2. Configurar variáveis de ambiente

Opção A — variável de ambiente no crontab:
```bash
crontab -e
# Adicionar:
DB_PASSWORD=sua_senha_aqui
ALERT_EMAIL=suporte@invexa.com.br
0 3 * * * /usr/local/bin/invexa-backup.sh >> /var/log/invexa-backup.log 2>&1
```

Opção B — arquivo `~/.my.cnf` (mais seguro, evita senha no crontab):
```ini
[mysqldump]
user=root
password=sua_senha_aqui
```
```bash
chmod 600 ~/.my.cnf
```

### 3. Instalar e configurar rclone (Backblaze B2)

```bash
curl https://rclone.org/install.sh | sudo bash
rclone config
# Escolher: n (novo remote) → nome: b2 → tipo: b2
# Informar Account ID e Application Key do Backblaze
```

### 4. Testar o script manualmente

```bash
/usr/local/bin/invexa-backup.sh
# Verificar se o arquivo aparece em /var/backups/invexa/
# Verificar se o arquivo aparece no bucket do B2
```

---

## 🔄 Procedimento de Restore Completo

> ⚠️ **Execute em ambiente de teste primeiro. Em produção, avise os usuários antes.**

### Passo 1 — Identificar o backup mais recente

```bash
# Listar backups locais
ls -lh /var/backups/invexa/

# Ou listar do armazenamento remoto (B2)
rclone ls b2:invexa-backups/db/ | sort -k2 | tail -10
```

### Passo 2 — Baixar o backup (se necessário)

```bash
# Baixar do B2 para o servidor
rclone copy b2:invexa-backups/db/invexa_YYYYMMDD_HHMMSS.sql.gz /tmp/
```

### Passo 3 — Colocar a aplicação em manutenção

```bash
cd /var/www/invexa
php artisan down --message="Manutenção em andamento. Voltamos em breve." --retry=300
```

### Passo 4 — Restaurar o banco de dados

```bash
# Descompactar e restaurar
gunzip -c /tmp/invexa_YYYYMMDD_HHMMSS.sql.gz | mysql -u root -p invexa

# Verificar integridade
mysql -u root -p -e "USE invexa; SHOW TABLES; SELECT COUNT(*) FROM users;"
```

### Passo 5 — Limpar caches e reativar

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

### Passo 6 — Validar

- [ ] Login funcionando
- [ ] Dados de um tenant de teste visíveis
- [ ] Stripe webhook respondendo
- [ ] Envio de e-mail funcionando

---

## ⏱️ RTO e RPO

| Métrica | Valor alvo |
|---------|----------|
| **RPO** (perda máxima de dados) | 24 horas (backup diário) |
| **RTO** (tempo máximo para recovery) | 2 horas |

---

## 🧹 Checklist pós-incidente

- [ ] Restore testado com sucesso
- [ ] Usuários notificados (se houve perda de dados)
- [ ] Causa-raiz identificada e documentada
- [ ] Script de backup ajustado se necessário
- [ ] Alertas de monitoramento revisados

---

## 📞 Contatos de Emergência

| Papel | Contato |
|-------|--------|
| Dev / Admin | Augusto Castilho |
| Hospedagem (VPS) | Hostinger Suporte |
| Banco de dados | MySQL local no VPS |
| Storage externo | Backblaze B2 |
