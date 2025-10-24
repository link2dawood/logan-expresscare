<template>
    <a-card
        :bodyStyle="{ padding: '15px', height: '449px', overflow: 'hidden' }"
        v-if="
            permsArray.includes('admin') ||
            permsArray.includes('leaves_approve_reject') ||
            permsArray.includes('expenses_approve_reject')
        "
    >
        <template #title>
            <BellOutlined />
            {{ $t("hrm_dashboard.pending_approvals") }}
            <a-badge
                :count="
                    activeKey === 'expenses'
                        ? pendingExpenses.length
                        : pendingLeaves.length
                "
            />
        </template>

        <a-tabs
            v-model:activeKey="activeKey"
            @change="onTabChange(activeKey)"
            class="segmented-tabs"
        >
            <a-tab-pane
                key="leaves"
                :tab="`${$t('hrm_dashboard.pending_leaves')}`"
                v-if="
                    permsArray.includes('admin') ||
                    permsArray.includes('leaves_approve_reject')
                "
            >
                <a-result v-if="pendingLeaves.length <= 0"
                    ><template #icon>
                        <ExclamationCircleFilled
                            :style="{
                                fontSize: '52px',
                                padding: '0px',
                                marginTop: '35px !important',
                            }"
                        />
                    </template>
                    <template #extra>
                        <span
                            type="primary"
                            :style="{
                                fontSize: '28px',
                            }"
                            >{{
                                $t(`hrm_dashboard.there_is_no_pending_leaves`)
                            }}</span
                        >
                    </template>
                </a-result>

                <div class="pending-leave-hrm">
                    <perfect-scrollbar
                        :options="{
                            wheelSpeed: 1,
                            swipeEasing: true,
                            suppressScrollX: true,
                        }"
                    >
                        <a-list
                            v-if="pendingLeaves.length > 0"
                            :loading="initLoading"
                            item-layout="horizontal"
                            :data-source="pendingLeaves"
                        >
                            <template #renderItem="{ item }">
                                <a-list-item>
                                    <template #extra>
                                        <a-space>
                                            <a-button
                                                type="primary"
                                                @click="viewLeaves(item)"
                                                v-if="
                                                    (permsArray.includes(
                                                        'admin'
                                                    ) &&
                                                        activeKey ==
                                                            'leaves') ||
                                                    permsArray.includes(
                                                        'leaves_approve_reject'
                                                    )
                                                "
                                            >
                                                <template #icon
                                                    ><EyeOutlined
                                                /></template>
                                            </a-button>
                                            <a-button
                                                v-if="
                                                    item.status == 'pending' &&
                                                    ((permsArray.includes(
                                                        'admin'
                                                    ) &&
                                                        activeKey ==
                                                            'leaves') ||
                                                        permsArray.includes(
                                                            'leaves_approve_reject'
                                                        ))
                                                "
                                                @click="approveLeave(item.xid)"
                                                type="primary"
                                            >
                                                <template #icon
                                                    ><CheckOutlined
                                                /></template>
                                            </a-button>
                                            <a-button
                                                v-if="
                                                    item.status == 'pending' &&
                                                    (permsArray.includes(
                                                        'admin'
                                                    ) ||
                                                        permsArray.includes(
                                                            'leaves_approve_reject'
                                                        ))
                                                "
                                                @click="rejectLeave(item.xid)"
                                                type="primary"
                                                danger
                                            >
                                                <template #icon
                                                    ><CloseOutlined
                                                /></template>
                                            </a-button>
                                        </a-space>
                                    </template>
                                    <a-skeleton
                                        avatar
                                        :title="false"
                                        :loading="!!item.loading"
                                        active
                                    >
                                        <a-list-item-meta
                                            :description="item.reason"
                                        >
                                            <template #title>
                                                <a-typography-text strong>
                                                    {{ item.user.name }}
                                                </a-typography-text>
                                                ({{
                                                    item.date_time
                                                        ? formatDate(
                                                              item.date_time
                                                          )
                                                        : formatDate(
                                                              item.start_date
                                                          )
                                                }}
                                                -
                                                {{ formatDate(item.end_date) }})
                                            </template>
                                            <template #avatar>
                                                <a-avatar
                                                    :src="
                                                        item.user
                                                            .profile_image_url
                                                    "
                                                />
                                            </template>
                                        </a-list-item-meta>
                                    </a-skeleton>
                                </a-list-item>
                                <hr />
                            </template>
                        </a-list>
                    </perfect-scrollbar>
                </div>
            </a-tab-pane>
            <a-tab-pane
                key="expenses"
                :tab="`${$t('hrm_dashboard.pending_expenses')}`"
                v-if="
                    permsArray.includes('admin') ||
                    permsArray.includes('expenses_approve_reject')
                "
            >
                <a-result v-if="pendingExpenses.length <= 0"
                    ><template #icon>
                        <ExclamationCircleFilled
                            :style="{
                                fontSize: '52px',
                                padding: '0px',
                                marginTop: '35px !important',
                            }"
                        />
                    </template>
                    <template #extra>
                        <span
                            type="primary"
                            :style="{
                                fontSize: '28px',
                            }"
                            >{{
                                $t(`hrm_dashboard.there_is_no_pending_expenses`)
                            }}</span
                        >
                    </template>
                </a-result>

                <div class="pending-leave-hrm">
                    <perfect-scrollbar
                        :options="{
                            wheelSpeed: 1,
                            swipeEasing: true,
                            suppressScrollX: true,
                        }"
                    >
                        <a-list
                            v-if="pendingExpenses.length > 0"
                            :loading="initLoading"
                            item-layout="horizontal"
                            :data-source="pendingExpenses"
                        >
                            <template #renderItem="{ item }">
                                <a-list-item>
                                    <template #extra>
                                        <a-space>
                                            <a-button
                                                type="primary"
                                                @click="viewExpense(item)"
                                                v-if="
                                                    (permsArray.includes(
                                                        'admin'
                                                    ) &&
                                                        activeKey ==
                                                            'expenses') ||
                                                    permsArray.includes(
                                                        'expenses_approve_reject'
                                                    )
                                                "
                                            >
                                                <template #icon
                                                    ><EyeOutlined
                                                /></template>
                                            </a-button>
                                            <a-button
                                                v-if="
                                                    item.status == 'pending' &&
                                                    ((permsArray.includes(
                                                        'admin'
                                                    ) &&
                                                        activeKey ==
                                                            'expenses') ||
                                                        permsArray.includes(
                                                            'expenses_approve_reject'
                                                        ))
                                                "
                                                @click="
                                                    updateExpenseStatus(item)
                                                "
                                                type="primary"
                                            >
                                                <template #icon
                                                    ><CheckOutlined
                                                /></template>
                                            </a-button>

                                            <a-button
                                                v-if="
                                                    item.status == 'pending' &&
                                                    (permsArray.includes(
                                                        'admin'
                                                    ) ||
                                                        permsArray.includes(
                                                            'expenses_approve_reject'
                                                        ))
                                                "
                                                @click="rejectExpense(item.xid)"
                                                type="primary"
                                                danger
                                            >
                                                <template #icon
                                                    ><CloseOutlined
                                                /></template>
                                            </a-button>
                                        </a-space>
                                    </template>
                                    <a-skeleton
                                        avatar
                                        :title="false"
                                        :loading="!!item.loading"
                                        active
                                    >
                                        <a-list-item-meta
                                            :description="item.reason"
                                        >
                                            <template #title>
                                                <a-typography-text strong>
                                                    {{ item.user.name }}
                                                </a-typography-text>
                                                ({{
                                                    item.date_time
                                                        ? formatDate(
                                                              item.date_time
                                                          )
                                                        : formatDate(
                                                              item.start_date
                                                          )
                                                }}
                                                -
                                                {{ formatDate(item.end_date) }})
                                            </template>
                                            <template #avatar>
                                                <a-avatar
                                                    :src="
                                                        item.user
                                                            .profile_image_url
                                                    "
                                                />
                                            </template>
                                        </a-list-item-meta>
                                    </a-skeleton>
                                </a-list-item>
                                <hr />
                            </template>
                        </a-list>
                    </perfect-scrollbar></div
            ></a-tab-pane>
        </a-tabs>
    </a-card>
    <UpdateStatus
        :data="visibleStatusData"
        :visible="visibleStatus"
        @close="closedStatus"
        :pageTitle="$t('expense.update_expense_status')"
        :tabVisible="tabVisible"
    />
    <LeaveView
        :data="modalData"
        :visible="leaveVisible"
        :pageTilte="activeKey"
        @close="closeLeaves"
    />
    <ExpenseView
        :data="modalData"
        :visible="expenseVisible"
        :pageTilte="activeKey"
        @close="closeExpense"
    />
