from abc import ABC, abstractmethod
from typing import TypeVar, Generic

D = TypeVar("D")
R = TypeVar("R")


class Detector(ABC, Generic[D, R]):

    @abstractmethod
    def evaluate(self, dataset: D) -> R:
        pass
