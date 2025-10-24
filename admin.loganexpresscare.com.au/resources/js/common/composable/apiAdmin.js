import { ref } from "vue";
import { useI18n } from "vue-i18n";
import { forEach, find, some, includes } from "lodash-es";
import { message, notification } from "ant-design-vue";
import common from "../composable/common";

const api = (defaultTabName = "") => {
    const loading = ref(false);
    const rules = ref({});
    const { t } = useI18n();
    const { appSetting } = common();
    const addEditActiveTab = ref(defaultTabName);

    const extractFormItemNames = (formItems) => {
        return Array.from(formItems).map((item) =>
            item
                .querySelector("label")
                .getAttribute("for")
                .replace("form_item_", "")
                .trim()
        );
    };

    // Function to find the common prefix in an array of strings
    const findCommonPrefix = (strings) => {
        if (strings.length === 0) return "";

        // Split each string into components using '-' as the delimiter
        const splitStrings = strings.map((str) => str.split("-"));

        // Find the shortest string for comparison purposes
        const minLength = Math.min(
            ...splitStrings.map((parts) => parts.length)
        );

        let prefix = [];

        // Compare components at each position
        for (let i = 0; i < minLength; i++) {
            const currentPart = splitStrings[0][i]; // Take the part from the first string
            if (splitStrings.every((parts) => parts[i] === currentPart)) {
                prefix.push(currentPart); // Add to prefix if all strings match
            } else {
                break; // Stop at the first mismatch
            }
        }

        // Join the common components back into a string
        return prefix.join("-") + "-";
    };

    const getAllFormFields = (idName) => {
        // Select the parent form
        const formElement = document.getElementById(idName);

        // Find all tab panes dynamically
        const tabPanes = formElement.querySelectorAll(".ant-tabs-tabpane");
        // Extract all IDs into an array
        const ids = Array.from(tabPanes)
            .filter((pane) => {
                const formItems = pane.querySelectorAll(".ant-form-item-label");

                return extractFormItemNames(formItems).length > 0;
            })
            .map((div) => div.id);

        // Find the common prefix dynamically
        const commonPrefix = findCommonPrefix(ids);

        const tabData = Array.from(tabPanes)
            .filter((pane) => {
                const formItems = pane.querySelectorAll(".ant-form-item-label");

                return extractFormItemNames(formItems).length > 0;
            })
            .map((pane) => {
                const tabClassName = pane.getAttribute("class");
                const isActiveTab = tabClassName.includes(
                    "ant-tabs-tabpane-active"
                )
                    ? true
                    : false;

                // Remove the common prefix
                const tabFullName = pane.getAttribute("id");
                const tabName = tabFullName.replace(commonPrefix, "");

                const formItems = pane.querySelectorAll(".ant-form-item-label");

                return {
                    tab: tabName,
                    fields: extractFormItemNames(formItems),
                    active: isActiveTab,
                };
            });

        return tabData;
    };

    const addEditRequestAdmin = (configObject) => {
        loading.value = true;
        const { url, data, success } = configObject;
        var formData = {};

        // Replace undefined values to null
        forEach(data, function (value, key) {
            if (value == undefined) {
                formData[key] = null;
            } else {
                formData[key] = value;
            }
        });

        axiosAdmin
            .post(url, formData)
            .then((response) => {
                // Toastr Notificaiton
                if (configObject.successMessage) {
                    notification.success({
                        placement: appSetting.value.rtl
                            ? "bottomLeft"
                            : "bottomRight",
                        message: t("common.success"),
                        description: configObject.successMessage,
                    });
                }

                success(response.data);
                loading.value = false;
                rules.value = {};
            })
            .catch((errorResponse) => {
                var err = errorResponse.data;
                const errorCode = errorResponse.status;
                var errorRules = {};

                if (errorCode == 422) {
                    if (err.error && typeof err.error.details != "undefined") {
                        var keys = Object.keys(err.error.details);
                        for (var i = 0; i < keys.length; i++) {
                            // Escape dot that comes with error in array fields
                            // var key = keys[i].replace(".", "\\.");
                            var key = keys[i];

                            errorRules[key] = {
                                required: true,
                                message: err.error.details[keys[i]][0],
                            };
                        }
                    }

                    rules.value = errorRules;
                    message.error(t("common.fix_errors"));
                }

                if (err && err.message) {
                    message.error(err.message);
                    err = {
                        error: {
                            ...err,
                        },
                    };
                }

                if (configObject.error) {
                    configObject.error(err);
                }

                if (configObject.id) {
                    const allFormFields = getAllFormFields(configObject.id);
                    const errorObjectKeyArray = Object.keys(rules.value);

                    const activeTabFields = find(allFormFields, [
                        "active",
                        true,
                    ]);

                    const inActiveTabFields = find(
                        allFormFields,
                        (tabField) =>
                            !tabField.active &&
                            some(errorObjectKeyArray, (value) =>
                                includes(tabField.fields, value)
                            )
                    );

                    if (
                        activeTabFields != undefined &&
                        some(errorObjectKeyArray, (value) =>
                            includes(activeTabFields.fields, value)
                        )
                    ) {
                        addEditActiveTab.value = activeTabFields.tab;
                    } else if (inActiveTabFields != undefined) {
                        addEditActiveTab.value = inActiveTabFields.tab;
                    }
                }

                loading.value = false;
            });
    };

    const addEditFileRequestAdmin = (configObject) => {
        loading.value = true;
        const { url, data, success } = configObject;
        const formData = new FormData();

        // Replace undefined values to null
        forEach(data, function (value, key) {
            if (value == undefined) {
                formData.append(key, null);
            } else if (key == "file") {
                formData.append(key, value.originFileObj);
            } else {
                formData.append(key, value);
            }
        });

        axiosAdmin
            .post(url, formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            })
            .then((response) => {
                // Toastr Notificaiton
                if (configObject.successMessage) {
                    notification.success({
                        placement: appSetting.value.rtl
                            ? "bottomLeft"
                            : "bottomRight",
                        message: t("common.success"),
                        description: configObject.successMessage,
                    });
                }

                success(response.data);
                loading.value = false;
                rules.value = {};
            })
            .catch((errorResponse) => {
                var err = errorResponse.data;
                const errorCode = errorResponse.status;
                var errorRules = {};

                if (errorCode == 422) {
                    if (err.error && typeof err.error.details != "undefined") {
                        var keys = Object.keys(err.error.details);
                        for (var i = 0; i < keys.length; i++) {
                            // Escape dot that comes with error in array fields
                            var key = keys[i].replace(".", "\\.");

                            errorRules[key] = {
                                required: true,
                                message: err.error.details[keys[i]][0],
                            };
                        }
                    }

                    rules.value = errorRules;
                    message.error(t("common.fix_errors"));
                }

                if (err && err.message) {
                    message.error(err.message);
                    err = {
                        error: {
                            ...err,
                        },
                    };
                }

                if (configObject.error) {
                    configObject.error(err);
                }

                loading.value = false;
            });
    };

    return {
        loading,
        rules,
        addEditActiveTab,
        addEditRequestAdmin,
        addEditFileRequestAdmin,
    };
};

export default api;
