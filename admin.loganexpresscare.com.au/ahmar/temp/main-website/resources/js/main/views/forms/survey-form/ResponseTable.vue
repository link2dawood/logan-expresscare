<template>
    <admin-page-table-content :isPageTableContent="isPageTableContent">
        <template v-if="$slots.tabs">
            <slot name="tabs" :setUrlData="setUrlData"></slot>
        </template>
        <a-row>
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table
                        :data-source="table.data"
                        :pagination="table.pagination"
                        :loading="table.loading"
                        @change="handleTableChange"
                        bordered
                        size="middle"
                        :columns="responseColumns"
                        :row-key="(record) => record.xid"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'user_id'">
                                {{ record.user.name }}
                            </template>
                            <template v-if="column.dataIndex === 'feedback'">
                                {{ record.feedback.title }}
                            </template>
                            <template v-if="column.dataIndex === 'submit_date'">
                                {{
                                    record.submit_date
                                        ? formatDate(record.submit_date)
                                        : "-"
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'rating'">
                                <div v-if="record.rating">
                                    <a-rate :value="record.rating" allow-half disabled />
                                    {{ formatRating(record.rating) }}
                                </div>
                                <span v-else>-</span>
                            </template>

                            <template v-if="column.dataIndex === 'action'">
                                <a-button
                                    v-if="record.feedback_given == 1"
                                    type="primary"
                                    @click="viewResponse(record)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><EyeOutlined /></template>
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
    <ViewResponse
        :data="recordData"
        :visible="visibleResponse"
        :pageTitle="pageTitle"
        @close="closed"
        @updateRatingResponse="setUrlData()"
    />
    <user-view-page :visible="userOpen" :userId="userId" @closed="closeUser" />
</template>

<script>
import { ref, watch } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    AppstoreOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import UserInfo from "../../../../common/components/user/UserInfo.vue";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import ViewResponse from "./ViewResponse.vue";
import datatable from "../../../../common/composable/datatable";

export default {
    props: {
        selectable: {
            default: false,
        },
        isPageTableContent: {
            default: true,
        },
        tableSize: {
            default: "large",
        },
        bordered: {
            default: false,
        },
        visible: {
            default: false,
        },
        filters: {
            default: {},
        },
        responseColumns: {
            default: [],
        },
        perPageItems: Number,
    },
    emits: ["onRowSelection"],
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AppstoreOutlined,
        EyeOutlined,
        ViewResponse,
        UserInfo,
        UserListDisplay,
    },
    setup(props, { emit }) {
        const { formatDate } = common();

        const datatableVariables = datatable();
        const recordData = ref({});
        const visibleResponse = ref(false);
        const pageTitle = ref("");

        const formatRating = (item) => {
            return item.toFixed(2);
        };

        const viewResponse = (item) => {
            visibleResponse.value = true;
            recordData.value = item;
            pageTitle.value = item.feedback.title;
        };

        const closed = () => {
            visibleResponse.value = false;
        };

        const userOpen = ref(false);
        const userId = ref(undefined);

        const openUserView = (item) => {
            userId.value = item.x_to_user_id;
            userOpen.value = true;
        };

        const closeUser = () => {
            userId.value = undefined;
            userOpen.value = false;
        };

        const setUrlData = () => {
            const tableFilter = props.filters;

            var extraFilterObject = {};

            if (tableFilter.feedback_id) {
                extraFilterObject.feedback_id = tableFilter.feedback_id;
            }
            if (tableFilter.tab_key) {
                extraFilterObject.type = tableFilter.tab_key;
            }
            if (tableFilter.user_id) {
                extraFilterObject.user_id = tableFilter.user_id;
            }
            datatableVariables.tableUrl.value = {
                url: `get-assigned-survey?fields=id,xid,rating,allowed,data,rating_details,submit_date,feedback_given,user_id,x_user_id,user{id,xid,name,email},feedback_id,x_feedback_id,feedback{id,xid,title,last_date,visible_to,form_id,x_form_id,description,feedback_form_fields}`,
                extraFilters: extraFilterObject,
            };

            datatableVariables.fetch({
                page: 1,
            });
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (newVal) {
                    setUrlData();
                }
            }
        );

        return {
            ...datatableVariables,
            formatDate,
            setUrlData,
            userOpen,
            userId,
            openUserView,
            closeUser,
            formatRating,
            visibleResponse,
            recordData,
            pageTitle,
            viewResponse,
            closed,
        };
    },
};
</script>
