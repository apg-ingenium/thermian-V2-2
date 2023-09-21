from abc import ABC, abstractmethod


class HotspotAnalysisRepository(ABC):

    @abstractmethod
    def save(self, analysis_id: str, image_id: str) -> None:
        pass
