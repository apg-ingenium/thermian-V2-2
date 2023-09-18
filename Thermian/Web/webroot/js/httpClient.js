export class HttpClient {

    async makeRequest(url, options) {
        const response = await fetch(url, options);
        const body = await response.json();

        if (!response.ok) {
            throw new Error(body["message"]);
        }

        return body;
    }

    async delete(url, options = {}) {
        options = {
            method: "DELETE",
            headers: {"Accept": "application/json"},
            ...options
        };
        const response = await fetch(url, options);

        if (!response.ok) {
            const body = await response.json();
            return {status: response.status, ...body};
        }

        return {status: response.status};
    }

}
