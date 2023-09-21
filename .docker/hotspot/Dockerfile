FROM python:3.7-slim as base
WORKDIR /app
RUN apt-get update && apt-get install -y --no-install-recommends libmagic1
ARG HOTSPOT_AI_ROOT
COPY ${HOTSPOT_AI_ROOT}/requirements.txt /app/
RUN pip install --no-cache-dir --upgrade pip setuptools wheel \
    && pip install --no-cache-dir -r requirements.txt
COPY ${HOTSPOT_AI_ROOT}/ /app/
CMD ["uvicorn", "web.app:api", "--host", "0.0.0.0", "--port", "81", "--reload", "--reload-dir", "/app"]


FROM base as production
COPY .env.example /app/.env
CMD ["uvicorn", "web.app:api", "--host", "0.0.0.0", "--port", "81"]
