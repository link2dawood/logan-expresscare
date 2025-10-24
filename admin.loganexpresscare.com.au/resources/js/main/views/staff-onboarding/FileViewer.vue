<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header
                :title="fileTitle"
                @back="$router.push({ name: 'admin.staff_onboarding.view', params: { id: applicationId } })"
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
                    <router-link :to="{ name: 'admin.staff_onboarding.view', params: { id: applicationId } }">
                        View Application
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ fileTitle }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-spin :spinning="loading" style="height: calc(100vh - 120px);">
        <div style="height: 100%; display: flex; flex-direction: column;">
            <!-- Action Bar -->
            <div style="padding: 16px; background: #fff; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0;">{{ fileTitle }}</h3>
                    <p style="margin: 4px 0 0 0; color: #666; font-size: 14px;">
                        {{ fileType.toUpperCase() }} Document
                    </p>
                </div>
                <a-space>
                    <a-button @click="openInNewTab">
                        <ExportOutlined /> Open in New Tab
                    </a-button>
                    <a-button type="primary" @click="downloadFile">
                        <DownloadOutlined /> Download
                    </a-button>
                    <a-button @click="goBack">
                        <ArrowLeftOutlined /> Back to Application
                    </a-button>
                </a-space>
            </div>

            <!-- File Content Area -->
            <div style="flex: 1; background: #f5f5f5;">
                <!-- PDF Viewer -->
                <iframe
                    v-if="fileType === 'pdf' && blobUrl"
                    :src="blobUrl"
                    style="width: 100%; height: 100%; border: none; background: #fff;"
                    @load="loading = false"
                    @error="handleError"
                ></iframe>

                <!-- Image Viewer -->
                <div
                    v-else-if="fileType === 'image' && blobUrl"
                    style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #fff;"
                >
                    <img
                        :src="blobUrl"
                        style="max-width: 100%; max-height: 100%; object-fit: contain; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"
                        @load="loading = false"
                        @error="handleError"
                    />
                </div>

                <!-- Document Viewer (Office files) -->
                <iframe
                    v-else-if="fileType === 'document' && blobUrl"
                    :src="`https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(blobUrl)}`"
                    style="width: 100%; height: 100%; border: none; background: #fff;"
                    @load="loading = false"
                    @error="handleError"
                ></iframe>

                <!-- Fallback for other file types -->
                <div
                    v-else-if="!loading"
                    style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 24px; background: #fff;"
                >
                    <FileOutlined style="font-size: 80px; color: #1890ff;" />
                    <div style="text-align: center;">
                        <h3 style="color: #333; margin-bottom: 8px;">File Preview Not Available</h3>
                        <p style="color: #666; margin-bottom: 16px;">
                            This file type cannot be previewed directly. Please download the file to view it.
                        </p>
                        <a-space>
                            <a-button type="primary" size="large" @click="downloadFile">
                                <DownloadOutlined /> Download File
                            </a-button>
                            <a-button size="large" @click="openInNewTab">
                                <ExportOutlined /> Open in New Tab
                            </a-button>
                        </a-space>
                    </div>
                </div>

                <!-- Error State -->
                <div
                    v-if="error"
                    style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px; background: #fff;"
                >
                    <ExclamationCircleOutlined style="font-size: 64px; color: #ff4d4f;" />
                    <div style="text-align: center;">
                        <h3 style="color: #333; margin-bottom: 8px;">Failed to Load File</h3>
                        <p style="color: #666; margin-bottom: 16px;">{{ error }}</p>
                        <a-space>
                            <a-button type="primary" @click="retryLoad">
                                <ReloadOutlined /> Try Again
                            </a-button>
                            <a-button @click="downloadFile">
                                <DownloadOutlined /> Download Instead
                            </a-button>
                        </a-space>
                    </div>
                </div>
            </div>
        </div>
    </a-spin>
</template>

<script>
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import {
    DownloadOutlined,
    ExportOutlined,
    FileOutlined,
    ArrowLeftOutlined,
    ExclamationCircleOutlined,
    ReloadOutlined,
} from "@ant-design/icons-vue";
import { message } from "ant-design-vue";
import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";

