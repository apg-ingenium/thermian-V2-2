from typing import TypeVar, Generic, List

from src.detection.domain.files.FileCollection import FileCollection
from src.detection.domain.model.Stage import Stage

D = TypeVar("D")
R = TypeVar("R")


class Pipeline(Generic[D, R]):

    def __init__(self, stages: List[Stage[R, D]]):
        self.__stages = stages

    def process(self, dataset: D, results: R) -> List[FileCollection]:
        output = [FileCollection() for _ in dataset]
        for stage in self.__stages:
            stage_results = stage.process(dataset, results)
            for (result, stage_result) in zip(output, stage_results):
                result.update_with(stage_result)
        return output
