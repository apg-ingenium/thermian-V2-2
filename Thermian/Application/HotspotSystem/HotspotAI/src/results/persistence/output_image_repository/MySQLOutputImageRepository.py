import os
import uuid
from typing import Iterable, Tuple, List
from uuid import UUID

from mysql.connector import connect

from src.detection.domain.files.Image import Image
from src.results.domain.output_image_repository.OutputImageRepository import OutputImageRepository


class MySQLOutputImageRepository(OutputImageRepository):

    def __init__(self) -> None:
        self.__connection = connect(
            host=os.getenv("MYSQL_HOST"),
            user=os.getenv("MYSQL_USER"),
            password=os.getenv("MYSQL_PASSWORD"),
            database=os.getenv("MYSQL_DATABASE")
        )

    def save(self, images: Iterable[Image], analysis_id: str, image_id: str) -> None:
        query = (
            "insert into output_image (id, analysis_id, image_id, name, format, size, content) "
            "values (%s, %s, %s, %s, %s, %s, %s)"
        )
        rows = tuple(self.__to_row(analysis_id, image_id, image) for image in images)
        with self.__connection.cursor() as cursor:
            cursor.executemany(query, rows)
            self.__connection.commit()

    @staticmethod
    def __to_row(analysis_id: str, image_id: str, image: Image) -> Tuple[bytes, bytes, bytes, str, str, int, bytes]:
        return (uuid.uuid4().bytes, UUID(analysis_id).bytes, UUID(image_id).bytes,
                image.name, image.format, image.size, image.to_bytes())

    def save_all(self, images: Iterable[Iterable[Image]], analysis_id: str, image_ids: List[str]) -> None:
        query = (
            "insert into output_image (id, analysis_id, image_id, name, format, size, content) "
            "values (%s, %s, %s, %s, %s, %s, %s)"
        )

        rows = tuple(self.__to_row(analysis_id, image_id, image)
                     for image_id, entry_images in zip(image_ids, images)
                     for image in entry_images)

        with self.__connection.cursor() as cursor:
            cursor.executemany(query, rows)
            self.__connection.commit()
