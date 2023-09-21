import os
from typing import List
from uuid import UUID

from mysql.connector import connect

from src.dataset.domain.dataset_repository.DatasetRepository import DatasetRepository


class MySQLDatasetRepository(DatasetRepository):

    def __init__(self):
        self.__connection = connect(
            host=os.getenv("MYSQL_HOST"),
            user=os.getenv("MYSQL_USER"),
            password=os.getenv("MYSQL_PASSWORD"),
            database=os.getenv("MYSQL_DATABASE")
        )

    def find_dataset_image_ids(self, dataset_id: str) -> List[str]:
        find_image = "select image_id from dataset_image where dataset_id = %s"
        values = [UUID(dataset_id).bytes]
        with self.__connection.cursor() as cursor:
            cursor.execute(find_image, values)
            image_ids = cursor.fetchall()

        return [str(UUID(bytes=bytes(image_id[0]))) for image_id in image_ids]