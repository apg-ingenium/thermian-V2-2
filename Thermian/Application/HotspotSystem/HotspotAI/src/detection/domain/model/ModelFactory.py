from abc import ABC, abstractmethod

from src.detection.domain.model.Model import Model


class ModelFactory(ABC):

    @abstractmethod
    def create_model(self, **config) -> Model:
        pass
