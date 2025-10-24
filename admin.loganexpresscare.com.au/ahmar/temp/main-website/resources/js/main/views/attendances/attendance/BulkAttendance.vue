<template>
    <div
        v-if="
            permsArray.includes('attendance_view') ||
            permsArray.includes('admin')
        "
    >
        <a-button @click="opened" type="primary">
            <template #icon><FilePdfOutlined /> </template>
            {{ $t("attendance.bulk_attendance") }}
        </a-button>
    </div>
    <a-modal
        :title="$t('attendance.mark_attendance')"
        :open="visible"
        :closable="false"
        :centered="true"
        :width="drawerWidth"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="8" :lg="8">
                    <a-form-item
                        :label="$t('user.department_id')"
                        name="department_id"
                        :help="
                            rules.department_id
                                ? rules.department_id.message
                                : null
                        "
                        :validateStatus="rules.department_id ? 'error' : null"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.department_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('user.department_id'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="title"
                                show-search
                            >
                                <a-select-option
                                    v-for="department in departments"
                                    :key="department.xid"
                                    :value="department.xid"
                                    :title="department.name"
                                >
                                    {{ department.name }}
                                </a-select-option>
                            </a-select>
                            <!-- <DepartmentAddButton
                                @onAddSuccess="departmentAdded"
                            /> -->
                        </span>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="8" :lg="8">
                    <a-form-item
                        :label="$t('user.location_id')"
                        name="location_id"
                        :help="
                            rules.location_id ? rules.location_id.message : null
                        "
                        :validateStatus="rules.location_id ? 'error' : null"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.location_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('user.location_id'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="title"
                                show-search
                            >
                                <a-select-option
                                    v-for="location in locations"
                                    :key="location.xid"
                                    :value="location.xid"
                                    :title="location.name"
                                >
                                    {{ location.name }}
                                </a-select-option>
                            </a-select>
                            <!-- <LocationAddButton @onAddSuccess="locationAdded" /> -->
                        </span>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="8" :lg="8">
                    <a-form-item
                        :label="$t('attendance.employees')"
                        name="user_ids"
                        :help="rules.user_ids ? rules.user_ids.message : null"
                        :validateStatus="rules.user_ids ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="userIds"
                                mode="multiple"
                                style="width: 100%"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('attendance.employees'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="user in employees"
                                    :key="user.xid"
                                    :value="user.xid"
                                    :label="user.name"
                                >
                                    {{ user.name }}
                                </a-select-option>
                            </a-select>
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('attendance.mark_attendance_by')"
                        name="mark_attendance_by"
                        :help="
                            rules.mark_attendance_by
                                ? rules.mark_attendance_by.message
                                : null
                        "
                        :validateStatus="
                            rules.mark_attendance_by ? 'error' : null
                        "
                    >
                        <a-radio-group
                            v-model:value="formData.mark_attendance_by"
                            button-style="solid"
                            size="small"
                        >
                            <a-radio-button :value="'month'">
                                {{ $t("common.month") }}
                            </a-radio-button>
                            <a-radio-button :value="'date'">
                                {{ $t("common.date") }}
                            </a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="12"
                    :lg="12"
                    v-if="formData.mark_attendance_by == 'date'"
                >
                    <a-form-item
                        :label="$t('account.date')"
                        name="date"
                        :help="rules.date ? rules.date.message : null"
                        :validateStatus="rules.date ? 'error' : null"
                        class="required"
                    >
                        <DateRangePicker
                            ref="dateRangePicker"
                            @dateTimeChanged="
                                (changedDateTime) => {
                                    formData.date = changedDateTime;
                                }
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="6"
                    :lg="6"
                    v-if="formData.mark_attendance_by == 'month'"
                >
                    <a-form-item
                        :label="$t('leave.clock_in_month')"
                        name="clock_in_month"
                        :help="
                            rules.attendance_month
                                ? rules.attendance_month.message
                                : null
                        "
                        :validateStatus="
                            rules.attendance_month ? 'error' : null
                        "
                        class="required"
                    >
                        <a-date-picker
                            v-model:value="formData.attendance_month"
                            valueFormat="YYYY-MM"
                            picker="month"
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
                    </a-form-item>
                </a-col>
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
                    </a-form-item> </a-col
            ></a-row>
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
                            <!-- <LeaveTypeAddButton
                                @onAddSuccess="leaveTypeAdded"
                            /> -->
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
                            :rows="3"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item>
                        <a-checkbox
                            v-model:checked="formData.attendanceOverwrite"
                        >
                            {{ $t("leave.attendance_overwrite") }}
                        </a-checkbox>
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
                    {{ $t("common.create") }}
                </a-button>
                <a-button key="back" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, ref, reactive, watch, nextTick } from "vue";
import {
    DownloadOutlined,
    FilePdfOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import DateRangePicker from "@/common/components/common/calendar/DateRangePicker.vue";
import PdfDownload from "@/main/components/pdf/PdfDownload.vue";
import common from "@/common/composable/common";
import { filter, forEach } from "lodash-es";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import hrmManagement from "../../../../common/composable/hrmManagement";

export default defineComponent({
    props: ["pageTitle", "successMessage"],
    emits: ["fetchAttendance"],
    components: {
        DownloadOutlined,
        FilePdfOutlined,
        DateRangePicker,
        PdfDownload,
        SaveOutlined,
        UserListDisplay,
    },
    setup(props, { emit }) {
        const { permsArray, formatDate } = common();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { formatShiftTime24Hours, formatShiftTime12Hours } =
            hrmManagement();
        const visible = ref(false);
        const formData = ref({
            location_id: undefined,
            department_id: undefined,
            user_id: undefined,
            clock_in_time: undefined,
            clock_out_time: undefined,
            is_late: 0,
            is_half_day: 0,
            leave_type_id: undefined,
            attendance_month: undefined,
            date: [],
            attendanceOverwrite: false,
            mark_attendance_by: 0,
        });
        const dateRangePicker = ref(null);
        const departments = ref([]);
        const locations = ref([]);
        const users = ref([]);
        const allLeaveTypes = ref([]);
        const userUrl =
            "users?fields=id,xid,user_type,ctc_value,name,email,employee_number,gender,allow_login,dob,joining_date,is_married,marriage_date,personal_email,personal_phone,report_to,x_report_to,is_manager,visibility,reporter{id,xid,name},location_id,x_location_id,location{id,xid,name},designation_id,x_designation_id,designation{id,xid,name},profile_image,profile_image_url,phone,address,status,created_at,role_id,x_role_id,role{id,xid,name,display_name},basic_salary,probation_start_date,probation_end_date,notice_start_date,notice_end_date,duration,shift_id,x_shift_id,shift{id,xid,name},department_id,x_department_id,department{id,xid,name},end_date,annual_ctc,salary_group_id,x_salary_group_id,salaryGroup{id,xid},employee_status_id,x_employee_status_id,employeeWorkStatus{id,xid,name},userDocument{id,xid,field_type_id,x_field_type_id,values,values_url},userDocument:fieldTypes{id,xid,name,type,is_required}&limit=10000";
        const departmentUrl = "departments?limit=10000";
        const locationUrl = "locations?limit=10000";
        const allLeaveTypeUrl = "leave-types?limit=10000";
        const employees = ref([]);
        const userIds = ref([]);

        const opened = () => {
            visible.value = true;
            const locationPromise = axiosAdmin.get(locationUrl);
            const departmentsPromise = axiosAdmin.get(departmentUrl);
            const usersPromise = axiosAdmin.get(userUrl);
            const allLeaveTypePromise = axiosAdmin.get(allLeaveTypeUrl);
            Promise.all([
                departmentsPromise,
                locationPromise,
                usersPromise,
                allLeaveTypePromise,
            ]).then(
                ([
                    departmentResponse,
                    locationResponse,
                    userResponse,
                    allLeaveTypeResponse,
                ]) => {
                    departments.value = departmentResponse.data;
                    locations.value = locationResponse.data;
                    users.value = userResponse.data;
                    allLeaveTypes.value = allLeaveTypeResponse.data;
                    updateEmployees();
                }
            );
        };

        const onClose = () => {
            formData.value = {
                location_id: undefined,
                department_id: undefined,
                clock_in_time: undefined,
                clock_out_time: undefined,
                is_late: 0,
                is_half_day: 0,
                leave_type_id: undefined,
                attendance_month: undefined,
                date: [],
                attendanceOverwrite: false,
                mark_attendance_by: "date",
            };
            userIds.value = [];
            rules.value = {};
            visible.value = false;
        };

        const errorHandle = (obj) => {
            rules.value = obj;
        };

        const getFilteredEmployees = () => {
            const deptId = formData.value.department_id;
            const locId = formData.value.location_id;
            if (deptId && locId) {
                return users.value.filter(
                    (user) =>
                        user.x_department_id === deptId &&
                        user.x_location_id === locId
                );
            } else if (deptId) {
                return users.value.filter(
                    (user) => user.x_department_id === deptId
                );
            } else if (locId) {
                return users.value.filter(
                    (user) => user.x_location_id === locId
                );
            } else {
                return users.value;
            }
        };

        const updateEmployees = () => {
            employees.value = getFilteredEmployees();
        };

        const onSubmit = () => {
            const clockInTime = formatShiftTime24Hours(
                formData.value.clock_in_time
            );
            const clockOutTime = formatShiftTime24Hours(
                formData.value.clock_out_time
            );
            let newFormData = {
                ...formData.value,
                user_ids: userIds.value,
                clock_in_time: clockInTime,
                clock_out_time: clockOutTime,
            };
            addEditRequestAdmin({
                url: `add-users-attendance`,
                data: newFormData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("fetchAttendance");
                    visible.value = false;
                },
            });
        };

        watch(
            () => visible.value,
            async (newVal, oldVal) => {
                await nextTick();
                if (newVal) {
                    formData.value = {
                        location_id: undefined,
                        department_id: undefined,
                        user_id: undefined,
                        clock_in_time: undefined,
                        clock_out_time: undefined,
                        is_late: 0,
                        is_half_day: 0,
                        leave_type_id: undefined,
                        attendance_month: undefined,
                        date: [],
                        attendanceOverwrite: false,
                        mark_attendance_by: "date",
                    };
                    userIds.value = [];
                    dateRangePicker.value.resetPicker();
                }
            }
        );

        watch(
            () => [
                formData.value.department_id,
                formData.value.location_id,
                formData.value.mark_attendance_by,
                formData.value.is_half_day,
            ],
            () => {
                updateEmployees();
                if (formData.value.mark_attendance_by === "date") {
                    formData.value.attendance_month = undefined;
                } else {
                    formData.value.date = [];
                }
                if (formData.value.is_half_day == 0) {
                    formData.value.leave_type_id = undefined;
                    formData.value.reason = undefined;
                }
            }
        );

        return {
            permsArray,
            rules,
            opened,
            onClose,
            formatDate,
            formData,
            visible,
            dateRangePicker,
            errorHandle,
            departments,
            locations,
            users,
            onSubmit,
            allLeaveTypes,
            loading,
            employees,
            userIds,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "50%",
        };
    },
});
</script>
