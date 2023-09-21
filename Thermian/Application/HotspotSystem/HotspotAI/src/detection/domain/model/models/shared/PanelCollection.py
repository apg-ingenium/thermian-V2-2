from typing import List, Iterable

from src.detection.domain.model.models.shared.ObjectDetectionResults import ObjectDetectionResults
from src.detection.domain.model.models.shared.HotspotCollection import HotspotCollection
from src.detection.domain.model.models.shared.Panel import Panel


class PanelCollection:

    def __init__(self, panels: ObjectDetectionResults, hotspots: List[ObjectDetectionResults]):
        self.__panels = panels
        self.__hotspots = hotspots

    def __iter__(self) -> Iterable[Panel]:
        for (index, score, box, hotspots) in zip(self.ids, self.scores, self.boxes, self.__hotspots):
            yield Panel(index, score, box, hotspots)

    def __len__(self):
        return len(self.__panels.boxes)

    @property
    def ids(self):
        return list(range(1, len(self) + 1))

    @property
    def boxes(self):
        return self.__panels.boxes

    @property
    def scores(self):
        return self.__panels.scores

    @property
    def hotspots(self) -> HotspotCollection:
        return HotspotCollection(self.ids, self.__hotspots)
