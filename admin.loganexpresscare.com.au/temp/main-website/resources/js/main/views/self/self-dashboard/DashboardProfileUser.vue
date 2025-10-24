<template>
    <a-card
        class="profile-card"
        :headStyle="{ background: '#333333', color: 'white' }"
    >
        <template #title>
            <div class="profile-header">
                <a-avatar :size="35" :src="profile?.profile_image_url" />
                <div class="profile-info">
                    <div class="profile-name">
                        <strong>{{ profile?.name }}</strong>
                    </div>
                    <div class="profile-meta">
                        <span class="designation">{{
                            profile.designation != null
                                ? profile.designation.name
                                : ""
                        }}</span>
                        <span class="divider-dot"> ● </span>
                        <span class="department">{{
                            profile.department != null
                                ? profile.department.name
                                : ""
                        }}</span>
                    </div>
                </div>
                <a-tooltip title="Edit Profile">
                    <router-link
                        :to="{
                            name: 'admin.self.profile.index',
                        }"
                    >
                        <a-button type="text" shape="circle" class="edit-btn">
                            <EditOutlined />
                        </a-button>
                    </router-link>
                </a-tooltip>
            </div>
        </template>

        <div class="profile-details">
            <div class="detail-item">
                <span class="detail-label">
                    {{ $t("hrm_dashboard.phone_number") }}</span
                >
                <span class="detail-value">{{
                    profile.phone != null ? profile.phone : "-"
                }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">
                    {{ $t("hrm_dashboard.email") }}</span
                >
                <span class="detail-value">{{
                    profile.email != null ? profile.email : "-"
                }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">{{
                    $t("hrm_dashboard.address")
                }}</span>
                <span class="detail-value">{{
                    profile.address != null ? profile.address : "-"
                }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">{{
                    $t("hrm_dashboard.joining_date")
                }}</span>
                <span class="detail-value">{{
                    profile.joining_date != null ? profile.joining_date : "-"
                }}</span>
            </div>
        </div>
    </a-card>
</template>

<script>
import { ref, watchEffect, defineComponent, watch } from "vue";
import { EditOutlined } from "@ant-design/icons-vue";
import { theme } from "ant-design-vue";

export default defineComponent({
    props: ["data"],

    components: {
        EditOutlined,
    },

    setup(props) {
        const { token } = theme.useToken();
        const profile = ref({});

        watchEffect(() => {
            document.documentElement.style.setProperty(
                "--text-color",
                token.colorText
            );
            document.documentElement.style.setProperty(
                "--secondary-text-color",
                token.colorTextSecondary
            );
            document.documentElement.style.setProperty(
                "--background-color",
                token.colorBgContainer
            );
        });

        watch(props, (newVal) => {
            profile.value = newVal.data?.user;
        });

        return {
            profile,
        };
    },
});
</script>

<style scoped>
.profile-card {
    max-width: 100%;
    color: var(--text-color);
}

.profile-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.profile-info {
    display: flex;
    flex-direction: column;
    line-height: 1;
    margin-inline-start: 12px;
    flex-grow: 1;
    align-items: start;
}

.profile-name {
    font-size: 14px;
    color: var(--text-color);
    align-self: start;
}

.edit-btn {
    color: var(--text-color);
    background: transparent;
    font-size: 14px;
}

.profile-meta {
    display: flex;
    align-items: center;
    font-size: 12px;
    color: var(--secondary-text-color);
    margin-top: 2px;
}

.designation,
.department {
    font-size: 14px;
    color: var(--text-color);
    margin-top: 5px;
}

.divider-dot {
    font-size: 14px;
    color: orange;
    margin: 0 5px;
    margin-top: 5px;
}

.profile-details {
    margin-top: 10px;
}

.detail-item {
    margin-bottom: 6px;
}

.detail-label {
    display: block;
    font-size: 13px;
    color: var(--secondary-text-color);
}

.detail-value {
    font-size: 15px;
    font-weight: 500;
}
</style>