</template>

<script>
import { onMounted, ref, createVNode } from "vue";
import {
    CheckOutlined,
    CloseOutlined,
    BellOutlined,
    DoubleRightOutlined,
    ExclamationCircleFilled,
    EyeOutlined,
    ExclamationCircleOutlined,
} from "@ant-design/icons-vue";
import { Modal, notification } from "ant-design-vue";
import { useI18n } from "vue-i18n";
import common from "../../../common/composable/common";
import UpdateStatus from "../accountings/expenses/UpdateStatus.vue";
import LeaveView from "../../views/leave/leaves/View.vue";
import ExpenseView from "../../views/accountings/expenses/View.vue";

export default {
    components: {
        CheckOutlined,
        CloseOutlined,
        BellOutlined,
        EyeOutlined,
        DoubleRightOutlined,
        UpdateStatus,
        LeaveView,
        ExpenseView,
        ExclamationCircleFilled,
        ExclamationCircleOutlined,
    },
    setup() {
        const { permsArray, dayjs, formatTime, formatDate } = common();
        const { t } = useI18n();
        const initLoading = ref(false);
        const pendingLeaves = ref([]);
        const pendingExpenses = ref([]);
        const activeKey = ref("leaves");

        const visibleStatusData = ref({});
        const visibleStatus = ref(false);
        const url = ref("");
        const tabVisible = ref(false);
        const leaveVisible = ref(false);
        const expenseVisible = ref(false);
        const modalData = ref({});

        onMounted(() => {
            if (
                permsArray.value.includes("admin") ||
                permsArray.value.includes("leaves_approve_reject")
            ) {
                activeKey.value = "leaves";
                refetchLeaves();
            } else if (permsArray.value.includes("expenses_approve_reject")) {
                activeKey.value = "expenses";
                refetchExpense();
            }
        });

        const viewExpense = (item) => {
            modalData.value = {};
            modalData.value = item;
            expenseVisible.value = true;
        };
        const closeExpense = () => {
            expenseVisible.value = false;
        };

        const viewLeaves = (item) => {
            modalData.value = {};
            modalData.value = item;
            leaveVisible.value = true;
        };
        const closeLeaves = () => {
            leaveVisible.value = false;
        };

        const updateExpenseStatus = (item) => {
            visibleStatusData.value = item;
            refetchExpense();
            visibleStatus.value = true;
        };
        const closedStatus = () => {
            visibleStatus.value = false;
            refetchExpense();
        };

        const refetchLeaves = () => {
            initLoading.value = true;
            axiosAdmin.get("hrm/dashboard/pending-leaves").then((response) => {
                pendingLeaves.value = response.data.pending_leaves;
                initLoading.value = false;
            });
        };

        const refetchExpense = () => {
            initLoading.value = true;
            axiosAdmin.get("hrm/dashboard/pending-expense").then((response) => {
                pendingExpenses.value = response.data.pending_expense;
                initLoading.value = false;
            });
        };

        const onTabChange = (value) => {
            activeKey.value = value;
            if (activeKey.value === "leaves") {
                refetchLeaves();
            } else if (activeKey.value === "expenses") {
                refetchExpense();
            }
        };

        const approveLeave = (xid) => {
            Modal.confirm({
                title: t("common.approved") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("common.approved_message"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),

                onOk() {
                    return axiosAdmin
                        .post(`leaves/update-status/${xid}`, {
                            status: "approved",
                        })
                        .then(() => {
                            refetchLeaves();
                            notification.success({
                                message: t("common.success"),
                                description: t(`common.status_changed`),
                                placement: "bottomRight",
                            });
                        });
                },
            });
        };

        const approveExpense = (xid) => {
            Modal.confirm({
                title: t("common.approved") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("common.approved_message"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),

                onOk() {
                    return axiosAdmin
                        .post(`expenses/update-status/${xid}`, {
                            status: "approved",
                        })
                        .then(() => {
                            refetchExpense();
                            notification.success({
                                message: t("common.success"),
                                description: t(`common.status_changed`),
                                placement: "bottomRight",
                            });
                        });
                },
            });
        };

        const rejectLeave = (xid) => {
            Modal.confirm({
                title: t("common.rejected") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("common.rejected_message"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),

                onOk() {
                    return axiosAdmin
                        .post(`leaves/update-status/${xid}`, {
                            status: "rejected",
                        })
                        .then(() => {
                            notification.success({
                                message: t("common.success"),
                                description: t(`common.status_changed`),
                                placement: "bottomRight",
                            });

                            refetchLeaves();
                        });
                },
            });
        };
        const rejectExpense = (xid) => {
            Modal.confirm({
                title: t("common.rejected") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("common.rejected_message"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),

                onOk() {
                    return axiosAdmin
                        .post(`update-expense-status/${xid}`, {
                            status: "rejected",
                        })
                        .then(() => {
                            notification.success({
                                message: t("common.success"),
                                description: t(`common.status_changed`),
                                placement: "bottomRight",
                            });
                            refetchExpense();
                        });
                },
            });
        };

        return {
            initLoading,
            pendingLeaves,
            permsArray,
            activeKey,
            formatDate,
            onTabChange,
            approveLeave,
            rejectLeave,
            visibleStatusData,
            updateExpenseStatus,
            visibleStatus,
            closedStatus,
            tabVisible,
            viewExpense,
            closeExpense,
            modalData,
            leaveVisible,
            expenseVisible,
            viewLeaves,
            closeLeaves,
            pendingExpenses,
            approveExpense,
            rejectExpense,
        };
    },
};
</script>

