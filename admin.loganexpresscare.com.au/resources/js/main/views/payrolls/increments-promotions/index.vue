<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header
                :title="$t(`menu.increments_promotions`)"
                class="p-0"
            />
        </template>
        <template #breadcrumb>
            <a-row align="middle" justify="space-between">
                <a-col>
                    <a-breadcrumb separator="-" style="font-size: 12px">
                        <a-breadcrumb-item>
                            <router-link
                                :to="{ name: 'admin.dashboard.index' }"
                            >
                                {{ $t(`menu.dashboard`) }}
                            </router-link>
                        </a-breadcrumb-item>
                        <a-breadcrumb-item>
                            {{ $t(`menu.payrolls`) }}
                        </a-breadcrumb-item>
                        <a-breadcrumb-item>
                            {{ $t(`menu.increments_promotions`) }}
                        </a-breadcrumb-item>
                    </a-breadcrumb>
                </a-col>
                <a-col>
                    <a-switch
                        v-model:checked="salaryVisibleAll"
                        :checked-children="$t('common.visible')"
                        :un-checked-children="$t('common.hidden')"
                        :style="{ top: '-20px', marginRight: '2px' }"
                    />
                </a-col>
            </a-row>
        </template>
    </AdminPageHeader>

    <IncreamentTable
        :filters="filters"
        tableSize="middle"
        :bordered="true"
        :selectable="true"
        :salaryVisibleAll="salaryVisibleAll"
    >
        <template
            #actions="{ addItem, showSelectedDeleteConfirm, setUrlData, table }"
        >
            <admin-page-filters>
                <a-row :gutter="[16, 16]">
                    <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                        <a-space>
                            <template
                                v-if="
                                    permsArray.includes(
                                        'increments_promotions_create'
                                    ) || permsArray.includes('admin')
                                "
                            >
                                <a-button type="primary" @click="addItem">
                                    <PlusOutlined />
                                    {{ $t("increment_promotion.add") }}
                                </a-button>
                            </template>
                            <a-button
                                v-if="
                                    table.selectedRowKeys.length > 0 &&
                                    (permsArray.includes(
                                        'increments_promotions_delete'
                                    ) ||
                                        permsArray.includes('admin'))
                                "
                                type="primary"
                                @click="showSelectedDeleteConfirm"
                                danger
                            >
                                <template #icon><DeleteOutlined /></template>
                                {{ $t("common.delete") }}
                            </a-button>
                        </a-space>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                        <a-row :gutter="[16, 16]" justify="end">
                            <a-col
                                v-if="
                                    permsArray.includes(
                                        'increments_promotions_view'
                                    ) || permsArray.includes('admin')
                                "
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="8"
                                :xl="8"
                            >
                                <a-select
                                    v-model:value="filters.user_id"
                                    @change="setUrlData"
                                    show-search
                                    style="width: 100%"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('leave.user'),
                                        ])
                                    "
                                    :allowClear="true"
                                    optionFilterProp="title"
                                >
                                    <a-select-option
                                        v-for="allUser in allUsers"
                                        :key="allUser.xid"
                                        :value="allUser.xid"
                                        :title="allUser.name"
                                        ><user-list-display
                                            :user="allUser"
                                            whereToShow="select"
                                        />
                                    </a-select-option>
                                </a-select>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                                <a-range-picker
                                    :format="appSetting.date_format"
                                    valueFormat="YYYY-MM-DD"
                                    v-model:value="filters.dates"
                                    :style="{ width: '100%' }"
                                    @change="setUrlData"
                                />
                            </a-col>
                        </a-row>
                    </a-col>
                </a-row>
            </admin-page-filters>
        </template>
        <template #tabs="{ setUrlData }">
            <a-row>
                <a-col :span="24">
                    <a-tabs
                        v-model:activeKey="filters.type"
                        @change="setUrlData"
                    >
                        <a-tab-pane key="all" :tab="`${$t('common.all')}`" />
                        <a-tab-pane
                            key="increment"
                            :tab="`${$t('increment_promotion.increment')}`"
                        />
                        <a-tab-pane
                            key="promotion"
                            :tab="`${$t('increment_promotion.promotion')}`"
                        />
                        <a-tab-pane
                            key="increment_promotion"
                            :tab="`${$t(
                                'increment_promotion.increment_promotion'
                            )}`"
                        />
                        <a-tab-pane
                            key="decrement"
                            :tab="`${$t('increment_promotion.decrement')}`"
                        />
                        <a-tab-pane
                            key="decrement_demotion"
                            :tab="`${$t(
                                'increment_promotion.decrement_demotion'
                            )}`"
                        />
                    </a-tabs>
                </a-col>
            </a-row>
        </template>
    </IncreamentTable>
</template>
<script>
import { onMounted, ref, createVNode } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import { useI18n } from "vue-i18n";
import UserListDisplay from "../../../../common/components/user/UserListDisplay.vue";
import IncreamentTable from "./IncreamentTable.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        EyeOutlined,

        AdminPageHeader,
        IncreamentTable,
        UserListDisplay,
    },
    setup() {
        const { t } = useI18n();
        const { permsArray, appSetting, formatDate, formatAmountCurrency } =
            common();
        const allUsers = ref([]);
        const staffMembersUrl = "users";
        const filters = ref({ user_id: undefined, dates: [], type: "all" });
        const salaryVisibleAll = ref(false);

        onMounted(() => {
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);
            Promise.all([staffMemberPromise]).then(([staffMemberResponse]) => {
                allUsers.value = staffMemberResponse.data;
            });
        });

        return {
            permsArray,
            appSetting,
            allUsers,
            filters,
            salaryVisibleAll,
        };
    },
};
</script>
