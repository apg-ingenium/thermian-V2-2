from typing import Optional

from pydantic import BaseModel


class PostImageAnalysisRequest(BaseModel):
    analysis_id: str
    image_id: str
    model_name: Optional[str]
    model_config: Optional[dict]


class PostDatasetAnalysisRequest(BaseModel):
    analysis_id: str
    dataset_id: str
    model_name: Optional[str]
    model_config: Optional[dict]
