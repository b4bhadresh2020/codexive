const axios = require('axios').default;

const defaultHeaders = {
    isAuth: true,
    AdditionalParams: {},
    isJsonRequest: true
};



const ApiPostNoAuth = (url, userData) => {

    return new Promise((resolve, reject) => {
        axios.post(url, userData)
            .then((responseJson) => {
                resolve(responseJson);
            })
            .catch((error) => {
                if (error && error.hasOwnProperty('response') &&
                    error.response && error.response.hasOwnProperty('data') && error.response.data &&
                    error.response.data.hasOwnProperty('error') && error.response.data.error) {
                    reject(error.response.data.error);
                } else {
                    reject(error.error);
                }
            });
    });
}

const ApiGetNoAuth = (url) => {
    return new Promise((resolve, reject) => {
        axios.get(url)
            .then((responseJson) => {
                resolve(responseJson);
            })
            .catch((error) => {
                if (error && error.hasOwnProperty('response') &&
                    error.response && error.response.hasOwnProperty('data') && error.response.data &&
                    error.response.data.hasOwnProperty('error') && error.response.data.error) {
                    reject(error.response.data.error);
                } else {
                    reject(error);
                }
            });
    });
}

const ApiGet = (url) => {
    return new Promise((resolve, reject) => {
        axios.get(url)
            .then((responseJson) => {
                resolve(responseJson);
            })
            .catch((error) => {
                if (error && error.hasOwnProperty('response') &&
                    error.response && error.response.hasOwnProperty('data') && error.response.data &&
                    error.response.data.hasOwnProperty('error') && error.response.data.error) {
                    reject(error.response.data.error);
                } else {
                    reject(error);
                }
            });
    });
}

const ApiPost = (url, userData) => {
    return new Promise((resolve, reject) => {
        axios.post(url, userData)
            .then((responseJson) => {
                resolve(responseJson);
            })
            .catch((error) => {
                if (error && error.hasOwnProperty('response') &&
                    error.response && error.response.hasOwnProperty('data') && error.response.data &&
                    error.response.data.hasOwnProperty('error') && error.response.data.error) {
                    reject(error.response.data.error);
                } else {
                    reject(error);
                }
            });
    });
}

export default { ApiPostNoAuth, ApiGetNoAuth, ApiGet, ApiPost };
