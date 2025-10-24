<template>
    <template v-if="$slots.custom">
        <slot name="custom" :load="loading" :download="downloadPdf"> </slot>
    </template>
    <template v-else-if="$slots.icon">
        <a-button type="primary" @click="downloadPdf" :loading="loading">
            <template #icon><slot name="icon" /></template>
            {{ title }}
        </a-button>
    </template>
    <template v-else-if="!buttonType">
        <a-button type="primary" @click="downloadPdf" :loading="loading">
            <template #icon><DownloadOutlined /></template>
            {{ title }}
        </a-button>
    </template>
    <template v-if="buttonType">
        <a-typography-link @click="downloadPdf" :loading="loading">
            {{ title }}
        </a-typography-link>
    </template>
</template>

<script>
import { ref, watch } from "vue";
import { FilePdfOutlined, DownloadOutlined } from "@ant-design/icons-vue";
import print from "print-js";

export default {
    props: {
        title: {
            default: "",
        },
        url: {
            default: "generate-pdf",
        },
        fileName: {
            default: "file-pdf",
        },
        payload: {
            default: {},
        },
        isPrint: {
            default: false,
        },
        validate: {
            default: false,
        },
        buttonType: {
            default: false,
        },
    },
    components: {
        FilePdfOutlined,
        DownloadOutlined,
    },

    emits: ["downloaded", "addSuccess"],

    setup(props, { emit }) {
        const rule = ref({});
        const loading = ref(false);

        const downloadPdf = () => {
            loading.value = true;
            var docType = "";
            var type = "";
            var isValid = true;
            if (props.validate == true) {
                isValid = validateForm();
            }
            if (isValid) {
                if (props.payload.type === "csv") {
                    docType = "text/csv";
                    type = "csv";
                } else if (props.payload.type === "xlsx") {
                    docType =
                        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                    type = "xlsx";
                } else {
                    docType = "application/pdf";
                    type = "pdf";
                }

                return axiosPdf
                    .post(props.url, props.payload, {
                        responseType: "blob",
                    })
                    .then((response) => {
                        // Create a blob from the response data
                        const blob = new Blob([response.data], {
                            type: docType,
                        });

                        // Create a temporary URL for the blob
                        const url = window.URL.createObjectURL(blob);

                        if (props.isPrint) {
                            print(url);
                        } else {
                            // Create a temporary link element
                            const link = document.createElement("a");
                            link.href = url;

                            // Set the link attributes to trigger download
                            link.setAttribute(
                                "download",
                                `${props.fileName}.${type}`
                            );
                            document.body.appendChild(link);

                            // Simulate click on the link to start the download
                            link.click();

                            // Clean up by removing the temporary link and URL object
                            document.body.removeChild(link);
                        }

                        window.URL.revokeObjectURL(url);

                        loading.value = false;

                        emit("downloaded");
                    });
            } else {
                loading.value = false;
                emit("addSuccess", rule.value);
            }
        };

        const validateForm = () => {
            rule.value = {};
            let isValid = true;

            if (props.payload) {
                // Only require xid for account-related downloads
                if (
                    (props.url === "account-csv" ||
                        props.url === "account-xlsx" ||
                        props.url === "account-pdf") &&
                    !props.payload.xid
                ) {
                    rule.value["account_id"] = {
                        message: "account_id field is required",
                    };
                    isValid = false;
                }

                if (
                    "date" in props.payload &&
                    Array.isArray(props.payload.date) &&
                    props.payload.date.length === 0
                ) {
                    rule.value["date"] = {
                        message: "date range field is required",
                    };
                    isValid = false;
                }
            }
            return isValid;
        };

        return {
            downloadPdf,
            loading,
            rule,
        };
    },
};
</script>
<style></style>
