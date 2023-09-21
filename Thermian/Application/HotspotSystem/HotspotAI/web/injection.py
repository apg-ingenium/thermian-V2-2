from src.dataset.persistence.input_image_repository.MySQLInputImageRepository import MySQLInputImageRepository
from src.dataset.use_case.find_dataset.FindDatasetUseCase import FindDatasetUseCase
from src.dataset.persistence.dataset_repository.MySQLDatasetRepository import MySQLDatasetRepository
from src.detection.domain.model.ModelSelector import ModelSelector
from src.detection.domain.model.models.fake.Factory import FakeHotspotDetectionModelFactory
from src.detection.domain.model.models.two_phase.Factory import TwoPhaseHotspotDetectionModelFactory
from src.detection.use_case.execute_dataset_hotspot_analysis.DatasetHotspotAnalysisUseCase import DatasetHotspotAnalysisUseCase
from src.detection.use_case.execute_image_hotspot_analysis.ImageHotspotAnalysisUseCase import ImageHotspotAnalysisUseCase
from src.results.persistence.output_csv_repository.MySQLOutputCsvRepository import MySQLOutputCsvRepository
from src.results.persistence.output_image_repository.MySQLOutputImageRepository import MySQLOutputImageRepository
from src.results.use_case.store_hotspot_detection_files.StoreHotspotDetectionFilesUseCase import StoreHotspotDetectionFilesUseCase
from web.analysis.post.PostAnalysisController import PostAnalysisController


def post_analysis_controller():

    input_image_repository = MySQLInputImageRepository()
    output_image_repository = MySQLOutputImageRepository()
    output_csv_repository = MySQLOutputCsvRepository()
    dataset_repository = MySQLDatasetRepository()

    store_detection_files_use_case = StoreHotspotDetectionFilesUseCase(
        output_image_repository,
        output_csv_repository
    )

    model_selector = ModelSelector({
        "two_phase": TwoPhaseHotspotDetectionModelFactory(
            store_detection_files_use_case
        ),
        "fake": FakeHotspotDetectionModelFactory(
            store_detection_files_use_case
        ),
        "default": FakeHotspotDetectionModelFactory(
            store_detection_files_use_case
        )
    })

    return PostAnalysisController(
        ImageHotspotAnalysisUseCase(
            input_image_repository,
            model_selector
        ),
        DatasetHotspotAnalysisUseCase(
            FindDatasetUseCase(
                dataset_repository,
                input_image_repository
            ),
            model_selector,
        )
    )
