import CommonAdminSettings from "./common/adminSettings";

export default [{
    path: "/admin/settings/",
    component: () =>
        import("../../common/layouts/Admin.vue"),
    children: [{
        path: "company",
        component: () =>
            import("../views/settings/company/Edit.vue"),
        name: "admin.settings.company.index",
        meta: {
            requireAuth: true,
            menuParent: "settings",
            menuKey: (route) => "company",
            permission: "companies_edit",
        },
    },
    {
        path: "profile",
        component: () =>
            import("../views/settings/profile/Edit.vue"),
        name: "admin.settings.profile.index",
        meta: {
            requireAuth: true,
            menuParent: "settings",
            menuKey: (route) => "profile",
        },
    },
    {
        path: "langs",
        component: () =>
            import("../views/settings/translations/langs/index.vue"),
        name: "admin.settings.langs.index",
        meta: {
            requireAuth: true,
            menuParent: "settings",
            menuKey: (route) => "translations",
            permission: "translations_view",
        },
    },
    {
        path: "roles",
        component: () =>
            import("../views/settings/roles/index.vue"),
        name: "admin.settings.roles.index",
        meta: {
            requireAuth: true,
            menuParent: "settings",
            menuKey: (route) => "roles",
            permission: "roles_view",
        },
    },
    {
        path: "currencies",
        component: () =>
            import("../views/settings/currency/index.vue"),
        name: "admin.settings.currencies.index",
        meta: {
            requireAuth: true,
            menuParent: "settings",
            menuKey: (route) => "currencies",
            permission: "currencies_view",
        },
    },
    {
        path: "locations",
        component: () =>
            import("../views/settings/location/index.vue"),
        name: "admin.settings.locations.index",
        meta: {
            requireAuth: true,
            menuParent: "settings",
            menuKey: (route) => "locations",
            permission: "locations_view",
        },
    },
    {
        path: "salary-groups",
        component: () =>
            import(
                "../views/settings/payroll-settings/salary-groups/index.vue"
            ),
        name: "admin.settings.salary_groups.index",
        meta: {
            requireAuth: true,
            menuParent: "settings",
            menuKey: (route) => "salary_groups",
            permission: "salary_groups_view",
        },
    },
    {
        path: "salary-components",
        component: () =>
            import(
                "../views/settings/payroll-settings/salary-components/index.vue"
            ),
        name: "admin.settings.salary_components.index",
        meta: {
            requireAuth: true,
            menuParent: "settings",
            menuKey: (route) => "salary_components",
            permission: "salary_components_view",
        },
    },
    {
        path: "employee-work-status",
        component: () =>
            import("../views/settings/employee-work-status/index.vue"),
        name: "admin.settings.employee_work_status.index",
        meta: {
            requireAuth: true,
            menuParent: "settings",
            menuKey: (route) => "employee_work_status",
            permission: "employee_work_status_view",
        },
    },
    {
        path: "pdf-fonts",
        component: () =>
            import("../views/settings/pdf-fonts/index.vue"),
        name: "admin.settings.pdf-fonts.index",
        meta: {
            requireAuth: true,
            menuParent: "settings",
            menuKey: (route) => "pdf-fonts",
            permission: "pdf_fonts_view",
        },
    },
    {
        path: "employee-custom-fields",
        component: () =>
            import("../views/settings/employee-custom-fields/index.vue"),
        name: "admin.settings.employee-custom-fields.index",
        meta: {
            requireAuth: true,
            menuParent: "settings",
            menuKey: (route) => "employee_custom_fields",
            permission: "employee_custom_fields_view",
        },
    },
    // We put email templates here because it will be always
    // visible for saas or non-saas admin panel
    {
        path: "email-templates",
        component: () => import("../views/settings/email/templates/index.vue"),
        name: `admin.settings.email_templates.index`,
        meta: {
            requireAuth: true,
            menuParent: "settings",
            menuKey: (route) => "email_settings",
            permission: "email_edit",
        },
    },
    ...CommonAdminSettings,
    ],
},];
