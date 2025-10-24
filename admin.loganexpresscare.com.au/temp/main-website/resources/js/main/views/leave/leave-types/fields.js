import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "leave-types?fields=id,xid,count_type,name,is_paid,total_leaves,max_leaves_per_month,employeeSpecificLeaveCount{id,xid,user_id,x_user_id,total_leaves,x_leave_type_id}";
    const addEditUrl = "leave-types";
    const { t } = useI18n();
    const hashableColumns = [];

    const initData = {
        name: "",
        total_leaves: 0,
        max_leaves_per_month: "",
        is_paid: 0,
        count_type: "same_for_all",
    };

    const columns = [
        {
            title: t("leave_type.name"),
            dataIndex: "name",
        },
        {
            title: t("leave_type.total_leaves"),
            dataIndex: "total_leaves",
        },
        {
            title: t("leave_type.max_leaves_per_month"),
            dataIndex: "max_leaves_per_month",
        },
        {
            title: t("leave_type.is_paid"),
            dataIndex: "is_paid",
        },
        {
            title: t("leave_type.edit_count"),
            dataIndex: "edit_count",
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
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;
