from abc import ABC, abstractmethod
from typing import List


class DatasetRepository(ABC):

    @abstractmethod
    def find_dataset_image_ids(self, dataset_id: str) -> List[str]:
        pass
