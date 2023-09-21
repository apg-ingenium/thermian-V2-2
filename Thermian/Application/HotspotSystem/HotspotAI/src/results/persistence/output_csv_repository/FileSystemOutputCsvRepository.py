import os
from typing import Iterable, List

from src.detection.domain.files.Csv import Csv
from src.results.domain.output_csv_repository.OutputCsvRepository import OutputCsvRepository


class FileSystemOutputCsvRepository(OutputCsvRepository):

    def __init__(self):
        self.__base_directory = "/app/persistence/analysis"

    def save(self, csvs: Iterable[Csv], analysis_id: str, image_id: str):
        for csv in csvs:
            self.__create_output_csv_directory(analysis_id, image_id)
            with open(self.__path_to_output_csv(analysis_id, image_id, csv), "wb") as file:
                file.write(csv.to_bytes())

    def __path_to_output_csv(self, analysis_id: str, image_id: str, csv: Csv):
        return f"{self.__base_directory}/{analysis_id}/{image_id}/{csv.name}"

    def __create_output_csv_directory(self, analysis_id: str, image_id: str) -> None:
        directory = f"{self.__base_directory}/{analysis_id}/{image_id}"
        if not os.path.isdir(directory):
            os.makedirs(directory)

    def csv(self, csvs: Iterable[Iterable[Csv]], analysis_id: str, image_ids: List[str]) -> None:
        for image_id, entry_csvs in zip(image_ids, csvs):
            self.save(entry_csvs, analysis_id, image_id)
