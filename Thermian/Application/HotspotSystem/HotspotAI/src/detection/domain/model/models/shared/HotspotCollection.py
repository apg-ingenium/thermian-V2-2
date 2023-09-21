from typing import List, Iterable

import numpy as np

from src.detection.domain.model.models.shared.ObjectDetectionResults import ObjectDetectionResults
from src.detection.domain.model.models.shared.Hotspot import Hotspot


class HotspotCollection:

    def __init__(self, panel_ids: List[int], hotspots: List[ObjectDetectionResults]):
        self.__panel_ids = panel_ids
        self.__hotspots = hotspots

    def __iter__(self) -> Iterable[Hotspot]:
        for (panel_id, panel_hotspots) in zip(self.__panel_ids, self.__hotspots):
            for hotspot_index, (score, box) in enumerate(zip(panel_hotspots.scores, panel_hotspots.boxes)):
                yield Hotspot(hotspot_index+1, panel_id, score, box)

    @property
    def panel_ids(self):
        return np.concatenate([np.repeat(panel_id, len(hotspots)) for (panel_id, hotspots) in zip(self.__panel_ids, self.__hotspots)] or [[]], axis=0)

    @property
    def ids(self):
        return np.concatenate([np.arange(1, len(hotspots) + 1) for hotspots in self.__hotspots] or [[]], axis=0)

    @property
    def boxes(self):
        return np.concatenate([hotspots.boxes for hotspots in self.__hotspots] or [[]], axis=0)

    @property
    def scores(self):
        return np.concatenate([hotspots.scores for hotspots in self.__hotspots] or [[]], axis=0)