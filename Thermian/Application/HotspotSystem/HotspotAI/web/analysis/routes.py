from typing import Union

from fastapi import APIRouter, Depends

from web.analysis.post.PostAnalysisController import PostAnalysisController
from web.analysis.post.PostAnalysisRequest import PostImageAnalysisRequest, PostDatasetAnalysisRequest
from web.analysis.post.PostAnalysisResponse import PostAnalysisResponse
from web.injection import post_analysis_controller

router = APIRouter(tags=["Analysis"])


@router.post("/hotspots/analysis", response_model=PostAnalysisResponse)
async def hotspot_analysis(
        request: Union[PostImageAnalysisRequest, PostDatasetAnalysisRequest],
        controller: PostAnalysisController = Depends(post_analysis_controller)
):
    return controller.handle(request)
