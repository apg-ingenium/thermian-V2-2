from typing import List

from src.detection.domain.model.models.shared.HotspotCollection import HotspotCollection
from src.detection.domain.model.models.shared.ObjectDetectionResults import ObjectDetectionResults
from src.detection.domain.model.models.shared.PanelCollection import PanelCollection


class HotspotDetectionResults:

    def __init__(self, image_name: str, panels: ObjectDetectionResults, hotspots: List[ObjectDetectionResults]):
        self.image_name = image_name
        self.__panels = PanelCollection(panels, hotspots)

    @property
    def name(self) -> str:
        return self.image_name

    @property
    def panels(self) -> PanelCollection:
        return self.__panels

    @property
    def hotspots(self) -> HotspotCollection:
        return self.panels.hotspots
