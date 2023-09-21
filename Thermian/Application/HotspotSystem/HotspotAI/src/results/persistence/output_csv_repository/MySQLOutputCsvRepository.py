import os
from typing import Tuple, Any, Iterable, List
from uuid import UUID, uuid4

from mysql.connector import connect

from src.detection.domain.files.Csv import Csv
from src.results.domain.output_csv_repository.OutputCsvRepository import OutputCsvRepository


class MySQLOutputCsvRepository(OutputCsvRepository):

    def __init__(self) -> None:
        self.__connection = connect(
            host=os.getenv("MYSQL_HOST"),
            user=os.getenv("MYSQL_USER"),
            password=os.getenv("MYSQL_PASSWORD"),
            database=os.getenv("MYSQL_DATABASE")
        )

    def save(self, csvs: Iterable[Csv], analysis_id: str, image_id: str) -> None:
        query = (
            "insert into output_csv (id, analysis_id, image_id, name, size, content) "
            "values (%s, %s, %s, %s, %s, %s)"
        )
        rows = tuple(self.__to_row(analysis_id, image_id, csv) for csv in csvs)
        with self.__connection.cursor() as cursor:
            cursor.executemany(query, rows)
            self.__connection.commit()

    def __to_row(self, analysis_id, image_id, csv) -> Tuple[bytes, bytes, bytes, Any, Any, Any]:
        return (uuid4().bytes, UUID(analysis_id).bytes, UUID(image_id).bytes,
                csv.name, csv.size, csv.to_bytes())

    def save_all(self, csvs: Iterable[Iterable[Csv]], analysis_id: str, image_ids: List[str]) -> None:
        query = (
            "insert into output_csv (id, analysis_id, image_id, name, size, content) "
            "values (%s, %s, %s, %s, %s, %s)"
        )

        rows = tuple(self.__to_row(analysis_id, image_id, csv)
                     for image_id, entry_csvs in zip(image_ids, csvs)
                     for csv in entry_csvs)

        with self.__connection.cursor() as cursor:
            cursor.executemany(query, rows)
            self.__connection.commit()
