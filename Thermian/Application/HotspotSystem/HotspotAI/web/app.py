from dotenv import load_dotenv
from fastapi import FastAPI

from web.analysis.routes import router as analysis_router

load_dotenv()
api = FastAPI()
api.include_router(analysis_router)
