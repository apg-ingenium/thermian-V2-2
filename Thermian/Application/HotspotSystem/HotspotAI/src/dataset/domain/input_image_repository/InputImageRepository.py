from abc import ABC, abstractmethod
from typing import List, Iterable

from src.detection.domain.files.Image import Image


class InputImageRepository(ABC):

    @abstractmethod
    def find(self, image_id: str) -> Image:
        pass

    @abstractmethod
    def find_all(self, image_ids: List[str]) -> Iterable[Image]:
        pass

    @abstractmethod
    def save(self, image: Image, image_id: str):
        pass
