from typing import Union

from src.detection.use_case.execute_dataset_hotspot_analysis.DatasetHotspotAnalysisCommand import DatasetHotspotAnalysisCommand
from src.detection.use_case.execute_dataset_hotspot_analysis.DatasetHotspotAnalysisUseCase import DatasetHotspotAnalysisUseCase
from src.detection.use_case.execute_image_hotspot_analysis.ImageHotspotAnalysisCommand import ImageHotspotAnalysisCommand
from src.detection.use_case.execute_image_hotspot_analysis.ImageHotspotAnalysisUseCase import ImageHotspotAnalysisUseCase
from web.analysis.post.PostAnalysisRequest import PostImageAnalysisRequest, PostDatasetAnalysisRequest
from web.analysis.post.PostAnalysisResponse import PostAnalysisResponse


class PostAnalysisController:

    def __init__(
            self,
            image_hotspot_analysis_use_case: ImageHotspotAnalysisUseCase,
            dataset_hotspot_analysis_use_case: DatasetHotspotAnalysisUseCase
    ):
        self.__dataset_hotspot_analysis_use_case = dataset_hotspot_analysis_use_case
        self.__image_hotspot_analysis_use_case = image_hotspot_analysis_use_case

    def handle(self, request: Union[PostImageAnalysisRequest, PostDatasetAnalysisRequest]) -> PostAnalysisResponse:

        if isinstance(request, PostImageAnalysisRequest):
            self.__image_hotspot_analysis_use_case.execute(
                ImageHotspotAnalysisCommand(
                    request.analysis_id,
                    request.image_id,
                    request.model_name,
                    request.model_config
                )
            )

        if isinstance(request, PostDatasetAnalysisRequest):
            self.__dataset_hotspot_analysis_use_case.execute(
                DatasetHotspotAnalysisCommand(
                    request.analysis_id,
                    request.dataset_id,
                    request.model_name,
                    request.model_config
                )
            )

        return PostAnalysisResponse(analysis_id=request.analysis_id)
