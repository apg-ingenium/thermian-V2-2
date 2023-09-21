from src.results.domain.output_csv_repository.OutputCsvRepository import OutputCsvRepository
from src.results.domain.output_image_repository.OutputImageRepository import OutputImageRepository
from src.results.use_case.store_hotspot_detection_files.StoreHotspotDetectionFilesCommand import StoreHotspotDetectionFilesCommand


class StoreHotspotDetectionFilesUseCase:

    def __init__(
            self,
            output_image_repository: OutputImageRepository,
            output_csv_repository: OutputCsvRepository
    ) -> None:
        self.__output_image_repository = output_image_repository
        self.__output_csv_repository = output_csv_repository

    def execute(self, command: StoreHotspotDetectionFilesCommand) -> None:
        images = map(lambda entry: entry.images(), command.detection_files)
        self.__output_image_repository.save_all(images, command.analysis_id, command.image_ids)

        csvs = map(lambda entry: entry.csvs(), command.detection_files)
        self.__output_csv_repository.save_all(csvs, command.analysis_id, command.image_ids)
