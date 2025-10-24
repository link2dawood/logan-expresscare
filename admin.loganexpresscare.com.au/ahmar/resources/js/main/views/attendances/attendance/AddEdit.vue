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
                        :label="$t('attendance.user')"
                        name="user_id"
                        :help="rules.user_id ? rules.user_id.message : null"
                        :validateStatus="rules.user_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.user_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('attendance.user'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="title"
                                show-search
                                @change="getUserShift(formData.user_id)"
                            >
                                <a-select-option
                                    v-for="user in users"
                                    :key="user.xid"
                                    :value="user.xid"
                                    :title="user.name"
                                >
                                    <user-list-display
                                        :user="user"
                                        whereToShow="select"
                                    />
                                </a-select-option>
                            </a-select>
                            <UserAddButton @onAddSuccess="userAdded" />
                        </span>
                        <template v-if="formData.user_id">
                            <small
                                style="
                                    color: #7c8db5 !important;
                                    margin-bottom: 20px;
                                "
                            >
                                {{
                                    `${userShift.staff_name} has ${
                                        userShift.name
                                    } shift from ${formatOnlyTime(
                                        userShift.clock_in_time
                                    )} to ${formatOnlyTime(
                                        userShift.clock_out_time
                                    )}`
                                }}
                            </small>
                        </template>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('attendance.date')"
                        name="date"
                        :help="rules.date ? rules.date.message : null"
                        :validateStatus="rules.date ? 'error' : null"
                        class="required"
                    >
                        <a-date-picker
                            v-model:value="formData.date"
                            :format="appSetting.date_format"
                            valueFormat="YYYY-MM-DD"
                            style="width: 100%"
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
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
                        <a-time-picker
                            v-model:value="formData.clock_in_time"
                            valueFormat="hh:mm A"
                            style="width: 100%"
                            use12-hours
                        />
                        <div
                            style="
                                font-size: small;
                                color: red;
                                margin-top: 1px;
                            "
                            v-if="errors.clock_in_time"
                        >
                            {{ errors.clock_in_time }}
                        </div>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('attendance.clock_in_ip_address')"
                        name="clock_in_ip_address"
                        :help="
                            rules.clock_in_ip_address
                                ? rules.clock_in_ip_address.message
                                : null
                        "
                        :validateStatus="
                            rules.clock_in_ip_address ? 'error' : null
                        "
                    >
                        <a-input
                            v-model:value="formData.clock_in_ip_address"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('attendance.clock_in_ip_address'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('shift.clock_out_time')"
                        name="clock_out_time"
                        :help="
                            rules.clock_out_time
                                ? rules.clock_out_time.message
                                : null
                        "
                        :validateStatus="rules.clock_out_time ? 'error' : null"
                        class="required"
                    >
                        <a-time-picker
                            v-model:value="formData.clock_out_time"
                            valueFormat="hh:mm A"
                            style="width: 100%"
                            use12-hours
                        />
                        <div
                            style="
                                font-size: small;
                                color: red;
                                margin-top: 1px;
                            "
                            v-if="errors.clock_out_time"
                        >
                            {{ errors.clock_out_time }}
                        </div>
                    </a-form-item>
                </a-col>

                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('attendance.clock_out_ip_address')"
                        name="clock_out_ip_address"
                        :help="
                            rules.clock_out_ip_address
                                ? rules.clock_out_ip_address.message
                                : null
                        "
                        :validateStatus="
                            rules.clock_out_ip_address ? 'error' : null
                        "
                    >
                        <a-input
                            v-model:value="formData.clock_out_ip_address"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('attendance.clock_out_ip_address'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('attendance.is_late')"
                        name="is_late"
                        :help="rules.is_late ? rules.is_late.message : null"
                        :validateStatus="rules.is_late ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="formData.is_late"
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
                        :label="$t('attendance.is_half_day')"
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
                    v-if="formData.is_half_day == 1"
                >
                    <a-form-item
                        :label="$t('attendance.leave_type')"
                        name="leave_type_id"
                        :help="
                            rules.leave_type_id
                                ? rules.leave_type_id.message
                                : null
                        "
                        :validateStatus="rules.leave_type_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.leave_type_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('attendance.leave_type'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="allLeaveType in allLeaveTypes"
                                    :key="allLeaveType.xid"
                                    :value="allLeaveType.xid"
                                >
                                    {{ allLeaveType.name }}
                                </a-select-option>
                            </a-select>
                            <LeaveTypeAddButton
                                @onAddSuccess="leaveTypeAdded"
                            />
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16" v-if="formData.is_half_day == 1">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('attendance.reason')"
                        name="reason"
                        :help="rules.reason ? rules.reason.message : null"
                        :validateStatus="rules.reason ? 'error' : null"
                        class="required"
                    >
                        <a-textarea
                            v-model:value="formData.reason"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('attendance.reason'),
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
import { defineComponent, onMounted, ref, watch, reactive } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";
import UserAddButton from "../../staff-members/users/StaffAddButton.vue";
import DateTimePicker from "../../../../common/components/common/calendar/DateTimePicker.vue";
import LeaveTypeAddButton from "../../leave/leave-types/AddButton.vue";
import dayjs from "dayjs";
import hrmManagement from "../../../../common/composable/hrmManagement";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import { useHrmStore } from "../../../store/hrmStore"; // adjust the path as needed
import { find } from "lodash-es";
import { message } from "ant-design-vue";
import { useI18n } from "vue-i18n";

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
        UserAddButton,
        DateTimePicker,
        LeaveTypeAddButton,
        hrmManagement,
        UserListDisplay,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        const { appSetting, disabledDate, permsArray, formatOnlyTime } =
            common();
        const { t } = useI18n();
        const users = ref([]);
        const userUrl = "users?limit=10000";
        const allLeaveTypes = ref([]);
        const leaveTypeUrl = "leave-types";
        const userShift = ref({});
        const { formatShiftTime24Hours, formatShiftTime12Hours } =
            hrmManagement();
        const hrmStore = useHrmStore();

        const errors = reactive({
            clock_in_time: "",
            clock_out_time: "",
        });

        // const onSubmit = () => {
        //  let clockInTime = formatShiftTime24Hours(
        //         props.formData.clock_in_time
        //     );
        //     let clockOutTime = formatShiftTime24Hours(
        //         props.formData.clock_out_time
        //     );

        //     addEditRequestAdmin({
        //         url: props.url,
        //         data: {
        //             ...props.formData,
        //             clock_in_time: props.formData.clock_in_time
        //                 ? clockInTime
        // : "",
        //             clock_out_time: props.formData.clock_out_time
        //                 ? clockOutTime
        //                 : "",
        //         },

        //         successMessage: props.successMessage,
        //         success: (res) => {
        //             hrmStore.updateShowClockOutButton(true);
        //             hrmStore.updateShowClockInButton(true);
        //              emit("addEditSuccess", res.xid);
        //          },
        //     });
        // };

        const onSubmit = () => {
            // Clear all errors first
            errors.clock_in_time = "";
            errors.clock_out_time = "";
            rules.value = {};

            const clockInTime = formatShiftTime24Hours(
                props.formData.clock_in_time
            );
            const clockOutTime = formatShiftTime24Hours(
                props.formData.clock_out_time
            );

            const isClockInValid = props.formData.clock_in_time
                ? validateShiftTime(
                      clockInTime,
                      userShift.value,
                      "clock_in_time"
                  )
                : true;

            const isClockOutValid = props.formData.clock_out_time
                ? validateShiftTime(
                      clockOutTime,
                      userShift.value,
                      "clock_out_time"
                  )
                : true;

            if (!isClockInValid || !isClockOutValid) {
                return;
            }

            addEditRequestAdmin({
                url: props.url,
                data: {
                    ...props.formData,
                    clock_in_time: props.formData.clock_in_time
                        ? clockInTime
                        : "",
                    clock_out_time: props.formData.clock_out_time
                        ? clockOutTime
                        : "",
                },
                successMessage: props.successMessage,
                success: (res) => {
                    hrmStore.updateShowClockOutButton(true);
                    hrmStore.updateShowClockInButton(true);
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const validateShiftTime = (time, userShift, fieldKey) => {
            if (!userShift) return true;

            const today = dayjs().startOf("day");

            let shiftClockIn = dayjs(
                `${today.format("YYYY-MM-DD")} ${userShift.clock_in_time}`,
                "YYYY-MM-DD HH:mm:ss"
            );
            let shiftClockOut = dayjs(
                `${today.format("YYYY-MM-DD")} ${userShift.clock_out_time}`,
                "YYYY-MM-DD HH:mm:ss"
            );

            if (!shiftClockOut.isAfter(shiftClockIn)) {
                shiftClockOut = shiftClockOut.add(1, "day");
            }
            var allowTime = 0;
            allowTime = userShift.allow_clock_out_till;
            allowTime = allowTime + 1;
            const minAllowedClockIn = shiftClockIn.subtract(
                userShift.early_clock_in_time,
                "minute"
            );
            const maxAllowedClockOut = shiftClockOut.add(allowTime, "minute");

            let selectedTime = dayjs(
                `${today.format("YYYY-MM-DD")} ${time}`,
                "YYYY-MM-DD HH:mm"
            );

            if (selectedTime.isBefore(minAllowedClockIn)) {
                const tryNextDay = selectedTime.add(1, "day");
                if (tryNextDay.isBefore(maxAllowedClockOut)) {
                    selectedTime = tryNextDay;
                }
            }

            if (
                selectedTime.isBefore(minAllowedClockIn) ||
                selectedTime.isAfter(maxAllowedClockOut)
            ) {
                errors[fieldKey] =
                    "Please select time between allowed shift times.";
                return false;
            }

            errors[fieldKey] = ""; // Clear any previous error
            return true;
        };

        const timeZoneFormatter = (clockIndate, clockInTime, clockOutTime) => {
            const clockInDateTime = `${clockIndate}T${clockInTime}::+00:00`;
            const clockOutDateTime = `${clockIndate}T${clockOutTime}::+00:00`;

            return {
                clockInDateTime,
                clockOutDateTime,
            };
        };

        const timeFormatter = (clockInTime, clockOutTime) => {
            let h1 = clockInTime.slice(0, 2);
            let m1 = clockInTime.slice(3, 5);

            let h2 = clockOutTime.slice(0, 2);
            let m2 = clockOutTime.slice(3, 5);

            let workingMinutes = (h2 - h1 - 1) * 60 + (60 - m1) + Number(m2);
        };

        onMounted(() => {
            const usersPromise = axiosAdmin.get(userUrl);
            const leaveTypePromise = axiosAdmin.get(leaveTypeUrl);

            Promise.all([usersPromise, leaveTypePromise]).then(
                ([userResponse, leaveTypeResponse]) => {
                    users.value = userResponse.data;
                    allLeaveTypes.value = leaveTypeResponse.data;
                }
            );
        });

        const getUserShift = (xid) => {
            var selectedStaff = find(users.value, ["xid", xid]);
            if (selectedStaff) {
                userShift.value = {
                    ...selectedStaff.shift,
                    staff_name: selectedStaff.name,
                };
            } else {
                userShift.value = {};
            }
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const userAdded = () => {
            axiosAdmin.get(userUrl).then((response) => {
                users.value = response.data;
                emit("addListSuccess", { type: "user_id", id: xid });
            });
        };
        const leaveTypeAdded = () => {
            axiosAdmin.get(leaveTypeUrl).then((response) => {
                allLeaveTypes.value = response.data;
                emit("addListSuccess", { type: "leave_type_id", id: xid });
            });
        };

        watch(
            () => props.visible,

            (newVal, oldVal) => {
                if (props.addEditType == "edit") {
                    getUserShift(props.data.x_user_id);
                    props.formData.clock_in_time =
                        formatShiftTime12Hours(props.data.clock_in_time) != null
                            ? formatShiftTime12Hours(props.data.clock_in_time)
                            : undefined;
                    props.formData.clock_out_time =
                        formatShiftTime12Hours(props.data.clock_out_time) !=
                        null
                            ? formatShiftTime12Hours(props.data.clock_out_time)
                            : undefined;
                } else {
                    props.formData.clock_in_time = undefined;
                    props.formData.clock_out_time = undefined;
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
            users,
            userAdded,
            allLeaveTypes,
            leaveTypeAdded,
            getUserShift,
            formatOnlyTime,
            userShift,
            errors,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
