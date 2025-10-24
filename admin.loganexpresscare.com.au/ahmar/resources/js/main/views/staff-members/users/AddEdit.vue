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
        <a-form layout="vertical" id="add_edit_user_form">
            <a-tabs v-model:activeKey="addEditActiveTab" @change="onTabChange">
                <a-tab-pane key="basic" :tab="$t('user.basic_info')">
                    <a-row>
                        <a-col :xs="24" :sm="24" :md="6" :lg="6">
                            <a-form-item
                                :label="$t('user.profile_image')"
                                name="profile_image"
                                :help="
                                    rules.profile_image
                                        ? rules.profile_image.message
                                        : null
                                "
                                :validateStatus="
                                    rules.profile_image ? 'error' : null
                                "
                            >
                                <Upload
                                    :formData="formData"
                                    folder="user"
                                    imageField="profile_image"
                                    @onFileUploaded="
                                        (file) => {
                                            formData.profile_image = file.file;
                                            formData.profile_image_url =
                                                file.file_url;
                                        }
                                    "
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="18" :lg="18">
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <a-form-item
                                        :label="$t('user.name')"
                                        name="name"
                                        :help="
                                            rules.name
                                                ? rules.name.message
                                                : null
                                        "
                                        :validateStatus="
                                            rules.name ? 'error' : null
                                        "
                                        class="required"
                                    >
                                        <a-input
                                            v-model:value="formData.name"
                                            :placeholder="
                                                $t(
                                                    'common.placeholder_default_text',
                                                    [$t('user.name')]
                                                )
                                            "
                                        />
                                    </a-form-item>
                                </a-col>
                                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <a-form-item
                                        :label="$t('user.employee_id')"
                                        name="employee_number"
                                        :help="
                                            rules.employee_number
                                                ? rules.employee_number.message
                                                : null
                                        "
                                        :validateStatus="
                                            rules.employee_number
                                                ? 'error'
                                                : null
                                        "
                                        class="required"
                                    >
                                        <a-input
                                            v-model:value="employeeId"
                                            :placeholder="
                                                $t(
                                                    'common.placeholder_default_text',
                                                    [$t('user.employee_id')]
                                                )
                                            "
                                        />
                                    </a-form-item>
                                </a-col>
                            </a-row>
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <a-form-item
                                        :label="$t('user.working_email')"
                                        name="email"
                                        :help="
                                            rules.email
                                                ? rules.email.message
                                                : null
                                        "
                                        :validateStatus="
                                            rules.email ? 'error' : null
                                        "
                                        class="required"
                                    >
                                        <a-input
                                            v-model:value="formData.email"
                                            :placeholder="
                                                $t(
                                                    'common.placeholder_default_text',
                                                    [$t('user.working_email')]
                                                )
                                            "
                                        />
                                    </a-form-item>
                                </a-col>
                                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <a-form-item
                                        :label="$t('user.working_phone')"
                                        name="phone"
                                        :help="
                                            rules.phone
                                                ? rules.phone.message
                                                : null
                                        "
                                        :validateStatus="
                                            rules.phone ? 'error' : null
                                        "
                                    >
                                        <a-input
                                            v-model:value="formData.phone"
                                            :placeholder="
                                                $t(
                                                    'common.placeholder_default_text',
                                                    [$t('user.working_phone')]
                                                )
                                            "
                                        />
                                    </a-form-item>
                                </a-col>
                            </a-row>
                            <a-row :gutter="16">
                                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                    <a-form-item
                                        :label="$t('user.allow_login')"
                                        name="allow_login"
                                        :help="
                                            rules.allow_login
                                                ? rules.allow_login.message
                                                : null
                                        "
                                        :validateStatus="
                                            rules.allow_login ? 'error' : null
                                        "
                                    >
                                        <a-switch
                                            v-model:checked="
                                                formData.allow_login
                                            "
                                            :checkedValue="1"
                                            :unCheckedValue="0"
                                        />
                                    </a-form-item>
                                </a-col>
                                <a-col
                                    :xs="24"
                                    :sm="24"
                                    :md="12"
                                    :lg="12"
                                    v-if="formData.allow_login == 1"
                                >
                                    <a-form-item
                                        :label="$t('user.password')"
                                        name="password"
                                        :help="
                                            rules.password
                                                ? rules.password.message
                                                : null
                                        "
                                        :validateStatus="
                                            rules.password ? 'error' : null
                                        "
                                        class="required"
                                    >
                                        <a-input-password
                                            v-model:value="formData.password"
                                            :placeholder="
                                                $t(
                                                    'common.placeholder_default_text',
                                                    [$t('user.password')]
                                                )
                                            "
                                        />
                                    </a-form-item>
                                </a-col>
                            </a-row>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.joining_date')"
                                name="joining_date"
                                :help="
                                    rules.joining_date
                                        ? rules.joining_date.message
                                        : null
                                "
                                :validateStatus="
                                    rules.joining_date ? 'error' : null
                                "
                                class="required"
                            >
                                <a-date-picker
                                    v-model:value="joiningDate"
                                    :format="appSetting.date_format"
                                    valueFormat="YYYY-MM-DD"
                                    style="width: 100%"
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.status')"
                                name="status"
                                :help="
                                    rules.status ? rules.status.message : null
                                "
                                :validateStatus="rules.status ? 'error' : null"
                            >
                                <a-radio-group
                                    v-model:value="formData.status"
                                    button-style="solid"
                                    size="small"
                                >
                                    <a-radio-button value="active">
                                        {{ $t("common.active") }}
                                    </a-radio-button>
                                    <a-radio-button value="inactive">
                                        {{ $t("common.inactive") }}
                                    </a-radio-button>
                                </a-radio-group>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('user.address')"
                                name="address"
                                :help="
                                    rules.address ? rules.address.message : null
                                "
                                :validateStatus="rules.address ? 'error' : null"
                            >
                                <a-textarea
                                    v-model:value="formData.address"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('user.address'),
                                        ])
                                    "
                                    :auto-size="{ minRows: 2, maxRows: 3 }"
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-tab-pane>
                <a-tab-pane
                    key="personal"
                    :tab="$t('user.personal_info')"
                    force-render
                >
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.gender')"
                                name="gender"
                                :help="
                                    rules.gender ? rules.gender.message : null
                                "
                                :validateStatus="rules.gender ? 'error' : null"
                                class="required"
                            >
                                <a-select
                                    v-model:value="formData.gender"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('user.gender'),
                                        ])
                                    "
                                >
                                    <a-select-option value="male">{{
                                        $t("user.male")
                                    }}</a-select-option>
                                    <a-select-option value="female">{{
                                        $t("user.female")
                                    }}</a-select-option>
                                    <a-select-option value="other">{{
                                        $t("user.other")
                                    }}</a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.date_of_birth')"
                                name="dob"
                                :help="rules.dob ? rules.dob.message : null"
                                :validateStatus="rules.dob ? 'error' : null"
                            >
                                <a-date-picker
                                    v-model:value="formData.dob"
                                    :format="appSetting.date_format"
                                    valueFormat="YYYY-MM-DD"
                                    style="width: 100%"
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.personal_email')"
                                name="personal_email"
                                :help="
                                    rules.personal_email
                                        ? rules.personal_email.message
                                        : null
                                "
                                :validateStatus="
                                    rules.personal_email ? 'error' : null
                                "
                            >
                                <a-input
                                    v-model:value="formData.personal_email"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('user.personal_email'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.personal_phone')"
                                name="personal_phone"
                                :help="
                                    rules.personal_phone
                                        ? rules.personal_phone.message
                                        : null
                                "
                                :validateStatus="
                                    rules.personal_phone ? 'error' : null
                                "
                            >
                                <a-input
                                    v-model:value="formData.personal_phone"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('user.personal_phone'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.is_married')"
                                name="is_married"
                                :help="
                                    rules.is_married
                                        ? rules.is_married.message
                                        : null
                                "
                                :validateStatus="
                                    rules.is_married ? 'error' : null
                                "
                            >
                                <a-switch
                                    v-model:checked="formData.is_married"
                                    :checkedValue="1"
                                    :unCheckedValue="0"
                                />
                            </a-form-item>
                        </a-col>
                        <a-col
                            :xs="24"
                            :sm="24"
                            :md="12"
                            :lg="12"
                            v-if="formData.is_married == 1"
                        >
                            <a-form-item
                                :label="$t('user.marriage_date')"
                                name="marriage_date"
                                :help="
                                    rules.marriage_date
                                        ? rules.marriage_date.message
                                        : null
                                "
                                :validateStatus="
                                    rules.marriage_date ? 'error' : null
                                "
                            >
                                <a-date-picker
                                    v-model:value="formData.marriage_date"
                                    :format="appSetting.date_format"
                                    valueFormat="YYYY-MM-DD"
                                    style="width: 100%"
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-tab-pane>
                <a-tab-pane
                    key="company"
                    :tab="$t('user.company_relation')"
                    force-render
                >
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.location_id')"
                                name="location_id"
                                :help="
                                    rules.location_id
                                        ? rules.location_id.message
                                        : null
                                "
                                :validateStatus="
                                    rules.location_id ? 'error' : null
                                "
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="formData.location_id"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('user.location_id'),
                                            ])
                                        "
                                        :allowClear="true"
                                        optionFilterProp="title"
                                        show-search
                                    >
                                        <a-select-option
                                            v-for="location in locations"
                                            :key="location.xid"
                                            :value="location.xid"
                                            :title="location.name"
                                        >
                                            {{ location.name }}
                                        </a-select-option>
                                    </a-select>
                                    <LocationAddButton
                                        @onAddSuccess="locationAdded"
                                    />
                                </span>
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.shift_id')"
                                name="shift_id"
                                :help="
                                    rules.shift_id
                                        ? rules.shift_id.message
                                        : null
                                "
                                :validateStatus="
                                    rules.shift_id ? 'error' : null
                                "
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="formData.shift_id"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('user.shift_id'),
                                            ])
                                        "
                                        :allowClear="true"
                                        optionFilterProp="title"
                                        show-search
                                    >
                                        <a-select-option
                                            v-for="shift in shifts"
                                            :key="shift.xid"
                                            :value="shift.xid"
                                            :title="shift.name"
                                        >
                                            {{ shift.name }}
                                        </a-select-option>
                                    </a-select>
                                    <ShiftAddButton
                                        @onAddSuccess="shiftAdded"
                                    />
                                </span>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.department_id')"
                                name="department_id"
                                :help="
                                    rules.department_id
                                        ? rules.department_id.message
                                        : null
                                "
                                :validateStatus="
                                    rules.department_id ? 'error' : null
                                "
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="formData.department_id"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('user.department_id'),
                                            ])
                                        "
                                        :allowClear="true"
                                        optionFilterProp="title"
                                        show-search
                                    >
                                        <a-select-option
                                            v-for="department in departments"
                                            :key="department.xid"
                                            :value="department.xid"
                                            :title="department.name"
                                        >
                                            {{ department.name }}
                                        </a-select-option>
                                    </a-select>
                                    <DepartmentAddButton
                                        @onAddSuccess="departmentAdded"
                                    />
                                </span>
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.designation_id')"
                                name="designation_id"
                                :help="
                                    rules.designation_id
                                        ? rules.designation_id.message
                                        : null
                                "
                                :validateStatus="
                                    rules.designation_id ? 'error' : null
                                "
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="formData.designation_id"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('user.designation_id'),
                                            ])
                                        "
                                        :allowClear="true"
                                        optionFilterProp="title"
                                        show-search
                                    >
                                        <a-select-option
                                            v-for="designation in designations"
                                            :key="designation.xid"
                                            :value="designation.xid"
                                            :title="designation.name"
                                        >
                                            {{ designation.name }}
                                        </a-select-option>
                                    </a-select>
                                    <DesignationAddButton
                                        @onAddSuccess="designationAdded"
                                    />
                                </span>
                            </a-form-item>
                        </a-col>
                    </a-row>

                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.report_to')"
                                name="report_to"
                                :help="
                                    rules.report_to
                                        ? rules.report_to.message
                                        : null
                                "
                                :validateStatus="
                                    rules.report_to ? 'error' : null
                                "
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="formData.report_to"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('user.report_to'),
                                            ])
                                        "
                                        :allowClear="true"
                                        optionFilterProp="title"
                                        show-search
                                    >
                                        <a-select-option
                                            v-for="allStaffMember in allMembers"
                                            :key="allStaffMember.xid"
                                            :value="allStaffMember.xid"
                                            :title="allStaffMember.name"
                                        >
                                            {{ allStaffMember.name }}
                                        </a-select-option>
                                    </a-select>
                                </span>
                            </a-form-item>
                        </a-col>
                        <a-col
                            v-if="permsArray.includes('admin')"
                            :xs="24"
                            :sm="24"
                            :md="12"
                            :lg="12"
                        >
                            <a-form-item
                                :label="$t('user.is_manager')"
                                name="is_manager"
                                :help="
                                    rules.is_manager
                                        ? rules.is_manager.message
                                        : null
                                "
                                :validateStatus="
                                    rules.is_manager ? 'error' : null
                                "
                            >
                                <a-switch
                                    v-model:checked="isManager"
                                    :checkedValue="1"
                                    :unCheckedValue="0"
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16" v-if="permsArray.includes('admin')">
                        <a-col
                            :xs="24"
                            :sm="24"
                            :md="12"
                            :lg="12"
                            v-if="isManager == 1"
                        >
                            <a-form-item
                                :label="$t('user.role')"
                                name="role_id"
                                :help="
                                    rules.role_id ? rules.role_id.message : null
                                "
                                :validateStatus="rules.role_id ? 'error' : null"
                                class="required"
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="staffRole"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('user.role'),
                                            ])
                                        "
                                        :allowClear="true"
                                        optionFilterProp="title"
                                        show-search
                                        @change="assignRole(staffRole)"
                                    >
                                        <a-select-option
                                            v-for="role in roles"
                                            :key="role.xid"
                                            :value="role.xid"
                                            :title="role.display_name"
                                        >
                                            {{ role.display_name }}
                                        </a-select-option>
                                    </a-select>
                                    <RoleAddButton @onAddSuccess="roleAdded" />
                                </span>
                            </a-form-item>
                        </a-col>
                        <a-col
                            :xs="24"
                            :sm="24"
                            :md="12"
                            :lg="12"
                            v-if="showVisibilty && isManager"
                        >
                            <a-form-item
                                :label="$t('user.visibility')"
                                name="visibility"
                                :help="
                                    rules.visibility
                                        ? rules.visibility.message
                                        : null
                                "
                                :validateStatus="
                                    rules.visibility ? 'error' : null
                                "
                                class="required"
                            >
                                <a-select
                                    v-model:value="selectedVisibility"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('user.visibility'),
                                        ])
                                    "
                                    :allowClear="true"
                                >
                                    <a-select-option value="manager">{{
                                        $t("user.manager")
                                    }}</a-select-option>
                                    <a-select-option value="department">{{
                                        $t("user.department")
                                    }}</a-select-option>
                                    <a-select-option value="location">{{
                                        $t("user.location")
                                    }}</a-select-option>
                                    <a-select-option value="company">{{
                                        $t("user.company")
                                    }}</a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-tab-pane>
                <a-tab-pane key="work" :tab="$t('user.work_info')" force-render>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.probation_start_date')"
                                name="probation_start_date"
                                :help="
                                    rules.probation_start_date
                                        ? rules.probation_start_date.message
                                        : null
                                "
                                :validateStatus="
                                    rules.probation_start_date ? 'error' : null
                                "
                            >
                                <a-date-picker
                                    v-model:value="
                                        formData.probation_start_date
                                    "
                                    :format="appSetting.date_format"
                                    valueFormat="YYYY-MM-DD"
                                    style="width: 100%"
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.probation_end_date')"
                                name="probation_end_date"
                                :help="
                                    rules.probation_end_date
                                        ? rules.probation_end_date.message
                                        : null
                                "
                                :validateStatus="
                                    rules.probation_end_date ? 'error' : null
                                "
                            >
                                <a-date-picker
                                    v-model:value="formData.probation_end_date"
                                    :format="appSetting.date_format"
                                    valueFormat="YYYY-MM-DD"
                                    style="width: 100%"
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.notice_start_date')"
                                name="notice_start_date"
                                :help="
                                    rules.notice_start_date
                                        ? rules.notice_start_date.message
                                        : null
                                "
                                :validateStatus="
                                    rules.notice_start_date ? 'error' : null
                                "
                            >
                                <a-date-picker
                                    v-model:value="formData.notice_start_date"
                                    :format="appSetting.date_format"
                                    valueFormat="YYYY-MM-DD"
                                    style="width: 100%"
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.notice_end_date')"
                                name="notice_end_date"
                                :help="
                                    rules.notice_end_date
                                        ? rules.notice_end_date.message
                                        : null
                                "
                                :validateStatus="
                                    rules.notice_end_date ? 'error' : null
                                "
                            >
                                <a-date-picker
                                    v-model:value="formData.notice_end_date"
                                    :format="appSetting.date_format"
                                    valueFormat="YYYY-MM-DD"
                                    style="width: 100%"
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.end_date')"
                                name="end_date"
                                :help="
                                    rules.end_date
                                        ? rules.end_date.message
                                        : null
                                "
                                :validateStatus="
                                    rules.end_date ? 'error' : null
                                "
                            >
                                <a-date-picker
                                    v-model:value="formData.end_date"
                                    :format="appSetting.date_format"
                                    valueFormat="YYYY-MM-DD"
                                    style="width: 100%"
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('user.employee_work_status')"
                                name="employee_status_id"
                                :help="
                                    rules.employee_status_id
                                        ? rules.employee_status_id.message
                                        : null
                                "
                                :validateStatus="
                                    rules.employee_status_id ? 'error' : null
                                "
                                class="required"
                            >
                                <span style="display: flex">
                                    <a-select
                                        v-model:value="
                                            formData.employee_status_id
                                        "
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('user.employee_work_status'),
                                            ])
                                        "
                                        :allowClear="true"
                                        optionFilterProp="title"
                                        show-search
                                    >
                                        <a-select-option
                                            v-for="employeeStatus in employeeWorkStatus"
                                            :key="employeeStatus.xid"
                                            :value="employeeStatus.xid"
                                            :title="employeeStatus.display_name"
                                        >
                                            {{
                                                employeeStatus.name ===
                                                "fulltime"
                                                    ? $t(
                                                          "employee_work_status.fulltime"
                                                      )
                                                    : employeeStatus.name ===
                                                      "contract"
                                                    ? $t(
                                                          "employee_work_status.contract"
                                                      )
                                                    : employeeStatus.name ===
                                                      "probation"
                                                    ? $t(
                                                          "employee_work_status.probation"
                                                      )
                                                    : employeeStatus.name ===
                                                      "work_from_home"
                                                    ? $t(
                                                          "employee_work_status.work_from_home"
                                                      )
                                                    : employeeStatus.name
                                            }}
                                        </a-select-option>
                                    </a-select>
                                    <EmployeeWorkStatusAddButton
                                        @onAddSuccess="employeeWorkStatusAdded"
                                    />
                                </span>
                            </a-form-item>
                        </a-col>
                    </a-row>
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
                    <BasicSalary
                        ref="basicSalaryRef"
                        :visible="adjustedVisible"
                        :user="adjustedUser"
                        @updateSalaryData="updateSalaryData"
                    />
                </a-tab-pane>

                <a-tab-pane
                    key="custom_data_fields"
                    :tab="$t('user.custom_data_fields')"
                    force-render
                >
                    <div v-for="fieldType in modifyField" :key="fieldType.id">
                        <FormItemHeading>
                            {{ fieldType.name }}
                        </FormItemHeading>
                        <a-row
                            v-for="(
                                rowFields, rowIndex
                            ) in fieldType.employee_field"
                            :key="rowIndex"
                            :gutter="16"
                        >
                            <a-col
                                v-for="(field, index) in rowFields"
                                :key="field.xid"
                                :xs="24"
                                :sm="24"
                                :md="12"
                                :lg="12"
                            >
                                <a-form-item
                                    :label="field.name"
                                    :name="`field_${field.name}`"
                                >
                                    <a-input
                                        v-if="field.type === 'text'"
                                        v-model:value="
                                            formFields[field.originalIndex]
                                                .value
                                        "
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [$t(field.name)]
                                            )
                                        "
                                    />

                                    <a-textarea
                                        v-else-if="field.type === 'large_text'"
                                        v-model:value="
                                            formFields[field.originalIndex]
                                                .value
                                        "
                                        :rows="4"
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [$t(field.name)]
                                            )
                                        "
                                    />

                                    <a-date-picker
                                        v-else-if="field.type === 'date'"
                                        v-model:value="
                                            formFields[field.originalIndex]
                                                .value
                                        "
                                        :format="appSetting.date_format"
                                        valueFormat="YYYY-MM-DD"
                                        style="width: 100%"
                                        :placeholder="$t('common.select_date')"
                                    />

                                    <UploadFile
                                        v-else-if="field.type === 'file'"
                                        :acceptFormat="'image/*,.pdf'"
                                        :formData="
                                            formFields[field.originalIndex]
                                        "
                                        folder="userDocument"
                                        uploadField="value"
                                        @onFileUploaded="
                                            (file) => {
                                                formFields[
                                                    field.originalIndex
                                                ].value = file.file;
                                                formFields[
                                                    field.originalIndex
                                                ].value_url = file.file_url;
                                            }
                                        "
                                        @onDelete="
                                            () => {
                                                formFields[
                                                    field.originalIndex
                                                ].value = undefined;
                                                formFields[
                                                    field.originalIndex
                                                ].value_url = undefined;
                                            }
                                        "
                                    />

                                    <a-select
                                        v-else-if="field.type === 'select'"
                                        v-model:value="
                                            formFields[field.originalIndex]
                                                .value
                                        "
                                        style="width: 100%"
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t(field.name),
                                            ])
                                        "
                                    >
                                        <a-select-option
                                            v-for="option in field.default_value.split(
                                                ','
                                            )"
                                            :key="option.trim()"
                                            :value="option.trim()"
                                        >
                                            {{ option.trim() }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </div>
                </a-tab-pane>
            </a-tabs>
        </a-form>
        <template #footer>
            <a-space>
                <a-button type="primary" @click="onSubmit" :loading="loading">
                    <template #icon> <SaveOutlined /> </template>
                    {{
                        addEditType == "add"
                            ? $t("common.create")
                            : $t("common.update")
                    }}
                </a-button>
                <a-button @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>

<script>
import {
    defineComponent,
    ref,
    onMounted,
    watch,
    nextTick,
    computed,
} from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import Upload from "../../../../common/core/ui/file/Upload.vue";
import RoleAddButton from "../../settings/roles/AddButton.vue";
import common from "../../../../common/composable/common";
import DepartmentAddButton from "../departments/AddButton.vue";
import DesignationAddButton from "../designations/AddButton.vue";
import ShiftAddButton from "../shifts/AddButton.vue";
import LocationAddButton from "../../settings/location/AddButton.vue";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";
import { forEach, find } from "lodash-es";
import BasicSalary from "../../payrolls/basic-salary/BasicSalary.vue";
import EmployeeWorkStatusAddButton from "../../settings/employee-work-status/AddButton.vue";
import { useAuthStore } from "../../../../main/store/authStore";
import UploadFile from "../../../../common/core/ui/file/UploadFile.vue";
import _ from "lodash";

export default defineComponent({
    props: [
        "formData",
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "reFetchListOfData",
        "successMessage",
    ],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        Upload,
        RoleAddButton,
        DepartmentAddButton,
        DesignationAddButton,
        ShiftAddButton,
        LocationAddButton,
        FormItemHeading,
        BasicSalary,
        EmployeeWorkStatusAddButton,
        UploadFile,
    },
    setup(props, { emit }) {
        const { permsArray, user, appSetting, dayjs } = common();
        const { addEditRequestAdmin, loading, rules, addEditActiveTab } =
            apiAdmin("basic");
        const roles = ref([]);
        const roleUrl = "roles?limit=10000";
        const authStore = useAuthStore();
        const departments = ref([]);
        const locations = ref([]);
        const designations = ref([]);
        const selectedVisibility = ref("manager");
        const shifts = ref([]);
        const joiningDate = ref("");
        const showVisibilty = ref(false);
        const isManager = ref(0);
        const staffRole = ref(undefined);
        const designationUrl = "designations?limit=10000";
        const departmentUrl = "departments?limit=10000";
        const locationUrl = "locations?limit=10000";
        const shiftUrl = "shifts?limit=10000";
        const allMembers = ref([]);
        const allStaffMemberUrl = "users?limit=10000";
        const employeeId = ref("");
        const adjustedVisible = ref(false);
        const adjustedUser = ref({});
        const newData = ref({});
        const basicSalaryRef = ref(null);
        const employeeWorkStatusUrl = "employee-work-status?limit=10000";
        const employeeWorkStatus = ref([]);
        const fieldTypesData = ref([]);
        const fieldTypeDataUrl =
            "field-types?fields=id,xid,name,employeeField{id,xid,name,field_type_id,type,default_value}";
        const formFields = ref([]);

        onMounted(() => {
            const rolesPromise = axiosAdmin.get(roleUrl);
            const shiftsPromise = axiosAdmin.get(shiftUrl);
            const locationPromise = axiosAdmin.get(locationUrl);
            const departmentsPromise = axiosAdmin.get(departmentUrl);
            const designationsPromise = axiosAdmin.get(designationUrl);
            const employeeWorkStatusPromise = axiosAdmin.get(
                employeeWorkStatusUrl
            );
            const fieldTypesDataPromise = axiosAdmin.get(fieldTypeDataUrl);

            Promise.all([
                rolesPromise,
                departmentsPromise,
                locationPromise,
                designationsPromise,
                shiftsPromise,
                employeeWorkStatusPromise,
                fieldTypesDataPromise,
            ]).then(
                ([
                    rolesResponse,
                    departmentsResponse,
                    locationResponse,
                    designationsResponse,
                    shiftsResponse,
                    employeeWorkStatusResponse,
                    fieldTypesDataResponse,
                ]) => {
                    roles.value = rolesResponse.data;
                    departments.value = departmentsResponse.data;
                    designations.value = designationsResponse.data;
                    shifts.value = shiftsResponse.data;
                    locations.value = locationResponse.data;
                    employeeWorkStatus.value = employeeWorkStatusResponse.data;
                    fieldTypesData.value = fieldTypesDataResponse.data;
                }
            );

            employeeId.value =
                appSetting.value.employee_id_prefix +
                "-" +
                appSetting.value.employee_id_start;
        });

        const onSubmit = () => {
            var newFormData = {
                ...props.formData,
                joining_date: joiningDate.value,
                is_manager: isManager.value,
                role_id: staffRole.value,
                visibility: selectedVisibility.value,
                employee_number: employeeId.value,
                ...newData.value,
                employee_fields_values: formFields.value,
            };
            addEditRequestAdmin({
                id: "add_edit_user_form",
                url: props.url,
                data: newFormData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                    authStore.updateAppAction();

                    if (user.value.xid == res.xid) {
                        authStore.updateUserAction();
                    }
                },
            });
        };

        const modifyField = computed(() =>
            fieldTypesData.value.map((type) => {
                const fieldsWithIndex = type.employee_field
                    .map((field) => {
                        const originalIndex = formFields.value.findIndex(
                            (f) => f.field_type_id === field.xid
                        );
                        if (originalIndex === -1) return null; // Skip if not found
                        return {
                            ...field,
                            originalIndex,
                        };
                    })
                    .filter(Boolean); // Remove nulls

                const rows = fieldsWithIndex.reduce((acc, field, i) => {
                    const rowIndex = Math.floor(i / 2); // 2 columns per row
                    if (!acc[rowIndex]) acc[rowIndex] = [];
                    acc[rowIndex].push(field);
                    return acc;
                }, []);

                return {
                    ...type,
                    employee_field: rows,
                };
            })
        );

        const assignRole = (id) => {
            if (id) {
                forEach(roles.value, (role) => {
                    if (role.xid == id) {
                        if (role.name == "admin") {
                            showVisibilty.value = false;
                        } else {
                            showVisibilty.value = true;
                        }
                    }
                });
            } else {
                showVisibilty.value = false;
            }
        };
        const updateSalaryData = (updatedData) => {
            Object.assign(newData.value, updatedData);
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        const roleAdded = (xid) => {
            axiosAdmin.get(roleUrl).then((response) => {
                roles.value = response.data;
                staffRole.value = xid;
                assignRole(xid);
            });
        };

        const departmentAdded = (xid) => {
            axiosAdmin.get(departmentUrl).then((response) => {
                departments.value = response.data;
                emit("addListSuccess", {
                    type: "department_id",
                    id: xid,
                });
            });
        };

        const designationAdded = (xid) => {
            axiosAdmin.get(designationUrl).then((response) => {
                designations.value = response.data;
                emit("addListSuccess", { type: "designation_id", id: xid });
            });
        };

        const shiftAdded = (xid) => {
            axiosAdmin.get(shiftUrl).then((response) => {
                shifts.value = response.data;
                emit("addListSuccess", { type: "shift_id", id: xid });
            });
        };
        const locationAdded = (xid) => {
            axiosAdmin.get(locationUrl).then((response) => {
                locations.value = response.data;
                emit("addListSuccess", { type: "location_id", id: xid });
            });
        };

        const employeeWorkStatusAdded = (xid) => {
            axiosAdmin.get(employeeWorkStatusUrl).then((response) => {
                employeeWorkStatus.value = response.data;
                emit("addListSuccess", { type: "employee_status_id", id: xid });
            });
        };

        const resetUserDocumentFields = () => {
            formFields.value = [];

            forEach(fieldTypesData.value, (type) => {
                forEach(type.employee_field, (field) => {
                    if (
                        props.addEditType === "add" ||
                        props.data.user_document.length === 0
                    ) {
                        formFields.value.push({
                            value:
                                field.type === "file" || field.type === "select"
                                    ? undefined
                                    : "",
                            field_type_id: field.xid,
                            value_url: undefined,
                        });
                    } else {
                        const searchedDoc = find(props.data.user_document, [
                            "x_field_type_id",
                            field.xid,
                        ]);

                        formFields.value.push({
                            value:
                                searchedDoc?.values ??
                                (field.type === "file" ||
                                field.type === "select"
                                    ? undefined
                                    : ""),
                            value_url: searchedDoc?.values_url ?? undefined,
                            field_type_id: field.xid,
                        });
                    }
                });
            });
        };

        watch(
            () => staffRole.value,
            (newVal, oldVal) => {
                if (newVal === undefined) {
                    selectedVisibility.value = "individual";
                }
            }
        );

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                addEditActiveTab.value = "basic";
                if (newVal) {
                    resetUserDocumentFields();
                    const allMembersPromise = axiosAdmin.get(allStaffMemberUrl);
                    Promise.all([allMembersPromise]).then(
                        ([allMembersResponse]) => {
                            allMembers.value = allMembersResponse.data;
                        }
                    );
                    staffRole.value = undefined;
                    isManager.value = 0;
                    showVisibilty.value = false;

                    if (props.addEditType == "add") {
                        joiningDate.value = dayjs().format("YYYY-MM-DD");

                        employeeId.value =
                            appSetting.value.employee_id_prefix +
                            "-" +
                            appSetting.value.employee_id_start;
                    }

                    if (props.addEditType == "edit") {
                        joiningDate.value = props.data.joining_date;
                        isManager.value = props.data.is_manager;
                        staffRole.value = props.data.x_role_id;
                        employeeId.value = props.data.employee_number;

                        if (
                            props.data.visibility != "" &&
                            props.data.visibility != null &&
                            props.data.role &&
                            props.data.role.name != "admin"
                        ) {
                            selectedVisibility.value = props.data.visibility;
                            showVisibilty.value = true;
                        }

                        if (props.data.is_manager == 1) {
                            isManager.value = 1;
                        }
                    }

                    if (!newVal) {
                        adjustedVisible.value = false;
                    }
                }
            }
        );

        const onTabChange = async (addEditActiveTab) => {
            adjustedUser.value = {};
            adjustedVisible.value = false;
            await nextTick();
            if (addEditActiveTab === "salary_details" && props.visible) {
                if (basicSalaryRef.value) {
                    adjustedVisible.value = true;
                    adjustedUser.value = { ...props.data };
                }
            } else {
                adjustedVisible.value = false;
            }
        };

        watch(
            () => isManager.value,
            (newVal, oldVal) => {
                if (newVal == 0) {
                    staffRole.value = undefined;
                    showVisibilty.value = false;
                    selectedVisibility.value = undefined;
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            roles,
            formFields,
            roleAdded,
            permsArray,
            appSetting,
            designationAdded,
            departmentAdded,
            departments,
            designations,
            shifts,
            shiftAdded,
            locations,
            locationAdded,
            assignRole,
            joiningDate,
            allMembers,
            showVisibilty,
            isManager,
            staffRole,
            selectedVisibility,
            addEditActiveTab,
            employeeId,
            updateSalaryData,
            adjustedVisible,
            adjustedUser,
            onTabChange,
            basicSalaryRef,
            employeeWorkStatusAdded,
            employeeWorkStatus,
            fieldTypesData,
            modifyField,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "65%",
        };
    },
});
</script>
