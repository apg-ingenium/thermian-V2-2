class FindDatasetCommand:

    def __init__(self, dataset_id: str) -> None:
        self.__dataset_id = dataset_id

    @property
    def dataset_id(self) -> str:
        return self.__dataset_id
