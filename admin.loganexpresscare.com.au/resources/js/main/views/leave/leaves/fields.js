import { useI18n } from "vue-i18n";
import common from "@/common/composable/common";

const fields = () => {
    const addEditUrl = "leaves";
    const { t } = useI18n();
    const hashableColumns = ["leave_type_id", "user_id"];
    const { dayjs } = common();

    const initData = {
        user_id: undefined,
        leave_type_id: undefined,
        start_date: undefined,
        end_date: undefined,
        is_half_day: 0,
        half_leave: "before_break",
        reason: "",
        date: undefined,
        status: "pending",
    };

    const columns = [
        {
            title: t("leave.user_id"),
            dataIndex: "user_id",
        },
        {
            title: t("leave.leave_type"),
            dataIndex: "leave_type_id",
        },
        {
            title: t("leave.start_date"),
            dataIndex: "start_date",
        },
        {
            title: t("leave.end_date"),
            dataIndex: "end_date",
        },
        {
            title: t("leave.is_half_day"),
            dataIndex: "is_half_day",
        },
        {
            title: t("leave.total_days"),
            dataIndex: "total_leaves",
        },
        {
            title: t("leave.status"),
            dataIndex: "status",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("common.name"),
        },
    ];

    return {
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;
