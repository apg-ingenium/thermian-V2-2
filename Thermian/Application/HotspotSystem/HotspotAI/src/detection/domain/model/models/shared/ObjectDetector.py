import numpy as np
import tensorflow.compat.v1 as tf

from src.detection.domain.model.models.shared.ObjectDetectionResults import ObjectDetectionResults


class ObjectDetector:

    def __init__(self, graph_file: str):
        self.__graph = self.__load_graph(graph_file)

    @staticmethod
    def __load_graph(graph_file_path: str) -> tf.Graph:
        graph = tf.Graph()
        with graph.as_default():
            with tf.gfile.GFile(graph_file_path, 'rb') as graph_file_path:
                graph_definition = tf.GraphDef()
                graph_definition.ParseFromString(graph_file_path.read())
                tf.import_graph_def(graph_definition, name='')
        return graph

    def evaluate(self, image: np.ndarray) -> ObjectDetectionResults:
        with self.__graph.as_default():
            with tf.Session(graph=self.__graph) as session:
                (boxes, scores, classes) = session.run(
                    ['detection_boxes:0', 'detection_scores:0', 'detection_classes:0'],
                    feed_dict={'image_tensor:0': np.expand_dims(image, axis=0)}  # shape: [1, None, None, 3]
                )

        confident = scores > 0.7
        confident_detection_scores = scores[confident]
        confident_detection_boxes = boxes[confident]
        confident_detection_classes = classes[confident]

        image_size = np.tile(image.shape[0:2], 2)
        confident_detection_boxes = (confident_detection_boxes * image_size).astype(int)
        confident_detection_classes = confident_detection_classes.astype(int)

        return ObjectDetectionResults({
            "boxes": confident_detection_boxes,
            "scores": confident_detection_scores,
            "classes": confident_detection_classes
        })
