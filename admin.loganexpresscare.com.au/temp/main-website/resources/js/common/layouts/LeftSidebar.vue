<template>
    <a-layout-sider
        :width="240"
        :style="{
            margin: '0 0 0 0',
            overflowY: 'auto',
            position: 'fixed',
            paddingTop: '8px',
            zIndex: 998,
            borderRight:
                themeMode == 'dark' ? '1px solid #303030' : '1px solid #f0f0f0',
        }"
        :trigger="null"
        :collapsed="menuCollapsed"
        :theme="themeMode == 'dark' ? 'light' : appSetting.left_sidebar_theme"
        class="sidebar-right-border"
    >
        <div v-if="menuCollapsed" class="logo">
            <img
                :style="{
                    height: '32px',
                }"
                :src="
                    themeMode == 'dark'
                        ? appSetting.small_dark_logo_url
                        : appSetting.left_sidebar_theme == 'dark'
                        ? appSetting.small_dark_logo_url
                        : appSetting.small_light_logo_url
                "
            />
        </div>
        <div v-else>
            <img
                :style="{
                    width: '150px',
                    height: '53px',
                    paddingLeft: appSetting.rtl ? '0px' : '30px',
                    paddingRight: appSetting.rtl ? '30px' : '0px',
                    paddingTop: '5px',
                    paddingBottom: '20px',
                    marginLeft: appSetting.rtl ? '0px' : '10px',
                    marginRight: appSetting.rtl ? '10px' : '0px',
                    display: 'inline-flex',
                }"
                :src="
                    themeMode == 'dark'
                        ? appSetting.dark_logo_url
                        : appSetting.left_sidebar_theme == 'dark'
                        ? appSetting.dark_logo_url
                        : appSetting.light_logo_url
                "
            />
            <CloseOutlined
                v-if="innerWidth <= 991"
                :style="{
                    marginLeft: appSetting.rtl ? '0px' : '45px',
                    marginRight: appSetting.rtl ? '45px' : '0px',
                    verticalAlign: 'super',
                    color:
                        themeMode == 'dark'
                            ? '#fff'
                            : appSetting.left_sidebar_theme == 'dark'
                            ? '#fff'
                            : '#000000',
                }"
                @click="menuSelected"
            />
        </div>

        <div class="main-sidebar">
            <a-tabs
                v-if="authStore && authStore.user && authStore.user.is_manager"
                v-model:activeKey="activeKey"
                :tabBarStyle="{
                    color:
                        themeMode == 'dark' ||
                        appSetting.left_sidebar_theme == 'dark'
                            ? '#fff'
                            : '#000',
                    padding: '0 22px',
                }"
                @change="
                    () =>
                        activeKey == 'manager'
                            ? $router.push({
                                  name: 'admin.dashboard.index',
                              })
                            : $router.push({
                                  name: 'admin.self.dashboard.index',
                              })
                "
            >
                <a-tab-pane key="self" force-render>
                    <template #tab>
                        <span>
                            <UserOutlined />
                            {{ $t("common.self") }}
                        </span>
                    </template>
                </a-tab-pane>
                <a-tab-pane key="manager" force-render>
                    <template #tab>
                        <span>
                            <UserOutlined />
                            {{ $t("common.manager") }}
                        </span>
                    </template>
                </a-tab-pane>
            </a-tabs>
            <div class="scroll-container" v-if="activeKey == 'manager'">
                <a-menu
                    :theme="
                        themeMode == 'dark'
                            ? 'light'
                            : appSetting.left_sidebar_theme
                    "
                    :openKeys="openKeys"
                    v-model:selectedKeys="selectedKeys"
                    :mode="mode"
                    @openChange="onOpenChange"
                    :style="{ borderRight: 'none' }"
                >
                    <a-menu-item
                        @click="
                            () => {
                                menuSelected();
                                $router.push({ name: 'admin.dashboard.index' });
                            }
                        "
                        key="dashboard"
                    >
                        <HomeOutlined />
                        <span>{{ $t("menu.dashboard") }}</span>
                    </a-menu-item>
                    <a-sub-menu
                        key="staff"
                        v-if="
                            permsArray.includes('users_view') ||
                            permsArray.includes('departments_view') ||
                            permsArray.includes('designations_view') ||
                            permsArray.includes('shifts_view') ||
                            permsArray.includes('admin')
                        "
                    >
                        <template #title>
                            <span>
                                <UserOutlined />
                                <span>{{ $t("menu.staff_members") }}</span>
                            </span>
                        </template>
                        <a-menu-item
                            v-if="
                                permsArray.includes('users_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.staffs.index',
                                    });
                                }
                            "
                            key="staff"
                        >
                            <span>{{ $t("menu.staff_members") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('departments_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.departments.index',
                                    });
                                }
                            "
                            key="departments"
                        >
                            <span>{{ $t("menu.departments") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('designations_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.designations.index',
                                    });
                                }
                            "
                            key="designations"
                        >
                            <span>{{ $t("menu.designations") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('shifts_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.shifts.index',
                                    });
                                }
                            "
                            key="shifts"
                        >
                            <span>{{ $t("menu.shifts") }}</span>
                        </a-menu-item>
                    </a-sub-menu>
                    <a-sub-menu
                        key="assets"
                        v-if="
                            (permsArray.includes('asset_types_view') ||
                                permsArray.includes('assets_view') ||
                                permsArray.includes('admin')) &&
                            willSubscriptionModuleVisible('assets')
                        "
                    >
                        <template #title>
                            <span>
                                <LaptopOutlined />
                                <span>{{ $t("menu.assets") }}</span>
                            </span>
                        </template>
                        <a-menu-item
                            v-if="
                                permsArray.includes('asset_types_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.asset_types.index',
                                    });
                                }
                            "
                            key="asset_types"
                        >
                            <span>{{ $t("menu.asset_types") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('assets_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.assets.index',
                                    });
                                }
                            "
                            key="assets"
                        >
                            <span>{{ $t("menu.assets") }}</span>
                        </a-menu-item>
                    </a-sub-menu>
                    <a-sub-menu
                        key="holidays"
                        v-if="
                            (permsArray.includes('holidays_view') ||
                                permsArray.includes('admin')) &&
                            willSubscriptionModuleVisible('holidays')
                        "
                    >
                        <template #title>
                            <span>
                                <ScheduleOutlined />
                                <span>{{ $t("menu.holidays") }}</span>
                            </span>
                        </template>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.holidays.index',
                                    });
                                }
                            "
                            key="holidays"
                        >
                            <span>{{ $t("menu.holidays") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.weekends.index',
                                    });
                                }
                            "
                            key="weekends"
                        >
                            <span>{{ $t("menu.weekends") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.all-holidays.index',
                                    });
                                }
                            "
                            key="all_holidays"
                        >
                            <span>{{ $t("menu.all_holidays") }}</span>
                        </a-menu-item>
                    </a-sub-menu>
                    <a-sub-menu
                        key="appreciations"
                        v-if="
                            (permsArray.includes('appreciations_view') ||
                                permsArray.includes('awards_view') ||
                                permsArray.includes('admin')) &&
                            willSubscriptionModuleVisible('appreciations')
                        "
                    >
                        <template #title>
                            <span>
                                <CrownOutlined />
                                <span>{{ $t("menu.appreciations") }}</span>
                            </span>
                        </template>
                        <a-menu-item
                            v-if="
                                permsArray.includes('appreciations_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.appreciations.index',
                                    });
                                }
                            "
                            key="appreciations"
                        >
                            <span>{{ $t("menu.appreciations") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('awards_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.awards.index',
                                    });
                                }
                            "
                            key="awards"
                        >
                            <span>{{ $t("menu.awards") }}</span>
                        </a-menu-item>
                    </a-sub-menu>
                    <a-sub-menu
                        key="leaves"
                        v-if="
                            (permsArray.includes('leaves_view') ||
                                permsArray.includes('leave_types_view') ||
                                permsArray.includes('admin')) &&
                            willSubscriptionModuleVisible('leaves')
                        "
                    >
                        <template #title>
                            <span>
                                <CarOutlined />
                                <span>{{ $t("menu.leaves") }}</span>
                            </span>
                        </template>
                        <a-menu-item
                            v-if="
                                permsArray.includes('leaves_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.leaves.index',
                                    });
                                }
                            "
                            key="leaves"
                        >
                            <span>{{ $t("menu.leaves") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('leave_types_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.leave_types.index',
                                    });
                                }
                            "
                            key="leave_types"
                        >
                            <span>{{ $t("menu.leave_types") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('leaves_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.remaining-leaves.index',
                                    });
                                }
                            "
                            key="remaining_leaves"
                        >
                            <span>{{ $t("menu.remaining_leaves") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('leaves_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.unpaid-leaves.index',
                                    });
                                }
                            "
                            key="unpaid_leaves"
                        >
                            <span>{{ $t("menu.unpaid_leaves") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('leaves_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.paid-leaves.index',
                                    });
                                }
                            "
                            key="paid_leaves"
                        >
                            <span>{{ $t("menu.paid_leaves") }}</span>
                        </a-menu-item>
                    </a-sub-menu>
                    <a-sub-menu
                        key="attendances"
                        v-if="
                            permsArray.includes('attendances_view') ||
                            permsArray.includes('admin')
                        "
                    >
                        <template #title>
                            <span>
                                <ProfileOutlined />
                                <span>{{ $t("menu.attendances") }}</span>
                            </span>
                        </template>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.attendance.details',
                                    });
                                }
                            "
                            key="attendance_details"
                        >
                            <span>{{ $t("menu.attendance_details") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.attendance.summary',
                                    });
                                }
                            "
                            key="attendance_summary"
                        >
                            <span>{{ $t("menu.attendance_summary") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.attendances.index',
                                    });
                                }
                            "
                            key="attendances"
                        >
                            <span>{{ $t("menu.attendances") }}</span>
                        </a-menu-item>
                    </a-sub-menu>
                    <a-menu-item
                        v-if="
                            (permsArray.includes('news_view') ||
                                permsArray.includes('admin')) &&
                            willSubscriptionModuleVisible('news')
                        "
                        @click="
                            () => {
                                menuSelected();
                                $router.push({ name: 'admin.news.index' });
                            }
                        "
                        key="news"
                    >
                        <ReadOutlined />
                        <span>{{ $t("menu.news") }}</span>
                    </a-menu-item>
                    <a-sub-menu
                        key="payrolls"
                        v-if="
                            (permsArray.includes('pre_payments_view') ||
                                permsArray.includes(
                                    'increments_promotions_view'
                                ) ||
                                permsArray.includes('payrolls_view') ||
                                permsArray.includes('salary_settings') ||
                                permsArray.includes('admin')) &&
                            willSubscriptionModuleVisible('payrolls')
                        "
                    >
                        <template #title>
                            <span>
                                <ScheduleOutlined />
                                <span>{{ $t("menu.payrolls") }}</span>
                            </span>
                        </template>
                        <a-menu-item
                            v-if="
                                permsArray.includes('pre_payments_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.pre_payments.index',
                                    });
                                }
                            "
                            key="pre_payments"
                        >
                            <span>{{ $t("menu.pre_payments") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes(
                                    'increments_promotions_view'
                                ) || permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.increments_promotions.index',
                                    });
                                }
                            "
                            key="increments_promotions"
                        >
                            <span>{{ $t("menu.increments_promotions") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('payrolls_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.payrolls.index',
                                    });
                                }
                            "
                            key="payrolls"
                        >
                            <span>{{ $t("menu.payrolls") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('salary_settings') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.basic_salaries.index',
                                    });
                                }
                            "
                            key="basic_salaries"
                        >
                            <span>{{ $t("menu.basic_salaries") }}</span>
                        </a-menu-item>
                    </a-sub-menu>
                    <a-menu-item
                        v-if="
                            (permsArray.includes('company_policies_view') ||
                                permsArray.includes('admin')) &&
                            willSubscriptionModuleVisible('company_policies')
                        "
                        @click="
                            () => {
                                menuSelected();
                                $router.push({
                                    name: 'admin.company-policies.index',
                                });
                            }
                        "
                        key="company_policies"
                    >
                        <FilePptOutlined />
                        <span>{{ $t("menu.company_policies") }}</span>
                    </a-menu-item>

                    <a-sub-menu
                        key="offboardings"
                        v-if="
                            (permsArray.includes('warnings_view') ||
                                permsArray.includes('resignations_view') ||
                                permsArray.includes('terminations_view') ||
                                permsArray.includes('complaints_view') ||
                                permsArray.includes('admin')) &&
                            willSubscriptionModuleVisible('offboardings')
                        "
                    >
                        <template #title>
                            <span>
                                <LaptopOutlined />
                                <span>{{ $t("menu.offboardings") }}</span>
                            </span>
                        </template>
                        <a-menu-item
                            v-if="
                                permsArray.includes('warnings_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.warnings.index',
                                    });
                                }
                            "
                            key="warnings"
                        >
                            <span>{{ $t("menu.warnings") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('resignations_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.resignations.index',
                                    });
                                }
                            "
                            key="resignations"
                        >
                            <span>{{ $t("menu.resignations") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('terminations_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.terminations.index',
                                    });
                                }
                            "
                            key="terminations"
                        >
                            <span>{{ $t("menu.terminations") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('complaints_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.complaints.index',
                                    });
                                }
                            "
                            key="complaints"
                        >
                            <span>{{ $t("menu.complaints") }}</span>
                        </a-menu-item>
                    </a-sub-menu>

                    <a-sub-menu
                        key="letter_heads"
                        v-if="
                            (permsArray.includes(
                                'letter_head_templates_view'
                            ) ||
                                permsArray.includes('generates_view') ||
                                permsArray.includes('admin')) &&
                            willSubscriptionModuleVisible('letter_heads')
                        "
                    >
                        <template #title>
                            <span>
                                <FolderOpenOutlined />
                                <span>{{ $t("menu.letter_heads") }}</span>
                            </span>
                        </template>
                        <a-menu-item
                            v-if="
                                permsArray.includes(
                                    'letter_head_templates_view'
                                ) || permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.email-templates.index',
                                    });
                                }
                            "
                            key="templates"
                        >
                            <span>{{ $t("menu.templates") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('generates_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.generates.index',
                                    });
                                }
                            "
                            key="generates"
                        >
                            <span>{{ $t("menu.generates") }}</span>
                        </a-menu-item>
                    </a-sub-menu>

                    <a-sub-menu
                        key="forms"
                        v-if="
                            (permsArray.includes('forms_view') ||
                                permsArray.includes('indicators_view') ||
                                permsArray.includes('feedbacks_view') ||
                                permsArray.includes('admin')) &&
                            willSubscriptionModuleVisible('survey_forms')
                        "
                    >
                        <template #title>
                            <span>
                                <FolderOpenOutlined />
                                <span>{{ $t("menu.survey_forms") }}</span>
                            </span>
                        </template>
                        <a-menu-item
                            v-if="
                                permsArray.includes('forms_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({ name: 'admin.forms.index' });
                                }
                            "
                            key="forms"
                        >
                            <span>{{ $t("menu.forms") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('indicators_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.indicators.index',
                                    });
                                }
                            "
                            key="indicators"
                        >
                            <span>{{ $t("menu.indicators") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                permsArray.includes('feedbacks_view') ||
                                permsArray.includes('admin')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.feedbacks.index',
                                    });
                                }
                            "
                            key="feedbacks"
                        >
                            <span>{{ $t("menu.feedbacks") }}</span>
                        </a-menu-item>
                    </a-sub-menu>
                    <!-- <a-menu-item
                        v-if="
                            permsArray.includes('reports_view') ||
                            permsArray.includes('admin')
                        "
                        @click="
                            () => {
                                menuSelected();
                                $router.push({ name: 'admin.reports.index' });
                            }
                        "
                        key="reports"
                    >
                        <LineChartOutlined />
                        <span>{{ $t("menu.reports") }}</span>
                    </a-menu-item> -->
                    <a-sub-menu
                        key="accounts"
                        v-if="
                            (permsArray.includes('accounts_view') ||
                                permsArray.includes('payees_view') ||
                                permsArray.includes('payers_view') ||
                                permsArray.includes(
                                    'deposit_categories_view'
                                ) ||
                                permsArray.includes(
                                    'expense_categories_view'
                                ) ||
                                permsArray.includes('deposits_view') ||
                                permsArray.includes('expenses_view') ||
                                permsArray.includes('admin')) &&
                            (willSubscriptionModuleVisible('accounts') ||
                                willSubscriptionModuleVisible('expenses'))
                        "
                    >
                        <template #title>
                            <span>
                                <BankOutlined />
                                <span>{{ $t("menu.finance") }}</span>
                            </span>
                        </template>
                        <a-menu-item
                            v-if="
                                (permsArray.includes('accounts_view') ||
                                    permsArray.includes('admin')) &&
                                willSubscriptionModuleVisible('accounts')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.accounts.index',
                                    });
                                }
                            "
                            key="accounts"
                        >
                            <span>{{ $t("menu.accounts") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                (permsArray.includes('payees_view') ||
                                    permsArray.includes('admin')) &&
                                willSubscriptionModuleVisible('accounts')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.payees.index',
                                    });
                                }
                            "
                            key="payees"
                        >
                            <span>{{ $t("menu.payees") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                (permsArray.includes('payers_view') ||
                                    permsArray.includes('admin')) &&
                                willSubscriptionModuleVisible('accounts')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.payers.index',
                                    });
                                }
                            "
                            key="payers"
                        >
                            <span>{{ $t("menu.payers") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                (permsArray.includes(
                                    'deposit_categories_view'
                                ) ||
                                    permsArray.includes('admin')) &&
                                willSubscriptionModuleVisible('accounts')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.deposit_categories.index',
                                    });
                                }
                            "
                            key="deposit_categories"
                        >
                            <span>{{ $t("menu.deposit_categories") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                (permsArray.includes(
                                    'expense_categories_view'
                                ) ||
                                    permsArray.includes('admin')) &&
                                willSubscriptionModuleVisible('expenses')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.expense_categories.index',
                                    });
                                }
                            "
                            key="expense_categories"
                        >
                            <span>{{ $t("menu.expense_categories") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                (permsArray.includes('deposits_view') ||
                                    permsArray.includes('admin')) &&
                                willSubscriptionModuleVisible('accounts')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.deposits.index',
                                    });
                                }
                            "
                            key="deposits"
                        >
                            <span>{{ $t("menu.deposits") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            v-if="
                                (permsArray.includes('expenses_view') ||
                                    permsArray.includes('admin')) &&
                                willSubscriptionModuleVisible('expenses')
                            "
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.expenses.index',
                                    });
                                }
                            "
                            key="expenses"
                        >
                            <span>{{ $t("menu.expenses") }}</span>
                        </a-menu-item>
                    </a-sub-menu>

                    <component
                        v-for="(appModule, index) in appModules"
                        :key="index"
                        v-bind:is="appModule + 'Menu'"
                        @menuSelected="menuSelected"
                    />

                    <a-menu-item
                        @click="
                            () => {
                                menuSelected();
                                $router.push({
                                    name: 'admin.settings.profile.index',
                                });
                            }
                        "
                        key="settings"
                    >
                        <SettingOutlined />
                        <span>{{ $t("menu.settings") }}</span>
                    </a-menu-item>

                    <a-menu-item
                        v-if="
                            appType == 'saas' &&
                            appSetting.x_admin_id == user.xid
                        "
                        @click="
                            () => {
                                menuSelected();
                                $router.push({
                                    name: 'admin.subscription.current_plan',
                                });
                            }
                        "
                        key="subscription"
                    >
                        <DollarCircleOutlined />
                        <span>{{ $t("menu.subscription") }}</span>
                    </a-menu-item>

                    <a-menu-item @click="logout" key="logout">
                        <LogoutOutlined />
                        <span>{{ $t("menu.logout") }}</span>
                    </a-menu-item>
                </a-menu>
            </div>
            <div class="scroll-container" v-else>
                <a-menu
                    :theme="
                        themeMode == 'dark'
                            ? 'light'
                            : appSetting.left_sidebar_theme
                    "
                    :openKeys="openKeys"
                    v-model:selectedKeys="selectedKeys"
                    :mode="mode"
                    @openChange="onOpenChange"
                    :style="{ borderRight: 'none' }"
                >
                    <a-menu-item
                        @click="
                            () => {
                                menuSelected();
                                $router.push({
                                    name: 'admin.self.dashboard.index',
                                });
                            }
                        "
                        key="dashboard"
                    >
                        <HomeOutlined />
                        <span>{{ $t("menu.dashboard") }}</span>
                    </a-menu-item>

                    <a-menu-item
                        v-if="willSubscriptionModuleVisible('assets')"
                        @click="
                            () => {
                                menuSelected();
                                $router.push({
                                    name: 'admin.self.assets.index',
                                });
                            }
                        "
                        key="assets"
                        ><GiftOutlined />
                        <span>{{ $t("menu.assets") }}</span>
                    </a-menu-item>
                    <a-menu-item
                        v-if="willSubscriptionModuleVisible('holidays')"
                        @click="
                            () => {
                                menuSelected();
                                $router.push({
                                    name: 'admin.self.holidays.index',
                                });
                            }
                        "
                        key="holidays"
                        ><HeartOutlined />
                        <span>{{ $t("menu.holidays") }}</span>
                    </a-menu-item>
                    <a-menu-item
                        v-if="willSubscriptionModuleVisible('appreciations')"
                        @click="
                            () => {
                                menuSelected();
                                $router.push({
                                    name: 'admin.self.appreciations.index',
                                });
                            }
                        "
                        key="appreciations"
                        ><TrophyOutlined />
                        <span>{{ $t("menu.appreciations") }}</span>
                    </a-menu-item>
                    <a-sub-menu
                        v-if="willSubscriptionModuleVisible('leaves')"
                        key="leaves"
                    >
                        <template #title>
                            <span>
                                <CarOutlined />
                                <span>{{ $t("menu.leaves") }}</span>
                            </span>
                        </template>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.self.leaves.index',
                                    });
                                }
                            "
                            key="leaves"
                        >
                            <span>{{ $t("menu.leaves") }}</span>
                        </a-menu-item>

                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.self.remaining-leaves.index',
                                    });
                                }
                            "
                            key="remaining_leaves"
                        >
                            <span>{{ $t("menu.remaining_leaves") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.self.unpaid-leaves.index',
                                    });
                                }
                            "
                            key="unpaid_leaves"
                        >
                            <span>{{ $t("menu.unpaid_leaves") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.self.paid-leaves.index',
                                    });
                                }
                            "
                            key="paid_leaves"
                        >
                            <span>{{ $t("menu.paid_leaves") }}</span>
                        </a-menu-item>
                    </a-sub-menu>
                    <a-sub-menu key="attendances">
                        <template #title>
                            <span>
                                <ProfileOutlined />
                                <span>{{ $t("menu.attendances") }}</span>
                            </span>
                        </template>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.self.attendance.details',
                                    });
                                }
                            "
                            key="attendance_details"
                        >
                            <span>{{ $t("menu.attendance_details") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.self.attendance.summary',
                                    });
                                }
                            "
                            key="attendance_summary"
                        >
                            <span>{{ $t("menu.attendance_summary") }}</span>
                        </a-menu-item>
                    </a-sub-menu>
                    <a-menu-item
                        v-if="willSubscriptionModuleVisible('news')"
                        @click="
                            () => {
                                menuSelected();
                                $router.push({ name: 'admin.self.news.index' });
                            }
                        "
                        key="news"
                    >
                        <ReadOutlined />
                        <span>{{ $t("menu.news") }}</span>
                    </a-menu-item>
                    <a-sub-menu
                        v-if="willSubscriptionModuleVisible('offboardings')"
                        key="offboardings"
                    >
                        <template #title>
                            <span>
                                <LaptopOutlined />
                                <span>{{ $t("menu.offboardings") }}</span>
                            </span>
                        </template>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.self.warnings.index',
                                    });
                                }
                            "
                            key="warnings"
                        >
                            <WomanOutlined />
                            <span>{{ $t("menu.warnings") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.self.resignations.index',
                                    });
                                }
                            "
                            key="resignations"
                        >
                            <ReconciliationOutlined />
                            <span>{{ $t("menu.resignations") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.self.complaints.index',
                                    });
                                }
                            "
                            key="complaints"
                        >
                            <CopyrightOutlined />
                            <span>{{ $t("menu.complaints") }}</span>
                        </a-menu-item>
                    </a-sub-menu>

                    <a-menu-item
                        v-if="willSubscriptionModuleVisible('letter_heads')"
                        @click="
                            () => {
                                menuSelected();
                                $router.push({
                                    name: 'admin.self.generates.index',
                                });
                            }
                        "
                        key="generates"
                    >
                        <SnippetsOutlined />
                        <span>{{ $t("menu.letter_heads") }}</span>
                    </a-menu-item>
                    <a-menu-item
                        v-if="willSubscriptionModuleVisible('expenses')"
                        key="expenses"
                        @click="
                            $router.push({ name: 'admin.self.expenses.index' })
                        "
                    >
                        <template #icon>
                            <UserOutlined />
                        </template>
                        {{ $t("menu.expenses") }}
                    </a-menu-item>

                    <a-sub-menu
                        v-if="willSubscriptionModuleVisible('payrolls')"
                        key="payrolls"
                    >
                        <template #title>
                            <span>
                                <ScheduleOutlined />
                                <span>{{ $t("menu.payrolls") }}</span>
                            </span>
                        </template>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.self.pre_payments.index',
                                    });
                                }
                            "
                            key="pre_payments"
                        >
                            <span>{{ $t("menu.pre_payments") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.self.increments_payments.index',
                                    });
                                }
                            "
                            key="increments_promotions"
                        >
                            <span>{{ $t("menu.increments_promotions") }}</span>
                        </a-menu-item>
                        <a-menu-item
                            @click="
                                () => {
                                    menuSelected();
                                    $router.push({
                                        name: 'admin.self.payrolls.index',
                                    });
                                }
                            "
                            key="payrolls"
                        >
                            <span>{{ $t("menu.payrolls") }}</span>
                        </a-menu-item>
                    </a-sub-menu>
                    <a-menu-item
                        v-if="willSubscriptionModuleVisible('company_policies')"
                        @click="
                            () => {
                                menuSelected();
                                $router.push({
                                    name: 'admin.self.company-policies.index',
                                });
                            }
                        "
                        key="company_policies"
                    >
                        <FilePptOutlined />
                        <span>{{ $t("menu.company_policies") }}</span>
                    </a-menu-item>

                    <a-menu-item
                        v-if="willSubscriptionModuleVisible('survey_forms')"
                        @click="
                            () => {
                                menuSelected();
                                $router.push({
                                    name: 'admin.self.assigned_survey.index',
                                });
                            }
                        "
                        key="assigned_survey"
                        ><PushpinOutlined />
                        <span>{{ $t("menu.assigned_survey") }}</span>
                    </a-menu-item>
                    <a-menu-item
                        key="custom_data_fields"
                        @click="
                            $router.push({
                                name: 'admin.self.custom-data-fields.index',
                            })
                        "
                    >
                        <template #icon>
                            <PicCenterOutlined />
                        </template>
                        {{ $t("menu.custom_data_fields") }}
                    </a-menu-item>
                    <a-menu-item
                        key="profile"
                        @click="
                            $router.push({ name: 'admin.self.profile.index' })
                        "
                    >
                        <template #icon>
                            <UserOutlined />
                        </template>
                        {{ $t("menu.profile") }}
                    </a-menu-item>

                    <component
                        v-for="(appModule, index) in appModules"
                        :key="index"
                        v-bind:is="appModule + 'Menu'"
                        @menuSelected="menuSelected"
                    />

                    <a-menu-item @click="logout" key="logout">
                        <LogoutOutlined />
                        <span>{{ $t("menu.logout") }}</span>
                    </a-menu-item>
                </a-menu>
            </div>
        </div>
    </a-layout-sider>
