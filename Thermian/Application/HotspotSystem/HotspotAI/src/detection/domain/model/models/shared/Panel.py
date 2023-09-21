from typing import List

from src.detection.domain.model.models.shared.ObjectDetectionResults import ObjectDetectionResults
from src.detection.domain.model.models.shared.HotspotCollection import HotspotCollection


class Panel:

    def __init__(self, id: int, score: float, box: List[int], hotspots: ObjectDetectionResults):
        self.__id = id
        self.__score = score
        self.__box = box
        self.__hotspots = hotspots

    @property
    def id(self) -> int:
        return self.__id

    @property
    def score(self) -> float:
        return self.__score

    @property
    def box(self) -> List[int]:
        return self.__box

    @property
    def hotspots(self) -> HotspotCollection:
        return HotspotCollection([self.id], [self.__hotspots])

    def __str__(self) -> str:
        return f"Panel(id: {self.id}, score: {self.score}, box: {self.box})"
