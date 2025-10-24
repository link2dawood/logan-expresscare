export default [
    {
        path: "/admin/holidays/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "holidays",
                component: () => import("../views/holiday/holidays/index.vue"),
                name: "admin.holidays.index",
                meta: {
                    requireAuth: true,
                    menuParent: "holidays",
                    menuKey: (route) => "holidays",
                    holidayType: "holiday",
                    permission: "holidays_view",
                },
            },
            {
                path: "weekends",
                component: () => import("../views/holiday/holidays/index.vue"),
                name: "admin.weekends.index",
                meta: {
                    requireAuth: true,
                    menuParent: "holidays",
                    menuKey: (route) => "weekends",
                    holidayType: "weekend",
                    permission: "holidays_view",
                },
            },
            {
                path: "all-holidays",
                component: () => import("../views/holiday/all-holidays/index.vue"),
                name: "admin.all-holidays.index",
                meta: {
                    requireAuth: true,
                    menuParent: "holidays",
                    menuKey: (route) => "all_holidays",
                    holidayType: "holiday",
                    permission: "holidays_view",
                },
            },
        ],
    },
];
