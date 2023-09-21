from abc import ABC, abstractmethod
from typing import Iterable, List

from src.detection.domain.files.Csv import Csv


class OutputCsvRepository(ABC):

    @abstractmethod
    def save(self, csvs: Iterable[Csv], analysis_id: str, image_id: str) -> None:
        pass

    @abstractmethod
    def save_all(self, csvs: Iterable[Iterable[Csv]], analysis_id: str, image_ids: List[str]) -> None:
        pass
