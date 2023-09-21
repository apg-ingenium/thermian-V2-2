class ImageHotspotAnalysisCommand:

    def __init__(self, analysis_id: str, image_id: str, model_name: str, model_config: dict) -> None:
        self.__analysis_id = analysis_id
        self.__image_id = image_id
        self.__model_name = model_name
        self.__model_config = model_config

    @property
    def analysis_id(self) -> str:
        return self.__analysis_id

    @property
    def image_id(self) -> str:
        return self.__image_id

    @property
    def model_name(self) -> str:
        return self.__model_name

    @property
    def model_config(self) -> dict:
        return self.__model_config
