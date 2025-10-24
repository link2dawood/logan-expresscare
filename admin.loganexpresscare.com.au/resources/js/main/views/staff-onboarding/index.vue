<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t('Staff Onboarding Applications')" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t('menu.dashboard') }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t('Staff Onboarding') }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-card :loading="loading">
        <!-- Statistics Cards -->
        <a-row :gutter="[16, 16]" class="mb-4">
            <a-col :span="24">
                <a-statistic-group>
                    <a-row :gutter="[16, 16]">
                        <a-col :xs="24" :sm="12" :md="8" :lg="4">
                            <a-card>
                                <a-statistic
                                    title="Total Applications"
                                    :value="statistics.total_applications || 0"
                                    :value-style="{ color: '#3f8600' }"
                                />
                            </a-card>
                        </a-col>
                        <a-col :xs="24" :sm="12" :md="8" :lg="4">
                            <a-card>
                                <a-statistic
                                    title="Pending"
                                    :value="statistics.pending || 0"
                                    :value-style="{ color: '#faad14' }"
                                />
                            </a-card>
                        </a-col>
                        <a-col :xs="24" :sm="12" :md="8" :lg="4">
                            <a-card>
                                <a-statistic
                                    title="Under Review"
                                    :value="statistics.under_review || 0"
                                    :value-style="{ color: '#1890ff' }"
                                />
                            </a-card>
                        </a-col>
                        <a-col :xs="24" :sm="12" :md="8" :lg="4">
                            <a-card>
                                <a-statistic
                                    title="Approved"
                                    :value="statistics.approved || 0"
                                    :value-style="{ color: '#52c41a' }"
                                />
                            </a-card>
                        </a-col>
                        <a-col :xs="24" :sm="12" :md="8" :lg="4">
                            <a-card>
                                <a-statistic
                                    title="Rejected"
                                    :value="statistics.rejected || 0"
                                    :value-style="{ color: '#f5222d' }"
                                />
                            </a-card>
                        </a-col>
                        <a-col :xs="24" :sm="12" :md="8" :lg="4">
                            <a-card>
                                <a-statistic
                                    title="This Month"
                                    :value="statistics.this_month || 0"
                                />
                            </a-card>
                        </a-col>
                    </a-row>
                </a-statistic-group>
            </a-col>
        </a-row>

        <!-- Search and Filters -->
        <a-row :gutter="[16, 16]" class="mb-4">
            <a-col :xs="24" :sm="24" :md="8">
                <a-input-search
                    v-model:value="searchTerm"
                    placeholder="Search by name, email, or phone"
                    @search="fetchData"
                    allow-clear
                />
            </a-col>
            <a-col :xs="24" :sm="12" :md="5">
                <a-select
                    v-model:value="statusFilter"
                    @change="fetchData"
                    style="width: 100%"
                    placeholder="Filter by status"
                    allow-clear
                >
                    <a-select-option value="all">All Statuses</a-select-option>
                    <a-select-option value="pending">Pending</a-select-option>
                    <a-select-option value="under_review">Under Review</a-select-option>
                    <a-select-option value="approved">Approved</a-select-option>
                    <a-select-option value="rejected">Rejected</a-select-option>
                    <a-select-option value="completed">Completed</a-select-option>
                </a-select>
            </a-col>
            <a-col :xs="24" :sm="12" :md="5">
                <a-select
                    v-model:value="positionFilter"
                    @change="fetchData"
                    style="width: 100%"
                    placeholder="Filter by position"
                    allow-clear
                >
                    <a-select-option value="all">All Positions</a-select-option>
                    <a-select-option value="disability_support_worker">Disability Support Worker</a-select-option>
                    <a-select-option value="nurse">Nurse</a-select-option>
                    <a-select-option value="other">Other</a-select-option>
                </a-select>
            </a-col>
        </a-row>

        <!-- Applications Table -->
        <a-table
            :columns="columns"
            :data-source="applications.data"
            :pagination="paginationConfig"
            :loading="loading"
            @change="handleTableChange"
            :scroll="{ x: 1200 }"
            :row-key="record => record.xid || record.id"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'name'">
                    <div>
                        <div style="font-weight: 500">{{ record.first_name }} {{ record.last_name }}</div>
                        <div style="font-size: 12px; color: #999">{{ record.email }}</div>
                    </div>
                </template>

                <template v-if="column.key === 'position'">
                    <span style="text-transform: capitalize">
                        {{ record.position.replace('_', ' ') }}
                    </span>
                </template>

                <template v-if="column.key === 'employment_type'">
                    <span style="text-transform: capitalize">
                        {{ record.employment_type }}
                    </span>
                </template>

                <template v-if="column.key === 'status'">
                    <a-tag :color="getStatusColor(record.status)">
                        {{ record.status.replace('_', ' ').toUpperCase() }}
                    </a-tag>
                </template>

                <template v-if="column.key === 'created_at'">
                    {{ formatDate(record.created_at) }}
                </template>

                <template v-if="column.key === 'actions'">
                    <a-space>
                        <router-link :to="{ name: 'admin.staff_onboarding.view', params: { id: record.xid || record.id } }" v-if="record.xid || record.id">
                            <a-button type="primary" size="small">
                                <EyeOutlined /> View
                            </a-button>
                        </router-link>
                        <a-popconfirm
                            title="Are you sure you want to delete this application?"
                            @confirm="deleteApplication(record.xid || record.id)"
                            v-if="record.xid || record.id"
                        >
                            <a-button type="primary" danger size="small">
                                <DeleteOutlined /> Delete
                            </a-button>
                        </a-popconfirm>
                    </a-space>
                </template>
            </template>
        </a-table>
    </a-card>
