# Security Guide - API Gateway

## 🔒 Tổng quan Bảo mật

API Gateway này được thiết kế với nhiều lớp bảo mật để bảo vệ dữ liệu Moodle.

## 🛡️ Security Layers

### Layer 1: Request Logging
**Middleware:** `RequestLogger`
**Mục đích:** Audit trail, detect suspicious activity

**Chức năng:**
- Log tất cả API requests
- Track IP, user agent, response time
- Mask API keys trong logs
- Alert khi có unauthorized attempts

**Logs:**
```
storage/logs/api.log       - Tất cả requests
storage/logs/security.log  - Security events
```

### Layer 2: Input Sanitization
**Middleware:** `InputSanitizer`
**Mục đích:** Chống XSS, SQL Injection, Code Injection

**Chức năng:**
- Strip HTML tags
- Remove SQL injection patterns
- Detect suspicious patterns
- Log potential attacks

**Patterns detected:**
- XSS: `<script>`, `javascript:`, `onerror=`
- SQL Injection: `union select`, `insert into`
- Path Traversal: `../`, `/etc/passwd`
- Code Injection: `eval()`, `base64_decode`

### Layer 3: API Key Authentication
**Middleware:** `ApiKeyAuth`
**Mục đích:** Xác thực clients

**Chức năng:**
- Validate X-API-Key header
- Check against whitelist
- Return 401 if invalid

**Best practices:**
- Dùng random strings dài (32+ chars)
- Rotate keys định kỳ
- Mỗi client 1 key riêng
- Revoke keys khi không dùng

### Layer 4: Rate Limiting
**Middleware:** `RateLimitMiddleware`
**Mục đích:** Chống DDoS, brute force

**Chức năng:**
- Limit: 100 requests/minute per API key
- Return 429 if exceeded
- Auto-reset sau 1 phút

**Configuration:**
```env
RATE_LIMIT_MAX_ATTEMPTS=100
RATE_LIMIT_DECAY_MINUTES=1
```

### Layer 5: IP Whitelist (Optional)
**Middleware:** `IpWhitelist`
**Mục đích:** Restrict access by IP

**Chức năng:**
- Chỉ allow IPs trong whitelist
- Return 403 if not allowed
- Log blocked attempts

**Configuration:**
```env
# Leave empty to disable
ALLOWED_IPS=192.168.1.100,10.0.0.50
```

## 🔐 Moodle Token Protection

### Token KHÔNG BAO GIỜ exposed

```
✅ Token stored in: .env (server-side)
✅ Token used by: Laravel only
✅ Token sent to: Moodle only (internal)
❌ Token NEVER sent to: Clients
```

### Token Security Checklist

- [ ] Token stored in .env (not in code)
- [ ] .env in .gitignore
- [ ] Use service account (not admin)
- [ ] Service account has minimal permissions
- [ ] Token has expiry date
- [ ] Rotate token every 3-6 months
- [ ] Monitor token usage in Moodle logs

## 🚨 Attack Scenarios & Defense

### 1. Token Theft

**Attack:**
```
Attacker decompiles chatbot → Tries to find Moodle token
```

**Defense:**
```
✅ Token not in client code
✅ Only API key in client (less sensitive)
✅ Even if API key stolen, can revoke immediately
✅ Attacker cannot access Moodle directly
```

### 2. DDoS Attack

**Attack:**
```
Attacker sends 10,000 requests/second
```

**Defense:**
```
✅ Rate limiting: Max 100 req/min
✅ Returns 429 after limit
✅ Moodle protected from overload
✅ Can add IP-based rate limiting
```

### 3. SQL Injection

**Attack:**
```
GET /students/admin' OR '1'='1/results
```

**Defense:**
```
✅ InputSanitizer removes SQL patterns
✅ Laravel query builder (parameterized)
✅ Moodle API uses prepared statements
✅ Multiple layers of protection
```

### 4. XSS Attack

**Attack:**
```
GET /courses?search=<script>alert('xss')</script>
```

**Defense:**
```
✅ InputSanitizer strips HTML tags
✅ Laravel escapes output
✅ Suspicious pattern detected & logged
```

### 5. Brute Force

**Attack:**
```
Attacker tries 1000 different API keys
```

**Defense:**
```
✅ Rate limiting per IP
✅ All attempts logged
✅ Can implement IP ban after X failed attempts
```

### 6. Man-in-the-Middle

**Attack:**
```
Attacker intercepts traffic between client and server
```

