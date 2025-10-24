<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header
                title="Application Details"
                @back="$router.push({ name: 'admin.staff_onboarding.index' })"
                class="p-0"
            />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t('menu.dashboard') }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.staff_onboarding.index' }">
                        Staff Onboarding
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ application.first_name }} {{ application.last_name }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-spin :spinning="loading">
        <a-row :gutter="[16, 16]">
            <!-- Main Content -->
            <a-col :xs="24" :md="16">
                <!-- Personal Information -->
                <a-card title="Personal Information" class="mb-4">
                    <a-descriptions bordered :column="2">
                        <a-descriptions-item label="First Name">{{ application?.first_name || '-' }}</a-descriptions-item>
                        <a-descriptions-item label="Last Name">{{ application?.last_name || '-' }}</a-descriptions-item>
                        <a-descriptions-item label="Email">{{ application?.email || '-' }}</a-descriptions-item>
                        <a-descriptions-item label="Phone">{{ application?.phone || '-' }}</a-descriptions-item>
                        <a-descriptions-item label="Position">
                            <span style="text-transform: capitalize">
                                {{ application?.position?.replace('_', ' ') || '-' }}
                            </span>
                        </a-descriptions-item>
                        <a-descriptions-item label="Employment Type">
                            <span style="text-transform: capitalize">
                                {{ application?.employment_type || '-' }}
                            </span>
                        </a-descriptions-item>
                        <template v-if="application?.employment_type === 'contractor'">
                            <a-descriptions-item label="ABN">{{ application?.abn || '-' }}</a-descriptions-item>
                            <a-descriptions-item label="Business Name">{{ application?.business_name || '-' }}</a-descriptions-item>
                        </template>
                    </a-descriptions>
                </a-card>

                <!-- Documents -->
                <a-card title="Documents & Certificates" class="mb-4">
                    <a-list item-layout="horizontal" :data-source="documents">
                        <template #renderItem="{ item }">
                            <a-list-item>
                                <a-list-item-meta>
                                    <template #title>
                                        <a-space>
                                            {{ item.label }}
                                            <a-tag v-if="item.expiry && isExpired(item.expiry)" color="red">EXPIRED</a-tag>
                                            <a-tag v-else-if="item.expiry && isExpiringSoon(item.expiry)" color="orange">EXPIRING SOON</a-tag>
                                        </a-space>
                                    </template>
                                    <template #description v-if="item.expiry">
                                        Expiry: {{ formatDate(item.expiry) }}
                                    </template>
                                </a-list-item-meta>
                                <template #actions>
                                    <div v-if="item.value" style="display: flex; gap: 8px; align-items: center;">
                                        <a-button 
                                            type="primary" 
                                            size="small" 
                                            @click="viewFile(item.value)"
                                            title="View File"
                                        >
                                            <EyeOutlined /> View
                                        </a-button>
                                        <a-button 
                                            type="default" 
                                            size="small" 
                                            @click="downloadFile(item.value, item.label)"
                                            title="Download File"
                                        >
                                            <DownloadOutlined /> Download
                                        </a-button>
                                    </div>
                                    <a-tag v-else color="red">
                                        <CloseOutlined /> Not Uploaded
                                    </a-tag>
                                </template>
                            </a-list-item>
                        </template>
                    </a-list>
                </a-card>

                <!-- Acknowledgments -->
                <a-card title="Acknowledgments">
                    <a-row :gutter="[16, 16]">
                        <a-col :span="12" v-for="ack in acknowledgments" :key="ack.key">
                            <a-space>
                                <CheckCircleOutlined v-if="ack.value" style="color: #52c41a" />
                                <CloseCircleOutlined v-else style="color: #f5222d" />
                                {{ ack.label }}
                            </a-space>
                        </a-col>
                    </a-row>
                </a-card>
            </a-col>

            <!-- Sidebar -->
            <a-col :xs="24" :md="8">
                <!-- Status Management -->
                <a-card title="Status Management" class="mb-4">
                    <a-form layout="vertical">
                        <a-form-item label="Status">
                            <a-select v-model:value="statusForm.status" style="width: 100%">
                                <a-select-option value="pending">Pending</a-select-option>
                                <a-select-option value="under_review">Under Review</a-select-option>
                                <a-select-option value="approved">Approved</a-select-option>
                                <a-select-option value="rejected">Rejected</a-select-option>
                                <a-select-option value="completed">Completed</a-select-option>
                            </a-select>
                        </a-form-item>
                        <a-form-item label="Admin Notes">
                            <a-textarea
                                v-model:value="statusForm.admin_notes"
                                :rows="4"
                                placeholder="Add notes about this application..."
                            />
                        </a-form-item>
                        <a-button type="primary" block @click="updateStatus" :loading="updating">
                            Update Status
                        </a-button>
                    </a-form>
                </a-card>

                <!-- Application Info -->
                <a-card title="Application Info">
                    <a-descriptions :column="1" bordered>
                        <a-descriptions-item label="Submitted">
                            {{ formatDate(application.created_at) }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Status">
                            <a-tag :color="getStatusColor(application.status)">
                                {{ application.status?.replace('_', ' ').toUpperCase() }}
                            </a-tag>
                        </a-descriptions-item>
                        <a-descriptions-item label="Reviewed By" v-if="application.reviewer">
                            {{ application.reviewer.name }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Reviewed At" v-if="application.reviewed_at">
                            {{ formatDate(application.reviewed_at) }}
                        </a-descriptions-item>
                    </a-descriptions>
                </a-card>
            </a-col>
        </a-row>
    </a-spin>
</template>

<script>
import { ref, reactive, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import {
    CheckOutlined,
    CloseOutlined,
    CheckCircleOutlined,
    CloseCircleOutlined,
    DownloadOutlined,
    EyeOutlined,
} from "@ant-design/icons-vue";
import { message } from "ant-design-vue";
import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";

export default {
    components: {
        AdminPageHeader,
        CheckOutlined,
        CloseOutlined,
        CheckCircleOutlined,
        CloseCircleOutlined,
        DownloadOutlined,
        EyeOutlined,
    },
    setup() {
        const route = useRoute();
        const router = useRouter();
        const loading = ref(false);
        const updating = ref(false);
        const application = ref({});

        const statusForm = reactive({
            status: "",
            admin_notes: "",
        });

        const documents = computed(() => [
            { label: "NDIS Worker Screening Check", value: application.value.ndis_screening, field: "ndis_screening" },
            { label: "Working with Children Check", value: application.value.children_check, expiry: application.value.children_check_expiry, field: "children_check" },
            { label: "Passport", value: application.value.passport, field: "passport" },
            { label: "Visa", value: application.value.visa, field: "visa" },
            { label: "Driver's License", value: application.value.drivers_license, field: "drivers_license" },
            { label: "NDIS Worker Orientation Module", value: application.value.ndis_orientation, field: "ndis_orientation" },
            { label: "Supporting Safe and Enjoyable Meals", value: application.value.meals_certificate, expiry: application.value.meals_certificate_expiry, field: "meals_certificate" },
            { label: "Infection Prevention and Control", value: application.value.ipc_certificate, expiry: application.value.ipc_certificate_expiry, field: "ipc_certificate" },
            { label: "First Aid Certificate", value: application.value.first_aid_certificate, expiry: application.value.first_aid_expiry, field: "first_aid_certificate" },
            { label: "CPR Certificate", value: application.value.cpr_certificate, expiry: application.value.cpr_expiry, field: "cpr_certificate" },
            { label: "Qualifications/Resume", value: application.value.resume, field: "resume" },
            { label: "Staff Contract", value: application.value.staff_contract, field: "staff_contract" },
            { label: "National Police Check", value: application.value.police_check, expiry: application.value.police_check_expiry, field: "police_check" },
        ]);

        const acknowledgments = computed(() => [
            { label: "Staff Detail Form", key: "ack_staff_details", value: application.value.ack_staff_details },
            { label: "Staff Induction Checklist", key: "ack_induction", value: application.value.ack_induction },
            { label: "Code of Conduct", key: "ack_code_conduct", value: application.value.ack_code_conduct },
            { label: "Training Tracker", key: "ack_training_tracker", value: application.value.ack_training_tracker },
            { label: "Employee Handbook", key: "ack_handbook", value: application.value.ack_handbook },
            { label: "Tax Declaration", key: "ack_tax_declaration", value: application.value.ack_tax_declaration },
            { label: "Bank Details", key: "ack_bank_details", value: application.value.ack_bank_details },
            { label: "Final Consent", key: "final_consent", value: application.value.final_consent },
        ]);

        const fetchApplication = async () => {
            loading.value = true;
            try {
                console.log("Fetching application with ID:", route.params.id);
                const response = await axiosAdmin.get(`/self/staff-onboarding/${route.params.id}`);
                console.log("View API Response:", response);
                console.log("Application data:", response.data);
                
                if (response.success) {
                    application.value = response.data.application;
                    statusForm.status = application.value.status;
                    statusForm.admin_notes = application.value.admin_notes || "";
                    console.log("Application set:", application.value);
                    console.log("Documents with values:", documents.value.filter(doc => doc.value));
                }
            } catch (error) {
                console.error("Error fetching application:", error);
                message.error("Failed to load application");
            } finally {
                loading.value = false;
            }
        };

        const updateStatus = async () => {
            updating.value = true;
            try {
                const response = await axiosAdmin.put(
                    `/self/staff-onboarding/${route.params.id}/status`,
                    statusForm
                );
                if (response.success) {
                    message.success("Status updated successfully");
                    fetchApplication();
                }
            } catch (error) {
                console.error("Error updating status:", error);
                message.error("Failed to update status");
            } finally {
                updating.value = false;
            }
        };

        const getStatusColor = (status) => {
            const colors = {
                pending: "orange",
                under_review: "blue",
                approved: "green",
                rejected: "red",
                completed: "default",
            };
            return colors[status] || "default";
        };

        const formatDate = (dateString) => {
            if (!dateString) return "-";
            const date = new Date(dateString);
            return date.toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        };

        const isExpired = (expiryDate) => {
            if (!expiryDate) return false;
            return new Date(expiryDate) < new Date();
        };

        const isExpiringSoon = (expiryDate) => {
            if (!expiryDate) return false;
            const thirtyDaysFromNow = new Date();
            thirtyDaysFromNow.setDate(thirtyDaysFromNow.getDate() + 30);
            const expiry = new Date(expiryDate);
            return expiry <= thirtyDaysFromNow && expiry >= new Date();
        };

        const viewFile = async (filePath) => {
            if (!filePath) {
                message.error("File not available");
                return;
            }
            
            try {
                // Access file directly from storage
                const fileUrl = `${window.location.origin}/storage/staff-onboarding/${filePath}`;
                
                // Fetch the file
                const response = await fetch(fileUrl);
                
                if (!response.ok) {
                    throw new Error('Failed to load file');
                }
                
                // Create a blob URL and open in new tab
                const blob = await response.blob();
                const blobUrl = window.URL.createObjectURL(blob);
                
                // Open in new tab
                window.open(blobUrl, '_blank', 'noopener,noreferrer');
                
                // Clean up the blob URL after a delay
                setTimeout(() => {
                    window.URL.revokeObjectURL(blobUrl);
                }, 10000);
                
            } catch (error) {
                console.error("Error viewing file:", error);
                message.error("Failed to open file");
            }
        };

        const downloadFile = async (filePath, fileName) => {
            if (!filePath) {
                message.error("File not available");
                return;
            }
            
            try {
                // Access file directly from storage
                const fileUrl = `${window.location.origin}/storage/staff-onboarding/${filePath}`;
                
                // Fetch the file
                const response = await fetch(fileUrl);
                
                if (!response.ok) {
                    throw new Error('Download failed');
                }
                
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                
                // Extract file extension from original path
                const extension = filePath.split('.').pop();
                link.download = `${fileName}.${extension}`;
                
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
                message.success("File downloaded successfully");
            } catch (error) {
                console.error("Error downloading file:", error);
                message.error("Failed to download file");
            }
        };

        onMounted(() => {
            fetchApplication();
        });

        return {
            loading,
            updating,
            application,
            statusForm,
            documents,
            acknowledgments,
            updateStatus,
            getStatusColor,
            formatDate,
            isExpired,
            isExpiringSoon,
            viewFile,
            downloadFile,
        };
    },
};
</script>



