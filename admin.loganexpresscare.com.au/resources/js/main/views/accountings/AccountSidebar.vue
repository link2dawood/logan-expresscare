<template>
    <div class="setting-sidebar">
        <perfect-scrollbar
            :options="{
                wheelSpeed: 1,
                swipeEasing: true,
                suppressScrollX: true,
            }"
        >
            <a-menu v-model:selectedKeys="selectedKeys">
                <a-menu-item
                    v-if="
                        permsArray.includes('accounts_view') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.accounts.index' })"
                    key="accounts"
                >
                    <template #icon>
                        <DollarOutlined />
                    </template>
                    {{ $t("menu.accounts") }}
                </a-menu-item>
                <a-menu-item
                    v-if="
                        permsArray.includes('payees_view') || permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.payees.index' })"
                    key="payees"
                >
                    <template #icon>
                        <PaperClipOutlined />
                    </template>
                    {{ $t("menu.payees") }}
                </a-menu-item>
                <a-menu-item
                    v-if="
                        permsArray.includes('payers_view') || permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.payers.index' })"
                    key="payers"
                >
                    <template #icon>
                        <FileSyncOutlined />
                    </template>
                    {{ $t("menu.payers") }}
                </a-menu-item>
                <a-menu-item
                    v-if="
                        permsArray.includes('deposit_categories_view') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.deposit_categories.index' })"
                    key="deposit_categories"
                >
                    <template #icon>
                        <FileSyncOutlined />
                    </template>
                    {{ $t("menu.deposit_categories") }}
                </a-menu-item>
                <a-menu-item
                    v-if="
                        permsArray.includes('expense_categories_view') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.expense_categories.index' })"
                    key="expense_categories"
                >
                    <template #icon>
                        <FileSyncOutlined />
                    </template>
                    {{ $t("menu.expense_categories") }}
                </a-menu-item>

                <a-menu-item
                    v-if="
                        permsArray.includes('deposits_view') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.deposits.index' })"
                    key="deposits"
                >
                    <template #icon>
                        <PayCircleOutlined />
                    </template>
                    {{ $t("menu.deposits") }}
                </a-menu-item>
                <a-menu-item
                    v-if="
                        permsArray.includes('expenses_view') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.expenses.index' })"
                    key="expenses"
                >
                    <template #icon>
                        <FileSyncOutlined />
                    </template>
                    {{ $t("menu.expenses") }}
                </a-menu-item>
            </a-menu>
        </perfect-scrollbar>
    </div>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import {
    ScheduleOutlined,
    TabletOutlined,
    BarsOutlined,
    DollarOutlined,
    PayCircleOutlined,
    ForkOutlined,
    FileSyncOutlined,
    PaperClipOutlined,
} from "@ant-design/icons-vue";
import { useRoute } from "vue-router";
import common from "../../../common/composable/common";

export default defineComponent({
    components: {
        ScheduleOutlined,
        TabletOutlined,
        BarsOutlined,
        DollarOutlined,
        PayCircleOutlined,
        ForkOutlined,
        FileSyncOutlined,
        PaperClipOutlined,
    },
    setup() {
        const { appSetting, user, permsArray, appModules, appType } = common();
        const route = useRoute();
        const selectedKeys = ref([]);

        onMounted(() => {
            const menuKey =
                typeof route.meta.menuKey == "function"
                    ? route.meta.menuKey(route)
                    : route.meta.menuKey;

            selectedKeys.value = [menuKey.replace("-", "_")];
        });

        watch(route, (newVal, oldVal) => {
            const menuKey =
                typeof newVal.meta.menuKey == "function"
                    ? newVal.meta.menuKey(newVal)
                    : newVal.meta.menuKey;

            selectedKeys.value = [menuKey.replace("-", "_")];
        });

        return {
            permsArray,

            selectedKeys,
            appType,
        };
    },
});
</script>

<style lang="less">
.setting-sidebar .ps {
    height: calc(100vh - 145px);
}
</style>
