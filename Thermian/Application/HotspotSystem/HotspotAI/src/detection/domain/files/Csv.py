from os import path
from typing import TextIO


class Csv:

    @staticmethod
    def empty() -> 'Csv':
        return Csv(b"")

    @staticmethod
    def from_path(path_to_csv, name: str = "") -> 'Csv':
        with open(path_to_csv, "rb") as file:
            name = name or path.split("/")[-1].split(".")[0]
            return Csv(file.read(), name)

    @staticmethod
    def from_string(text: str, name: str = "") -> 'Csv':
        return Csv(text.encode(), name)

    @staticmethod
    def from_text_io(text_io: TextIO, name: str = "") -> 'Csv':
        return Csv(str.encode(text_io.read()), name)

    def __init__(self, content: bytes, name: str = "") -> None:
        self.__name = name
        self.__bytes = content

    @property
    def name(self) -> str:
        return self.__name

    @property
    def size(self) -> int:
        return len(self.__bytes)

    def __str__(self):
        return f"Csv(size={self.size})"

    def __repr__(self):
        return str(self)

    def to_bytes(self) -> bytes:
        return self.__bytes
