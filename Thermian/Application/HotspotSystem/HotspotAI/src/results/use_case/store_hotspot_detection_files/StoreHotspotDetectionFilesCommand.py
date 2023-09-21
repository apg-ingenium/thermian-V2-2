from typing import List

from src.detection.domain.files.FileCollection import FileCollection


class StoreHotspotDetectionFilesCommand:

    def __init__(self, analysis_id: str, image_ids: List[str], detection_files: List[FileCollection]):
        self.__analysis_id = analysis_id
        self.__image_ids = image_ids
        self.__detection_files = detection_files

    @property
    def analysis_id(self) -> str:
        return self.__analysis_id

    @property
    def image_ids(self) -> List[str]:
        return self.__image_ids

    @property
    def detection_files(self) -> List[FileCollection]:
        return self.__detection_files
