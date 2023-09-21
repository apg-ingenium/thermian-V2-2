from src.detection.domain.model.GenericModel import GenericModel
from src.detection.domain.model.Model import Model
from src.detection.domain.model.ModelFactory import ModelFactory
from src.detection.domain.model.Pipeline import Pipeline
from src.detection.domain.model.models.shared.ObjectDetector import ObjectDetector
from src.detection.domain.model.models.two_phase.TwoPhaseHotspotDetector import TwoPhaseHotspotDetector
from src.detection.domain.model.stages.BoundingBoxDrawingStage import BoundingBoxDrawingStage
from src.detection.domain.model.stages.GpsCoordinateExtractionStage import GpsCoordinateExtractionStage
from src.detection.domain.model.stages.HotspotsCsvWritingStage import HotspotsCsvWritingStage
from src.detection.domain.model.stages.PanelsCsvWritingStage import PanelsCsvWritingStage
from src.results.use_case.store_hotspot_detection_files.StoreHotspotDetectionFilesUseCase import StoreHotspotDetectionFilesUseCase


class TwoPhaseHotspotDetectionModelFactory(ModelFactory):

    def __init__(self, store_detection_files_use_case: StoreHotspotDetectionFilesUseCase):
        self.__store_detection_files_use_case = store_detection_files_use_case

    def create_model(self, panel_detector: str, hotspot_detector: str) -> Model:
        models = "/app/models/two_phase"

        return GenericModel(
            TwoPhaseHotspotDetector(
                ObjectDetector(f"{models}/panel_detectors/{panel_detector}.pb"),
                ObjectDetector(f"{models}/hotspot_detectors/{hotspot_detector}.pb"),
            ),
            Pipeline([
                BoundingBoxDrawingStage(),
                HotspotsCsvWritingStage(),
                PanelsCsvWritingStage(),
                GpsCoordinateExtractionStage()
            ]),
            self.__store_detection_files_use_case,
            batch_size=20
        )