</template>

<script>
import { defineComponent, ref, watch, onMounted, computed } from "vue";
import { Layout } from "ant-design-vue";
import { useAuthStore } from "../../main/store/authStore";
import { useRoute } from "vue-router";
import {
    HomeOutlined,
    LogoutOutlined,
    UserOutlined,
    SettingOutlined,
    CloseOutlined,
    ShoppingOutlined,
    ShoppingCartOutlined,
    AppstoreOutlined,
    ShopOutlined,
    BarChartOutlined,
    CalculatorOutlined,
    TeamOutlined,
    WalletOutlined,
    BankOutlined,
    RocketOutlined,
    LaptopOutlined,
    CarOutlined,
    DollarCircleOutlined,
    CrownOutlined,
    ProfileOutlined,
    TransactionOutlined,
    ToolOutlined,
    ScheduleOutlined,
    ReadOutlined,
    FieldTimeOutlined,
    FolderOpenOutlined,
    FilePptOutlined,
    TabletOutlined,
    TagsOutlined,
    GiftOutlined,
    HeartOutlined,
    TrophyOutlined,
    FrownOutlined,
    PushpinOutlined,
    ReconciliationOutlined,
    WomanOutlined,
    SnippetsOutlined,
    CopyrightOutlined,
    TagOutlined,
    LineChartOutlined,
    PicCenterOutlined,
} from "@ant-design/icons-vue";
import common from "../../common/composable/common";
const { Sider } = Layout;

