import { ref } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "self/payrolls?fields=id,xid,user_id,x_user_id,user{id,xid,name},month,year,payment_date,status,net_salary,total_days,working_days,present_days,total_office_time,total_worked_time,half_days,late_days,paid_leaves,unpaid_leaves,holiday_count,pre_payment_amount,expense_amount,basic_salary,salary_amount";
    const addEditUrl = "payrolls";
    const { t } = useI18n();
    const hashableColumns = ["user_id"];

    const initData = {
        user_id: undefined,
        month: undefined,
        year: undefined,
        payment_date: undefined,
        status: "generated",
        net_salary: "",
    };

    const columns = ref([
        {
            title: t("payroll.net_salary"),
            dataIndex: "net_salary",
        },
        {
            title: t("payroll.month"),
            dataIndex: "month",
        },
        {
            title: t("payroll.payment_date"),
            dataIndex: "payment_date",
        },
        {
            title: t("payroll.status"),
            dataIndex: "status",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ]);

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
        url,
    };
};

export default fields;
