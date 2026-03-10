<template>
    <a-drawer
        :title="pageTitle"
        :width="720"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
    >
        <a-spin :spinning="loading">
            <template v-if="data && data.xid">
                <a-row :gutter="[16, 16]" v-for="(fields, sectionTitle) in sections" :key="sectionTitle">
                    <a-col :span="24">
                        <div class="referral-view-section">
                            <div class="referral-view-section-title">{{ sectionTitle }}</div>
                            <a-descriptions
                                :column="1"
                                layout="vertical"
                                size="small"
                                bordered
                            >
                                <a-descriptions-item
                                    v-for="(label, key) in fields"
                                    :key="key"
                                    :label="label"
                                >
                                    {{ formatValue(key, data[key]) }}
                                </a-descriptions-item>
                            </a-descriptions>
                        </div>
                    </a-col>
                </a-row>
            </template>
        </a-spin>
        <template #footer>
            <a-button type="primary" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-drawer>
</template>

<script>
import { watch, ref } from "vue";
import common from "../../../common/composable/common";

const sections = {
    "Participant Details": {
        first_name: "First Name",
        last_name: "Last Name",
        dob: "Date of Birth",
        gender: "Gender",
        home_address: "Home Address",
        phone_number: "Phone",
        email_address: "Email Address",
        ndis_number: "NDIS Number",
        has_guardian: "Has Guardian",
    },
    "Cultural Information": {
        country_of_birth: "Country of Birth",
        require_interpreter: "Requires Interpreter",
        relevant_culture: "Cultural Considerations",
        identify_aboriginal: "Aboriginal/Torres Strait Islander",
    },
    "Service Requirements": {
        primary_service: "Primary Service",
        hours_required: "Hours Required",
        secondary_service: "Secondary Service",
        additional_service: "Additional Service",
        participant_conditions: "Conditions/Disability",
        extra_information: "Extra Information",
        special_assessments: "Special Assessments",
        practitioner_notes: "Practitioner Notes",
    },
    "Referrer Information": {
        referral_source: "Completed By",
        referrer_name: "Referrer Name",
        referrer_relationship: "Referrer Relationship",
        referrer_phone: "Referrer Phone",
        referrer_email: "Referrer Email",
    },
    "Consent & Meta": {
        consent_privacy: "Privacy Consent",
        consent_contact: "Contact Consent",
        submission_date: "Submission Date",
        ip_address: "IP Address",
        status: "Status",
    },
};

export default {
    name: "ReferralView",
    props: {
        visible: Boolean,
        referralXid: String,
        pageTitle: { type: String, default: "" },
    },
    emits: ["close"],
    setup(props, { emit }) {
        const { formatDateTime } = common();
        const loading = ref(false);
        const data = ref(null);

        function friendlyLabel(value) {
            if (value == null || value === "") return "N/A";
            return String(value)
                .trim()
                .replace(/_/g, " ")
                .replace(/\b\w/g, (c) => c.toUpperCase());
        }

        function formatValue(key, value) {
            if (value === null || value === undefined || value === "") return "N/A";
            const text = String(value).trim();
            if (key === "submission_date") {
                return formatDateTime(value) || text;
            }
            if (["consent_privacy", "consent_contact", "has_guardian", "require_interpreter"].includes(key)) {
                return /^yes$/i.test(text) ? "Yes" : "No";
            }
            if (["referral_source", "gender", "identify_aboriginal", "primary_service", "secondary_service", "additional_service", "status"].includes(key)) {
                return friendlyLabel(text);
            }
            return text;
        }

        function fetchReferral() {
            if (!props.referralXid || !props.visible) return;
            loading.value = true;
            data.value = null;
            window.axiosAdmin
                .get(`referrals/${props.referralXid}`)
                .then((res) => {
                    data.value = res.data || res;
                })
                .finally(() => {
                    loading.value = false;
                });
        }

        watch(
            () => [props.visible, props.referralXid],
            () => {
                if (props.visible && props.referralXid) fetchReferral();
                else data.value = null;
            },
            { immediate: true }
        );

        const onClose = () => {
            emit("close");
        };

        return {
            data,
            sections,
            loading,
            formatValue,
            onClose,
        };
    },
};
</script>

<style scoped>
.referral-view-section {
    margin-bottom: 16px;
}
.referral-view-section-title {
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 14px;
}
</style>
