import os
from typing import List, Iterable
from uuid import UUID

from mysql.connector import connect

from src.dataset.domain.input_image_repository.InputImageRepository import InputImageRepository
from src.detection.domain.files.Image import Image


class MySQLInputImageRepository(InputImageRepository):

    def __init__(self):
        self.__connection = connect(
            host=os.getenv("MYSQL_HOST"),
            user=os.getenv("MYSQL_USER"),
            password=os.getenv("MYSQL_PASSWORD"),
            database=os.getenv("MYSQL_DATABASE")
        )

    def find(self, image_id: str) -> Image:
        find_image = "select id, content from image where id = %s"
        values = [UUID(image_id).bytes]
        with self.__connection.cursor() as cursor:
            cursor.execute(find_image, values)
            image_id, content = cursor.fetchone()

        return Image.from_bytes(content)

    def find_all(self, image_ids: List[str]) -> Iterable[Image]:
        inputs = ", ".join(["%s"]*len(image_ids))
        find_image = f"select id, content from image where id in ({inputs})"
        values = [UUID(image_id).bytes for image_id in image_ids]
        with self.__connection.cursor() as cursor:
            cursor.execute(find_image, values)
            rows = cursor.fetchall()

        return map(lambda row: Image.from_bytes(row[1]), rows)

    def save(self, image: Image, image_id: str):
        insert_image = "insert into image (id, format, size, content) values (%s, %s, %s, %s)"
        values = [UUID(image_id).bytes, image.format, image.size, image.to_bytes()]
        with self.__connection.cursor() as cursor:
            cursor.execute(insert_image, values)
            self.__connection.commit()