**Defense:**
```
✅ Use HTTPS in production (mandatory)
✅ Even if intercepted, only sees API key
✅ Moodle token never transmitted to client
✅ Internal traffic (Gateway ↔ Moodle) can be secured
```

## 📊 Security Monitoring

### Check Logs

```bash
# API requests
tail -f storage/logs/api.log

# Security events
tail -f storage/logs/security.log

# Filter unauthorized attempts
grep "Unauthorized" storage/logs/security.log

# Filter suspicious inputs
grep "Suspicious" storage/logs/security.log
```

### Metrics to Monitor

1. **Failed authentication attempts**
   - Nhiều 401 từ cùng IP → Potential brute force
   
2. **Rate limit hits**
   - Nhiều 429 → Potential DDoS hoặc misconfigured client
   
3. **Suspicious input patterns**
   - XSS/SQL injection attempts → Active attack
   
4. **Response times**
   - Tăng đột ngột → Potential attack hoặc performance issue

## 🔧 Security Configuration

### Development (.env)
```env
APP_ENV=local
APP_DEBUG=true
RATE_LIMIT_MAX_ATTEMPTS=1000
ALLOWED_IPS=
CORS_ALLOWED_ORIGINS=*
```

### Production (.env)
```env
APP_ENV=production
APP_DEBUG=false
RATE_LIMIT_MAX_ATTEMPTS=100
ALLOWED_IPS=192.168.1.100,10.0.0.50
CORS_ALLOWED_ORIGINS=https://chatbot.domain.com,https://app.domain.com
```

## 🚀 Production Security Checklist

### Before Deployment

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Use HTTPS (SSL certificate)
- [ ] Generate strong API keys (32+ chars)
- [ ] Configure rate limiting
- [ ] Setup IP whitelist (if needed)
- [ ] Configure CORS properly
- [ ] Enable security logging
- [ ] Setup log rotation
- [ ] Configure firewall rules
- [ ] Use service account (not admin)
- [ ] Set Moodle token expiry
- [ ] Backup .env file securely

### After Deployment

- [ ] Test all security layers
- [ ] Monitor logs daily
- [ ] Setup alerts for suspicious activity
- [ ] Regular security audits
- [ ] Rotate API keys every 3 months
- [ ] Rotate Moodle token every 6 months
- [ ] Keep Laravel updated
- [ ] Review access logs weekly

## 🆚 So sánh: Có vs Không có Gateway

### Không có Gateway (Kém bảo mật)

```
Security Layers: 1 (Moodle token only)
Token Exposure: High (in client code)
Attack Surface: Large (direct Moodle access)
Monitoring: Difficult (scattered logs)
Rate Limiting: None
Input Validation: Moodle only
Revocation: Difficult (must change Moodle token)
```

### Có Gateway (Bảo mật tốt)

```
Security Layers: 5+ (stacked middlewares)
Token Exposure: None (server-side only)
Attack Surface: Small (controlled access)
Monitoring: Centralized (all in one place)
Rate Limiting: Yes (configurable)
Input Validation: Multiple layers
Revocation: Easy (just remove API key)
```

## 📚 Best Practices

### 1. API Keys
- ✅ Generate với `openssl rand -hex 32`
- ✅ Mỗi client 1 key riêng
- ✅ Rotate every 3-6 months
- ✅ Revoke unused keys
- ❌ Không hardcode trong code
- ❌ Không commit vào Git

### 2. HTTPS
- ✅ Bắt buộc trong production
- ✅ Use Let's Encrypt (free)
- ✅ Force HTTPS redirect
- ✅ HSTS header
- ❌ Không dùng HTTP trong production

### 3. Logging
- ✅ Log tất cả requests
- ✅ Log security events
- ✅ Rotate logs (daily/weekly)
- ✅ Monitor logs regularly
- ❌ Không log sensitive data (passwords, full tokens)

### 4. Updates
- ✅ Keep Laravel updated
- ✅ Keep PHP updated
- ✅ Monitor security advisories
- ✅ Test updates in staging first

## 🔗 Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security](https://laravel.com/docs/security)
- [Moodle Security](https://docs.moodle.org/en/Security)

## 📞 Security Issues

Nếu phát hiện security vulnerability:
1. KHÔNG tạo public issue
2. Email trực tiếp: [your-security-email]
3. Mô tả chi tiết vulnerability
4. Chờ response trước khi public disclosure

---

**Remember:** Security is a process, not a product. Continuously monitor, update, and improve!
