from typing import Dict

from src.detection.domain.model.Model import Model
from src.detection.domain.model.ModelFactory import ModelFactory


class ModelSelector:

    def __init__(self, model_factories: Dict[str, ModelFactory]):
        self.__model_factories = model_factories

    def create_model(self, name: str, config: dict) -> Model:
        factory = self.__model_factories.get(name, self.__model_factories["default"])
        return factory.create_model(**config)
