from src.dataset.domain.dataset_repository.DatasetRepository import DatasetRepository
from src.dataset.domain.input_image_repository.InputImageRepository import InputImageRepository
from src.dataset.use_case.find_dataset.FindDatasetCommand import FindDatasetCommand
from src.detection.domain.dataset.Dataset import Dataset


class FindDatasetUseCase:

    def __init__(
            self,
            dataset_repository: DatasetRepository,
            image_repository: InputImageRepository
    ) -> None:
        self.__image_repository = image_repository
        self.__dataset_repository = dataset_repository

    def execute(self, command: FindDatasetCommand) -> Dataset:
        return Dataset.containing_images_from_repository(
            self.__dataset_repository.find_dataset_image_ids(command.dataset_id),
            self.__image_repository
        )
