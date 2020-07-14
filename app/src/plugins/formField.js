import { fieldFormValidation } from '../utils/functions.js';

const FormFields = {
    install(Vue) {
        Vue.mixin({
            methods: {
                isValidField(formContent, getFormContent) {
                    return fieldFormValidation(formContent, getFormContent);
                },
            },
        });
    },
};

export default FormFields;
