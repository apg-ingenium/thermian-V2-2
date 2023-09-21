from src.dataset.use_case.find_dataset.FindDatasetCommand import FindDatasetCommand
from src.dataset.use_case.find_dataset.FindDatasetUseCase import FindDatasetUseCase
from src.detection.domain.model.ModelSelector import ModelSelector
from src.detection.use_case.execute_dataset_hotspot_analysis.DatasetHotspotAnalysisCommand import DatasetHotspotAnalysisCommand
from src.results.use_case.store_hotspot_detection_files.StoreHotspotDetectionFilesUseCase import StoreHotspotDetectionFilesUseCase


class DatasetHotspotAnalysisUseCase:

    def __init__(
            self,
            find_dataset_use_case: FindDatasetUseCase,
            model_selector: ModelSelector,
    ) -> None:
        self.__find_dataset_use_case = find_dataset_use_case
        self.__model_selector = model_selector

    def execute(self, command: DatasetHotspotAnalysisCommand) -> None:

        model = self.__model_selector.create_model(
            command.model_name,
            command.model_config
        )

        dataset = self.__find_dataset_use_case.execute(
            FindDatasetCommand(command.dataset_id)
        )

        model.execute_analysis(command.analysis_id, dataset)