export default {
    components: {
        AdminPageHeader,
        DownloadOutlined,
        ExportOutlined,
        FileOutlined,
        ArrowLeftOutlined,
        ExclamationCircleOutlined,
        ReloadOutlined,
    },
    setup() {
        const route = useRoute();
        const router = useRouter();
        const loading = ref(true);
        const error = ref("");
        const blobUrl = ref("");

        // Get route parameters
        const applicationId = route.params.id;
        const documentType = route.params.documentType;
        const fileName = route.query.fileName || "";
        const fileTitle = route.query.title || "Document Viewer";

        // Determine file type from file name
        const fileType = computed(() => {
            if (!fileName) return 'unknown';
            
            const extension = fileName.split('.').pop().toLowerCase();
            
            if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'].includes(extension)) {
                return 'image';
            }
            if (extension === 'pdf') {
                return 'pdf';
            }
            if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(extension)) {
                return 'document';
            }
            return 'other';
        });

        const loadFile = async () => {
            try {
                loading.value = true;
                error.value = "";

                console.log('Loading file:', {
                    applicationId,
                    documentType,
                    fileName,
                    fileTitle
                });

                // Fetch file with authentication using axiosAdmin
                const response = await axiosAdmin.get(
                    `/self/staff-onboarding/${applicationId}/document/${documentType}?inline=true`,
                    { 
                        responseType: 'blob'
                    }
                );

                console.log('File response:', {
                    status: response.status,
                    type: response.type,
                    size: response.size,
                    data: response.data
                });

                // Determine MIME type based on file extension
                const fileExtension = fileName.split('.').pop().toLowerCase();
                let mimeType = 'application/octet-stream';
                
                switch (fileExtension) {
                    case 'pdf':
                        mimeType = 'application/pdf';
                        break;
                    case 'jpg':
                    case 'jpeg':
                        mimeType = 'image/jpeg';
                        break;
                    case 'png':
                        mimeType = 'image/png';
                        break;
                    case 'gif':
                        mimeType = 'image/gif';
                        break;
                    case 'doc':
                        mimeType = 'application/msword';
                        break;
                    case 'docx':
                        mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                        break;
                    case 'xls':
                        mimeType = 'application/vnd.ms-excel';
                        break;
                    case 'xlsx':
                        mimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                        break;
                }

                // Create blob URL from the response with correct MIME type
                const blob = new Blob([response.data], { type: response.type || mimeType });
                
                console.log('Blob created:', {
                    size: blob.size,
                    type: blob.type
                });
                
                // Revoke previous blob URL if exists
                if (blobUrl.value) {
                    URL.revokeObjectURL(blobUrl.value);
                }
                
                // Create new blob URL
                blobUrl.value = URL.createObjectURL(blob);
                
                console.log('Blob URL created:', blobUrl.value);

            } catch (err) {
                console.error("Error loading file:", err);
                console.error("Error details:", {
                    message: err.message,
                    response: err.response,
                    status: err.response?.status,
                    data: err.response?.data
                });
                
                // Get detailed error message from backend
                let errorMessage = "Failed to load file. Please try downloading instead.";
                
                if (err.response?.data?.message) {
                    errorMessage = err.response.data.message;
                } else if (err.response?.data?.debug) {
                    errorMessage = `Error: ${err.response.data.message}\nDebug: ${JSON.stringify(err.response.data.debug, null, 2)}`;
                } else if (err.message) {
                    errorMessage = err.message;
                }
                
                error.value = errorMessage;
                loading.value = false;
            }
        };

        const downloadFile = async () => {
            try {
                const response = await axiosAdmin.get(
                    `/self/staff-onboarding/${applicationId}/document/${documentType}`,
                    { 
                        responseType: 'blob'
                    }
                );
                
                // Create blob URL and trigger download
                const blob = new Blob([response.data], { type: response.type || 'application/octet-stream' });
                const url = URL.createObjectURL(blob);
                
                const link = document.createElement('a');
                link.href = url;
                link.download = fileTitle || documentType;
                
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Clean up
                URL.revokeObjectURL(url);
                
                message.success("File downloaded successfully");
            } catch (error) {
                console.error("Error downloading file:", error);
                message.error("Failed to download file");
            }
        };

        const openInNewTab = async () => {
            try {
                const response = await axiosAdmin.get(
                    `/self/staff-onboarding/${applicationId}/document/${documentType}?inline=true`,
                    { 
                        responseType: 'blob'
                    }
                );
                
                // Create blob URL and open in new tab
                const blob = new Blob([response.data], { 
                    type: response.type || 'application/pdf' 
                });
                const url = URL.createObjectURL(blob);
                
                const newWindow = window.open(url, '_blank', 'noopener,noreferrer');
                
                // Clean up blob URL after a delay
                setTimeout(() => {
                    URL.revokeObjectURL(url);
                }, 10000);
                
            } catch (error) {
                console.error("Error opening file in new tab:", error);
                message.error("Failed to open file in new tab");
            }
        };

        const handleError = () => {
            loading.value = false;
            error.value = "Failed to load file. The file may be corrupted or not accessible.";
        };

        const retryLoad = () => {
            loadFile();
        };

        const goBack = () => {
            router.push({ name: 'admin.staff_onboarding.view', params: { id: applicationId } });
        };

        const fetchApplicationData = async () => {
            try {
                console.log('Fetching application data for ID:', applicationId);
                const response = await axiosAdmin.get(`/self/staff-onboarding/${applicationId}`);
                console.log('Application response:', response);
                
                if (response.success && response.data.application) {
                    const application = response.data.application;
                    console.log('Application data:', application);
                    console.log('Document field value:', application[documentType]);
                    
                    // Get the filename from the application data
                    const fileName = application[documentType];
                    if (fileName && !route.query.fileName) {
                        console.log('Updating route with filename:', fileName);
                        // Update the route query with the actual filename
                        router.replace({
                            ...route,
                            query: {
                                ...route.query,
                                fileName: fileName
                            }
                        });
                    }
                }
            } catch (err) {
                console.error('Could not fetch application data:', err);
                error.value = 'Failed to load application data: ' + err.message;
            }
        };

        onMounted(async () => {
            // First try to get the filename from application data if not provided
            if (!route.query.fileName) {
                await fetchApplicationData();
            }
            loadFile();
        });

        return {
            loading,
            error,
            blobUrl,
            applicationId,
            documentType,
            fileName,
            fileTitle,
            fileType,
            loadFile,
            downloadFile,
            openInNewTab,
            handleError,
            retryLoad,
            goBack,
        };
    },
};
</script>

<style scoped>
/* Custom styles for the file viewer */
.ant-spin-container {
    height: 100%;
}

/* Ensure iframe takes full height */
iframe {
    min-height: calc(100vh - 200px);
}

/* Image container styling */
img {
    border-radius: 8px;
}
</style>
