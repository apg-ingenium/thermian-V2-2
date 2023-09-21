class FindImageCommand:

    def __init__(self, image_id: str) -> None:
        self.__image_id = image_id

    @property
    def image_id(self) -> str:
        return self.__image_id
