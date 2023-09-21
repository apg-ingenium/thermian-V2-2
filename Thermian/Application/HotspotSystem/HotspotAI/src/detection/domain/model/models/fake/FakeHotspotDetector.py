from typing import List, Iterable

import numpy as np

from src.detection.domain.files.Image import Image
from src.detection.domain.model.Detector import Detector
from src.detection.domain.model.models.shared.HotspotDetectionResults import HotspotDetectionResults
from src.detection.domain.model.models.shared.ObjectDetectionResults import ObjectDetectionResults


class FakeHotspotDetector(Detector):

    def evaluate(self, dataset: Iterable[Image]) -> List[HotspotDetectionResults]:
        return list(map(self.__generate_random_hotspot_detection_results, dataset))

    def __generate_random_hotspot_detection_results(self, image: Image) -> HotspotDetectionResults:
        [image_width, image_height] = image.to_PIL_image().size
        panels = self.__generate_random_panels(0, 0, image_height, image_width)
        hotspots = self.__generate_random_hotspots(panels)
        return HotspotDetectionResults(image.name, panels, hotspots)

    @staticmethod
    def __generate_random_panels(y_min, x_min, y_max, x_max) -> ObjectDetectionResults:
        return FakeHotspotDetector.__generate_random_object_detection_results(
            y_min, x_min, y_max, x_max, num_rows=2, num_cols=4,
            min_size_factor=[0.6, 0.3], max_size_factor=[0.9, 0.6],
            keep_factor=0.7
        )

    @staticmethod
    def __generate_random_hotspots(panels: ObjectDetectionResults) -> List[ObjectDetectionResults]:
        return [
            FakeHotspotDetector.__generate_random_object_detection_results(
                y_min, x_min, y_max, x_max, num_rows=10, num_cols=5,
                min_size_factor=[0.3, 0.3], max_size_factor=[0.6, 0.6],
                keep_factor=0.15
            )
            for (y_min, x_min, y_max, x_max) in panels.boxes
        ]

    @staticmethod
    def __generate_random_object_detection_results(
            y_min, x_min, y_max, x_max, num_rows, num_cols,
            min_size_factor, max_size_factor, keep_factor
    ) -> ObjectDetectionResults:

        min_size_factor = np.array(min_size_factor)
        max_size_factor = np.array(max_size_factor)

        x_coordinates = np.linspace(x_min, x_max, num_cols + 1)
        y_coordinates = np.linspace(y_min, y_max, num_rows + 1)
        grid = np.array(np.meshgrid(y_coordinates, x_coordinates)).T

        surviving = np.random.random(num_rows * num_cols) < keep_factor
        num_boxes = np.sum(surviving)

        start = grid[:-1, :-1].reshape(-1, 2)[surviving]
        end = grid[1:, 1:].reshape(-1, 2)[surviving]
        size = end - start

        r1 = min_size_factor + (max_size_factor - min_size_factor) * np.random.random((num_boxes, 2))
        r2 = np.random.random((num_boxes, 1)) * (1 - r1)

        box_size = r1 * size
        box_offset = r2 * size
        box_start = start + box_offset
        box_end = box_start + box_size

        boxes = np.round(np.concatenate([box_start, box_end], axis=1)).astype(int)
        scores = np.random.random(num_boxes)
        labels = np.ones(num_boxes, dtype=int)

        return ObjectDetectionResults({"boxes": boxes, "scores": scores, "classes": labels})
