from abc import ABC, abstractmethod

from src.detection.domain.dataset.Dataset import Dataset


class Model(ABC):

    @abstractmethod
    def execute_analysis(self, analysis_id: str, dataset: Dataset) -> None:
        pass
