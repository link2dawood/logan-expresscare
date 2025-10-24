<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('shift.name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="newFormData.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('shift.name'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>

                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('shift.clock_in_time')"
                        name="clock_in_time"
                        :help="
                            rules.clock_in_time
                                ? rules.clock_in_time.message
                                : null
                        "
                        :validateStatus="rules.clock_in_time ? 'error' : null"
                        class="required"
                    >
                        <a-time-range-picker
                            v-model:value="newFormData.time"
                            @change="calculateDifference(newFormData.time)"
                            valueFormat="hh:mm A"
                            format="h:mm a"
                            style="width: 100%"
                            use12-hours
                            :order="false"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('shift.early_clock_in_time')"
                        name="early_clock_in_time"
                        :help="
                            rules.early_clock_in_time
                                ? rules.early_clock_in_time.message
                                : null
                        "
                        :validateStatus="
                            rules.early_clock_in_time ? 'error' : null
                        "
                    >
                        <a-input-number
                            v-model:value="newFormData.early_clock_in_time"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('shift.early_clock_in_time'),
                                ])
                            "
                            style="width: 100%"
                            :disabled="extraTimeDisabled"
                        >
                            <template #addonAfter>
                                {{ $t("common.minutes") }}
                            </template>
                        </a-input-number>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('shift.allow_clock_out_till')"
                        name="allow_clock_out_till"
                        :help="
                            rules.allow_clock_out_till
                                ? rules.allow_clock_out_till.message
                                : null
                        "
                        :validateStatus="
                            rules.allow_clock_out_till ? 'error' : null
                        "
                    >
                        <a-input-number
                            v-model:value="newFormData.allow_clock_out_till"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('shift.allow_clock_out_till'),
                                ])
                            "
                            style="width: 100%"
                            :disabled="extraTimeDisabled"
                        >
                            <template #addonAfter>
                                {{ $t("common.minutes") }}
                            </template>
                        </a-input-number>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('shift.late_mark_after')"
                        name="late_mark_after"
                        :help="
                            rules.late_mark_after
                                ? rules.late_mark_after.message
                                : null
                        "
                        :validateStatus="rules.late_mark_after ? 'error' : null"
                    >
                        <a-input-number
                            v-model:value="newFormData.late_mark_after"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('shift.late_mark_after'),
                                ])
                            "
                            style="width: 100%"
                        >
                            <template #addonAfter>
                                {{ $t("common.minutes") }}
                            </template>
                        </a-input-number>
                    </a-form-item>
                </a-col>

                <a-col :xs="24" :sm="24" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('shift.self_clocking')"
                        name="self_clocking"
                        :help="
                            rules.self_clocking
                                ? rules.self_clocking.message
                                : null
                        "
                        :validateStatus="rules.self_clocking ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="newFormData.self_clocking"
                            button-style="solid"
                            size="small"
                        >
                            <a-radio-button :value="1">
                                {{ $t("common.yes") }}
                            </a-radio-button>
                            <a-radio-button :value="0">
                                {{ $t("common.no") }}
                            </a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>

                <a-col :xs="24" :sm="24" :md="6" :lg="6">
                    <a-form-item
                        name="capture_location"
                        :help="
                            rules.capture_location
                                ? rules.capture_location.message
                                : null
                        "
                        :validateStatus="
                            rules.capture_location ? 'error' : null
                        "
                    >
                        <template #label>
                            <span>
                                {{ $t("shift.capture_location") }}
                                <a-tooltip
                                    :title="
                                        $t(
                                            'attendance.geo_code_api_key_message'
                                        )
                                    "
                                >
                                    <InfoCircleOutlined
                                        style="
                                            color: #1890ff;
                                            cursor: pointer;
                                            margin-left: 4px;
                                        "
                                    />
                                </a-tooltip>
                            </span>
                        </template>
                        <a-radio-group
                            v-model:value="newFormData.capture_location"
                            button-style="solid"
                            size="small"
                        >
                            <a-radio-button :value="1">
                                {{ $t("common.yes") }}
                            </a-radio-button>
                            <a-radio-button :value="0">
                                {{ $t("common.no") }}
                            </a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        name="shift_duration"
                        :help="
                            rules.shift_duration
                                ? rules.shift_duration.message
                                : null
                        "
                        :validateStatus="rules.shift_duration ? 'error' : null"
                    >
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row
                :gutter="16"
                v-for="formField in formFields"
                :key="formField.id"
                style="display: flex; align-items: center"
            >
                <a-col :xs="24" :sm="24" :md="23" :lg="23">
                    <a-form-item
                        :label="$t('shift.allowed_ip_address')"
                        name="allowed_ip_address"
                    >
                        <a-input
                            v-model:value="formField.allowed_ip_address"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('shift.allowed_ip_address'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :span="1" style="margin-top: 6px">
                    <MinusSquareOutlined @click="removeFormField(formField)" />
                </a-col>
            </a-row>
            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <a-form-item>
                    <a-button type="dashed" block @click="addFormField">
                        <PlusOutlined />

                        {{ $t("shift.allowed_ip_address") }}
                    </a-button>
                </a-form-item>
            </a-col>
        </a-form>
        <template #footer>
            <a-space>
                <a-button
                    key="submit"
                    type="primary"
                    :loading="loading"
                    @click="onSubmit"
                >
                    <template #icon>
                        <SaveOutlined />
                    </template>
                    {{
                        addEditType == "add"
                            ? $t("common.create")
                            : $t("common.update")
                    }}
                </a-button>
                <a-button key="back" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, watch } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    MinusSquareOutlined,
    InfoCircleOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";
