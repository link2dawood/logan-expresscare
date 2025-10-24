<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.hrm_settings`)" class="p-0">
                <template
                    v-if="
                        permsArray.includes('hrm_settings') ||
                        permsArray.includes('font_settings') ||
                        permsArray.includes('admin')
                    "
                    #extra
                >
                    <a-space>
                        <a-button type="primary" @click="onSubmit">
                            <template #icon> <SaveOutlined /> </template>
                            {{ $t("common.update") }}
                        </a-button>
                        <span
                            v-if="
                                permsArray.includes('font_settings') ||
                                permsArray.includes('admin')
                            "
                        >
                            <PdfFontSettings btnType="primary">
                                <template #icon>
                                    <SettingOutlined />
                                </template>
                                {{ $t("pdf_font.pdf_settings") }}
                            </PdfFontSettings>
                        </span>
                    </a-space>
                </template>
            </a-page-header>
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t("menu.hrm_settings") }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>
    <a-row>
        <a-col
            :xs="24"
            :sm="24"
            :md="24"
            :lg="4"
            :xl="4"
            class="bg-setting-sidebar"
        >
            <SettingSidebar />
        </a-col>

        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-table-content>
                <a-card class="page-content-container mt-20 mb-20">
                    <a-form layout="vertical">
                        <a-tabs v-model:activeKey="activeKey">
                            <a-tab-pane key="attendance">
                                <template #tab>
                                    <span>
                                        <FileTextOutlined />
                                        {{
                                            $t(
                                                "hrm_settings.attendance_settings"
                                            )
                                        }}
                                    </span>
                                </template>
                                <a-row :gutter="16">
                                    <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                        <a-form-item
                                            :label="
                                                $t(
                                                    'hrm_settings.leave_start_month'
                                                )
                                            "
                                            name="leave_start_month"
                                            :help="
                                                rules.leave_start_month
                                                    ? rules.leave_start_month
                                                          .message
                                                    : null
                                            "
                                            :validateStatus="
                                                rules.leave_start_month
                                                    ? 'error'
                                                    : null
                                            "
                                            class="required"
                                        >
                                            <a-select
                                                v-model:value="
                                                    formData.leave_start_month
                                                "
                                                :placeholder="
                                                    $t(
                                                        'common.select_default_text',
                                                        [
                                                            $t(
                                                                'hrm_settings.leave_start_month'
                                                            ),
                                                        ]
                                                    )
                                                "
                                                style="width: 100%"
                                                optionFilterProp="title"
                                                show-search
                                            >
                                                <a-select-option
                                                    v-for="month in monthArrays"
                                                    :key="month.name"
                                                    :value="month.value"
                                                >
                                                    {{ month.name }}
                                                </a-select-option>
                                            </a-select>
                                        </a-form-item>
                                    </a-col>
                                </a-row>
                                <a-row :gutter="16">
                                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                        <a-form-item
                                            :label="
                                                $t('hrm_settings.clock_in_time')
                                            "
                                            name="clock_in_time"
                                            :help="
                                                rules.clock_in_time
                                                    ? rules.clock_in_time
                                                          .message
                                                    : null
                                            "
                                            :validateStatus="
                                                rules.clock_in_time
                                                    ? 'error'
                                                    : null
                                            "
                                            class="required"
                                        >
                                            <a-time-picker
                                                v-model:value="
                                                    formData.clock_in_time
                                                "
                                                valueFormat="hh:mm:ss A"
                                                style="width: 100%"
                                                use12-hours
                                                :allowClear="false"
                                            />
                                        </a-form-item>
                                    </a-col>
                                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                        <a-form-item
                                            :label="
                                                $t(
                                                    'hrm_settings.clock_out_time'
                                                )
                                            "
                                            name="clock_out_time"
                                            :help="
                                                rules.clock_out_time
                                                    ? rules.clock_out_time
                                                          .message
                                                    : null
                                            "
                                            :validateStatus="
                                                rules.clock_out_time
                                                    ? 'error'
                                                    : null
                                            "
                                            class="required"
                                        >
                                            <a-time-picker
                                                v-model:value="
                                                    formData.clock_out_time
                                                "
                                                valueFormat="h:mm:ss A"
                                                style="width: 100%"
                                                use12-hours
                                                :allowClear="false"
                                            />
                                        </a-form-item>
                                    </a-col>
                                </a-row>
                                <a-row :gutter="16">
                                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                        <a-form-item
                                            :label="
                                                $t(
                                                    'hrm_settings.early_clock_in_time'
                                                )
                                            "
                                            name="early_clock_in_time"
                                            :help="
                                                rules.early_clock_in_time
                                                    ? rules.early_clock_in_time
                                                          .message
                                                    : null
                                            "
                                            :validateStatus="
                                                rules.early_clock_in_time
                                                    ? 'error'
                                                    : null
                                            "
                                        >
                                            <a-input-number
                                                v-model:value="
                                                    formData.early_clock_in_time
                                                "
                                                :placeholder="
                                                    $t(
                                                        'common.placeholder_default_text',
                                                        [
                                                            $t(
                                                                'hrm_settings.early_clock_in_time'
                                                            ),
                                                        ]
                                                    )
                                                "
                                                style="width: 100%"
                                            >
                                                <template #addonAfter>
                                                    {{ $t("common.minutes") }}
                                                </template>
                                            </a-input-number>
                                        </a-form-item>
                                    </a-col>
                                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                        <a-form-item
                                            :label="
                                                $t(
                                                    'hrm_settings.allow_clock_out_till'
                                                )
                                            "
                                            name="allow_clock_out_till"
                                            :help="
                                                rules.allow_clock_out_till
                                                    ? rules.allow_clock_out_till
                                                          .message
                                                    : null
                                            "
                                            :validateStatus="
                                                rules.allow_clock_out_till
                                                    ? 'error'
                                                    : null
                                            "
                                        >
                                            <a-input-number
                                                v-model:value="
                                                    formData.allow_clock_out_till
                                                "
                                                :placeholder="
                                                    $t(
                                                        'common.placeholder_default_text',
                                                        [
                                                            $t(
                                                                'hrm_settings.allow_clock_out_till'
                                                            ),
                                                        ]
                                                    )
                                                "
                                                style="width: 100%"
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
                                            :label="
                                                $t(
                                                    'hrm_settings.late_mark_after'
                                                )
                                            "
                                            name="late_mark_after"
                                            :help="
                                                rules.late_mark_after
                                                    ? rules.late_mark_after
                                                          .message
                                                    : null
                                            "
                                            :validateStatus="
                                                rules.late_mark_after
                                                    ? 'error'
                                                    : null
                                            "
                                        >
                                            <a-input-number
                                                v-model:value="
                                                    formData.late_mark_after
                                                "
                                                :placeholder="
                                                    $t(
                                                        'common.placeholder_default_text',
                                                        [
                                                            $t(
                                                                'hrm_settings.late_mark_after'
                                                            ),
                                                        ]
                                                    )
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
                                            :label="
                                                $t('hrm_settings.self_clocking')
                                            "
                                            name="self_clocking"
                                            :help="
                                                rules.self_clocking
                                                    ? rules.self_clocking
                                                          .message
                                                    : null
                                            "
                                            :validateStatus="
                                                rules.self_clocking
                                                    ? 'error'
                                                    : null
                                            "
                                        >
                                            <a-radio-group
                                                v-model:value="
                                                    formData.self_clocking
                                                "
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
                                                    ? rules.capture_location
                                                          .message
                                                    : null
                                            "
                                            :validateStatus="
                                                rules.capture_location
                                                    ? 'error'
                                                    : null
                                            "
                                        >
                                            <template #label>
                                                <span>
                                                    {{
                                                        $t(
                                                            "hrm_settings.capture_location"
                                                        )
                                                    }}
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
                                                v-model:value="
                                                    formData.capture_location
                                                "
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
                                <a-row
                                    :gutter="16"
                                    v-for="formField in formFields"
                                    :key="formField.id"
                                    style="display: flex; align-items: center"
                                >
                                    <a-col :xs="24" :sm="24" :md="23" :lg="23">
                                        <a-form-item
                                            :label="
                                                $t(
                                                    'hrm_settings.allowed_ip_address'
                                                )
                                            "
                                            name="allowed_ip_address"
                                        >
                                            <a-input
                                                v-model:value="
                                                    formField.allowed_ip_address
                                                "
                                                :placeholder="
                                                    $t(
                                                        'common.placeholder_default_text',
                                                        [
                                                            $t(
                                                                'hrm_settings.allowed_ip_address'
                                                            ),
                                                        ]
                                                    )
                                                "
                                            />
                                        </a-form-item>
                                    </a-col>
                                    <a-col :span="1" style="margin-top: 6px">
                                        <MinusSquareOutlined
                                            @click="removeFormField(formField)"
                                        />
                                    </a-col>
                                </a-row>
                                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                    <a-form-item>
                                        <a-button
                                            type="dashed"
                                            block
                                            @click="addFormField"
                                        >
                                            <PlusOutlined />

                                            {{
                                                $t(
                                                    "hrm_settings.allowed_ip_address"
                                                )
                                            }}
                                        </a-button>
                                    </a-form-item>
                                </a-col>
                            </a-tab-pane>
                            <a-tab-pane key="front_job">
                                <template #tab>
                                    <span>
                                        <SettingOutlined />
                                        {{
                                            $t(
                                                "hrm_settings.front_job_details_settings"
                                            )
                                        }}
                                    </span>
                                </template>
                                <a-row>
                                    <a-col :xs="24" :sm="24" :md="6" :lg="6">
                                        <a-form-item
                                            :label="$t('user.profile_image')"
                                            name="profile_image"
                                            :help="
                                                rules.profile_image
                                                    ? rules.profile_image
                                                          .message
                                                    : null
                                            "
                                            :validateStatus="
                                                rules.profile_image
                                                    ? 'error'
                                                    : null
                                            "
                                        >
                                            <Upload
                                                :formData="formData"
                                                folder="jobDetail"
                                                imageField="profile_image"
                                                @onFileUploaded="
                                                    (file) => {
                                                        formData.profile_image =
                                                            file.file;
                                                        formData.profile_image_url =
                                                            file.file_url;
                                                    }
                                                "
                                            />
                                        </a-form-item>
                                    </a-col>
                                </a-row>
                            </a-tab-pane>

                            <a-tab-pane key="prefix">
                                <template #tab>
                                    <span>
                                        <DragOutlined />
                                        {{
                                            $t(
                                                "hrm_settings.employee_id_prefix_setting"
                                            )
                                        }}
                                    </span>
                                </template>
                                <a-row :gutter="16">
                                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                        <a-form-item
                                            :label="
                                                $t(
                                                    'hrm_settings.employee_id_prefix'
                                                )
                                            "
                                            name="employee_id_prefix"
                                            :help="
                                                rules.employee_id_prefix
                                                    ? rules.employee_id_prefix
                                                          .message
                                                    : null
                                            "
                                            :validateStatus="
                                                rules.employee_id_prefix
                                                    ? 'error'
                                                    : null
                                            "
                                            class="required"
                                        >
                                            <a-input
                                                v-model:value="
                                                    formData.employee_id_prefix
                                                "
                                                :placeholder="
                                                    $t(
                                                        'common.placeholder_default_text',
                                                        [
                                                            $t(
                                                                'hrm_settings.employee_id_prefix'
                                                            ),
                                                        ]
                                                    )
                                                "
                                            />
                                        </a-form-item>
                                    </a-col>
                                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                        <a-form-item
                                            :label="
                                                $t(
                                                    'hrm_settings.employee_id_start'
                                                )
                                            "
                                            name="employee_id_start"
                                            :help="
                                                rules.employee_id_start
                                                    ? rules.employee_id_start
                                                          .message
                                                    : null
                                            "
                                            :validateStatus="
                                                rules.employee_id_start
                                                    ? 'error'
                                                    : null
                                            "
                                            class="required"
                                        >
                                            <a-input
                                                v-model:value="
                                                    formData.employee_id_start
                                                "
                                                :placeholder="
                                                    $t(
                                                        'common.placeholder_default_text',
                                                        [
                                                            $t(
                                                                'hrm_settings.employee_id_start'
                                                            ),
                                                        ]
                                                    )
                                                "
                                            />
                                        </a-form-item>
                                    </a-col>
                                </a-row>
                                <a-row :gutter="16">
                                    <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                        <a-form-item
                                            :label="
                                                $t(
                                                    'hrm_settings.employee_id_digits'
                                                )
                                            "
                                            name="employee_id_digits"
                                            :help="
                                                rules.employee_id_digits
                                                    ? rules.employee_id_digits
                                                          .message
                                                    : null
                                            "
                                            :validateStatus="
                                                rules.employee_id_digits
                                                    ? 'error'
                                                    : null
                                            "
                                            class="required"
                                        >
                                            <a-input-number
                                                v-model:value="
                                                    formData.employee_id_digits
                                                "
                                                :placeholder="
                                                    $t(
                                                        'common.placeholder_default_text',
                                                        [
                                                            $t(
                                                                'hrm_settings.employee_id_digits'
                                                            ),
                                                        ]
                                                    )
                                                "
                                                style="width: 100%"
                                            >
                                            </a-input-number>
                                        </a-form-item>
                                    </a-col>
                                </a-row>
                            </a-tab-pane>
                        </a-tabs>
                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <a-form-item>
                                    <a-button
                                        type="primary"
                                        @click="onSubmit"
                                        :loading="loading"
                                    >
                                        <template #icon>
                                            <SaveOutlined />
                                        </template>
                                        {{ $t("common.update") }}
                                    </a-button>
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </a-form>
                </a-card>
            </admin-page-table-content>
        </a-col>
    </a-row>
</template>
<script>
import { defineComponent, ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import apiAdmin from "../../../common/composable/apiAdmin";
import hrmManagement from "../../../common/composable/hrmManagement";
import common from "../../../common/composable/common";
import dayjs from "dayjs";
import { forEach } from "lodash-es";
import { ColorPicker } from "vue3-colorpicker";
import "vue3-colorpicker/style.css";
import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";
import SettingSidebar from "../settings/SettingSidebar.vue";
import FormItemHeading from "../../../common/components/common/typography/FormItemHeading.vue";
import Upload from "../../../common/core/ui/file/Upload.vue";
import PdfFontSettings from "../settings/pdf-fonts/PdfFontSettings.vue";
import { useAuthStore } from "../../store/authStore";
import { useHrmStore } from "../../store/hrmStore";

import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    SettingOutlined,
    MinusSquareOutlined,
    FileTextOutlined,
    EyeOutlined,
    DragOutlined,
    InfoCircleOutlined,
} from "@ant-design/icons-vue";

export default defineComponent({
    emits: ["success"],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        SettingOutlined,
        MinusSquareOutlined,
        FileTextOutlined,
        EyeOutlined,
        DragOutlined,
        AdminPageHeader,
        SettingSidebar,
        FormItemHeading,
        Upload,
        ColorPicker,
        PdfFontSettings,
        InfoCircleOutlined,
    },
    setup({ emit }) {
        const { addEditRequestAdmin, rules } = apiAdmin();
        const { permsArray, appSetting } = common();
        const authStore = useAuthStore();
        const hrmStore = useHrmStore();
        const { t } = useI18n();
        const { monthArrays } = hrmManagement();
        const removedIpAddress = ref([]);
        const activeKey = ref("attendance");
        const gradientColor = ref(
            "linear-gradient(0deg, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 1) 100%)"
        );
        const formFields = ref([]);
        const formData = ref({
            leave_start_month: appSetting.value.leave_start_month,
            clock_in_time: dayjs(
                appSetting.value.clock_in_time,
                "HH:mm:ss"
            ).format("hh:mm:ss A"),
            clock_out_time: dayjs(
                appSetting.value.clock_out_time,
                "HH:mm:ss"
            ).format("hh:mm:ss A"),
            late_mark_after: appSetting.value.late_mark_after,
            self_clocking: appSetting.value.self_clocking,
            early_clock_in_time: appSetting.value.early_clock_in_time,
            allow_clock_out_till: appSetting.value.allow_clock_out_till,
            allowed_ip_address: appSetting.value.allowed_ip_address
                ? appSetting.value.allowed_ip_address
                : [],
            profile_image: appSetting.value.profile_image,
            profile_image_url: appSetting.value.profile_image_url,
            employee_id_prefix: appSetting.value.employee_id_prefix,
            employee_id_start: appSetting.value.employee_id_start,
            employee_id_digits: appSetting.value.employee_id_digits,
            capture_location: appSetting.value.capture_location,
        });
        const loading = ref(false);

        onMounted(() => {
            formFields.value = appSetting.value.allowed_ip_address
                ? appSetting.value.allowed_ip_address
                : [];
        });

        const onSubmit = () => {
            loading.value = true;
            let clockInTime = dayjs(
                formData.value.clock_in_time,
                "hh:mm:ss A"
            ).format("HH:mm:ss");
            let clockOutTime = dayjs(
                formData.value.clock_out_time,
                "hh:mm:ss A"
            ).format("HH:mm:ss");

            let newFormData = {
                ...formData.value,
                clock_in_time: clockInTime,
                clock_out_time: clockOutTime,
                allowed_ip_address: ipAddressFilter(),
            };

            addEditRequestAdmin({
                url: "hrm/update-settings",
                data: newFormData,
                successMessage: t("hrm_settings.updated"),
                success: (res) => {
                    loading.value = false;
                    authStore.updateAppAction();
                    hrmStore.updateSelfClocking();
                    emit("success");
                },
            });
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

        return {
            formData,
            rules,
            permsArray,
            appSetting,
            monthArrays,
            formFields,
            addFormField,
            removeFormField,
            onSubmit,
            loading,
            gradientColor,
            activeKey,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "35%",
        };
    },
});
</script>
