from abc import ABC, abstractmethod
from typing import Generic, List, TypeVar

from src.detection.domain.files.FileCollection import FileCollection

D = TypeVar("D")
R = TypeVar("R")


class Stage(ABC, Generic[D, R]):

    @abstractmethod
    def process(self, dataset: D, results: R) -> List[FileCollection]:
        pass
