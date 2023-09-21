from pydantic import BaseModel


class PostAnalysisResponse(BaseModel):
    analysis_id: str
