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
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="24"
                    :lg="24"
                    v-if="
                        permsArray.includes('admin') ||
                        permsArray.includes('leaves_edit')
                    "
                >
                    <a-form-item
                        :label="$t('leave.user_id')"
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
                                        $t('leave.user_id'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="title"
                                show-search
                                @change="leaveTypeUser(formData.user_id)"
                            >
                                <a-select-option
                                    v-for="allStaffMember in allStaffMembers"
                                    :key="allStaffMember.xid"
                                    :value="allStaffMember.xid"
                                    :title="allStaffMember.name"
                                >
                                    <user-list-display
                                        :user="allStaffMember"
                                        whereToShow="select"
                                    />
                                </a-select-option>
                            </a-select>
                            <StaffMemberAddButton
                                @onAddSuccess="staffMemberAdded"
                            />
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
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
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.leave_type_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('leave.leave_type'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="title"
                                show-search
                            >
                                <a-select-option
                                    v-for="allLeaveType in leaveTypeUser(
                                        formData.user_id
                                    )"
                                    :key="allLeaveType.xid"
                                    :value="allLeaveType.xid"
                                    :title="allLeaveType.name"
                                >
                                    {{ allLeaveType.name }}
                                    <span
                                        v-if="
                                            getRemainingLeave(
                                                allLeaveType.xid
                                            ) !== undefined
                                        "
                                        style="
                                            color: #888;
                                            font-size: 12px;
                                            margin-left: 8px;
                                        "
                                    >
                                        ({{ $t("leave.remaining_leaves") }}:
                                        {{
                                            getRemainingLeave(allLeaveType.xid)
                                        }})
                                    </span>
                                </a-select-option>
                            </a-select>
                            <LeaveTypeAddButton
                                @onAddSuccess="leaveTypeAdded"
                            />
                        </span>
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
                            @change="validateMaxLeaves"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12" v-if="showHalfDay">
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
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";
import StaffMemberAddButton from "../../staff-members/users/StaffAddButton.vue";
import UploadFile from "../../../../common/core/ui/file/UploadFile.vue";
import LeaveTypeAddButton from "../leave-types/AddButton.vue";
import DateRangePicker from "../../../../common/components/common/calendar/DateRangePicker.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import axios from "axios";

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
        StaffMemberAddButton,
        LeaveTypeAddButton,
        UploadFile,
        DateRangePicker,
        UserListDisplay,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        const { appSetting, disabledDate, permsArray, dayjs } = common();
        const allStaffMembers = ref([]);
        const staffMembersUrl = "users?limit=10000";
        const allLeaveTypes = ref([]);
        const remainingLeaves = ref([]);
        const leaveTypeUrl =
            "leave-types?fields=id,xid,count_type,total_leaves,name,is_paid,total_leaves,max_leaves_per_month,employeeSpecificLeaveCount{id,xid,user_id,x_user_id,total_leaves,x_leave_type_id}";
        const showHalfDay = ref(false);

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            const leaveTypePromise = axiosAdmin.get(leaveTypeUrl);
            Promise.all([staffMemberPromise, leaveTypePromise]).then(
                ([staffMemberResponse, leaveTypeResponse]) => {
                    allStaffMembers.value = staffMemberResponse.data;
                    allLeaveTypes.value = leaveTypeResponse.data;
                }
            );
        });

        const leaveTypeUser = (userId) => {
            if (!userId) {
                return allLeaveTypes.value.filter(
                    (leaveType) => leaveType.count_type === "same_for_all"
                );
            }
            return allLeaveTypes.value.filter((leaveType) => {
                if (leaveType.count_type === "same_for_all") {
                    return true;
                }
                if (leaveType.count_type === "employee_specific") {
                    return (
                        Array.isArray(
                            leaveType.employee_specific_leave_count
                        ) &&
                        leaveType.employee_specific_leave_count.some(
                            (entry) => entry.x_user_id === userId
                        )
                    );
                }
                return false;
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

        const staffMemberAdded = (xid) => {
            axiosAdmin.get(staffMembersUrl).then((response) => {
                allStaffMembers.value = response.data;
                emit("addListSuccess", { type: "user_id", id: xid });
            });
        };

        const leaveTypeAdded = (xid) => {
            axiosAdmin.get(leaveTypeUrl).then((response) => {
                allLeaveTypes.value = response.data;
                emit("addListSuccess", { type: "leave_type_id", id: xid });
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const filterLeaveTypes = () => {
            const userId = props.formData.user_id;
            if (!userId) return;

            const filterYear = dayjs().format("YYYY");

            axiosAdmin
                .get(
                    `leaves/remaining-leaves?year=${filterYear}&user_id=${userId}`
                )
                .then((response) => {
                    remainingLeaves.value = response.data.data;
                });
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

        const getRemainingLeave = (xid) => {
            const leave = remainingLeaves.value.find((l) => l.xid === xid);
            return leave ? leave.remaining_leaves : undefined;
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
                    const start = dayjs(props.data.start_date).format(
                        "YYYY-MM-DD"
                    );
                    const end = dayjs(props.data.end_date).format("YYYY-MM-DD");
                    arr_dates.push(start);
                    arr_dates.push(end);
                    props.formData.date = arr_dates;
                    showHalfDay.value = start === end;
                }
            }
        );

        watch(
            [() => props.formData.date, () => props.formData.user_id],
            ([newDate, newUserId], [oldDate, oldUserId]) => {
                if (newDate && newDate.length === 2) {
                    showHalfDay.value = newDate[0] === newDate[1];
                } else {
                    showHalfDay.value = false;
                }

                const availableLeaveTypes = leaveTypeUser(newUserId);
                if (
                    !availableLeaveTypes.some(
                        (l) => l.xid === props.formData.leave_type_id
                    )
                ) {
                    props.formData.leave_type_id = undefined;
                }

                if (newUserId) {
                    filterLeaveTypes();
                } else {
                    remainingLeaves.value = [];
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
            showHalfDay,
            staffMemberAdded,
            allStaffMembers,
            leaveTypeAdded,
            leaveTypeUser,
            validateMaxLeaves,
            getRemainingLeave,
            remainingLeaves,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