export default defineComponent({
    components: {
        Sider,
        Layout,

        HomeOutlined,
        LogoutOutlined,
        UserOutlined,
        SettingOutlined,
        CloseOutlined,
        ShoppingOutlined,
        ShoppingCartOutlined,
        AppstoreOutlined,
        ShopOutlined,
        BarChartOutlined,
        CalculatorOutlined,
        TeamOutlined,
        WalletOutlined,
        BankOutlined,
        RocketOutlined,
        LaptopOutlined,
        CarOutlined,
        DollarCircleOutlined,
        CrownOutlined,
        ProfileOutlined,
        TransactionOutlined,
        ToolOutlined,
        ScheduleOutlined,
        ReadOutlined,
        FieldTimeOutlined,
        FolderOpenOutlined,
        FilePptOutlined,
        TagOutlined,
        TabletOutlined,
        TagsOutlined,
        GiftOutlined,
        HeartOutlined,
        TrophyOutlined,
        FrownOutlined,
        PushpinOutlined,
        ReconciliationOutlined,
        WomanOutlined,
        SnippetsOutlined,
        CopyrightOutlined,
        LineChartOutlined,
        PicCenterOutlined,
    },
    setup(props, { emit }) {
        const {
            appSetting,
            appType,
            user,
            permsArray,
            appModules,
            menuCollapsed,
            willSubscriptionModuleVisible,
            themeMode,
        } = common();
        const rootSubmenuKeys = [
            "dashboard",
            "users",
            "settings",
            "subscription",
        ];
        const authStore = useAuthStore();
        const route = useRoute();

        const innerWidth = window.innerWidth;
        const openKeys = ref([]);
        const selectedKeys = ref([]);
        const mode = ref("inline");
        const activeKey = ref(user.value.is_manager == 1 ? "manager" : "self");
        const leftbarHeight =
            user.value.is_manager == 1
                ? "calc(100vh - 124px)"
                : "calc(100vh - 61px)";

        onMounted(() => {
            if (route.meta.barKey && route.meta.barKey === "self") {
                activeKey.value = "self";
            }
            var menuKey =
                typeof route.meta.menuKey == "function"
                    ? route.meta.menuKey(route)
                    : route.meta.menuKey;

            if (route.meta.menuParent == "settings") {
                menuKey = "settings";
            }

            if (route.meta.menuParent == "subscription") {
                menuKey = "subscription";
            }

            if (innerWidth <= 991) {
                openKeys.value = [];
            } else {
                openKeys.value = menuCollapsed.value
                    ? []
                    : [route.meta.menuParent];
            }

            selectedKeys.value = [menuKey.replace("-", "_")];
        });

        const logout = () => {
            authStore.logoutAction();
        };

        const menuSelected = () => {
            if (innerWidth <= 991) {
                authStore.updateMenuCollapsed(true);
            }
        };

        const onOpenChange = (currentOpenKeys) => {
            const latestOpenKey = currentOpenKeys.find(
                (key) => openKeys.value.indexOf(key) === -1
            );

            if (rootSubmenuKeys.indexOf(latestOpenKey) === -1) {
                openKeys.value = currentOpenKeys;
            } else {
                openKeys.value = latestOpenKey ? [latestOpenKey] : [];
            }
        };

        watch(route, (newVal, oldVal) => {
            const menuKey =
                typeof newVal.meta.menuKey == "function"
                    ? newVal.meta.menuKey(newVal)
                    : newVal.meta.menuKey;

            if (innerWidth <= 991) {
                openKeys.value = [];
            } else {
                openKeys.value = [newVal.meta.menuParent];
            }

            if (newVal.meta.menuParent == "settings") {
                selectedKeys.value = ["settings"];
            } else if (newVal.meta.menuParent == "subscription") {
                selectedKeys.value = ["subscription"];
            } else {
                selectedKeys.value = [menuKey.replace("-", "_")];
            }
        });

        watch(
            () => menuCollapsed.value,
            (newVal, oldVal) => {
                const menuKey =
                    typeof route.meta.menuKey == "function"
                        ? route.meta.menuKey(route)
                        : route.meta.menuKey;

                if (innerWidth <= 991 && menuCollapsed.value) {
                    openKeys.value = [];
                } else {
                    openKeys.value = menuCollapsed.value
                        ? []
                        : [route.meta.menuParent];
                }

                if (route.meta.menuParent == "settings") {
                    selectedKeys.value = ["settings"];
                } else if (route.meta.menuParent == "subscription") {
                    selectedKeys.value = ["subscription"];
                } else {
                    selectedKeys.value = [menuKey.replace("-", "_")];
                }
            }
        );

        return {
            mode,
            selectedKeys,
            openKeys,
            logout,
            innerWidth: window.innerWidth,

            onOpenChange,
            menuSelected,
            menuCollapsed,
            appSetting,
            appType,
            user,
            permsArray,
            appModules,
            willSubscriptionModuleVisible,
            activeKey,
            themeMode,
            leftbarHeight,
            authStore,
        };
    },
});
</script>

<style lang="less">
.main-sidebar .ps {
    height: v-bind(leftbarHeight);
}

@media only screen and (max-width: 1150px) {
    .ant-layout-sider.ant-layout-sider-collapsed {
        left: -80px !important;
    }
}

/* Custom scrollbar */
.scroll-container {
    overflow-y: auto;
    height: v-bind(leftbarHeight); /* Set height as needed */
}

.scroll-container::-webkit-scrollbar {
    width: 8px;
}

.scroll-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.scroll-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
