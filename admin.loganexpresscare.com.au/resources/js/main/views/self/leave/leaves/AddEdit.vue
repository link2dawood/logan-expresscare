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
                <a-col :xs="24" :sm="24" :md="24" :lg="12">
                    <a-form-item
                        :label="$t('leave.leave_type')"
                        name="leave_type_id"
                        :help="
                            rules.leave_type_id
                                ? rules.leave_type_id.message
                                : null
                        "
                        :validateStatus="rules.leave_type_id ? 'error' : null"
                        class="required"
                    >
                        <a-select
                            v-model:value="formData.leave_type_id"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('leave.leave_type'),
                                ])
                            "
                            :allowClear="true"
                            optionFilterProp="label"
                            show-search
                        >
                            <a-select-option
                                v-for="allLeaveType in allLeaveTypes"
                                :key="allLeaveType.xid"
                                :value="allLeaveType.xid"
                                :label="allLeaveType.name"
                            >
                                {{ allLeaveType.name }}
                                <span
                                    v-if="
                                        getRemainingLeave(allLeaveType.xid) !==
                                        undefined
                                    "
                                    style="
                                        color: #888;
                                        font-size: 12px;
                                        margin-left: 8px;
                                    "
                                >
                                    ({{ $t("leave.remaining_leaves") }}:
                                    {{ getRemainingLeave(allLeaveType.xid) }})
                                </span>
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="12">
                    <a-form-item
                        :label="$t('holiday.date')"
                        name="date"
                        :help="rules.date ? rules.date.message : null"
                        :validateStatus="rules.date ? 'error' : null"
                        class="required"
                    >
                        <a-range-picker
                            :format="appSetting.date_format"
                            valueFormat="YYYY-MM-DD"
                            v-model:value="formData.date"
                            :style="{ width: '100%' }"
                            :disabledDate="disabledDate"
                            @change="validateMaxLeaves"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16" v-if="showHalfDay">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('leave.is_half_day')"
                        name="is_half_day"
                        :help="
                            rules.is_half_day ? rules.is_half_day.message : null
                        "
                        :validateStatus="rules.is_half_day ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="formData.is_half_day"
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
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="12"
                    :lg="12"
                    v-if="formData.is_half_day == 1 && showHalfDay"
                >
                    <a-form-item
                        :label="$t('leave.half_leave')"
                        name="half_leave"
                        :help="
                            rules.half_leave ? rules.half_leave.message : null
                        "
                        :validateStatus="rules.half_leave ? 'error' : null"
                    >
                        <a-select
                            v-model:value="formData.half_leave"
                            button-style="solid"
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
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('leave.reason')"
                        name="reason"
                        :help="rules.reason ? rules.reason.message : null"
                        :validateStatus="rules.reason ? 'error' : null"
                        class="required"
                    >
                        <a-textarea
                            v-model:value="formData.reason"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('leave.reason'),
                                ])
                            "
                            :rows="4"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
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
import { defineComponent, onMounted, ref, watch } from "vue";
import { SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../../common/composable/apiAdmin";
import common from "../../../../../common/composable/common";
import UploadFile from "../../../../../common/core/ui/file/UploadFile.vue";
import LeaveTypeAddButton from "../../../leave/leave-types/AddButton.vue";
import DateRangePicker from "../../../../../common/components/common/calendar/DateRangePicker.vue";

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
        SaveOutlined,
        LeaveTypeAddButton,
        UploadFile,
        DateRangePicker,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        const { appSetting, permsArray, dayjs, user } = common();
        const allLeaveTypes = ref([]);
        const remainingLeaves = ref([]);
        const showHalfDay = ref(false);

        onMounted(() => {
            filterLeaveTypes();
        });

        const filterLeaveTypes = () => {
            var filterYear = dayjs().format("YYYY");

            axiosAdmin
                .get(`self/remaining-leaves?year=${filterYear}`)
                .then((response) => {
                    remainingLeaves.value = response.data.data;
                    // selectedYearStartEndMonths.value = response.data.month_year;
                });
            axiosAdmin.get(`self/filter-leaves`).then((response) => {
                allLeaveTypes.value = response.data.data;
            });
        };

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: {
                    ...props.formData,
                    start_date: props.formData.date
                        ? props.formData.date[0]
                        : "",
                    end_date: props.formData.date ? props.formData.date[1] : "",
                },
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const getRemainingLeave = (xid) => {
            const leave = remainingLeaves.value.find((l) => l.xid === xid);
            return leave ? leave.remaining_leaves : undefined;
        };

        const disabledDate = (current) => {
            return current && current < dayjs().startOf("day");
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const validateMaxLeaves = () => {
            const selectedLeaveType = remainingLeaves.value.find(
                (leave) => leave.xid === props.formData.leave_type_id
            );
            let days = 0;
            if (props.formData.date && props.formData.date.length === 2) {
                const start = dayjs(props.formData.date[0]);
                const end = dayjs(props.formData.date[1]);
                days = end.diff(start, "day") + 1;
            }
            if (
                selectedLeaveType &&
                days > selectedLeaveType.remaining_leaves
            ) {
                rules.value.date = {
                    message: `You have only ${selectedLeaveType.remaining_leaves} remaining leaves for this type.`,
                };
                props.formData.date = undefined;
            } else {
                rules.value.date = null;
            }
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                let arr_dates = [];
                props.formData.date = undefined;
                if (props.addEditType == "add") {
                    arr_dates = [];
                    props.formData.is_half_day = 0;
                    props.formData.half_leave = "before_break";
                    showHalfDay.value = false;
                } else {
                    var start = dayjs(props.data.start_date).format(
                        "YYYY-MM-DD"
                    );
                    var end = dayjs(props.data.end_date).format("YYYY-MM-DD");
                    arr_dates.push(start);
                    arr_dates.push(end);
                    props.formData.date = arr_dates;
                    showHalfDay.value = start === end;
                }
            }
        );

        watch(
            () => props.formData.date,
            (newDate, oldDate) => {
                if (newDate && newDate.length === 2) {
                    showHalfDay.value = newDate[0] === newDate[1];
                } else {
                    showHalfDay.value = false;
                }

                const availableLeaveTypes = allLeaveTypes.value;
                if (
                    !availableLeaveTypes.some(
                        (l) => l.xid === props.formData.leave_type_id
                    )
                ) {
                    props.formData.leave_type_id = undefined;
                }
            },
            { immediate: true }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            appSetting,
            disabledDate,
            permsArray,
            allLeaveTypes,
            validateMaxLeaves,
            getRemainingLeave,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
            user,
            showHalfDay,
        };
    },
});
</script>