</template>

<script>
import { ref, reactive, onMounted } from "vue";
import { EyeOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import { message } from "ant-design-vue";
import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";

export default {
    components: {
        AdminPageHeader,
        EyeOutlined,
        DeleteOutlined,
    },
    setup() {
        const loading = ref(false);
        const searchTerm = ref("");
        const statusFilter = ref("all");
        const positionFilter = ref("all");
        const applications = ref({ data: [] });
        const statistics = ref({});

        const columns = [
            {
                title: "Name",
                key: "name",
                dataIndex: "first_name",
                width: 200,
            },
            {
                title: "Phone",
                key: "phone",
                dataIndex: "phone",
                width: 150,
            },
            {
                title: "Position",
                key: "position",
                dataIndex: "position",
                width: 180,
            },
            {
                title: "Employment Type",
                key: "employment_type",
                dataIndex: "employment_type",
                width: 150,
            },
            {
                title: "Status",
                key: "status",
                dataIndex: "status",
                width: 130,
            },
            {
                title: "Submitted",
                key: "created_at",
                dataIndex: "created_at",
                width: 120,
            },
            {
                title: "Actions",
                key: "actions",
                fixed: "right",
                width: 200,
            },
        ];

        const paginationConfig = reactive({
            current: 1,
            pageSize: 15,
            total: 0,
            showSizeChanger: true,
            showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} items`,
        });

        const fetchData = async () => {
            loading.value = true;
            try {
                const params = {
                    page: paginationConfig.current,
                    per_page: paginationConfig.pageSize,
                };

                if (searchTerm.value) params.search = searchTerm.value;
                if (statusFilter.value && statusFilter.value !== "all") params.status = statusFilter.value;
                if (positionFilter.value && positionFilter.value !== "all") params.position = positionFilter.value;

                const response = await axiosAdmin.get("/self/staff-onboarding", { params });
                
                console.log("API Response:", response);
                console.log("Response data:", response.data);
                
                if (response.success) {
                    applications.value = response.data;
                    paginationConfig.total = response.data.total;
                    console.log("Applications set:", applications.value);
                    console.log("Applications data array:", applications.value.data);
                    console.log("First record:", applications.value.data?.[0]);
                    console.log("First record xid:", applications.value.data?.[0]?.xid);
                    console.log("First record id:", applications.value.data?.[0]?.id);
                    console.log("All keys in first record:", Object.keys(applications.value.data?.[0] || {}));
                }
            } catch (error) {
                console.error("Error fetching data:", error);
                console.error("Error details:", {
                    status: error?.status,
                    data: error?.data,
                    error: error?.error,
                    message: error?.error?.message || error?.message
                });
                const errorMessage = error?.error?.message || error?.message || "Failed to load applications";
                message.error(errorMessage);
            } finally {
                loading.value = false;
            }
        };

        const fetchStatistics = async () => {
            try {
                const response = await axiosAdmin.get("/self/staff-onboarding/statistics");
                if (response.success) {
                    statistics.value = response.data;
                }
            } catch (error) {
                console.error("Error fetching statistics:", error);
                console.error("Statistics error details:", {
                    status: error?.status,
                    data: error?.data,
                    error: error?.error,
                    message: error?.error?.message || error?.message
                });
                const errorMessage = error?.error?.message || error?.message || "Failed to load statistics";
                message.error(errorMessage);
            }
        };

        const handleTableChange = (pagination) => {
            paginationConfig.current = pagination.current;
            paginationConfig.pageSize = pagination.pageSize;
            fetchData();
        };

        const deleteApplication = async (id) => {
            try {
                const response = await axiosAdmin.delete(`/self/staff-onboarding/${id}`);
                if (response.success) {
                    message.success("Application deleted successfully");
                    fetchData();
                    fetchStatistics();
                }
            } catch (error) {
                console.error("Error deleting application:", error);
                message.error("Failed to delete application");
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

        onMounted(() => {
            fetchData();
            fetchStatistics();
        });

        return {
            loading,
            searchTerm,
            statusFilter,
            positionFilter,
            applications,
            statistics,
            columns,
            paginationConfig,
            fetchData,
            handleTableChange,
            deleteApplication,
            getStatusColor,
            formatDate,
        };
    },
};
</script>



