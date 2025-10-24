<template>
    <a-card class="leave-policy-card">
        <div class="leave-policy-content">
            <div class="policy-info">
                <div class="policy-title">
                    {{ $t("menu.company_policies") }}
                </div>
                <div class="policy-updated">
                    {{ $t("company_policy.latest_compony_policy") }} :
                    {{ latestPolicy ? latestPolicy : "N/A" }}
                </div>
            </div>
            <router-link
                :to="{
                    name: 'admin.self.company-policies.index',
                }"
            >
                <a-button type="default" class="view-button">
                    {{ $t("holiday.view_all") }}
                </a-button>
            </router-link>
        </div>
    </a-card>
</template>

<script>
import { onMounted, ref, watch } from "vue";
import { ExclamationCircleFilled } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";

export default {
    props: ["data"],
    components: {
        ExclamationCircleFilled,
    },
    setup(props, { emit }) {
        const { t } = useI18n();
        const latestPolicy = ref("");

        watch(
            props,
            (newVal, oldVal) => {
                latestPolicy.value = newVal.data.getLatestPolicy?.title;
            },
            { deep: true }
        );

        return {
            latestPolicy,
        };
    },
};
</script>

<style scoped>
.leave-policy-card {
    background: #3e6f7b;
    color: white;
    padding: 24px;
    max-width: 100%;
}

.leave-policy-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.policy-info {
    display: flex;
    flex-direction: column;
}

.policy-title {
    font-weight: bold;
    font-size: 14px;
}

.policy-updated {
    font-size: 12px;
    opacity: 0.9;
}

.view-button {
    background: white;
    color: black;
    font-weight: bold;
    border-radius: 4px;
    padding: 4px 10px;
}
</style>
