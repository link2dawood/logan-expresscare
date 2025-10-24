export default [
    {
        path: "/admin/accounts/",
        component:  () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "accounts",
                component: () => import("../views/accountings/accounts/index.vue"),
                name: "admin.accounts.index",
                meta: {
                    requireAuth: true,
                    menuParent: "accounts",
                    menuKey: (route) => "accounts",
                },
            },
            {
                path: "payees",
                component:  () => import("../views/accountings/payees/index.vue"),
                name: "admin.payees.index",
                meta: {
                    requireAuth: true,
                    menuParent: "accounts",
                    menuKey: (route) => "payees",
                },
            },
            {
                path: "payers",
                component: () => import("../views/accountings/payers/index.vue"),
                name: "admin.payers.index",
                meta: {
                    requireAuth: true,
                    menuParent: "accounts",
                    menuKey: (route) => "payers",
                },
            },
            {
                path: "deposit-categories",
                component: () => import("../views/accountings/deposit-categories/index.vue"),
                name: "admin.deposit_categories.index",
                meta: {
                    requireAuth: true,
                    menuParent: "accounts",
                    menuKey: (route) => "deposit_categories",
                },
            },
            {
                path: "deposits",
                component: () => import("../views/accountings/deposits/index.vue"),
                name: "admin.deposits.index",
                meta: {
                    requireAuth: true,
                    menuParent: "accounts",
                    menuKey: (route) => "deposits",
                },
            },
            {
                path: '/admin/expense-categories',
                component: () => import("../views/accountings/expense-categories/index.vue"),
                name: 'admin.expense_categories.index',
                meta: {
                    requireAuth: true,
                    menuParent: "accounts",
                    menuKey: route => "expense_categories",
                    permission: "expense_categories_view",
                }
            },
            {
                path: '/admin/expenses',
                component:() => import("../views/accountings/expenses/index.vue"),
                name: 'admin.expenses.index',
                meta: {
                    requireAuth: true,
                    menuParent: "accounts",
                    menuKey: route => "expenses",
                    permission: "expenses_view",
                }
            }
        ],
    },
];