<style>
.pending-leave-hrm .ps {
    height: 378px;
}
hr {
    border: none;
    border-top: 1px solid var(--ant-border-color);
    margin: 4px 0;
}
.segmented-tabs .ant-tabs-nav {
    display: flex;
    justify-content: center;
    background: #f5f5f5; /* Light background */
    padding: 5px;
    width: fit-content;
    margin: auto;
    margin-bottom: 20px;
}

.segmented-tabs .ant-tabs-tab {
    display: flex;
    align-items: center; /* Ensure vertical centering */
    justify-content: center; /* Ensure horizontal centering */
    padding: 4px 8px;
    background-color: #eeeeee;
    transition: all 0.3s ease;
    font-weight: 600;
    text-align: center;
    min-width: 240px;
    color: #595959;
    cursor: pointer;
}

.segmented-tabs .ant-tabs-tab + .ant-tabs-tab {
    margin-left: -8px; /* Merge borders for a seamless effect */
}

.segmented-tabs .ant-tabs-tab-active {
    background-color: #ffffff;
    color: #ffffff !important; /* White text for active tab */
    font-weight: bold;
}

.segmented-tabs .ant-tabs-tab:hover {
    background-color: #c4c1c1;
    color: #000000 !important;
}

.segmented-tabs .ant-tabs-ink-bar {
    display: none; /* Hide underline */
}
</style>
