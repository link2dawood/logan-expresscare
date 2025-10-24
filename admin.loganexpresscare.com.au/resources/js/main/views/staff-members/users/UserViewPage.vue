<template>
    <a-drawer
        :title="user.name"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :maskClosable="false"
        @close="onClose"
    >
        <div class="user-details">
            <a-row :gutter="[16, 16]">
                <a-col :xs="24" :sm="24" :md="4" :lg="4">
                    <a-image :src="user.profile_image_url" />
                </a-col>
                <a-col :xs="24" :sm="24" :md="20" :lg="20">
                    <a-tabs v-model:activeKey="activeKey">
                        <a-tab-pane
                            key="basic"
                            :tab="$t('user.basic_info')"
                            force-render
                        >
                            <a-descriptions
                                layout="vertical"
                                :contentStyle="{
                                    fontWeight: 500,
                                    marginBottom: '5px',
                                }"
                                :labelStyle="{
                                    fontWeight: 'bold',
                                }"
                            >
                                <a-descriptions-item :label="$t('user.name')">
                                    {{ user.name }}
                                </a-descriptions-item>
                                <a-descriptions-item
                                    :label="$t('user.employee_id')"
                                >
                                    {{
                                        user.employee_number
                                            ? user.employee_number
                                            : "-"
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item :label="$t('user.email')">
                                    {{ user.email }}
                                </a-descriptions-item>
                                <a-descriptions-item :label="$t('user.phone')">
                                    {{ user.phone ? user.phone : "-" }}
                                </a-descriptions-item>
                                <a-descriptions-item
                                    :label="$t('user.joining_date')"
                                >
                                    {{
                                        user.joining_date
                                            ? formatDate(user.joining_date)
                                            : "-"
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item :label="$t('user.status')">
                                    <a-tag
                                        :color="userStatusColors[user.status]"
                                    >
                                        {{
                                            user.status
                                                ? $t(`common.${user.status}`)
                                                : "-"
                                        }}
                                    </a-tag>
                                </a-descriptions-item>
                                <a-descriptions-item
                                    :label="$t('user.address')"
                                    :span="2"
                                >
                                    {{ user.address ? user.address : "-" }}
                                </a-descriptions-item>
                            </a-descriptions>
                        </a-tab-pane>
                        <a-tab-pane
                            key="personal"
                            :tab="$t('user.personal_info')"
                            force-render
                        >
                            <a-descriptions
                                layout="vertical"
                                :labelStyle="{
                                    fontWeight: 'bold',
                                }"
                                :contentStyle="{
                                    fontWeight: 500,
                                    marginBottom: '5px',
                                }"
                            >
                                <a-descriptions-item :label="$t('user.gender')">
                                    {{ user.gender }}
                                </a-descriptions-item>
                                <a-descriptions-item
                                    :label="$t('user.date_of_birth')"
                                >
                                    {{ user.dob ? user.dob : "-" }}
                                </a-descriptions-item>
                                <a-descriptions-item
                                    :label="$t('user.personal_email')"
                                >
                                    {{
                                        user.personal_email
                                            ? user.personal_email
                                            : "-"
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item
                                    :label="$t('user.personal_phone')"
                                >
                                    {{
                                        user.personal_phone
                                            ? user.personal_phone
                                            : "-"
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item
                                    :label="$t('user.is_married')"
                                >
                                    {{
                                        user.is_married ? user.is_married : "-"
                                    }}
                                </a-descriptions-item>
                            </a-descriptions>
                        </a-tab-pane>
                        <a-tab-pane
                            key="company"
                            :tab="$t('user.company_relation')"
                            force-render
                        >
                            <a-descriptions
                                layout="vertical"
                                :labelStyle="{
                                    fontWeight: 'bold',
                                }"
                                :contentStyle="{
                                    fontWeight: 500,
                                    marginBottom: '5px',
                                }"
                            >
                                <a-descriptions-item
                                    :label="$t('user.location_id')"
                                >
                                    {{
                                        user.location ? user.location.name : "-"
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item
                                    :label="$t('user.shift_id')"
                                >
                                    {{ user.shift ? user.shift.name : "-" }}
                                </a-descriptions-item>
                                <a-descriptions-item
                                    :label="$t('user.department_id')"
                                >
                                    {{
                                        user.department
                                            ? user.department.name
                                            : "-"
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item
                                    :label="$t('user.designation_id')"
                                >
                                    {{
                                        user.designation
                                            ? user.designation.name
                                            : "-"
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item
                                    :label="$t('user.report_to')"
                                >
                                    {{
                                        user.reporter ? user.reporter.name : "-"
                                    }}
                                </a-descriptions-item>
                            </a-descriptions>
                        </a-tab-pane>
                        <a-tab-pane
                            key="work"
                            :tab="$t('user.work_info')"
                            force-render
                        >
                            <a-descriptions
                                layout="vertical"
                                :labelStyle="{
                                    fontWeight: 'bold',
                                }"
                                :contentStyle="{
                                    fontWeight: 500,
                                    marginBottom: '5px',
                                }"
                            >
                                <a-descriptions-item
                                    :label="$t('user.probation_start_date')"
                                >
                                    {{
                                        user.probation_start_date
                                            ? formatDate(
                                                  user.probation_start_date
                                              )
                                            : "-"
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item
                                    :label="$t('user.probation_end_date')"
                                >
                                    {{
                                        user.probation_end_date
                                            ? formatDate(
                                                  user.probation_end_date
                                              )
                                            : "-"
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item
                                    :label="$t('user.notice_start_date')"
                                >
                                    {{
                                        user.notice_start_date
                                            ? formatDate(user.notice_start_date)
                                            : "-"
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item
                                    :label="$t('user.notice_end_date')"
                                >
                                    {{
                                        user.notice_end_date
                                            ? formatDate(user.notice_end_date)
                                            : "-"
                                    }}
                                </a-descriptions-item>
                                <a-descriptions-item
                                    :label="$t('user.end_date')"
                                >
                                    {{
                                        user.end_date
                                            ? formatDate(user.end_date)
                                            : "-"
                                    }}
                                </a-descriptions-item>
                            </a-descriptions>
                        </a-tab-pane>
                        <a-tab-pane
                            key="salary_details"
                            :tab="$t('user.salary_details')"
                            v-if="
                                permsArray.includes('salary_settings') ||
                                permsArray.includes('admin')
                            "
                            force-render
                        >
                            <a-descriptions
                                layout="vertical"
                                :labelStyle="{
                                    fontWeight: 'bold',
                                }"
                                :contentStyle="{
                                    fontWeight: 500,
                                    marginBottom: '5px',
                                }"
                            >
                                <a-descriptions-item
                                    :label="$t('basic_salary.basic_salary')"
                                >
                                    {{
                                        user.basic_salary
                                            ? formatAmountCurrency(
                                                  user.basic_salary
                                              )
                                            : "-"
                                    }}
                                </a-descriptions-item>
                            </a-descriptions>
                        </a-tab-pane>
                        <a-tab-pane key="custom_data_fields">
                            <template #tab>
                                <span>{{ $t("user.custom_data_fields") }}</span>
                            </template>
                            <div
                                v-for="fieldType in fieldTypesData"
                                :key="fieldType.id"
                            >
                                <FormItemHeading>
                                    {{ fieldType.name }}
                                </FormItemHeading>
                                <a-table
                                    :columns="columns"
                                    :row-key="(record) => fieldType"
                                    :data-source="fieldType.employee_field"
                                    :pagination="false"
                                    @change="setUrlData()"
                                    bordered
                                    size="small"
                                >
                                    <template #bodyCell="{ column, record }">
                                        <template
                                            v-if="column.dataIndex === 'name'"
                                        >
                                            {{ record.name }}
                                        </template>
                                        <template
                                            v-if="column.dataIndex === 'value'"
                                        >
                                            <template
                                                v-if="record.type === 'file'"
                                            >
                                                <template
                                                    v-if="record.values_url"
                                                >
                                                    <a-button
                                                        :href="
                                                            record.values_url
                                                        "
                                                        target="_blank"
                                                        type="link"
                                                        style="
                                                            margin-left: -3.5%;
                                                        "
                                                    >
                                                        <a-tag
                                                            color="success"
                                                            style="
                                                                cursor: pointer;
                                                            "
                                                        >
                                                            <template #icon>
                                                                <DownloadOutlined />
                                                            </template>
                                                            {{
                                                                $t(
                                                                    "common.download"
                                                                )
                                                            }}
                                                        </a-tag>
                                                    </a-button>
                                                </template>
                                                <template v-else> - </template>
                                            </template>

                                            <template v-else>
                                                {{ record.value || "-" }}
                                            </template>
                                        </template>
                                    </template>
                                </a-table>
                            </div>
                            <div
                                v-if="fieldTypesData.length == 0"
                                style="text-align: center; margin-top: 40px"
                            >
                                <a-typography>
                                    {{
                                        $t(
                                            "employee_custom_field.no_data_found"
                                        )
                                    }}</a-typography
                                >
                            </div>
                        </a-tab-pane>
                    </a-tabs>
                </a-col>
            </a-row>
            <a-row :gutter="[16, 16]" class="mt-20">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-tabs v-model:activeKey="tabKeys" @change="onTabChange">
                        <a-tab-pane
                            key="appreciations_view"
                            :tab="$t('user.apprecation')"
                            v-if="
                                permsArray.includes('appreciations_view') ||
                                permsArray.includes('admin')
                            "
                            force-render
                        >
                            <AppreciationTable
                                ref="appreciationTableRef"
                                :filters="filters"
                                tableSize="middle"
                                :bordered="true"
                                :selectable="true"
                                :isPageTableContent="false"
                            >
                                <template
                                    #actions="{
                                        addItem,
                                        selectedRowKeys,
                                        showSelectedDeleteConfirm,
                                    }"
                                >
                                    <a-row :gutter="[16, 16]" class="mb-10">
                                        <a-col
                                            :xs="24"
                                            :sm="24"
                                            :md="12"
                                            :lg="10"
                                            :xl="10"
                                        >
                                            <a-space>
                                                <template
                                                    v-if="
                                                        permsArray.includes(
                                                            'appreciations_create'
                                                        ) ||
                                                        permsArray.includes(
                                                            'admin'
                                                        )
                                                    "
                                                >
                                                    <a-button
                                                        type="primary"
                                                        @click="addItem"
                                                        class="mb-5"
                                                    >
                                                        <PlusOutlined />
                                                        {{
                                                            $t(
                                                                "appreciation.add"
                                                            )
                                                        }}
                                                    </a-button>
                                                </template>
                                                <a-button
                                                    v-if="
                                                        selectedRowKeys.length >
                                                            0 &&
                                                        (permsArray.includes(
                                                            'appreciations_delete'
                                                        ) ||
                                                            permsArray.includes(
                                                                'admin'
                                                            ))
                                                    "
                                                    type="primary"
                                                    class="mb-5"
                                                    @click="
                                                        showSelectedDeleteConfirm
                                                    "
                                                    danger
                                                >
                                                    <template #icon
                                                        ><DeleteOutlined
                                                    /></template>
                                                    {{ $t("common.delete") }}
                                                </a-button>
                                            </a-space>
                                        </a-col>
                                    </a-row>
                                </template>
                            </AppreciationTable>
                        </a-tab-pane>

                        <a-tab-pane
                            key="warnings_view"
                            :tab="$t('menu.warnings')"
                            v-if="
                                permsArray.includes('warnings_view') ||
                                permsArray.includes('admin')
                            "
                            force-render
                        >
                            <WarningTable
                                ref="warningTableRef"
                                :filters="filters"
                                tableSize="middle"
                                :bordered="true"
                                :selectable="true"
                                :isPageTableContent="false"
                            >
                                <template
                                    #actions="{
                                        addItem,
                                        showSelectedDeleteConfirm,
                                        table,
                                    }"
                                >
                                    <a-row :gutter="[16, 16]" class="mb-10">
                                        <a-col
                                            :xs="24"
                                            :sm="24"
                                            :md="12"
                                            :lg="10"
                                            :xl="10"
                                        >
                                            <a-space>
                                                <template
                                                    v-if="
                                                        permsArray.includes(
                                                            'warnings_create'
                                                        ) ||
                                                        permsArray.includes(
                                                            'admin'
                                                        )
                                                    "
                                                >
                                                    <a-button
                                                        type="primary"
                                                        @click="addItem"
                                                        class="mb-5"
                                                    >
                                                        <PlusOutlined />
                                                        {{ $t("warning.add") }}
                                                    </a-button>
                                                </template>
                                                <a-button
                                                    v-if="
                                                        table.selectedRowKeys
                                                            .length > 0 &&
                                                        (permsArray.includes(
                                                            'warnings_delete'
                                                        ) ||
                                                            permsArray.includes(
                                                                'admin'
                                                            ))
                                                    "
                                                    type="primary"
                                                    class="mb-5"
                                                    @click="
                                                        showSelectedDeleteConfirm
                                                    "
                                                    danger
                                                >
                                                    <template #icon
                                                        ><DeleteOutlined
                                                    /></template>
                                                    {{ $t("common.delete") }}
                                                </a-button>
                                            </a-space>
                                        </a-col>
                                    </a-row>
                                </template>
                            </WarningTable>
                        </a-tab-pane>

                        <a-tab-pane
                            key="assets_view"
                            :tab="$t('user.assets')"
                            v-if="
                                permsArray.includes('assets_view') ||
                                permsArray.includes('admin')
                            "
                            force-render
                        >
                            <AssetUserTable
                                ref="assetUserTableRef"
                                :filters="filters"
                                tableSize="middle"
                                :bordered="true"
                                :selectable="true"
                                :isPageTableContent="false"
                            >
                            </AssetUserTable>
                        </a-tab-pane>

                        <a-tab-pane
                            key="leaves_view"
                            :tab="$t('user.leaves')"
                            v-if="
                                permsArray.includes('leaves_view') ||
                                permsArray.includes('admin')
                            "
                            force-render
                        >
                            <LeavesTable
                                ref="leavesTableRef"
                                :filters="filters"
                                tableSize="middle"
                                :bordered="true"
                                :selectable="true"
                                :isPageTableContent="false"
                            >
                                <template
                                    #actions="{
                                        addItem,
                                        showSelectedDeleteConfirm,
                                        table,
                                    }"
                                >
                                    <a-row :gutter="[16, 16]" class="mb-10">
                                        <a-col
                                            :xs="24"
                                            :sm="24"
                                            :md="12"
                                            :lg="10"
                                            :xl="10"
                                        >
                                            <a-space>
                                                <template
                                                    v-if="
                                                        permsArray.includes(
                                                            'leaves_create'
                                                        ) ||
                                                        permsArray.includes(
                                                            'admin'
                                                        )
                                                    "
                                                >
                                                    <a-button
                                                        type="primary"
                                                        @click="addItem"
                                                        class="mb-5"
                                                    >
                                                        <PlusOutlined />
                                                        {{ $t("leave.add") }}
                                                    </a-button>
                                                </template>
                                                <a-button
                                                    v-if="
                                                        table.selectedRowKeys
                                                            .length > 0 &&
                                                        (permsArray.includes(
                                                            'leaves_delete'
                                                        ) ||
                                                            permsArray.includes(
                                                                'admin'
                                                            ))
                                                    "
                                                    type="primary"
                                                    class="mb-5"
                                                    @click="
                                                        showSelectedDeleteConfirm
                                                    "
                                                    danger
                                                >
                                                    <template #icon
                                                        ><DeleteOutlined
                                                    /></template>
                                                    {{ $t("common.delete") }}
                                                </a-button>
                                            </a-space>
                                        </a-col>
                                    </a-row>
                                </template>
                            </LeavesTable>
                        </a-tab-pane>
                        <a-tab-pane
                            key="complaints_view"
                            :tab="$t('menu.complaints')"
                            v-if="
                                permsArray.includes('complaints_view') ||
                                permsArray.includes('admin')
                            "
                            force-render
                        >
                            <ComplaintTable
                                ref="complaintTableRef"
                                :filters="complaintFilters"
                                tableSize="middle"
                                :bordered="true"
                                :selectable="true"
                                :isPageTableContent="false"
                            >
                                <template
                                    #actions="{
                                        addItem,
                                        showSelectedDeleteConfirm,
                                        table,
                                    }"
                                >
                                    <a-row :gutter="[16, 16]" class="mb-10">
                                        <a-col
                                            :xs="24"
                                            :sm="24"
                                            :md="12"
                                            :lg="10"
                                            :xl="10"
                                        >
                                            <a-space>
                                                <template
                                                    v-if="
                                                        permsArray.includes(
                                                            'complaints_create'
                                                        ) ||
                                                        permsArray.includes(
                                                            'admin'
                                                        )
                                                    "
                                                >
                                                    <a-button
                                                        type="primary"
                                                        @click="addItem"
                                                        class="mb-5"
                                                    >
                                                        <PlusOutlined />
                                                        {{
                                                            $t("complaint.add")
                                                        }}
                                                    </a-button>
                                                </template>
                                                <a-button
                                                    v-if="
                                                        table.selectedRowKeys
                                                            .length > 0 &&
                                                        (permsArray.includes(
                                                            'complaints_delete'
                                                        ) ||
                                                            permsArray.includes(
                                                                'admin'
                                                            ))
                                                    "
                                                    type="primary"
                                                    class="mb-5"
                                                    @click="
                                                        showSelectedDeleteConfirm
                                                    "
                                                    danger
                                                >
                                                    <template #icon
                                                        ><DeleteOutlined
                                                    /></template>
                                                    {{ $t("common.delete") }}
                                                </a-button>
                                            </a-space>
                                        </a-col>
                                    </a-row>
                                </template>
                            </ComplaintTable>
                        </a-tab-pane>
                        <a-tab-pane
                            key="pre_payments_view"
                            :tab="$t('menu.pre_payments')"
                            v-if="
                                permsArray.includes('pre_payments_view') ||
                                permsArray.includes('admin')
                            "
                            force-render
                        >
                            <PrepaymentTable
                                ref="prePaymentTableRef"
                                :filters="filters"
                                tableSize="middle"
                                :bordered="true"
                                :selectable="true"
                                :isPageTableContent="false"
                            >
                                <template
                                    #actions="{
                                        addItem,
                                        showSelectedDeleteConfirm,
                                        table,
                                    }"
                                >
                                    <a-row :gutter="[16, 16]" class="mb-10">
                                        <a-col
                                            :xs="24"
                                            :sm="24"
                                            :md="12"
                                            :lg="10"
                                            :xl="10"
                                        >
                                            <a-space>
                                                <template
                                                    v-if="
                                                        permsArray.includes(
                                                            'pre_payments_create'
                                                        ) ||
                                                        permsArray.includes(
                                                            'admin'
                                                        )
                                                    "
                                                >
                                                    <a-button
                                                        type="primary"
                                                        @click="addItem"
                                                        class="mb-5"
                                                    >
                                                        <PlusOutlined />
                                                        {{
                                                            $t(
                                                                "pre_payment.add"
                                                            )
                                                        }}
                                                    </a-button>
                                                </template>
                                                <a-button
                                                    v-if="
                                                        table.selectedRowKeys
                                                            .length > 0 &&
                                                        (permsArray.includes(
                                                            'pre_payments_delete'
                                                        ) ||
                                                            permsArray.includes(
                                                                'admin'
                                                            ))
                                                    "
                                                    type="primary"
                                                    class="mb-5"
                                                    @click="
                                                        showSelectedDeleteConfirm
                                                    "
                                                    danger
                                                >
                                                    <template #icon
                                                        ><DeleteOutlined
                                                    /></template>
                                                    {{ $t("common.delete") }}
                                                </a-button>
                                            </a-space>
                                        </a-col>
                                    </a-row>
                                </template>
                            </PrepaymentTable>
                        </a-tab-pane>
                        <a-tab-pane
                            key="payrolls_view"
                            :tab="$t('menu.payrolls')"
                            v-if="
                                permsArray.includes('payrolls_view') ||
                                permsArray.includes('admin')
                            "
                            force-render
                        >
                            <PayRollTable
                                ref="payRollTableRef"
                                :filters="filters"
                                tableSize="middle"
                                :bordered="true"
                                :selectable="true"
                                :isPageTableContent="false"
                            >
                            </PayRollTable>
                        </a-tab-pane>
                        <a-tab-pane
                            v-if="
                                permsArray.includes(
                                    'increments_promotions_view'
                                ) || permsArray.includes('admin')
                            "
                            key="increments_promotions_view"
                            :tab="$t('menu.increments_promotions')"
                            force-render
                        >
                            <IncreamentTable
                                ref="incrementTableRef"
                                :filters="filters"
                                tableSize="middle"
                                :bordered="true"
                                :selectable="true"
                                :isPageTableContent="false"
                            >
                                <template
                                    #actions="{
                                        addItem,
                                        showSelectedDeleteConfirm,
                                        table,
                                    }"
                                >
                                    <a-row :gutter="[16, 16]" class="mb-10">
                                        <a-col
                                            :xs="24"
                                            :sm="24"
                                            :md="12"
                                            :lg="10"
                                            :xl="10"
                                        >
                                            <a-space>
                                                <a-button
                                                    type="primary"
                                                    class="mb-5"
                                                    @click="addItem"
                                                    v-if="
                                                        permsArray.includes(
                                                            'increments_promotions_create'
                                                        ) ||
                                                        permsArray.includes(
                                                            'admin'
                                                        )
                                                    "
                                                >
                                                    <PlusOutlined />
                                                    {{
                                                        $t(
                                                            "increment_promotion.add"
                                                        )
                                                    }}
                                                </a-button>

                                                <a-button
                                                    class="mb-5"
                                                    v-if="
                                                        table.selectedRowKeys
                                                            .length > 0 &&
                                                        (permsArray.includes(
                                                            'increments_promotions_delete'
                                                        ) ||
                                                            permsArray.includes(
                                                                'admin'
                                                            ))
                                                    "
                                                    type="primary"
                                                    @click="
                                                        showSelectedDeleteConfirm
                                                    "
                                                    danger
                                                >
                                                    <template #icon
                                                        ><DeleteOutlined
                                                    /></template>
                                                    {{ $t("common.delete") }}
                                                </a-button>
                                            </a-space>
                                        </a-col>
                                    </a-row>
                                </template>
                            </IncreamentTable>
                        </a-tab-pane>
                        <a-tab-pane
                            v-if="
                                permsArray.includes('feedbacks_view') ||
                                permsArray.includes('admin')
                            "
                            key="feedbacks_view"
                            :tab="$t('menu.feedbacks')"
                            force-render
                        >
                            <ResponseTable
                                ref="responseTable"
                                :filters="filters"
                                tableSize="middle"
                                :bordered="true"
                                :selectable="true"
                                :visible="adjustedVisible"
                                :responseColumns="responseColumns"
                                :isPageTableContent="false"
                            >
                            </ResponseTable>
                        </a-tab-pane>
                        <a-tab-pane
                            v-if="
                                permsArray.includes('attendances_view') ||
                                permsArray.includes('admin')
                            "
                            key="attendances_view"
                            :tab="$t('menu.attendance_details')"
                            force-render
                        >
                            <AttendanceDetail
                                ref="attendanceRef"
                                :filters="filters"
                                tableSize="middle"
                                :bordered="true"
                                :selectable="true"
                                :isPageTableContent="false"
                            >
                                <template
                                    #actions="{ filterData, monthArrays }"
                                >
                                    <a-row :gutter="[16, 16]" align="middle">
                                        <a-col
                                            :xs="24"
                                            :sm="24"
                                            :md="12"
                                            :lg="10"
                                            :xl="10"
                                        >
                                        </a-col>
                                        <a-col
                                            :xs="24"
                                            :sm="24"
                                            :md="12"
                                            :lg="14"
                                            :xl="14"
                                        >
                                            <a-row
                                                :gutter="[16, 16]"
                                                justify="end"
                                                style="margin-bottom: 15px"
                                            >
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                    :xl="8"
                                                >
                                                    <a-date-picker
                                                        v-model:value="
                                                            filters.year
                                                        "
                                                        :placeholder="
                                                            $t(
                                                                'common.select_default_text',
                                                                [
                                                                    $t(
                                                                        'holiday.year'
                                                                    ),
                                                                ]
                                                            )
                                                        "
                                                        picker="year"
                                                        @change="filterData"
                                                        style="width: 100%"
                                                        :allowClear="false"
                                                    />
                                                </a-col>
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="8"
                                                    :lg="8"
                                                    :xl="6"
                                                >
                                                    <a-select
                                                        v-model:value="
                                                            filters.month
                                                        "
                                                        :placeholder="
                                                            $t(
                                                                'common.select_default_text',
                                                                [
                                                                    $t(
                                                                        'holiday.month'
                                                                    ),
                                                                ]
                                                            )
                                                        "
                                                        :allowClear="false"
                                                        style="width: 100%"
                                                        optionFilterProp="title"
                                                        show-search
                                                        @change="filterData"
                                                    >
                                                        <a-select-option
                                                            v-for="month in monthArrays"
                                                            :key="month.name"
                                                            :value="month.value"
                                                        >
                                                            {{ month.name }}
                                                        </a-select-option>
                                                    </a-select>
                                                </a-col>
                                            </a-row>
                                        </a-col>
                                    </a-row>
                                </template>
                                <template
                                    #card="{
                                        totalPresentDays,
                                        workingDays,
                                        totalOfficeTime,
                                        formatMinutes,
                                        clockInDuration,
                                        clockInDurationPercentage,
                                        totalLateDaysPercentage,
                                        totalLateDays,
                                        halfDayCount,
                                    }"
                                >
                                    <div
                                        style="
                                            height: 180px;
                                            margin-bottom: 15px;
                                        "
                                    >
                                        <a-row :gutter="16">
                                            <a-col
                                                :xs="24"
                                                :sm="24"
                                                :md="8"
                                                :lg="5"
                                            >
                                                <a-card style="height: 100%">
                                                    <a-statistic
                                                        :title="
                                                            $t(
                                                                'attendance.present_working_days'
                                                            )
                                                        "
                                                        :value="`${totalPresentDays} / ${workingDays} ${$t(
                                                            'attendance.days'
                                                        )}`"
                                                        :value-style="{
                                                            color:
                                                                totalPresentDays >=
                                                                workingDays
                                                                    ? '#3f8600'
                                                                    : '#cf1322',
                                                        }"
                                                        style="
                                                            margin-right: 50px;
                                                        "
                                                    />
                                                </a-card>
                                            </a-col>
                                            <a-col
                                                :xs="24"
                                                :sm="24"
                                                :md="8"
                                                :lg="5"
                                            >
                                                <a-card style="height: 100%">
                                                    <a-statistic
                                                        :title="
                                                            $t(
                                                                'attendance.total_office_time'
                                                            )
                                                        "
                                                        :value="
                                                            totalOfficeTime > 0
                                                                ? formatMinutes(
                                                                      totalOfficeTime
                                                                  )
                                                                : '--'
                                                        "
                                                        :value-style="{
                                                            color: '#3f8600',
                                                        }"
                                                    />
                                                </a-card>
                                            </a-col>
                                            <a-col
                                                :xs="24"
                                                :sm="24"
                                                :md="8"
                                                :lg="6"
                                            >
                                                <a-card style="height: 100%">
                                                    <a-statistic
                                                        :title="
                                                            $t(
                                                                'attendance.total_worked_time'
                                                            )
                                                        "
                                                        :value="
                                                            clockInDurationPercentage >
                                                            0
                                                                ? `${formatMinutes(
                                                                      clockInDuration
                                                                  )} (${clockInDurationPercentage}%)`
                                                                : '--'
                                                        "
                                                        :value-style="{
                                                            color:
                                                                clockInDuration >=
                                                                totalOfficeTime
                                                                    ? '#3f8600'
                                                                    : '#cf1322',
                                                        }"
                                                    />
                                                </a-card>
                                            </a-col>

                                            <a-col
                                                :xs="24"
                                                :sm="24"
                                                :md="8"
                                                :lg="5"
                                            >
                                                <a-card style="height: 100%">
                                                    <a-statistic
                                                        :title="
                                                            $t(
                                                                'attendance.late'
                                                            )
                                                        "
                                                        :value="
                                                            totalLateDaysPercentage >
                                                            0
                                                                ? `${totalLateDays} ${$t(
                                                                      'attendance.days'
                                                                  )} (${totalLateDaysPercentage}%)`
                                                                : '--'
                                                        "
                                                        class="demo-class"
                                                        :value-style="{
                                                            color: '#cf1322',
                                                        }"
                                                    />
                                                </a-card>
                                            </a-col>
                                            <a-col
                                                :xs="24"
                                                :sm="24"
                                                :md="8"
                                                paging
                                                :lg="3"
                                            >
                                                <a-card style="height: 100%">
                                                    <a-statistic
                                                        :title="
                                                            $t(
                                                                'attendance.half_days'
                                                            )
                                                        "
                                                        :value="`${halfDayCount}`"
                                                        class="demo-class"
                                                        :value-style="{
                                                            color: '#cf1322',
                                                        }"
                                                    />
                                                </a-card>
                                            </a-col>
                                        </a-row>
                                    </div>
                                </template>
                            </AttendanceDetail>
                        </a-tab-pane>
                    </a-tabs>
                </a-col>
            </a-row>
        </div>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, watch, nextTick } from "vue";
import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";
import {
    UserOutlined,
    HomeOutlined,
    ExperimentOutlined,
    CrownOutlined,
    PlusOutlined,
    DeleteOutlined,
    ReloadOutlined,
    SendOutlined,
    DownloadOutlined,
} from "@ant-design/icons-vue";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";
import { forEach } from "lodash-es";
import AppreciationTable from "../../appreciation/appreciations/AppreciationTable.vue";
import WarningTable from "../../offboardings/warnings/warningTable.vue";
import LeavesTable from "../../leave/leaves/leavesTable.vue";
import ComplaintTable from "../../complaint/complaints/ComplaintTable.vue";
import PrepaymentTable from "../../payrolls/pre-payments/PrepaymentTable.vue";
import PayRollTable from "../../payrolls/payroll/PayrollTable.vue";
import IncreamentTable from "../../payrolls/increments-promotions/IncreamentTable.vue";
import AssetUserTable from "../../assets/asset/AssetUserTable.vue";
import dayjs from "dayjs";
import ResponseTable from "../../forms/survey-form/ResponseTable.vue";
import AttendanceDetail from "../../attendances/details/AttendanceDetail.vue";

export default defineComponent({
    props: ["userId", "getUserDocumentData", "visible"],
    components: {
        FormItemHeading,
        UserOutlined,
        HomeOutlined,
        ExperimentOutlined,
        CrownOutlined,
        PlusOutlined,
        DeleteOutlined,
        ReloadOutlined,
        SendOutlined,
        AppreciationTable,
        WarningTable,
        LeavesTable,
        ComplaintTable,
        PrepaymentTable,
        PayRollTable,
        IncreamentTable,
        AssetUserTable,
        ResponseTable,
        AttendanceDetail,
        DownloadOutlined,
    },
    emits: ["closed"],
    setup(props, { emit }) {
        const {
            formatAmountCurrency,
            formatDate,
            userStatusColors,
            permsArray,
            statusColors,
            formatDateTime,
            setTabKey,
            tabKeys,
        } = common();
        const { t } = useI18n();
        const activeKey = ref("basic");
        const employeeProfile = ref([]);
        const adjustedVisible = ref(false);
        const user = ref({});
        const filters = ref({
            user_id: undefined,
            status: "all",
            month: dayjs().format("MM"),
            year: dayjs(),
            type: "all",
        });
        const complaintFilters = ref({
            to_user_id: undefined,
            complaintStatus: "approved",
        });
        const appreciationTableRef = ref(null);
        const warningTableRef = ref(null);
        const leavesTableRef = ref(null);
        const complaintTableRef = ref(null);
        const prePaymentTableRef = ref(null);
        const payRollTableRef = ref(null);
        const incrementTableRef = ref(null);
        const assetUserTableRef = ref(null);
        const responseTable = ref(null);
        const attendanceRef = ref(null);
        const fieldTypesData = ref([]);
        const fieldTypeDataUrl =
            "field-types?fields=id,xid,name,employeeField{id,xid,name,field_type_id,type,default_value}";

        const onClose = () => {
            emit("closed");
        };

        const responseColumns = [
            {
                title: t("feedback.title"),
                dataIndex: "feedback",
            },
            {
                title: t("feedback.user_id"),
                dataIndex: "user_id",
            },
            {
                title: t("feedback.submit_date"),
                dataIndex: "submit_date",
            },
            {
                title: t("feedback.rating"),
                dataIndex: "rating",
            },
            {
                title: t("common.action"),
                dataIndex: "action",
            },
        ];

        const columns = [
            {
                title: t("user.name"),
                dataIndex: "name",
                key: "name",
            },
            {
                title: t("user.value"),
                dataIndex: "value",
                key: "value",
            },
        ];

        const setUrlData = () => {
            const fieldTypesDataPromise = axiosAdmin.get(fieldTypeDataUrl);

            Promise.all([fieldTypesDataPromise]).then(
                ([fieldTypesDataResponse]) => {
                    const fetchedFieldTypes = fieldTypesDataResponse.data;

                    const userDocMap = {};
                    (props.getUserDocumentData.user_document || []).forEach(
                        (document) => {
                            userDocMap[document.x_field_type_id] = {
                                value: document.values,
                                values_url: document.values_url || null,
                            };
                        }
                    );

                    fieldTypesData.value = fetchedFieldTypes.map((group) => {
                        return {
                            ...group,
                            employee_field: group.employee_field.map(
                                (field) => {
                                    const matched = userDocMap[field.xid] || {};
                                    return {
                                        ...field,
                                        value: matched.value || null,
                                        values_url: matched.values_url || null,
                                    };
                                }
                            ),
                        };
                    });
                }
            );
        };

        watch(
            () => props.visible,
            async (newVal, oldVal) => {
                filters.value.user_id = props.userId;
                complaintFilters.value.to_user_id = props.userId;

                setUrlData();

                setTabKey();

                await nextTick();
                if (props.visible) {
                    axiosAdmin
                        .get(`employee-profile/${props.userId}`)
                        .then((response) => {
                            user.value = response.data[0];

                            if (appreciationTableRef.value) {
                                appreciationTableRef.value.setUrlData();
                            }
                            if (warningTableRef.value) {
                                warningTableRef.value.setUrlData();
                            }
                            if (leavesTableRef.value) {
                                leavesTableRef.value.setUrlData();
                            }
                            if (complaintTableRef.value) {
                                complaintTableRef.value.setUrlData();
                            }
                            if (prePaymentTableRef.value) {
                                prePaymentTableRef.value.setUrlData();
                            }
                            if (payRollTableRef.value) {
                                payRollTableRef.value.setUrlData();
                            }
                            if (incrementTableRef.value) {
                                incrementTableRef.value.setUrlData();
                            }
                            if (assetUserTableRef.value) {
                                assetUserTableRef.value.setUrlData();
                            }
                            if (responseTable.value) {
                                responseTable.value.setUrlData();
                            }
                            if (attendanceRef.value) {
                                attendanceRef.value.setUrlData();
                            }
                        });
                    activeKey.value = "basic";
                } else {
                    props.userId = undefined;
                }
            }
        );

        const onTabChange = async (addEditActiveTab) => {
            adjustedVisible.value = false;
            await nextTick();
            if (addEditActiveTab === "feedbacks_view" && props.visible) {
                adjustedVisible.value = true;
            } else {
                adjustedVisible.value = false;
            }
        };

        return {
            permsArray,
            statusColors,

            filters,
            complaintFilters,
            responseColumns,

            appreciationTableRef,
            leavesTableRef,
            warningTableRef,
            complaintTableRef,
            prePaymentTableRef,
            payRollTableRef,
            incrementTableRef,
            assetUserTableRef,
            attendanceRef,

            formatDateTime,
            formatAmountCurrency,
            formatDate,
            onClose,
            userStatusColors,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "60%",
            activeKey,
            setTabKey,
            onTabChange,
            adjustedVisible,
            tabKeys,
            user,
            columns,
            fieldTypesData,
            setUrlData,
        };
    },
});
</script>

<style lang="less">
.user-details {
    .ant-descriptions-item {
        padding-bottom: 5px;
    }
}
</style>
