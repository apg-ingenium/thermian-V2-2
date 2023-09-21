import numpy as np


class ObjectDetectionResults:

    def __init__(self, results):
        self.__results = results

    def __len__(self):
        return self.__results["boxes"].shape[0]

    @property
    def boxes(self):
        return self.__results["boxes"].reshape(-1, 4)

    @property
    def labels(self):
        return self.__results["classes"]

    @property
    def scores(self):
        return self.__results["scores"]

    def offset_boxes_by(self, image_box):
        offset = np.tile(image_box[:2], 2)

        return ObjectDetectionResults({
            "boxes": self.boxes + offset,
            "classes": self.labels,
            "scores": self.scores
        })

    def __str__(self) -> str:
        return f"ObjectDetectionResults(scores: {self.scores}, boxes: {self.boxes}, labels: {self.labels})"
