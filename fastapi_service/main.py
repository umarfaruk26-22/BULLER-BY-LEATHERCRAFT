"""
Buller Leather - Regional Async Email Microservice (FastAPI)
Accepts incoming order payloads from Buller Leather store and dispatches emails in background tasks.
"""

from fastapi import FastAPI, BackgroundTasks, Header, HTTPException, status
from pydantic import BaseModel, EmailStr
from typing import Optional
import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
import os
import logging

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger("buller_mailer")

app = FastAPI(
    title="Buller Leather Regional Mail Dispatcher",
    description="Microservice for localized country-specific email queuing & dispatch",
    version="1.0.0"
)

# Optional Secret Token to protect endpoint
API_SECRET_TOKEN = os.getenv("MAILER_SECRET_TOKEN", "")

# Default SMTP settings (configure per deployment / .env)
SMTP_HOST = os.getenv("SMTP_HOST", "smtp.gmail.com")
SMTP_PORT = int(os.getenv("SMTP_PORT", 587))
SMTP_USER = os.getenv("SMTP_USER", "")
SMTP_PASS = os.getenv("SMTP_PASS", "")

class EmailPayload(BaseModel):
    to_email: EmailStr
    to_name: Optional[str] = None
    subject: str
    html_content: str
    country_code: str
    country_name: str
    sender_name: Optional[str] = "Buller Leather"
    sender_email: Optional[str] = "concierge@bullerleather.com"

def send_smtp_email(payload: EmailPayload):
    """Background worker function to send email via SMTP"""
    try:
        msg = MIMEMultipart("alternative")
        msg["Subject"] = payload.subject
        msg["From"] = f"{payload.sender_name} <{payload.sender_email}>"
        msg["To"] = f"{payload.to_name} <{payload.to_email}>" if payload.to_name else payload.to_email
        msg["Reply-To"] = payload.sender_email

        part = MIMEText(payload.html_content, "html", "utf-8")
        msg.attach(part)

        if SMTP_USER and SMTP_PASS:
            with smtplib.SMTP(SMTP_HOST, SMTP_PORT) as server:
                server.starttls()
                server.login(SMTP_USER, SMTP_PASS)
                server.sendmail(payload.sender_email, [payload.to_email], msg.as_string())
            logger.info(f"[{payload.country_code}] Email successfully dispatched to {payload.to_email}")
        else:
            logger.warning(f"[{payload.country_code}] SMTP credentials not set. Simulated send to {payload.to_email}")
    except Exception as e:
        logger.error(f"[{payload.country_code}] Error delivering email to {payload.to_email}: {str(e)}")

@app.post("/api/v1/send-email", status_code=status.HTTP_202_ACCEPTED)
async def dispatch_email(
    payload: EmailPayload,
    background_tasks: BackgroundTasks,
    authorization: Optional[str] = Header(None)
):
    """
    Asynchronously accept email dispatch request from Buller Leather website.
    Returns HTTP 202 immediately while processing in background.
    """
    # Verify Bearer token if configured
    if API_SECRET_TOKEN:
        if not authorization or not authorization.startswith("Bearer "):
            raise HTTPException(status_code=401, detail="Missing or invalid Bearer token")
        token = authorization.split(" ")[1]
        if token != API_SECRET_TOKEN:
            raise HTTPException(status_code=403, detail="Forbidden: Invalid token")

    # Queue email in background
    background_tasks.add_task(send_smtp_email, payload)

    return {
        "status": "queued",
        "message": f"Email queued for {payload.to_email} under region {payload.country_name} ({payload.country_code})",
        "recipient": payload.to_email
    }

@app.get("/health")
def health_check():
    return {"status": "ok", "service": "buller-regional-mailer"}
