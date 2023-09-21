from io import BytesIO

import numpy as np
from PIL import Image as PILImage
from magic import from_buffer


class Image:

    @staticmethod
    def from_path(path: str, name: str = "") -> 'Image':
        with open(path, "rb") as image:
            name = name or path.split("/")[-1].split(".")[0]
            return Image(image.read(), name)

    @staticmethod
    def from_bytes(content: bytes, name: str = "") -> 'Image':
        return Image(content, name)

    @staticmethod
    def from_PIL_image(image: PILImage.Image, name: str = "") -> 'Image':
        image_bytes_io = BytesIO()
        image.save(image_bytes_io, image.format or "jpeg")
        return Image(image_bytes_io.getvalue(), name)

    @staticmethod
    def from_numpy_array(array: np.ndarray, name: str = "") -> 'Image':
        return Image.from_PIL_image(PILImage.fromarray(array), name)

    def __init__(self, content: bytes, name: str = "") -> None:
        self.__format = from_buffer(content, mime=True).split("/")[1]
        self.__bytes = content
        self.__name = name

    @property
    def name(self) -> str:
        return self.__name

    @property
    def format(self) -> str:
        return self.__format

    @property
    def size(self) -> int:
        return len(self.__bytes)

    def __str__(self):
        return f"Image(name='{self.name}', format='{self.format}', size={self.size})"

    def __repr__(self):
        return str(self)

    def to_bytes(self) -> bytes:
        return self.__bytes

    def to_PIL_image(self) -> PILImage.Image:
        return PILImage.open(BytesIO(self.__bytes), formats=[self.format])

    def to_numpy_array(self) -> np.ndarray:
        image = self.to_PIL_image()
        (width, height) = image.size
        return np.array(image.getdata()).reshape((height, width, 3)).astype(np.uint8)