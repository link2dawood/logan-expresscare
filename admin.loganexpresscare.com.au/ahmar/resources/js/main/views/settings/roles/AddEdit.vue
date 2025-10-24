<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('role.display_name')"
                        name="display_name"
                        :help="rules.display_name ? rules.display_name.message : null"
                        :validateStatus="rules.display_name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.display_name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('role.display_name'),
                                ])
                            "
                            v-on:keyup="formData.name = slugify($event.target.value)"
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('role.role_name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('role.role_name'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('role.description')"
                        name="description"
                        :help="rules.description ? rules.description.message : null"
                        :validateStatus="rules.description ? 'error' : null"
                    >
                        <a-textarea
                            v-model:value="formData.description"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('role.description'),
                                ])
                            "
                            :rows="4"
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item :label="$t('role.search_permissions')">
                        <a-input-search
                            v-model:value="searchedPermission"
                            allowClear
                            :placeholder="$t('role.search_permissions')"
                            style="width: 100%"
                            @change="onSearch"
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('role.permissions')"
                        name="permissions"
                        :help="rules.permissions ? rules.permissions.message : null"
                        :validateStatus="rules.permissions ? 'error' : null"
                    >
                        <div class="d-flex flex-column scroll-y">
                            <div class="tbl-responsive">
                                <a-checkbox-group v-model:value="checkedPermissions">
                                    <table
                                        id="myTable"
                                        class="table align-middle table-row-dashed row-gap"
                                    >
                                        <tbody class="text-gray-600 fw-bold">
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.staff_members") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'users_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'users_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'users_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'users_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.assets") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'assets_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'assets_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'assets_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'assets_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.salary_components") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'salary_components_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'salary_components_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'salary_components_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'salary_components_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.salary_groups") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'salary_groups_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'salary_groups_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'salary_groups_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'salary_groups_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.departments") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'departments_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'departments_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'departments_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'departments_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.designations") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'designations_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'designations_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'designations_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'designations_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.shifts") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'shifts_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'shifts_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'shifts_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'shifts_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.asset_types") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'asset_types_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'asset_types_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'asset_types_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'asset_types_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.weekend_holiday") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'holidays_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'holidays_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'holidays_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'holidays_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.leaves") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'leaves_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'leaves_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'leaves_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'leaves_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.leave_approve_reject") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'leaves_approve_reject'
                                                                    ]
                                                                "
                                                            >
                                                                {{
                                                                    $t(
                                                                        "leave.approve_reject"
                                                                    )
                                                                }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{
                                                        $t("menu.expense_approve_reject")
                                                    }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expenses_approve_reject'
                                                                    ]
                                                                "
                                                            >
                                                                {{
                                                                    $t(
                                                                        "expense.approve_reject"
                                                                    )
                                                                }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.leave_types") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'leave_types_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'leave_types_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'leave_types_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'leave_types_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.attendances") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'attendances_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'attendances_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'attendances_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'attendances_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.pre_payments") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'pre_payments_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'pre_payments_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'pre_payments_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'pre_payments_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.employee_work_status") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'employee_work_status_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'employee_work_status_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'employee_work_status_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'employee_work_status_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.increments_promotions") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'increments_promotions_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'increments_promotions_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'increments_promotions_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'increments_promotions_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.news") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'news_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'news_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'news_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'news_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.payrolls") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'payrolls_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'payrolls_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'payrolls_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'payrolls_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.company_policies") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'company_policies_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'company_policies_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'company_policies_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'company_policies_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.warnings") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'warnings_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'warnings_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'warnings_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'warnings_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.resignations") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'resignations_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'resignations_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'resignations_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'resignations_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.terminations") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'terminations_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'terminations_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'terminations_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'terminations_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.indicators") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'indicators_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'indicators_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'indicators_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'indicators_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.mark_holiday_settings") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'mark_holidays_add_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{
                                                                    $t("common.add_edit")
                                                                }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.appreciations") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'appreciations_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'appreciations_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'appreciations_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'appreciations_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.awards") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'awards_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'awards_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'awards_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'awards_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.salary_setting") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'salary_settings'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.hrm_settings") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'hrm_settings'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.font_settings") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'font_settings'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.complaints") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'complaints_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'complaints_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'complaints_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'complaints_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.templates") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'letter_head_templates_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'letter_head_templates_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'letter_head_templates_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'letter_head_templates_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.generates") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'generates_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'generates_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'generates_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'generates_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.forms") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'forms_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'forms_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'forms_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'forms_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.feedbacks") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'feedbacks_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'feedbacks_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'feedbacks_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'feedbacks_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.accounts") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'accounts_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'accounts_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'accounts_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'accounts_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.payees") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'payees_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'payees_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'payees_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="payee-check payee-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'payees_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.payers") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'payers_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'payers_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'payers_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'payers_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.deposit_categories") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'deposit_categories_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'deposit_categories_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'deposit_categories_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'deposit_categories_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.expense_categories") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expense_categories_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expense_categories_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expense_categories_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expense_categories_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.deposits") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'deposits_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'deposits_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'deposits_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'deposits_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.expenses") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expenses_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expenses_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expenses_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expenses_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.company") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'companies_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.translations") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'translations_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'translations_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'translations_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'translations_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.roles") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'roles_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'roles_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'roles_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'roles_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.currencies") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'currencies_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'currencies_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'currencies_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'currencies_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.locations") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'locations_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'locations_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'locations_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'locations_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.pdf_fonts") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'pdf_fonts_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'pdf_fonts_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'pdf_fonts_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'pdf_fonts_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.company_expense") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'manage_company_expense'
                                                                    ]
                                                                "
                                                            >
                                                                {{
                                                                    $t(
                                                                        "common.manage_company_expense"
                                                                    )
                                                                }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.storage_settings") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'storage_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.email_settings") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'email_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.update_app") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'update_app'
                                                                    ]
                                                                "
                                                            >
                                                                {{
                                                                    $t(
                                                                        "common.update_app"
                                                                    )
                                                                }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.asset_lent_return") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'asset_lent_return_add_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{
                                                                    $t("common.add_edit")
                                                                }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </a-checkbox-group>
                            </div>
                        </div>
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-button
                type="primary"
                @click="onSubmit"
                style="margin-right: 8px"
                :loading="loading"
            >
                <template #icon> <SaveOutlined /> </template>
                {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
            </a-button>
            <a-button @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, onMounted, computed, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";

export default defineComponent({
    props: [
        "formData",
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
    ],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const roles = ref([]);
        const { t } = useI18n();
        const permissions = ref([]);
        const checkedPermissions = ref([]);
        const { slugify } = common();

        const searchedPermission = ref("");

        const onSearch = () => {
            var input, filter, table, tr, td, i, txtValue;
            input = searchedPermission.value;
            filter = input.toUpperCase();
            if (document && document.getElementById("myTable")) {
                table = document.getElementById("myTable");
                tr = table.getElementsByTagName("tr");

                for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName("td")[0];
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                        } else {
                            tr[i].style.display = "none";
                        }
                    }
                }
            }
        };

        onMounted(() => {
            axiosAdmin.get("permissions").then((response) => {
                const permissionArray = [];
                response.data.map((res) => {
                    permissionArray[res.name] = res.xid;
                });
                permissions.value = permissionArray;
            });
        });

        const onSubmit = () => {
            const newFormData = {
                ...props.formData,
                permissions: checkedPermissions.value,
            };

            addEditRequestAdmin({
                url: props.url,
                data: newFormData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (newVal) {
                    searchedPermission.value = "";
                    onSearch();
                }

                if (newVal && props.addEditType == "edit") {
                    props.data.perms.forEach((value) => {
                        checkedPermissions.value.push(value.xid);
                    });
                } else {
                    checkedPermissions.value = [];
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            roles,
            permissions,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
            checkedPermissions,
            slugify,
            searchedPermission,
            onSearch,
        };
    },
});
</script>

<style lang="less">
.flex-column {
    flex-direction: column !important;
}

.d-flex {
    display: flex !important;
}

.tbl-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
}

.align-middle {
    vertical-align: middle !important;
}
.table > tbody {
    vertical-align: inherit;
}
.text-gray-600 {
    color: #7e8299 !important;
}
.fw-bold {
    font-weight: 500 !important;
}
tbody,
td,
tfoot,
th,
thead,
tr {
    border-color: inherit;
    border-style: solid;
    border-width: 0;
}

.table.table-row-dashed tr {
    border-bottom-width: 1px;
    border-bottom-style: dashed;
    border-bottom-color: #eff2f5;
}

.table td:first-child,
.table th:first-child,
.table tr:first-child {
    padding-left: 0;
}

.form-check.form-check-custom {
    display: flex;
    align-items: center;
    padding-left: 0;
    margin: 0;
}

.me-9 {
    margin-right: 2.25rem !important;
}

.table.row-gap td,
.table.row-gap th {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
}

.me-lg-20 {
    margin-right: 5rem !important;
}

.ant-checkbox-group {
    width: 100%;
}
</style>
