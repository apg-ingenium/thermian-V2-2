from src.dataset.domain.input_image_repository.InputImageRepository import InputImageRepository
from src.detection.domain.dataset.Dataset import Dataset
from src.detection.domain.model.ModelSelector import ModelSelector
from src.detection.use_case.execute_image_hotspot_analysis.ImageHotspotAnalysisCommand import ImageHotspotAnalysisCommand


class ImageHotspotAnalysisUseCase:

    def __init__(
            self,
            image_repository: InputImageRepository,
            model_selector: ModelSelector,
    ) -> None:
        self.__image_repository = image_repository
        self.model_selector = model_selector

    def execute(self, command: ImageHotspotAnalysisCommand) -> None:

        dataset = Dataset.containing_images_from_repository(
            [command.image_id], self.__image_repository
        )

        model = self.model_selector.create_model(
            command.model_name,
            command.model_config
        )

        model.execute_analysis(command.analysis_id, dataset)
