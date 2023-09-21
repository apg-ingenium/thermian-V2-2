from abc import ABC, abstractmethod
from typing import Iterable, List

from src.detection.domain.files.Image import Image


class OutputImageRepository(ABC):

    @abstractmethod
    def save(self, images: Iterable[Image], analysis_id: str, image_id: str) -> None:
        pass

    @abstractmethod
    def save_all(self, images: Iterable[Iterable[Image]], analysis_id: str, image_ids: List[str]) -> None:
        pass
