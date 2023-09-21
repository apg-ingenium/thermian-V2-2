import os
from typing import Iterable, List

from src.detection.domain.files.Image import Image
from src.results.domain.output_image_repository.OutputImageRepository import OutputImageRepository


class FileSystemOutputImageRepository(OutputImageRepository):

    def __init__(self):
        self.__base_directory = "/app/persistence/analysis"

    def save(self, images: Iterable[Image], analysis_id: str, image_id: str) -> None:
        for image in images:
            self.__create_output_image_directory(analysis_id, image_id)
            with open(self.__path_to_output_image(analysis_id, image_id, image), "wb") as file:
                file.write(image.to_bytes())

    def __path_to_output_image(self, analysis_id: str, image_id: str, image: Image) -> str:
        return f"{self.__base_directory}/{analysis_id}/{image_id}/{image.name}"

    def __create_output_image_directory(self, analysis_id: str, image_id: str) -> None:
        directory = f"{self.__base_directory}/{analysis_id}/{image_id}"
        if not os.path.isdir(directory):
            os.makedirs(directory)

    def save_all(self, images: Iterable[Iterable[Image]], analysis_id: str, image_ids: List[str]) -> None:
        for image_id, entry_images in zip(image_ids, images):
            self.save(entry_images, analysis_id, image_id)
