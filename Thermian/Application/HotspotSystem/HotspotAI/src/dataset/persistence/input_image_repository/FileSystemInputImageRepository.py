import glob
import os
from typing import Optional, List, Iterable

from src.dataset.domain.input_image_repository.InputImageRepository import InputImageRepository
from src.detection.domain.files.Image import Image


class FileSystemInputImageRepository(InputImageRepository):

    def __init__(self):
        self.__base_directory = "/app/persistence/image"

    def find(self, image_id: str) -> Optional[Image]:
        path_to_image = self.__find_path_to_input_image(image_id)
        return Image.from_path(path_to_image) if path_to_image else None

    def __find_path_to_input_image(self, image_id: str) -> Optional[str]:
        paths = glob.glob(f"{self.__base_directory}/{image_id}/input-image.*")
        return paths[0] if paths else None

    def find_all(self, image_ids: List[str]) -> Iterable[Image]:
        return filter(lambda image: image is not None, map(self.find, image_ids))

    def save(self, image: Image, image_id: str) -> None:
        self.__create_input_image_directory(image_id)
        with open(self.__path_to_input_image(image_id, image), "wb") as image_file:
            image_file.write(image.to_bytes())

    def __create_input_image_directory(self, image_id: str) -> None:
        directory = f"{self.__base_directory}/{image_id}"
        if not os.path.isdir(directory):
            os.makedirs(directory)

    def __path_to_input_image(self, image_id: str, image: Image) -> str:
        return f"{self.__base_directory}/{image_id}/input-image.{image.format}"
