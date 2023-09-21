from typing import Union, Optional, Tuple, Iterable

from src.detection.domain.files.Csv import Csv
from src.detection.domain.files.Image import Image

File = Union[Image, Csv]


class FileCollection:

    @staticmethod
    def containing(*files: Tuple[str, File]) -> "FileCollection":
        file_collection = FileCollection()
        for (key, file) in files:
            file_collection.add(key, file)
        return file_collection

    def __init__(self):
        self.__collections = {Image.__name__: {}, Csv.__name__: {}}

    def __iter__(self):
        for collection in self.__collections.values():
            yield from collection.items()

    def images(self) -> Iterable[Image]:
        yield from self.__collections[Image.__name__].values()

    def csvs(self) -> Iterable[Csv]:
        yield from self.__collections[Csv.__name__].values()

    def __len__(self) -> int:
        return sum(map(len, self.__collections.values()))

    def add(self, key: str, file: File) -> None:
        collection = self.__collections.get(file.__class__.__name__, None)
        if collection is None:
            raise TypeError("invalid file type")
        collection[key] = file

    def update_with(self, collection: 'FileCollection'):
        for key in self.__collections.keys():
            self.__collections[key].update(collection.__collections[key])

    def get(self, key: str) -> Optional[File]:
        for collection in self.__collections.values():
            file = collection.get(key, None)
            if file:
                return file
        return None

    def get_image(self, key: str) -> Optional[Image]:
        return self.__get_file_of_type(Image.__name__, key)

    def get_csv(self, key: str) -> Optional[Csv]:
        return self.__get_file_of_type(Csv.__name__, key)

    def __get_file_of_type(self, file_type: str, key: str) -> File:
        return self.__collections.get(file_type).get(key, None)

    def __str__(self):
        return str(self.__collections)
