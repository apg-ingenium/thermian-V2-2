from typing import List, Iterable, Tuple, Union

from src.dataset.domain.input_image_repository.InputImageRepository import InputImageRepository
from src.detection.domain.files.Image import Image


class Dataset:

    @staticmethod
    def containing_images_from_repository(image_ids: List[str], repository: InputImageRepository) -> 'Dataset':
        return Dataset(image_ids, repository)

    def __init__(self, image_ids: List[str], repository: InputImageRepository) -> None:
        self.__image_ids = image_ids
        self.__repository = repository

    @property
    def image_ids(self) -> List[str]:
        return self.__image_ids

    @property
    def num_images(self) -> int:
        return len(self.__image_ids)

    def batches(self, size: Union[int, None] = None) -> Iterable[Tuple[List[str], Iterable[Image]]]:
        size = size or self.num_images
        id_batches = [self.__image_ids[index:index+size] for index in range(0, self.num_images, size)]
        for batch_of_ids in id_batches:
            yield batch_of_ids, self.__repository.find_all(batch_of_ids)

    def __iter__(self) -> Iterable[Image]:
        for image_id in self.__image_ids:
            yield self.__repository.find(image_id)
