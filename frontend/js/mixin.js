const INTERNAL_ERROR_MESSAGE = "Внутренняя ошибка сервера";

export default {
    methods: {
        handleError(error) {
            let message = null;
            if (error !== null) {
                if (typeof error === "string") {
                    message = error;
                } else if (error instanceof Error) {
                    message = error.message;
                } else if (error.data) {
                    message = error.data;
                } else {
                    message = error.body;
                }
            }
            if (!message) {
                message = INTERNAL_ERROR_MESSAGE;
            }
            this.$alert(message, 'Ошибка', {dangerouslyUseHTMLString: true, type: "error"});
            return {status: 4};
        },
        // GET-запрос, с обработкой ошибок
        async get(url, params) {
            try {
                let response = await this.$http.get(url, params);
                if (response instanceof Error) {
                    return this.handleError(response);
                }
                if (response.status == null) {
                    response.status = 0;
                }
                return response.data;
            } catch (error) {
                return this.handleError(error);
            }
        },
        // GET-запрос, ошибки прокидываются дальше
        async getWithErrors(url, params) {
            let response = await this.$http.get(url, params);
            if (response instanceof Error) {
                throw new Error(response.message ? response.message : INTERNAL_ERROR_MESSAGE);
            }
            if (response.status == null) {
                response.status = 0;
            }
            return response.data;
        },
        // POST-запрос, с обработкой ошибок
        async post(url, body, params) {
            try {
                let response = await this.$http.post(url, body, params);
                if (response instanceof Error) {
                    return this.handleError(response);
                }
                if (response.status == null) {
                    response.status = 0;
                }
                return response.data;
            } catch (error) {
                return this.handleError(error);
            }
        },
        // POST-запрос с прокидыванием ошибок
        async postWithErrors(url, body, params) {
            let response = await this.$http.post(url, body, params);
            if (response instanceof Error) {
                throw new Error(INTERNAL_ERROR_MESSAGE);
            }
            if (response.status == null) {
                response.status = 0;
            }
            return response.data;
        },

        findInArrayById(array, itemId) {
            return _.find(array, {id: itemId});
        },
    }
}

export const isNumberMethod = {
    methods: {
        isNumber(evt) {
            evt = (evt) ? evt : window.event;
            const charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode < 48 || charCode > 57) {
                evt.preventDefault();
            } else {
                return true;
            }
        }
    }
};