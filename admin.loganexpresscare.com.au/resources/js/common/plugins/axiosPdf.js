import axios from 'axios';
import { message } from "ant-design-vue";

var axiosPdf = axios.create({
    baseURL: window.config.path + '/api/v1'
});

// Axios default headers
axiosPdf.defaults.headers['Accept'] = 'application/json';
axiosPdf.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axiosPdf.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;

// Axios jwt token by default
if (localStorage.getItem('auth_token')) {
    axiosPdf.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('auth_token');
}
// Axios error listener
axiosPdf.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    const errorCode = error.response.status;

    if (errorCode === 401) {
        // If error 401 redirect to login
        window.location.href = window.config.path + '/admin/login';
        delete window.axiosPdf.defaults.headers.common.Authorization;
        localStorage.removeItem('auth_token');
        localStorage.setItem('auth_user', null);
    } else if (errorCode === 400) {
        var errMessage = error.response.data.error.message;
        message.error(errMessage);
    } else if (errorCode === 403) {
        var errMessage = error.response.data.error.message;
        message.error(errMessage);
    } else if (errorCode === 404) {
        var errMessage = error.response.data.error.message;
        message.error(errMessage);
    }

    return Promise.reject(error.response);
});

/**
 * Set global so you don't have to import it
 */
window.axiosPdf = axiosPdf;
