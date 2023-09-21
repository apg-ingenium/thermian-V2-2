from typing import TypeVar, Generic, Union

from src.detection.domain.dataset.Dataset import Dataset
from src.detection.domain.model.Detector import Detector
from src.detection.domain.model.Model import Model
from src.detection.domain.model.Pipeline import Pipeline
from src.results.use_case.store_hotspot_detection_files.StoreHotspotDetectionFilesCommand import StoreHotspotDetectionFilesCommand
from src.results.use_case.store_hotspot_detection_files.StoreHotspotDetectionFilesUseCase import StoreHotspotDetectionFilesUseCase

D = TypeVar("D")
R = TypeVar("R")


class GenericModel(Model, Generic[D, R]):

    def __init__(
            self,
            detector: Detector[D, R],
            pipeline: Pipeline[D, R],
            store_files_use_case: StoreHotspotDetectionFilesUseCase,
            batch_size: Union[int, None] = None
    ):
        self.__store_files_use_case = store_files_use_case
        self.__detector = detector
        self.__pipeline = pipeline
        self.__batch_size = batch_size

    def execute_analysis(self, analysis_id: str, dataset: Dataset) -> None:
        for (batch_ids, batch) in dataset.batches(self.__batch_size):
            batch = tuple(batch)
            results = self.__detector.evaluate(batch)
            files = self.__pipeline.process(batch, results)

            self.__store_files_use_case.execute(
                StoreHotspotDetectionFilesCommand(
                    analysis_id, batch_ids, files
                )
            )
