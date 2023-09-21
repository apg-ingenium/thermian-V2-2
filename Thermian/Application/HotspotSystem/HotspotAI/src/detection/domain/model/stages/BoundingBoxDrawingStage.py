from typing import Iterable, List

from src.detection.domain.files.FileCollection import FileCollection
from src.detection.domain.files.Image import Image
from src.detection.domain.model.Stage import Stage
from src.detection.domain.model.models.shared import HotspotCollection
from src.detection.domain.model.models.shared.HotspotDetectionResults import HotspotDetectionResults
from src.detection.domain.model.models.shared import PanelCollection
from src.detection.domain.model.stages.visualization_utils import visualize_boxes_and_labels_on_image_array


class BoundingBoxDrawingStage(Stage):

    def process(self, dataset: Iterable[Image], results: Iterable[HotspotDetectionResults]) -> List[FileCollection]:
        return [self.__draw(image, result) for (image, result) in zip(dataset, results)]

    def __draw(self, image: Image, result: HotspotDetectionResults):
        output_image = image.to_numpy_array().copy()
        self.__draw_panels(output_image, result.panels)
        self.__draw_hotspots(output_image, result.hotspots)
        output_image = Image.from_numpy_array(output_image, name=f"bounding-boxes.{image.format}")
        return FileCollection.containing(("bounding-boxes", output_image))

    def __draw_panels(self, image, panels: PanelCollection):
        labels = {index: {"id": index, "name": f"Panel {index}"} for index in panels.ids}
        visualize_boxes_and_labels_on_image_array(
            image,
            panels.boxes,
            panels.ids,
            panels.scores,
            labels,
            min_score_thresh=0.0,
            max_boxes_to_draw=None,
            use_normalized_coordinates=False,
            line_thickness=3,
            color="black",
            text_color="white"
        )

    def __draw_hotspots(self, image, hotspots: HotspotCollection):
        labels = {index: {"id": index, "name": f"HS {index}"} for index in hotspots.ids}
        visualize_boxes_and_labels_on_image_array(
            image,
            hotspots.boxes,
            hotspots.ids,
            hotspots.scores,
            labels,
            min_score_thresh=0.0,
            max_boxes_to_draw=None,
            use_normalized_coordinates=False,
            line_thickness=3,
            color="chartreuse",
            text_color="black"
        )
