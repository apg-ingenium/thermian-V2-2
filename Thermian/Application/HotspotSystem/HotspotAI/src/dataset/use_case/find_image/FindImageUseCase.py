from src.dataset.domain.input_image_repository import InputImageRepository
from src.dataset.use_case.find_image.FindImageCommand import FindImageCommand
from src.detection.domain.files.Image import Image


class FindImageUseCase:

    def __init__(self, image_repository: InputImageRepository) -> None:
        self.__image_repository = image_repository

    def execute(self, command: FindImageCommand) -> Image:
        return self.__image_repository.find(command.image_id)
