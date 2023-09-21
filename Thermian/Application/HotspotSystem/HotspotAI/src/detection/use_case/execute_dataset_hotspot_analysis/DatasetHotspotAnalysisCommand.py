class DatasetHotspotAnalysisCommand:

    def __init__(self, analysis_id: str, dataset_id: str, model_name: str, model_config: dict) -> None:
        self.__analysis_id = analysis_id
        self.__dataset_id = dataset_id
        self.__model_name = model_name
        self.__model_config = model_config

    @property
    def analysis_id(self) -> str:
        return self.__analysis_id

    @property
    def dataset_id(self) -> str:
        return self.__dataset_id

    @property
    def model_name(self) -> str:
        return self.__model_name

    @property
    def model_config(self) -> dict:
        return self.__model_config
