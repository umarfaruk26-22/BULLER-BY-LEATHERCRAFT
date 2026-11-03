# Buller Leather - Regional FastAPI Mail Service

This lightweight, asynchronous Python FastAPI microservice handles high-performance email dispatching across international jurisdictions.

## Features
- **Async Non-Blocking Execution**: Web storefront checkout returns in milliseconds while emails are dispatched concurrently via FastAPI `BackgroundTasks`.
- **Per-Country Routing**: Works seamlessly with Buller Super Admin Panel (`Super Admin > International Markets > Edit Market`).
- **Bearer Token Protection**: Secure your endpoints with custom authorization headers.
- **Fail-Safe Fallback**: If the microservice is temporarily offline, the Buller PHP Mailer engine gracefully falls back to standard server dispatch without disrupting customer checkout.

## Setup Instructions

1. **Install Python 3.9+ & Dependencies**:
   ```bash
   pip install -r requirements.txt
   ```

2. **Configure Environment Variables (Optional)**:
   ```bash
   export MAILER_SECRET_TOKEN="your_secure_random_token"
   export SMTP_HOST="smtp.gmail.com"
   export SMTP_PORT=587
   export SMTP_USER="your-email@bullerleather.com"
   export SMTP_PASS="your-app-password"
   ```

3. **Start the Microservice**:
   ```bash
   uvicorn main:app --host 0.0.0.0 --port 8000
   ```

4. **Connect in Super Admin Panel**:
   - Go to `https://bullerleather.com/superadmin/countries`
   - Click **Edit** on your target country (e.g. UAE, India, UK, USA)
   - Under **3. Regional Email & FastAPI Microservice Dispatcher**:
     - Delivery Engine: Select **FastAPI Async Microservice**
     - Endpoint URL: `http://your-server-ip:8000/api/v1/send-email`
     - Bearer Secret: (If set above)
     - Regional Sender: `concierge.ae@bullerleather.com`
     - Regional Admin Alert: `regional-manager@bullerleather.com`
   - Click **Save Country Details**.
