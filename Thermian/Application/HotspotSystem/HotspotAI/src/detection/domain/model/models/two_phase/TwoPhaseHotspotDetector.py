from typing import List, Iterable

from src.detection.domain.files.Image import Image
from src.detection.domain.model.Detector import Detector
from src.detection.domain.model.models.shared.HotspotDetectionResults import HotspotDetectionResults
from src.detection.domain.model.models.shared.ObjectDetector import ObjectDetector


class TwoPhaseHotspotDetector(Detector):

    def __init__(self, panel_detector: ObjectDetector, hotspot_detector: ObjectDetector):
        self.__panel_detector = panel_detector
        self.__hotspot_detector = hotspot_detector

    def evaluate(self, dataset: Iterable[Image]) -> List[HotspotDetectionResults]:
        results = []
        for image in dataset:
            image_array = image.to_numpy_array()
            panels = self.__panel_detector.evaluate(image_array)

            hotspots = []
            for panel_box in panels.boxes:
                panel_crop = self.__crop(image_array, panel_box)
                panel_hotspots = self.__hotspot_detector.evaluate(panel_crop)
                panel_hotspots = panel_hotspots.offset_boxes_by(panel_box)
                hotspots.append(panel_hotspots)

            results.append(HotspotDetectionResults(image.name, panels, hotspots))

        return results

    @staticmethod
    def __crop(image, box):
        return image[box[0]:box[2], box[1]:box[3]]
