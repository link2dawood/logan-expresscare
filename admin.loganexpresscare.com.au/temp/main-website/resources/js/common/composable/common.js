import { computed, ref } from "vue";
import moment from "moment";
import { useAuthStore } from "../../main/store/authStore";
import { useI18n } from "vue-i18n";
import { forEach, includes } from "lodash-es";
import { checkUserPermission } from "../scripts/functions";
import dayjs from "dayjs";

moment.suppressDeprecationWarnings = true;
import utc from "dayjs/plugin/utc";
import timezone from "dayjs/plugin/timezone";
dayjs.extend(utc);
dayjs.extend(timezone);

const common = () => {
    const authStore = useAuthStore();
    const { t } = useI18n();
    const appType = window.config.app_type;
    const menuCollapsed = computed(() => authStore.menuCollapsed);
    const cssSettings = computed(() => authStore.cssSettings);
    const appModules = computed(() => authStore.activeModules);
    const visibleSubscriptionModules = computed(
        () => authStore.visibleSubscriptionModules
    );
    const globalSetting = computed(() => authStore.globalSetting);
    const appSetting = computed(() => authStore.appSetting);
    const addMenus = computed(() => authStore.addMenus);
    const selectedLang = computed(() => authStore.lang);
    const user = computed(() => authStore.user);
    const themeMode = computed(() => authStore.themeMode);
    const tabKeys = ref("appreciations_view");

    const pendingTabs = [
        {
            key: "leaves",
            tab: t("menu.leaves"),
        },
        {
            key: "expenses",
            tab: t("menu.expenses"),
        },
    ];

    const statusColors = {
        enabled: "success",
        disabled: "error",
    };

    const userStatusColors = {
        active: "success",
        inactive: "error",
    };

    const assetsColors = {
        working: "success",
        not_working: "error",
    };

    const openingColors = {
        open: "success",
        closed: "error",
    };

    const newsColors = {
        draft: "warning",
        published: "success",
    };

    const resignationColors = {
        approved: "success",
        rejected: "error",
        pending: "yellow",
    };

    const complaintsColors = {
        approved: "success",
        rejected: "error",
        pending: "yellow",
    };

    const expenseStatusColors = {
        approved: "success",
        rejected: "error",
        pending: "yellow",
    };

    const leaveRequestColors = {
        approved: "success",
        rejected: "error",
        pending: "yellow",
    };

    const employeeFields = {
        yes: "success",
        no: "error",
    };

    const userStatus = [
        {
            key: "enabled",
            value: t("common.enabled"),
        },
        {
            key: "disabled",
            value: t("common.disabled"),
        },
    ];

    const applicationStages = [
        {
            key: "initial",
            value: t("common.initial"),
        },
        {
            key: "shortlist",
            value: t("common.shortlist"),
        },
        {
            key: "interview",
            value: t("common.interview"),
        },
        {
            key: "selected",
            value: t("common.selected"),
        },
        {
            key: "rejected",
            value: t("common.rejected"),
        },
        {
            key: "closed",
            value: t("common.closed"),
        },
    ];

    const disabledDate = (current) => {
        // Can not select days before today and today
        return current && current > moment().endOf("day");
    };

    const reFetchListOfData = (url) => {
        var dataList = [];
        axiosAdmin.get(url).then((response) => {
            dataList = response.data;
            return dataList;
        });
    };

    const dayjsObject = (date) => {
        if (date == undefined) {
            return dayjs().tz(appSetting.value.timezone);
        } else {
            return dayjs(date).tz(appSetting.value.timezone);
        }
    };

    const formatDate = (date) => {
        if (date == undefined) {
            return dayjs()
                .tz(appSetting.value.timezone)
                .format(`${appSetting.value.date_format}`);
        } else {
            return dayjs(date)
                .tz(appSetting.value.timezone)
                .format(`${appSetting.value.date_format}`);
        }
    };

    const formatDateTime = (dateTime) => {
        if (dateTime == undefined) {
            return dayjs()
                .tz(appSetting.value.timezone)
                .format(
                    `${appSetting.value.date_format} ${appSetting.value.time_format}`
                );
        } else {
            return dayjs(dateTime)
                .tz(appSetting.value.timezone)
                .format(
                    `${appSetting.value.date_format} ${appSetting.value.time_format}`
                );
        }
    };

    const formatTime = (dateTime) => {
        if (dateTime != undefined) {
            return dayjs(dateTime)
                .tz(appSetting.value.timezone)
                .format(`${appSetting.value.time_format}`);
        }
        return "";
    };

    const formatOnlyTime = (Time) => {
        if (Time != undefined) {
            return dayjs(Time, "HH:mm:ss").format(`hh:mm A`);
        }
        // .tz(appSetting.value.timezone)
        return "";
    };

    const formatAmount = (amount) => {
        return parseFloat(parseFloat(amount).toFixed(2));
    };

    const formatAmountCurrency = (amount) => {
        const newAmount = parseFloat(Math.abs(amount))
            .toFixed(2)
            .toString()
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        if (appSetting.value.currency.position == "front") {
            var newAmountString = `${appSetting.value.currency.symbol}${newAmount}`;
        } else {
            var newAmountString = `${newAmount}${appSetting.value.currency.symbol}`;
        }

        return amount < 0 ? `- ${newAmountString}` : newAmountString;
    };

    const formatAmountUsingCurrencyObject = (amount, currency) => {
        const newAmount = parseFloat(Math.abs(amount))
            .toFixed(2)
            .toString()
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        if (currency.position == "front") {
            var newAmountString = `${currency.symbol}${newAmount}`;
        } else {
            var newAmountString = `${newAmount}${currency.symbol}`;
        }

        return amount < 0 ? `- ${newAmountString}` : newAmountString;
    };

    const calculateFilterString = (filters) => {
        var filterString = "";

        forEach(filters, (filterValue, filterKey) => {
            if (filterValue != undefined) {
                filterString += `${filterKey} eq "${filterValue}" and `;
            }
        });

        if (filterString.length > 0) {
            filterString = filterString.substring(0, filterString.length - 4);
        }

        return filterString;
    };

    const checkPermission = (permissionName) =>
        checkUserPermission(permissionName, user.value);

    const updatePageTitle = (pageName) => {
        authStore.updatePageTitle(t(`menu.${pageName}`));
    };

    const permsArray = computed(() => {
        const permsArrayList =
            user && user.value && user.value.role ? [user.value.role.name] : [];

        if (user && user.value && user.value.role) {
            forEach(user.value.role.perms, (permission) => {
                permsArrayList.push(permission.name);
            });
        }

        return permsArrayList;
    });

    const slugify = (text) => {
        return text
            .toString()
            .toLowerCase()
            .replace(/\s+/g, "-") // Replace spaces with -
            .replace(/[^\w\-]+/g, "") // Remove all non-word chars
            .replace(/\-\-+/g, "-") // Replace multiple - with single -
            .replace(/^-+/, "") // Trim - from start of text
            .replace(/-+$/, ""); // Trim - from end of text
    };

    const convertToPositive = (amount) => {
        return amount < 0 ? amount * -1 : amount;
    };

    const willSubscriptionModuleVisible = (moduleName) => {
        if (appType == "non-saas") {
            return true;
        } else {
            if (
                appSetting.value.subscription_plan &&
                appSetting.value.subscription_plan.modules
            ) {
                return includes(
                    appSetting.value.subscription_plan.modules,
                    moduleName
                );
            } else {
                return false;
            }
        }
    };

    const setTabKey = () => {
        const keys = [
            "appreciations_view",
            "warnings_view",
            "assets_view",
            "leaves_view",
            "complaints_view",
            "pre_payments_view",
            "payrolls_view",
            "increments_promotions_view",
            "feedbacks_view",
            "attendances_view",
        ];

        for (const str of keys) {
            if (
                permsArray.value.includes(str) ||
                permsArray.value.includes("admin")
            ) {
                tabKeys.value = str;
                break;
            }
        }
    };

    return {
        menuCollapsed,
        appSetting,
        appType,
        addMenus,
        selectedLang,
        user,
        checkPermission,
        permsArray,
        statusColors,
        userStatus,
        leaveRequestColors,
        disabledDate,
        formatAmount,
        formatAmountCurrency,
        formatAmountUsingCurrencyObject,
        convertToPositive,

        calculateFilterString,
        updatePageTitle,

        appModules,
        dayjs,
        formatDate,
        formatDateTime,
        formatTime,
        dayjsObject,
        slugify,
        employeeFields,
        cssSettings,
        globalSetting,

        willSubscriptionModuleVisible,
        visibleSubscriptionModules,
        assetsColors,
        openingColors,
        applicationStages,
        newsColors,
        themeMode,
        resignationColors,
        complaintsColors,
        userStatusColors,
        expenseStatusColors,
        pendingTabs,
        reFetchListOfData,
        setTabKey,
        formatOnlyTime,
        tabKeys,
    };
};

export default common;
