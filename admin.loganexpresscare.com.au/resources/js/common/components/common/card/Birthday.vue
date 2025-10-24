<template>
    <a-card :bodyStyle="{ padding: '0px' }">
        <template #title>
            <BellOutlined />
            {{ $t("hrm_dashboard.birthday") }}
        </template>

        <div class="pending-leave-hrm">
            <perfect-scrollbar
                :options="{
                    wheelSpeed: 1,
                    swipeEasing: true,
                    suppressScrollX: true,
                }"
            >
                <a-list
                    :loading="initLoading"
                    item-layout="horizontal"
                    :data-source="birthdays"
                >
                    <template #renderItem="{ item }">
                        <a-list-item>
                            <template #extra>
                                <a-space>
                                    <a-button type="primary">
                                        <template #icon
                                            ><MailOutlined />{{
                                                $t("common.send")
                                            }}</template
                                        >
                                    </a-button>
                                </a-space>
                            </template>
                            <a-skeleton
                                avatar
                                :title="false"
                                :loading="!!item.loading"
                                active
                            >
                                <a-list-item-meta :description="item.reason">
                                    <template #title>
                                        <a-typography-text strong>
                                            {{ item.user.name }}
                                        </a-typography-text>
                                        ({{ formatDate(item.start_date) }} -
                                        {{ formatDate(item.end_date) }})
                                    </template>
                                    <template #avatar>
                                        <a-avatar :src="item.user.profile_image_url" />
                                    </template>
                                </a-list-item-meta>
                            </a-skeleton>
                        </a-list-item>
                    </template>
                </a-list>
                <a-result
                    v-show="initLoading == false && birthdays.length <= 0"
                    title="Your operation has been executed"
                >
                </a-result>
            </perfect-scrollbar>
        </div>
    </a-card>
</template>

<script>
import { onMounted, ref, createVNode } from "vue";
import {
    MailOutlined,
    BellOutlined,
    DoubleRightOutlined,
    ExclamationCircleOutlined,
} from "@ant-design/icons-vue";
import { Modal, notification } from "ant-design-vue";
import { useI18n } from "vue-i18n";
import common from "../../common/composable/common";

export default {
    props: ["data"],
    components: {
        MailOutlined,
        BellOutlined,
        DoubleRightOutlined,
    },
    setup() {
        const { dayjs, formatTime, formatDate } = common();
        const initLoading = ref(false);
        const birthdays = ref([]);

        onMounted(() => {
            refetchLeaves();
        });

        const refetchLeaves = () => {
            initLoading.value = true;

            axiosAdmin.get("hrm/dashboard/pending-leaves").then((response) => {
                birthdays.value = response.data.pending_leaves;

                initLoading.value = false;
            });
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
                        .post(`leaves/update-status/${xid}`, { status: "approved" })
                        .then((successResponse) => {
                            refetchLeaves();
                            // Update Visible Subscription Modules
                            notification.success({
                                message: t("common.success"),
                                description: t(`common.status_changed`),
                                placement: "bottomRight",
                            });
                        })
                        .catch(() => {});
                },
                onCancel() {},
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
                        .post(`leaves/update-status/${xid}`, { status: "rejected" })
                        .then((successResponse) => {
                            refetchLeaves();
                            // Update Visible Subscription Modules
                            notification.success({
                                message: t("common.success"),
                                description: t(`common.status_changed`),
                                placement: "bottomRight",
                            });
                        })
                        .catch(() => {});
                },
                onCancel() {},
            });
        };

        return {
            initLoading,
            birthdays,
            formatDate,
            approveLeave,
            rejectLeave,
        };
    },
};
</script>

<style lang="less">
.pending-leave-hrm .ps {
    height: 400px;
}
</style>
