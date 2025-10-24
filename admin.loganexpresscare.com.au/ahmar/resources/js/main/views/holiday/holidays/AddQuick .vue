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
            <a-row :gutter="16" class="mb-20">
                <a-col :xs="24" :sm="24" :md="11" :lg="11">
                    <a-form-item
                        :label="$t('holiday.import_holiday_from')"
                        name="import_holiday_from"
                    >
                        <a-date-picker
                            v-model:value="filterYear"
                            :placeholder="
                                $t('common.select_default_text', [$t('holiday.year')])
                            "
                            picker="year"
                            @change="getYearHolidayData()"
                            style="width: 100%"
                            :allowClear="false"
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="11" :lg="11">
                    <a-form-item
                        :label="$t('holiday.import_holiday_to')"
                        name="import_holiday_to"
                    >
                        <a-date-picker
                            v-model:value="holidayFielterYears.years"
                            :placeholder="
                                $t('common.select_default_text', [$t('holiday.year')])
                            "
                            picker="year"
                            @change="updateDateYear()"
                            style="width: 100%"
                            :allowClear="false"
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row
                :gutter="16"
                v-for="(formField, index) in formFields"
                :key="formField.id"
                style="display: flex; align-items: center"
            >
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="formField.is_half_day == 0 ? 8 : 6"
                    :lg="formField.is_half_day == 0 ? 8 : 6"
                >
                    <a-form-item :label="$t('user.name')" name="name" class="required">
                        <a-input
                            v-model:value="formField.name"
                            :status="validationErrors[`name-${index}`] ? 'error' : ''"
                            :placeholder="
                                $t('common.placeholder_default_text', [$t('user.name')])
                            "
                        />

                        <div
                            v-if="validationErrors[`name-${index}`]"
                            class="error-message"
                        >
                            {{ validationErrors[`name-${index}`] }}
                        </div>
                    </a-form-item>
                </a-col>

                <a-col
                    :xs="24"
                    :sm="24"
                    :md="formField.is_half_day == 0 ? 8 : 6"
                    :lg="formField.is_half_day == 0 ? 8 : 6"
                >
                    <a-form-item :label="$t('user.date')" name="date" class="required">
                        <a-date-picker
                            v-model:value="formField.date"
                            :status="validationErrors[`date-${index}`] ? 'error' : ''"
                            :format="appSetting.date_format"
                            valueFormat="YYYY-MM-DD"
                            style="width: 100%"
                        />
                        <div
                            v-if="validationErrors[`date-${index}`]"
                            class="error-message"
                        >
                            {{ validationErrors[`date-${index}`] }}
                        </div>
                    </a-form-item>
                </a-col>

                <a-col
                    :xs="24"
                    :sm="24"
                    :md="formField.is_half_day == 0 ? 6 : 4"
                    :lg="formField.is_half_day == 0 ? 6 : 4"
                >
                    <a-form-item :label="$t('holiday.is_half_day')" name="is_half_day">
                        <a-radio-group
                            v-model:value="formField.is_half_day"
                            button-style="solid"
                            size="medium"
                            :status="
                                validationErrors[`is-half-day-${index}`] ? 'error' : ''
                            "
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
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="6"
                    :lg="6"
                    v-if="formField.is_half_day == 1"
                >
                    <a-form-item :label="$t('holiday.half_holiday')" name="half_holiday">
                        <a-select
                            v-model:value="formField.half_holiday"
                            :status="
                                validationErrors[`half-holiday-${index}`] ? 'error' : ''
                            "
                            size="medium"
                        >
                            <a-select-option value="before_break">
                                {{ $t("common.before_break") }}
                            </a-select-option>
                            <a-select-option value="after_break">
                                {{ $t("common.after_break") }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
                <a-col :span="1" style="margin-top: 6px">
                    <MinusSquareOutlined @click="removeFormField(formField)" />
                </a-col>
            </a-row>
            <a-col :xs="24" :sm="24" :md="24" :lg="24" class="mt-20">
                <a-form-item>
                    <a-button
                        type="dashed"
                        block
                        @click="addFormField"
                        :disabled="addFormButtonStatus"
                    >
                        <PlusOutlined />

                        {{ $t("user.add_field") }}
                    </a-button>
                </a-form-item>
            </a-col>
        </a-form>
        <template #footer>
            <a-button
                key="submit"
                type="primary"
                :loading="loading"
                style="margin-right: 8px"
                @click="onSubmit"
            >
                <template #icon>
                    <SaveOutlined />
                </template>
                {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, watch, onMounted, computed } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    MinusSquareOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import { forEach, filter, some } from "lodash-es";
import common from "../../../../common/composable/common";
import DateTimePicker from "../../../../common/components/common/calendar/DateTimePicker.vue";
import { useI18n } from "vue-i18n";

export default defineComponent({
    props: ["holidayYearData", "visible", "addEditType", "pageTitle", "successMessage"],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        MinusSquareOutlined,
        DateTimePicker,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { t } = useI18n();
        const remvoedHolidays = ref([]);
        const { appSetting, dayjs } = common();
        const formFields = ref([
            { name: "", date: "", is_half_day: 0, half_holiday: "before_break" },
        ]);
        const filterYear = ref(dayjs());
        const holidayDataByYear = ref({});
        const holidayFielterYears = ref({
            years: props.holidayYearData,
        });
        const validationErrors = ref({});

        onMounted(() => {
            getYearHolidayData();
        });

        const addFormField = () => {
            formFields.value.push({
                name: "",
                date: "",
                is_half_day: 0,
                half_holiday: "before_break",
            });
        };
        189, 55, 55;

        const getYearHolidayData = () => {
            formFields.value = [];

            axiosAdmin
                .post(`get-holiday-year-data`, {
                    year: filterYear.value.format("YYYY"),
                })
                .then((response) => {
                    holidayDataByYear.value = response.data;

                    if (holidayDataByYear.value) {
                        forEach(holidayDataByYear.value, (yearData) => {
                            forEach(yearData, (year) => {
                                if (year) {
                                    formFields.value.push({
                                        name: year.name,
                                        date: year.date,
                                        is_half_day: year.is_half_day,
                                        half_holiday: year.half_holiday,
                                    });
                                }
                            });
                        });
                        updateDateYear();
                    }
                });
        };

        const updateDateYear = () => {
            formFields.value = formFields.value.map((field) => {
                const currentDate = dayjs(field.date);

                const updatedDate = currentDate.year(
                    parseInt(holidayFielterYears.value.years.format("YYYY"))
                );

                return {
                    ...field,
                    date: updatedDate.format("YYYY-MM-DD"),
                };
            });
        };

        const validateForm = () => {
            validationErrors.value = {};
            let isValid = true;

            formFields.value.forEach((field, index) => {
                if (!field.name) {
                    validationErrors.value[`name-${index}`] = t(
                        "holiday.the_name_field_is_required"
                    );
                    isValid = false;
                }
                if (!field.date) {
                    validationErrors.value[`date-${index}`] = t(
                        "holiday.the_date_field_is_required"
                    );
                    isValid = false;
                }
            });

            return isValid;
        };

        const removeFormField = (item) => {
            let index = formFields.value.indexOf(item);
            if (index !== -1) {
                formFields.value.splice(index, 1);
            }

            if (item.id != "") {
                remvoedHolidays.value.push(item.id);
            }
        };

        const addFormButtonStatus = computed(() => {
            if (formFields.value.length == 0) {
                return false;
            } else {
                return (
                    some(formFields.value, { name: "" }) ||
                    some(formFields.value, { date: "" })
                );
            }
        });

        const onSubmit = () => {
            const isValid = validateForm();
            var holidayFilterData = [];

            holidayFilterData = filter(formFields.value, (newPackage) => {
                return (
                    (newPackage.name != "" || newPackage.name != 0) &&
                    newPackage.date != undefined
                );
            });

            var newFormData = {
                holidayData: holidayFilterData,
            };

            if (isValid) {
                addEditRequestAdmin({
                    url: "add-quick-holiday",
                    data: newFormData,
                    successMessage: props.successMessage,
                    success: (res) => {
                        emit("closed");
                    },
                });
            }
        };

        const onClose = () => {
            validationErrors.value = {};
            rules.value = {};
            emit("closed");
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                holidayFielterYears.value.years = props.holidayYearData;
                filterYear.value = undefined;
                formFields.value = [
                    {
                        name: "",
                        date: "",
                        is_half_day: 0,
                        half_holiday: "before_break",
                    },
                ];
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            addFormField,
            removeFormField,
            formFields,
            addFormButtonStatus,
            appSetting,
            getYearHolidayData,
            updateDateYear,
            filterYear,
            holidayFielterYears,
            validationErrors,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "50%",
        };
    },
});
</script>
<style scoped>
.required .ant-form-item-label > label::after {
    content: "*";
    color: red;
}
.error-message {
    color: rgb(255, 0, 0);
    font-size: 12px;
}
</style>