import dayjs from "dayjs";
import hrmManagement from "../../../../common/composable/hrmManagement";
import { forEach } from "lodash-es";
import { useHrmStore } from "../../../../main/store/hrmStore";

export default defineComponent({
    props: [
        "formData",
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
    ],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        hrmManagement,
        MinusSquareOutlined,
        InfoCircleOutlined,
    },
    setup(props, { emit }) {
        const hrmStore = useHrmStore();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        const { appSetting, disabledDate, permsArray, dayjs } = common();
        const removedIpAddress = ref([]);
        const formFields = ref([]);
        const newFormData = ref({});
        const extraTimeDisabled = ref(false);
        const { formatShiftTime24Hours, formatShiftTime12Hours } =
            hrmManagement();

        const onSubmit = () => {
            rules.value = {};
            var clockInTime = "";
            var clockOutTime = "";

            if (newFormData.value.time.length > 0) {
                clockInTime = formatShiftTime24Hours(newFormData.value.time[0]);
                clockOutTime = formatShiftTime24Hours(
                    newFormData.value.time[1]
                );
            }

            let dataObject = {
                ...newFormData.value,
                clock_in_time: newFormData.value.time[0] ? clockInTime : "",
                clock_out_time: newFormData.value.time[1] ? clockOutTime : "",
                allowed_ip_address: ipAddressFilter(),
            };
            addEditRequestAdmin({
                url: props.url,
                data: dataObject,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                    hrmStore.updateSelfClockingAction();
                },
            });
        };

        const calculateDifference = (timeRange) => {
            const time1 = timeRange[0];
            const time2 = timeRange[1];

            const format = "hh:mm A"; // 12-hour format

            const parsed1 = dayjs(time1, format);
            const parsed2 = dayjs(time2, format);

            const isSame = parsed1.isSame(parsed2, "minute");

            if (isSame) {
                extraTimeDisabled.value = true;
                newFormData.value.early_clock_in_time = 0;
                newFormData.value.allow_clock_out_till = 0;
            } else {
                extraTimeDisabled.value = false;
            }
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const addFormField = () => {
            formFields.value.push({
                allowed_ip_address: "",
            });
        };

        const removeFormField = (item) => {
            let index = formFields.value.indexOf(item);
            if (index !== -1) {
                formFields.value.splice(index, 1);
            }

            if (item.id != "") {
                removedIpAddress.value.push(item.id);
            }
        };

        const ipAddressFilter = () => {
            var newFormField = [];

            forEach(formFields.value, (formField) => {
                if (formField.allowed_ip_address != "") {
                    newFormField.push(formField);
                }
            });

            return newFormField;
        };

        watch(
            () => props.visible,

            (newVal, oldVal) => {
                if (props.addEditType == "edit") {
                    newFormData.value = {
                        ...props.formData,

                        time: [
                            formatShiftTime12Hours(props.data.clock_in_time),
                            formatShiftTime12Hours(props.data.clock_out_time),
                        ],
                    };
                    calculateDifference(newFormData.value.time);

                    var insertIpAddress = [];
                    formFields.value = [];

                    forEach(props.data.allowed_ip_address, (ipAddress) => {
                        insertIpAddress.push({
                            allowed_ip_address: ipAddress.allowed_ip_address,
                        });
                        formFields.value = insertIpAddress;
                    });
                } else {
                    newFormData.value = {
                        ...props.formData,
                        clock_in_time: "",
                        clock_out_time: "",
                    };

                    formFields.value = [];
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            appSetting,
            disabledDate,
            permsArray,
            addFormField,
            removeFormField,
            formFields,
            newFormData,
            // handleTimeChange,
            calculateDifference,
            extraTimeDisabled,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
