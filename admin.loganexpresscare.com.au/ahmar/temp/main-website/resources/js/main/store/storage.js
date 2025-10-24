export const getValueFromStorage = (keyName, defaultValue) => {
    const value = window.localStorage.getItem(keyName);

    if (value === 'undefined' || value === 'null' || value === undefined || value === null) {
        return defaultValue;
    } else {
        return value;
    }
};

export const getValueFromJsonStorage = (keyName, defaultValue = null) => {
    const value = window.localStorage.getItem(keyName);

    if (value === 'undefined' || value === 'null' || value === undefined || value === null) {
        return defaultValue;
    } else {
        return JSON.parse(value);
    }
};

export const setValueInStorage = (keyName, defaultValue) => {
    if (defaultValue == '' || defaultValue == undefined || defaultValue == null) {
        window.localStorage.removeItem(keyName);
    } else {
        window.localStorage.setItem(keyName, defaultValue);
    }
};

export const setJSONValueInStorage = (keyName, defaultValue) => {
    if (defaultValue == '' || defaultValue == undefined || defaultValue == null) {
        window.localStorage.removeItem(keyName);
    } else {
        window.localStorage.setItem(keyName, JSON.stringify(defaultValue));
    }
};
